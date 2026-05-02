#!/usr/bin/env bash

set -Eeuo pipefail

# Optional external config file (e.g. /etc/baru-deploy.env)
DEPLOY_ENV_FILE="${DEPLOY_ENV_FILE:-}"
if [[ -n "${DEPLOY_ENV_FILE}" && -f "${DEPLOY_ENV_FILE}" ]]; then
    # shellcheck disable=SC1090
    source "${DEPLOY_ENV_FILE}"
fi

REPO_URL="${REPO_URL:-git@github.com:hilmanmaulana1237/baru.git}"
BRANCH="${BRANCH:-main}"
APP_DIR="${APP_DIR:-/var/www/baru}"
WEB_USER="${WEB_USER:-www-data}"
WEB_GROUP="${WEB_GROUP:-www-data}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-1}"
RUN_NPM_BUILD="${RUN_NPM_BUILD:-1}"
RUN_STORAGE_LINK="${RUN_STORAGE_LINK:-1}"
RUN_QUEUE_RESTART="${RUN_QUEUE_RESTART:-1}"
PHP_FPM_SERVICE="${PHP_FPM_SERVICE:-php8.3-fpm}"
WEB_SERVER_SERVICE="${WEB_SERVER_SERVICE:-nginx}"
HEALTHCHECK_URL="${HEALTHCHECK_URL:-}"

LOCK_FILE="/tmp/baru-deploy.lock"

if [[ "${1:-}" == "--help" ]]; then
    cat <<'HELP'
Usage:
  bash deploy.sh

Optional env vars:
  DEPLOY_ENV_FILE=/etc/baru-deploy.env
  REPO_URL=git@github.com:hilmanmaulana1237/baru.git
  BRANCH=main
  APP_DIR=/var/www/baru
  WEB_USER=www-data
  WEB_GROUP=www-data
  PHP_FPM_SERVICE=php8.3-fpm
  WEB_SERVER_SERVICE=nginx
  HEALTHCHECK_URL=https://your-domain.com/up

Notes:
  - First run: auto clone repo if APP_DIR does not exist.
  - Next run: auto git pull (fast-forward only).
HELP
    exit 0
fi

log() {
    printf '[deploy] %s\n' "$*"
}

fail() {
    printf '[deploy][error] %s\n' "$*" >&2
    exit 1
}

run_privileged() {
    if [[ "$(id -u)" -eq 0 ]]; then
        "$@"
    elif command -v sudo >/dev/null 2>&1; then
        sudo "$@"
    else
        fail "Need root privileges (or sudo) for command: $*"
    fi
}

ensure_cmd() {
    command -v "$1" >/dev/null 2>&1 || fail "Command not found: $1"
}

run_service_restart() {
    local service="$1"
    if [[ -z "${service}" ]]; then
        return 0
    fi

    if command -v systemctl >/dev/null 2>&1 && systemctl list-unit-files | grep -q "^${service}"; then
        log "Restarting service: ${service}"
        run_privileged systemctl restart "${service}"
    else
        log "Service not found or systemctl unavailable: ${service} (skipped)"
    fi
}

on_error() {
    local exit_code=$?
    log "Deploy failed at line ${BASH_LINENO[0]} (exit code ${exit_code})."
    exit "${exit_code}"
}
trap on_error ERR

ensure_cmd git
ensure_cmd "${PHP_BIN}"
ensure_cmd "${COMPOSER_BIN}"

if [[ "${RUN_NPM_BUILD}" == "1" ]]; then
    ensure_cmd "${NPM_BIN}"
fi

if command -v flock >/dev/null 2>&1; then
    exec 9>"${LOCK_FILE}"
    if ! flock -n 9; then
        fail "Another deployment process is running. Lock file: ${LOCK_FILE}"
    fi
else
    log "flock not found, skipping deploy lock"
fi

if [[ ! -d "${APP_DIR}/.git" ]]; then
    log "First deploy detected. Cloning repository into ${APP_DIR}"
    mkdir -p "$(dirname "${APP_DIR}")"
    git clone --branch "${BRANCH}" "${REPO_URL}" "${APP_DIR}"
else
    log "Existing app detected. Updating repository (${BRANCH})"
    git -C "${APP_DIR}" fetch origin "${BRANCH}"
    git -C "${APP_DIR}" checkout "${BRANCH}"
    git -C "${APP_DIR}" pull --ff-only origin "${BRANCH}"
fi

cd "${APP_DIR}"

if [[ ! -f ".env" && -f ".env.example" ]]; then
    log "Creating .env from .env.example"
    cp .env.example .env
fi

log "Installing PHP dependencies"
"${COMPOSER_BIN}" install --no-dev --prefer-dist --optimize-autoloader --no-interaction

if [[ "${RUN_NPM_BUILD}" == "1" && -f package.json ]]; then
    log "Installing Node dependencies and building assets"
    "${NPM_BIN}" ci --no-audit --no-fund
    "${NPM_BIN}" run build
else
    log "Skipping frontend build"
fi

if [[ -f artisan ]]; then
    if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
        log "Generating APP_KEY"
        "${PHP_BIN}" artisan key:generate --force
    fi

    if [[ "${RUN_MIGRATIONS}" == "1" ]]; then
        log "Running database migrations"
        "${PHP_BIN}" artisan migrate --force
    else
        log "Skipping migrations"
    fi

    log "Clearing and rebuilding Laravel caches"
    "${PHP_BIN}" artisan optimize:clear
    "${PHP_BIN}" artisan config:cache
    "${PHP_BIN}" artisan route:cache
    "${PHP_BIN}" artisan view:cache

    if [[ "${RUN_STORAGE_LINK}" == "1" ]]; then
        log "Ensuring storage symlink"
        "${PHP_BIN}" artisan storage:link || true
    fi

    if [[ "${RUN_QUEUE_RESTART}" == "1" ]]; then
        log "Restarting queue workers"
        "${PHP_BIN}" artisan queue:restart || true
    fi
fi

log "Setting directory ownership and permissions"
run_privileged chown -R "${WEB_USER}:${WEB_GROUP}" "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
run_privileged chmod -R ug+rwx "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"

run_service_restart "${PHP_FPM_SERVICE}"
run_service_restart "${WEB_SERVER_SERVICE}"

if [[ -n "${HEALTHCHECK_URL}" ]]; then
    log "Checking health endpoint: ${HEALTHCHECK_URL}"
    http_code="$(curl -s -o /dev/null -w "%{http_code}" "${HEALTHCHECK_URL}" || true)"
    log "Healthcheck HTTP status: ${http_code}"
fi

log "Deployment completed successfully."
