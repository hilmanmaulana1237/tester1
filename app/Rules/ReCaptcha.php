<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ReCaptcha implements ValidationRule
{
    /**
     * The action name for this reCAPTCHA validation.
     */
    protected string $action;

    /**
     * Create a new rule instance.
     */
    public function __construct(string $action = 'submit')
    {
        $this->action = $action;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip if no secret key configured
        if (empty(config('recaptcha.secret_key'))) {
            return;
        }

        // Skip for certain IPs (testing)
        if (in_array(request()->ip(), config('recaptcha.skip_ips', []))) {
            return;
        }

        // Verify with Google
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $result = $response->json();

        // Check if verification was successful
        if (!($result['success'] ?? false)) {
            $fail('Verifikasi reCAPTCHA gagal. Silakan coba lagi.');
            return;
        }

        // Check action matches
        if (($result['action'] ?? '') !== $this->action) {
            $fail('Verifikasi reCAPTCHA tidak valid.');
            return;
        }

        // Check score threshold
        $score = $result['score'] ?? 0;
        $threshold = config('recaptcha.score_threshold', 0.5);

        if ($score < $threshold) {
            $fail('Aktivitas mencurigakan terdeteksi. Silakan coba lagi.');
            return;
        }
    }
}
