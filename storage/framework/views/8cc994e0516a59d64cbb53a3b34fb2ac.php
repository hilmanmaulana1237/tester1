<div class="min-h-screen">
  <!-- Flash Messages -->
  <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
      class="fixed top-5 right-5 left-5 sm:left-auto z-50 max-w-md">
      <div class="bg-violet-50 border border-violet-200 rounded-lg p-4 shadow-lg">
        <div class="flex items-start">
          <svg class="w-5 h-5 text-violet-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-purple-800"><?php echo e(session('success')); ?></p>
          </div>
          <button @click="show = false" class="ml-4 text-violet-500 hover:text-purple-700">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path
                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

  <?php if(session()->has('error')): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
      class="fixed top-5 right-5 left-5 sm:left-auto z-50 max-w-md">
      <div class="bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg">
        <div class="flex items-start">
          <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-red-800"><?php echo e(session('error')); ?></p>
          </div>
          <button @click="show = false" class="ml-4 text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path
                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

  <!-- Android/Mobile View -->
  <div class="block sm:hidden">
    <div class="container mx-auto px-4 py-6 max-w-md">
      <!-- Page Header with Gradient -->
      <header class="mb-6">
        <div
          class="relative overflow-hidden bg-gradient-to-br from-violet-600 via-purple-700 to-purple-700 rounded-2xl p-6 shadow-2xl shadow-violet-500/20">
          <!-- Decorative Elements -->
          <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
          <div class="absolute bottom-0 left-0 w-24 h-24 bg-purple-400/10 rounded-full -ml-12 -mb-12 blur-2xl"></div>

          <div class="relative z-10">
            <div class="flex items-start gap-3 mb-4">
              <div
                class="flex-shrink-0 w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                  </path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-white leading-tight mb-1">
                  <?php echo e(optional($task)->title ?? 'Task Wizard'); ?>

                </h1>
                <div class="flex flex-wrap items-center gap-2">
                  <p class="text-sm text-violet-100">Selesaikan langkah-langkah untuk menyelesaikan task.</p>
                  <!--[if BLOCK]><![endif]--><?php if(Route::has('pages.tutorial-page')): ?>
                    <a href="<?php echo e(Route::has('pages.tutorial-page') ? route('pages.tutorial-page') : url('/pages/tutorial-page')); ?>"
                      target="_blank"
                      class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg text-[10px] font-bold text-white transition-colors">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      Cara Kerja
                    </a>
                  <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
              </div>
            </div>

            <!-- Cancel Button -->
            <!--[if BLOCK]><![endif]--><?php if($this->canCancelTask()): ?>
              <button wire:click="cancelTask" wire:confirm="Apakah Anda yakin ingin membatalkan dan kembali ke dashboard?"
                wire:loading.attr="disabled" wire:target="cancelTask" wire:loading.class="opacity-50 cursor-not-allowed"
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 text-white rounded-xl font-semibold text-sm transition-all">
                <svg wire:loading.remove wire:target="cancelTask" class="w-4 h-4" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <svg wire:loading wire:target="cancelTask" style="display: none;" class="w-4 h-4 animate-spin" fill="none"
                  viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                  </path>
                </svg>
                Batalkan
              </button>
            <?php else: ?>
              <div
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 backdrop-blur-sm border border-white/20 text-white/60 rounded-xl font-semibold text-sm cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                  </path>
                </svg>
                Tidak Dapat Dibatalkan
              </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
          </div>
        </div>
      </header>

      <!-- Modern Progress Stepper -->
      <div class="mb-6 bg-white rounded-2xl p-5 shadow-lg border border-zinc-200">
        <ul class="relative flex flex-row gap-x-2">
          <!--[if BLOCK]><![endif]--><?php for($i = 1; $i <= 4; $i++): ?>
            <li class="shrink basis-0 flex-1 group">
              <div class="min-w-7 min-h-7 w-full inline-flex flex-col items-center text-xs">
                <span class="size-10 flex justify-center items-center shrink-0 font-bold rounded-xl transition-all duration-300 shadow-md
                    <?php if($currentStep > $i): ?> bg-gradient-to-br from-violet-500 to-violet-600 text-white shadow-violet-500/30
                    <?php elseif($currentStep === $i): ?> bg-gradient-to-br from-violet-600 to-purple-700 text-white ring-4 ring-violet-200 shadow-violet-500/40
                    <?php else: ?> bg-zinc-100 text-zinc-400 <?php endif; ?>">
                  <!--[if BLOCK]><![endif]--><?php if($currentStep > $i): ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                  <?php else: ?>
                    <?php echo e($i); ?>

                  <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </span>
                <span class="block text-xs font-semibold mt-2 text-center leading-tight
                    <?php if($currentStep === $i): ?> text-violet-600
                    <?php elseif($currentStep > $i): ?> text-violet-600
                    <?php else: ?> text-zinc-400 <?php endif; ?>">
                  <?php echo e($this->getStepLabel($i)); ?>

                </span>
              </div>
            </li>
          <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->
        </ul>

        <!-- Progress Bar -->
        <div class="mt-4 relative h-2 bg-zinc-100 rounded-full overflow-hidden">
          <div
            class="absolute inset-0 bg-gradient-to-r from-violet-500 via-violet-600 to-purple-600 transition-all duration-500 ease-out"
            style="width: <?php echo e((($currentStep - 1) / 3) * 100); ?>%"></div>
        </div>
      </div>

      <!-- Chat widget moved to floating button at bottom -->

      <!-- Main Content Card -->
      <div class="bg-white rounded-2xl shadow-lg border border-zinc-200 overflow-hidden">
        <!--[if BLOCK]><![endif]--><?php if($currentStep === 1): ?>
          <!-- Step 1: Instructions -->
          <div class="p-5">
            <div class="space-y-5">
              <div class="text-center">
                <h2 class="text-xl font-bold text-zinc-900 mb-1">Instruksi Tugas</h2>
                <p class="text-sm text-zinc-600">Baca dengan teliti sebelum melanjutkan.</p>
              </div>

              <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                <h3 class="font-semibold text-purple-900 mb-2 text-base">Deskripsi Tugas</h3>
                <div class="prose prose-blue max-w-none text-purple-800 text-sm">
                  <?php echo optional($task)->description; ?>

                </div>
              </div>

              <div class="grid gap-4 md:grid-cols-2">
                <!--[if BLOCK]><![endif]--><?php if(optional($task)->vcf_data): ?>
                  <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 h-full">
                    <div class="flex flex-col items-center justify-between gap-3 h-full">
                      <div class="text-center">
                        <h3 class="font-semibold text-blue-900 text-base">File Kontak (VCF)</h3>
                        <p class="text-blue-800 mt-1 text-sm">Download dan simpan kontak yang diperlukan untuk tugas ini.</p>
                      </div>
                      <div class="w-full flex flex-col gap-2 items-stretch">
                        <a href="<?php echo e(route('user.task.vcf', $task)); ?>" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                          Download File VCF
                        </a>
                        <p class="text-[13px] text-blue-800 text-center">Gunakan file ini untuk import kontak secara instan.</p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <?php if(optional($task)->tutorial_link): ?>
                  <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 h-full">
                    <div class="flex flex-col items-center justify-between gap-3 h-full">
                      <div class="text-center">
                        <h3 class="font-semibold text-emerald-900 text-base">Panduan / Tutorial</h3>
                        <p class="text-emerald-800 mt-1 text-sm">Buka panduan langkah demi langkah agar pengerjaan sesuai.</p>
                      </div>
                      <div class="w-full flex flex-col gap-2 items-stretch">
                        <a href="<?php echo e($task->tutorial_link); ?>" target="_blank" rel="noopener noreferrer" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold transition-colors">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8m5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                          Buka Panduan
                        </a>
                        <p class="text-[13px] text-emerald-800 text-center">Link akan terbuka di tab baru.</p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <?php if(optional($task)->whatsapp_group_link): ?>
                  <div x-data="{ copied: false }" class="bg-violet-50 border border-violet-200 rounded-xl p-4 h-full">
                    <div class="flex flex-col items-center justify-between gap-3 h-full">
                      <div class="text-center">
                        <h3 class="font-semibold text-purple-900 text-base">Grup WhatsApp</h3>
                        <p class="text-purple-800 mt-1 text-sm">Bergabung ke grup koordinasi untuk tugas ini.</p>
                      </div>
                      <div class="w-full flex flex-col gap-2 items-stretch">
                        <a href="<?php echo e($task->whatsapp_group_link); ?>" target="_blank" rel="noopener noreferrer" aria-label="Buka Grup WhatsApp" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-violet-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-colors">
                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.148z"/></svg>
                          Gabung Grup
                        </a>
                        <button aria-label="Salin link grup WhatsApp" @click.prevent="(async () => { try { await navigator.clipboard.writeText('<?php echo e($task->whatsapp_group_link); ?>'); copied = true; setTimeout(() => copied = false, 2000); } catch(e) { const ta = document.createElement('textarea'); ta.value = '<?php echo e($task->whatsapp_group_link); ?>'; document.body.appendChild(ta); ta.select(); try { document.execCommand('copy'); copied = true; setTimeout(() => copied = false, 2000); } catch(e) { alert('Tidak dapat menyalin link. Silakan salin secara manual.'); } document.body.removeChild(ta); } })()" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-violet-200 text-purple-700 rounded-lg font-semibold hover:bg-violet-50 transition-colors">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8M12 8h4"></path></svg>
                          <span x-text="copied ? 'Disalin!' : 'Salin Link'">Salin Link</span>
                        </button>
                      </div>
                    </div>
                  </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
              </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4" x-data="{ understood: <?php if ((object) ('understoodInstructions') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('understoodInstructions'->value()); ?>')<?php echo e('understoodInstructions'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('understoodInstructions'); ?>')<?php endif; ?> }">
                  <label class="flex items-start gap-3 cursor-pointer group">
                    <div class="flex-shrink-0 mt-1">
                      <input type="checkbox" x-model="understood" class="w-5 h-5 text-violet-600 border-2 border-amber-300 rounded focus:ring-violet-500 focus:ring-offset-2 transition-all">
                    </div>
                    <div class="flex-1">
                      <span class="text-amber-800 font-semibold text-sm group-hover:text-amber-900 transition-colors">
                        Saya sudah membaca dan memahami semua instruksi tugas.
                      </span>
                      <p class="text-amber-700 text-xs">Anda harus menyetujui untuk melanjutkan.</p>
                    </div>
                  </label>

                  <div class="mt-4">
                    <button wire:click="nextStep" x-bind:disabled="!understood"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-violet-600 hover:bg-purple-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-violet-500/30">
                      Lanjutkan ke Langkah Berikutnya
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

        <?php elseif(in_array($currentStep, [2, 3])): ?>
          <!-- Step 2 & 3: Upload Proofs -->
          <?php
            $isStep2 = $currentStep === 2;
            $canSubmit = $isStep2 ? $this->canSubmitProof1() : $this->canSubmitProof2();

            // Check if user has submitted proof and is waiting for verification
            $hasSubmittedProof1 = optional($userTask)->verification_1_status &&
              strpos($userTask->verification_1_status, 'Submitted') !== false &&
              strpos($userTask->verification_1_status, 'Approved') === false &&
              strpos($userTask->verification_1_status, 'Rejected') === false;

            $hasSubmittedProof2 = optional($userTask)->verification_2_status &&
              strpos($userTask->verification_2_status, 'Submitted') !== false &&
              strpos($userTask->verification_2_status, 'Approved') === false &&
              strpos($userTask->verification_2_status, 'Rejected') === false;

            $isWaiting = $isStep2 ? $hasSubmittedProof1 : $hasSubmittedProof2;

            $rejectionMessage = null;
            if ($isStep2 && optional($userTask)->verification_1_status && strpos($userTask->verification_1_status, 'Rejected') !== false) {
              $rejectionMessage = $userTask->verification_1_status;
            } elseif (!$isStep2 && optional($userTask)->verification_2_status && strpos($userTask->verification_2_status, 'Rejected') !== false) {
              $rejectionMessage = $userTask->verification_2_status;
            }
          ?>
          <div class="p-5">
            <div class="space-y-5">
              <div class="text-center">
                <h2 class="text-xl font-bold text-zinc-900 mb-1">Upload Bukti Tahap <?php echo e($isStep2 ? 1 : 2); ?></h2>
                <p class="text-sm text-zinc-600">Kirimkan bukti pekerjaan Anda untuk diverifikasi.</p>
              </div>

              <!--[if BLOCK]><![endif]--><?php if($isStep2 && $userTask->status === 'taken' && $proof1Deadline): ?>
                <!-- Timer Countdown for Proof 1 -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-300 rounded-xl p-4" 
                     x-data="{ 
                       deadline: new Date('<?php echo e($proof1Deadline->toIso8601String()); ?>').getTime(),
                       timeLeft: 0,
                       minutes: 0,
                       seconds: 0,
                       hasReloaded: false,
                       interval: null,
                       init() {
                         this.calculateTimeLeft();
                         this.updateDisplay();
                         if (this.timeLeft <= 0) {
                           window.location.reload();
                           return;
                         }
                         this.interval = setInterval(() => {
                           this.calculateTimeLeft();
                           if (this.timeLeft > 0) {
                             this.updateDisplay();
                           } else if (!this.hasReloaded) {
                             this.hasReloaded = true;
                             clearInterval(this.interval);
                             window.location.reload();
                           }
                         }, 1000);
                       },
                       calculateTimeLeft() {
                         this.timeLeft = Math.max(0, Math.floor((this.deadline - Date.now()) / 1000));
                       },
                       updateDisplay() {
                         this.minutes = Math.floor(this.timeLeft / 60);
                         this.seconds = Math.floor(this.timeLeft % 60);
                       }
                     }">
                  <div class="flex items-center justify-center gap-3">
                    <div class="text-center">
                      <p class="text-sm font-semibold text-yellow-800 mb-1">⚠️ Waktu Submit Proof 1</p>
                      <p class="text-xs text-yellow-700 mb-2">Segera upload bukti sebelum waktu habis!</p>
                      <div class="flex items-center gap-2 justify-center">
                        <div class="bg-white rounded-lg px-3 py-1.5 border-2 border-yellow-400">
                          <span class="text-2xl font-bold text-yellow-600 tabular-nums" x-text="minutes.toString().padStart(2, '0')">00</span>
                          <span class="text-xs text-yellow-700 block">Menit</span>
                        </div>
                        <span class="text-2xl font-bold text-yellow-600">:</span>
                        <div class="bg-white rounded-lg px-3 py-1.5 border-2 border-yellow-400">
                          <span class="text-2xl font-bold text-yellow-600 tabular-nums" x-text="seconds.toString().padStart(2, '0')">00</span>
                          <span class="text-xs text-yellow-700 block">Detik</span>
                        </div>
                      </div>
                      <p class="text-xs text-red-600 mt-2 font-medium">⚠️ Task otomatis dibatalkan jika waktu habis!</p>
                    </div>
                  </div>
                </div>
              <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

              <!--[if BLOCK]><![endif]--><?php if($canSubmit): ?>
                <!--[if BLOCK]><![endif]--><?php if($rejectionMessage): ?>
                  <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                      <div class="flex-shrink-0 text-red-500"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg></div>
                      <div class="flex-1">
                        <h3 class="font-semibold text-red-900 text-base">Catatan dari Admin</h3>
                        <p class="text-red-800 mt-1 text-sm"><?php echo e($rejectionMessage); ?></p>
                        <p class="text-red-700 mt-2 text-xs">Silakan perbaiki dan kirim ulang.</p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <form wire:submit="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>" class="space-y-6" x-data="{ mode: 'upload' }">
                  <!-- Mode Toggles -->
                  <div class="flex p-1 bg-zinc-100 rounded-xl">
                    <button type="button" @click="mode = 'upload'" 
                            :class="mode === 'upload' ? 'bg-white shadow text-violet-600' : 'text-zinc-500 hover:text-zinc-700'" 
                            class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                      Upload Foto
                    </button>
                    <button type="button" @click="mode = 'link'"
                            :class="mode === 'link' ? 'bg-white shadow text-violet-600' : 'text-zinc-500 hover:text-zinc-700'" 
                            class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                      Kirim Link / Teks
                    </button>
                  </div>

                  <!-- Upload Mode -->
                  <div x-show="mode === 'upload'" 
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 translate-y-2"
                       x-transition:enter-end="opacity-100 translate-y-0"
                       class="space-y-4">
                    <div class="space-y-2">
                       <label class="block text-sm font-semibold text-gray-700">File Bukti (Gambar/Dokumen)</label>
                       <div x-data="{ isUploading: false, progress: 0, isDrag: false }"
                            x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                            class="relative">
                         <label 
                             for="<?php echo e($isStep2 ? 'proof1-files' : 'proof2-files'); ?>"
                             @dragover.prevent="isDrag = true"
                             @dragleave.prevent="isDrag = false"
                             @drop.prevent="isDrag = false"
                             :class="isDrag ? 'border-violet-500 bg-violet-50/70' : 'border-dashed border-zinc-200 bg-white'"
                             class="block cursor-pointer rounded-xl border px-4 py-6 text-sm text-zinc-600 transition-all duration-150 shadow-sm hover:shadow-md">
                           <div class="flex items-center gap-4">
                             <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow">
                               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v13h16V7M4 7l4-4h8l4 4M12 11v6"></path></svg>
                             </div>
                             <div class="flex-1">
                               <p class="font-semibold text-zinc-900">Tarik atau klik untuk memilih file</p>
                               <p class="text-xs text-zinc-500 mt-1">PNG, JPG, PDF, DOCX — Maks 10MB per file. Multiple file dapat dipilih.</p>
                             </div>
                             <div class="flex-shrink-0">
                               <span class="inline-flex px-3 py-2 rounded-lg bg-zinc-100 text-zinc-700 text-xs">Pilih</span>
                             </div>
                           </div>
                         </label>

                         <input id="<?php echo e($isStep2 ? 'proof1-files' : 'proof2-files'); ?>" type="file" wire:model="<?php echo e($isStep2 ? 'proof1Files' : 'proof2Files'); ?>" multiple accept="image/*,application/pdf,.doc,.docx" class="hidden" />

                         <!-- Upload Progress Overlay -->
                         <div x-show="isUploading" class="absolute inset-0 w-full h-full bg-white/80 backdrop-blur-sm rounded-lg flex items-center justify-center z-10">
                           <div class="w-full max-w-[80%] text-center">
                             <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                               <div class="bg-violet-600 h-2 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                             </div>
                             <p class="text-xs text-gray-600 font-medium" x-text="`Mengupload... ${progress}%`"></p>
                           </div>
                         </div>

                         <!-- Selected Files Preview -->
                         <!--[if BLOCK]><![endif]--><?php if($isStep2 ? (count($proof1Files ?? []) > 0) : (count($proof2Files ?? []) > 0)): ?>
                           <div class="mt-3 grid grid-cols-3 gap-3">
                             <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $isStep2 ? ($proof1Files ?? []) : ($proof2Files ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                               <div class="rounded-lg overflow-hidden bg-white border border-zinc-200 p-1">
                                <!--[if BLOCK]><![endif]--><?php if(\Illuminate\Support\Str::startsWith($file->getClientMimeType(), 'image')): ?>
                                   <img src="<?php echo e($file->temporaryUrl()); ?>" class="w-full h-24 object-cover" alt="preview" />
                                 <?php else: ?>
                                   <div class="w-full h-24 flex items-center justify-center text-xs text-zinc-600"><?php echo e($file->getClientOriginalName()); ?></div>
                                 <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                               </div>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                           </div>
                         <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                       </div>
                       <!--[if BLOCK]><![endif]--><?php $__errorArgs = [$isStep2 ? 'proof1Files.*' : 'proof2Files.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-red-600 text-xs flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                  </div>

                  <!-- Link/Text Mode -->
                  <div x-show="mode === 'link'" 
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 translate-y-2"
                       x-transition:enter-end="opacity-100 translate-y-0"
                       class="space-y-4">
                    <div class="space-y-2">
                      <label for="description" class="block text-sm font-semibold text-gray-700">Link / Catatan</label>
                      <div class="relative">
                        <textarea wire:model="<?php echo e($isStep2 ? 'proof1Description' : 'proof2Description'); ?>" id="description" rows="4" placeholder="Paste link Google Drive, Imgur, atau catatan jika upload gagal..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all text-sm shadow-sm"></textarea>

                      </div>
                      <p class="text-xs text-amber-600 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Gunakan opsi ini jika Anda mengalami kendala saat upload file foto.
                      </p>
                      <!--[if BLOCK]><![endif]--><?php $__errorArgs = [$isStep2 ? 'proof1Description' : 'proof2Description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-red-600 text-xs flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                  </div>
                </form>
              <?php else: ?>
                <!-- Awaiting Admin Verification -->
                <!-- Awaiting Admin Verification -->
                <div class="text-center py-12 px-4 flex flex-col items-center">
                  <!-- Animated Status Icon -->
                  <div class="relative mb-6 group">
                    <div class="absolute inset-0 bg-orange-500/20 blur-xl rounded-full animate-pulse group-hover:bg-orange-500/30 transition-all duration-500"></div>
                    <div class="relative w-24 h-24 bg-white rounded-full shadow-2xl flex items-center justify-center border-4 border-orange-50 group-hover:scale-105 transition-transform duration-300">
                      <svg class="w-10 h-10 text-orange-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                      <!-- Checkmark overlay -->
                      <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                         <svg class="w-8 h-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path></svg>
                      </div>
                    </div>
                  </div>

                  <!-- Text Content -->
                  <h3 class="text-2xl font-bold text-zinc-900 mb-2">Menunggu Verifikasi</h3>
                  <p class="text-zinc-500 text-sm max-w-sm mx-auto leading-relaxed mb-6">
                    Bukti Anda telah berhasil terkirim. Tim admin kami sedang memeriksa kevalidan data Anda.
                  </p>

                  <!-- Status Badge -->
                  <div class="inline-flex items-center gap-2 px-4 py-2 bg-orange-50 border border-orange-100 rounded-full text-orange-700 text-sm font-medium mb-8">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-orange-500"></span>
                    </span>
                    Sedang Diverifikasi Admin
                  </div>

                  <!-- Tip Card -->
                  <div class="w-full max-w-lg bg-gradient-to-br from-violet-50 to-purple-50 border border-violet-100 rounded-2xl p-5 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                     <div class="absolute top-0 right-0 w-24 h-24 bg-violet-200/20 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-violet-200/30 transition-all"></div>
                     <div class="relative z-10 flex gap-4 items-start text-left">
                        <div class="flex-shrink-0 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-lg">💡</div>
                        <div class="flex-1">
                           <h4 class="font-bold text-zinc-800 text-sm mb-1">Manfaatkan Waktu Anda</h4>
                           <p class="text-zinc-600 text-xs leading-relaxed mb-3">
                             Proses verifikasi membutuhkan waktu. Anda bisa mengerjakan tugas lain untuk menambah penghasilan.
                           </p>
                           <a href="<?php echo e(route('user.dashboard')); ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-violet-600 hover:text-purple-700 transition-colors">
                             Cari Tugas Lain
                             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                           </a>
                        </div>
                     </div>
                  </div>
                </div>
              <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-4 border-t border-zinc-200">
            <!--[if BLOCK]><![endif]--><?php if($canSubmit): ?>
              <div class="flex justify-end">
                <button 
                  wire:click="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>"
                  wire:loading.attr="disabled"
                  wire:target="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>"
                  class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-violet-600 hover:bg-purple-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-violet-500/30">
                  <svg wire:loading wire:target="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>" style="display: none;" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span wire:loading.remove wire:target="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>">Kirim untuk Verifikasi</span>
                  <span wire:loading wire:target="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>" style="display: none;">Memproses...</span>
                </button>
              </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
          </div>

        <?php else: ?>
          <!-- Step 4: Completed OR Failed -->
          <div class="p-5">
            <!--[if BLOCK]><![endif]--><?php if($this->isTaskRejectedAndCancelled()): ?>
              <div class="text-center py-10">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5">
                  <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-red-600 mb-2">Tugas Ditolak</h2>
                <p class="text-base text-zinc-600 mb-5 max-w-md mx-auto">Pengiriman Anda ditolak oleh admin dan tugas telah dikembalikan ke daftar tersedia.</p>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 max-w-lg mx-auto">
                  <h3 class="font-semibold text-red-900 text-base flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Catatan Admin:
                  </h3>
                  <p class="text-red-800 mt-2 text-left text-sm"><?php echo e($this->getRejectionFeedback() ?: 'Pengiriman Anda tidak memenuhi persyaratan.'); ?></p>
                </div>
              </div>
            <?php elseif($this->isTaskExpired()): ?>
              <!-- Expired / Deadline Passed — Read-only view -->
              <div class="text-center py-10">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-5">
                  <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-orange-600 mb-2">⏰ Tugas Kadaluarsa</h2>
                <p class="text-base text-zinc-600 mb-5 max-w-md mx-auto">Tugas ini sudah melewati batas waktu dan tidak dapat dilanjutkan lagi. Anda masih bisa melihat riwayat di halaman ini.</p>
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 max-w-lg mx-auto space-y-2">
                  <div class="flex justify-between items-center">
                    <span class="font-semibold text-orange-800 text-sm">Status:</span>
                    <span class="px-3 py-1 bg-orange-200 text-orange-800 rounded-full text-xs font-medium">Kadaluarsa / Gagal</span>
                  </div>
                  <!--[if BLOCK]><![endif]--><?php if(optional($userTask)->cancelled_at): ?>
                    <div class="flex justify-between items-center">
                      <span class="font-semibold text-orange-800 text-sm">Waktu Gugur:</span>
                      <span class="text-sm text-orange-700"><?php echo e($userTask->cancelled_at->format('d M Y, H:i')); ?></span>
                    </div>
                  <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <a href="<?php echo e(route('user.history')); ?>" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold transition-colors shadow-md">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  Lihat Riwayat Tugas
                </a>
              </div>
            <?php else: ?>
              <div class="text-center py-10">
                <!--[if BLOCK]><![endif]--><?php if($this->isCompletedButAwaitingPayment()): ?>
                  <!-- Completed but awaiting payment -->
                  <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  </div>
                  <h2 class="text-2xl font-bold text-yellow-600 mb-2">⏳ Tugas Selesai!</h2>
                  <p class="text-base text-zinc-600 mb-5 max-w-md mx-auto">Pekerjaan Anda telah diverifikasi dan disetujui. Pembayaran sedang diproses.</p>
                <?php else: ?>
                  <!-- Fully completed with payment -->
                  <div class="w-20 h-20 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  </div>
                  <h2 class="text-2xl font-bold text-zinc-900 mb-2">🎉 Tugas Selesai!</h2>
                  <p class="text-base text-zinc-600 mb-5 max-w-md mx-auto">Selamat! Anda berhasil menyelesaikan tugas ini dan telah menerima pembayaran.</p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <div class="bg-violet-50 border border-violet-200 rounded-xl p-4 max-w-lg mx-auto space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="font-semibold text-purple-800 text-sm">Status Akhir:</span>
                    <span class="px-3 py-1 bg-violet-200 text-purple-800 rounded-full text-xs font-medium"><?php echo e(\App\Models\UserTask::STATUSES[optional($userTask)->status] ?? 'Completed'); ?></span>
                  </div>
                  <?php if(optional($userTask)->payment_amount): ?>
                    <div class="flex justify-between items-center">
                      <span class="font-semibold text-purple-800 text-sm">Hadiah:</span>
                      <span class="text-lg font-bold text-purple-700">Rp <?php echo e(number_format($userTask->payment_amount, 0, ',', '.')); ?></span>
                    </div>
                  <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                  <?php if(optional($userTask)->payment_amount): ?>
                    <div class="flex justify-between items-center">
                      <span class="font-semibold text-purple-800 text-sm">Status Pembayaran:</span>
                      <?php if(optional($userTask)->payment_status === 'success'): ?>
                        <span class="px-3 py-1 bg-purple-200 text-purple-800 rounded-full text-xs font-medium flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                          Berhasil Dibayar
                        </span>
                      <?php elseif(optional($userTask)->payment_status === 'failed'): ?>
                        <span class="px-3 py-1 bg-red-200 text-red-800 rounded-full text-xs font-medium flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                          Pembayaran Gagal
                        </span>
                      <?php else: ?>
                        <span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-medium flex items-center gap-2">
                          <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                          Memproses Pembayaran
                        </span>
                      <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <?php if(optional($userTask)->payment_verified_at): ?>
                      <div class="text-center pt-2 border-t border-violet-200">
                        <p class="text-xs text-purple-700">
                          Pembayaran diverifikasi pada <?php echo e($userTask->payment_verified_at->format('d M Y \p\u\k\u\l H:i')); ?>

                        </p>
                      </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                  <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
              </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
          </div>
          <div class="bg-gray-50 px-5 py-4 border-t border-zinc-200">
            <div class="flex justify-center">
              <a href="<?php echo e(route('user.dashboard')); ?>" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-violet-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-violet-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Kembali ke Dashboard
              </a>
            </div>
          </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
      </div>
    </div>
  </div>

  <!-- Desktop View -->
  <div class="hidden sm:block">
    <div class="container mx-auto px-4 py-8 sm:py-12 max-w-5xl">
      
      <!-- Page Header -->
      <header class="mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
          <div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight tracking-tight">
              <?php echo e(optional($task)->title ?? 'Task Wizard'); ?>

            </h1>
            <div class="flex items-center gap-3">
              <p class="mt-2 text-lg text-gray-600">
                Selesaikan langkah-langkah di bawah untuk menyelesaikan tugas Anda.
              </p>
              <!--[if BLOCK]><![endif]--><?php if(Route::has('pages.tutorial-page')): ?>
                <a href="<?php echo e(Route::has('pages.tutorial-page') ? route('pages.tutorial-page') : url('/pages/tutorial-page')); ?>" target="_blank" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 bg-violet-100 text-purple-700 rounded-full text-xs font-bold hover:bg-violet-200 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  Lihat Tutorial
                </a>
              <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
          </div>
          <div class="flex-shrink-0">
            <!--[if BLOCK]><![endif]--><?php if($this->canCancelTask()): ?>
              <button
                wire:click="cancelTask"
                wire:confirm="Are you sure you want to cancel and return to the dashboard?"
                wire:loading.attr="disabled"
                wire:target="cancelTask"
                wire:loading.class="opacity-50 cursor-not-allowed"
                title="Cancel and return to dashboard"
                aria-label="Cancel task and return to dashboard"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-transparent border border-gray-200 text-gray-700 rounded-md font-semibold text-sm hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                <svg wire:loading.remove wire:target="cancelTask" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <svg wire:loading wire:target="cancelTask" style="display: none;" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="hidden sm:inline">Cancel</span>
              </button>
            <?php else: ?>
              <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 border border-gray-300 text-gray-400 rounded-md font-semibold text-sm cursor-not-allowed" title="Cannot cancel after submitting proof">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span class="hidden sm:inline">Tidak Dapat Dibatalkan</span>
              </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
          </div>
        </div>
      </header>
      <!-- Progress Stepper -->
      <div class="mb-8">
        <ul class="relative flex flex-row gap-x-2 justify-center max-w-3xl mx-auto">
          <!--[if BLOCK]><![endif]--><?php for($i = 1; $i <= 4; $i++): ?>
            <li class="shrink basis-0 flex-1 group">
              <div class="min-w-8 min-h-8 w-full inline-flex flex-col items-center text-xs align-middle">
                <div class="flex items-center w-full">
                  <div class="flex-1 h-1 bg-gray-200 group-first:invisible">
                    <div class="h-1 rounded-full transition-all duration-500
                      <?php if($currentStep >= $i): ?> bg-violet-600 w-full
                      <?php else: ?> bg-gray-200 w-0 <?php endif; ?>">
                    </div>
                  </div>
                  <span class="size-8 flex justify-center items-center shrink-0 font-bold rounded-full transition-all duration-300 mx-1
                    <?php if($currentStep > $i): ?> bg-violet-600 text-white
                    <?php elseif($currentStep === $i): ?> bg-violet-600 text-white ring-4 ring-violet-200
                    <?php else: ?> bg-gray-200 text-gray-600 <?php endif; ?>">
                    <!--[if BLOCK]><![endif]--><?php if($currentStep > $i): ?>
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    <?php else: ?>
                      <?php echo e($i); ?>

                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                  </span>
                  <div class="flex-1 h-1 bg-gray-200 group-last:invisible">
                    <div class="h-1 rounded-full transition-all duration-500
                      <?php if($currentStep > $i): ?> bg-violet-600 w-full
                      <?php elseif($currentStep === $i): ?> bg-violet-600 w-1/2
                      <?php else: ?> bg-gray-200 w-0 <?php endif; ?>">
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-3">
                <span class="block text-sm font-medium text-center
                  <?php if($currentStep === $i): ?> text-violet-600
                  <?php elseif($currentStep > $i): ?> text-zinc-800
                  <?php else: ?> text-zinc-500 <?php endif; ?>">
                  <?php echo e($this->getStepLabel($i)); ?>

                </span>
              </div>
            </li>
          <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->
        </ul>
      </div>
      
      <!-- Chat widget moved to floating button at bottom -->
      
      <!-- Main Content Card -->
      <div class="bg-white rounded-2xl shadow-xl border border-zinc-200 overflow-hidden">
        <!--[if BLOCK]><![endif]--><?php if($currentStep === 1): ?>
          <!-- Step 1: Instructions -->
          <div class="p-6 sm:p-8">
            <div class="space-y-6">
              <div class="text-center">
                <h2 class="text-2xl font-bold text-zinc-900 mb-1">Instruksi Tugas</h2>
                <p class="text-md text-zinc-600">Baca dengan teliti sebelum melanjutkan.</p>
              </div>

              <div class="bg-violet-50 border border-violet-200 rounded-xl p-5">
                <h3 class="font-semibold text-purple-900 mb-3 text-lg">Deskripsi Tugas</h3>
                <div class="prose prose-blue max-w-none text-purple-800">
                  <?php echo optional($task)->description; ?>

                </div>
              </div>

              <!--[if BLOCK]><![endif]--><?php if(optional($task)->vcf_data): ?>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                      <h3 class="font-semibold text-blue-900 text-lg">File Kontak (VCF)</h3>
                      <p class="text-blue-800 mt-1">Download dan simpan kontak yang diperlukan untuk tugas ini.</p>
                    </div>
                    <div class="inline-flex items-center gap-2">
                      <a href="<?php echo e(route('user.task.vcf', $task)); ?>" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors transform hover:scale-105 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download VCF
                      </a>
                    </div>
                  </div>
                </div>
              <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

              <!-- Admin Contact Info -->
              <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                <h3 class="font-semibold text-blue-900 mb-4 text-lg flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                  Admin yang Membuat Task
                </h3>
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                      <span class="text-white text-lg font-bold"><?php echo e(substr(optional(optional($task)->creator)->name ?? 'A', 0, 1)); ?></span>
                    </div>
                    <div>
                      <p class="font-semibold text-blue-900 text-lg"><?php echo e(optional(optional($task)->creator)->name ?? 'Admin'); ?></p>
                      <!--[if BLOCK]><![endif]--><?php if(optional(optional($task)->creator)->badge === 'premium_admin'): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-medium">
                          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                          Premium Admin
                        </span>
                      <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                  </div>
                  <div class="flex gap-2">
                    <?php if(optional(optional($task)->creator)->whatsapp): ?>
                      <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', optional(optional($task)->creator)->whatsapp)); ?>" target="_blank" 
                         class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.148z"/></svg>
                        Hubungi via WhatsApp
                      </a>
                    <?php elseif(optional(optional($task)->creator)->phone): ?>
                      <a href="tel:<?php echo e(optional(optional($task)->creator)->phone); ?>" 
                         class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Telepon: <?php echo e(optional(optional($task)->creator)->phone); ?>

                      </a>
                    <?php else: ?>
                      <span class="text-sm text-blue-700 italic">Kontak admin tidak tersedia</span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                  </div>
                </div>
              </div>

              <?php if(optional($task)->whatsapp_group_link): ?>
                <div class="bg-violet-50 border border-violet-200 rounded-xl p-5">
                  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                      <h3 class="font-semibold text-purple-900 text-lg">Grup WhatsApp</h3>
                      <p class="text-purple-800 mt-1">Bergabung ke grup koordinasi untuk tugas ini.</p>
                    </div>
                    <div x-data="{ copied: false }" class="inline-flex items-center gap-2">
                      <a href="<?php echo e($task->whatsapp_group_link); ?>" target="_blank" rel="noopener noreferrer" aria-label="Buka Grup WhatsApp" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-colors transform hover:scale-105 flex-shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.148z"/></svg>
                        Gabung Grup
                      </a>
                      <button aria-label="Salin link grup WhatsApp" @click.prevent="(async () => { try { await navigator.clipboard.writeText('<?php echo e($task->whatsapp_group_link); ?>'); copied = true; setTimeout(() => copied = false, 2000); } catch(e) { const ta = document.createElement('textarea'); ta.value = '<?php echo e($task->whatsapp_group_link); ?>'; document.body.appendChild(ta); ta.select(); try { document.execCommand('copy'); copied = true; setTimeout(() => copied = false, 2000); } catch(e) { alert('Tidak dapat menyalin link. Silakan salin secara manual.'); } document.body.removeChild(ta); } })()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-violet-200 text-purple-700 rounded-lg font-semibold hover:bg-violet-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8M12 8h4"></path></svg>
                        <span x-text="copied ? 'Disalin!' : 'Salin Link'">Salin Link</span>
                      </button>
                      <!-- Share/Kirimkan Link button removed for desktop -->
                    </div>
                  </div>
                </div>
              <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

              <div class="bg-amber-50 border border-amber-200 rounded-xl p-5" x-data="{ understood: <?php if ((object) ('understoodInstructions') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('understoodInstructions'->value()); ?>')<?php echo e('understoodInstructions'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('understoodInstructions'); ?>')<?php endif; ?> }">
                <label class="flex items-start gap-4 cursor-pointer group">
                  <div class="flex-shrink-0 mt-1">
                    <input type="checkbox" x-model="understood" class="w-5 h-5 text-violet-600 border-2 border-amber-300 rounded focus:ring-violet-500 focus:ring-offset-2 transition-all">
                  </div>
                  <div class="flex-1">
                    <span class="text-amber-800 font-semibold text-base group-hover:text-amber-900 transition-colors">
                      Saya sudah membaca dan memahami semua instruksi tugas.
                    </span>
                    <p class="text-amber-700 text-sm">Anda harus menyetujui untuk melanjutkan.</p>
                  </div>
                </label>

                <div class="mt-4">
                  <button wire:click="nextStep" x-bind:disabled="!understood"
                          class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-violet-600 hover:bg-purple-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-violet-500/30 disabled:transform-none disabled:shadow-none">
                    Lanjutkan ke Langkah Berikutnya
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

        <?php elseif(in_array($currentStep, [2, 3])): ?>
          <!-- Step 2 & 3: Upload Proofs -->
          <?php
            $isStep2 = $currentStep === 2;
            $canSubmit = $isStep2 ? $this->canSubmitProof1() : $this->canSubmitProof2();

            // Check if user has submitted proof and is waiting for verification
            $hasSubmittedProof1 = optional($userTask)->verification_1_status &&
              strpos($userTask->verification_1_status, 'Submitted') !== false &&
              strpos($userTask->verification_1_status, 'Approved') === false &&
              strpos($userTask->verification_1_status, 'Rejected') === false;

            $hasSubmittedProof2 = optional($userTask)->verification_2_status &&
              strpos($userTask->verification_2_status, 'Submitted') !== false &&
              strpos($userTask->verification_2_status, 'Approved') === false &&
              strpos($userTask->verification_2_status, 'Rejected') === false;

            $isWaiting = $isStep2 ? $hasSubmittedProof1 : $hasSubmittedProof2;

            $rejectionMessage = null;
            if ($isStep2 && optional($userTask)->verification_1_status && strpos($userTask->verification_1_status, 'Rejected') !== false) {
              $rejectionMessage = $userTask->verification_1_status;
            } elseif (!$isStep2 && optional($userTask)->verification_2_status && strpos($userTask->verification_2_status, 'Rejected') !== false) {
              $rejectionMessage = $userTask->verification_2_status;
            }
          ?>
          <div class="p-6 sm:p-8">
            <div class="space-y-6">
              <div class="text-center">
                <h2 class="text-2xl font-bold text-zinc-900 mb-1">Upload Bukti Tahap <?php echo e($isStep2 ? 1 : 2); ?></h2>
                <p class="text-md text-zinc-600">Kirimkan bukti pekerjaan Anda untuk diverifikasi.</p>
              </div>

              <!-- Timer for Proof 1 (Desktop) -->
              <!--[if BLOCK]><![endif]--><?php if($currentStep === 2 && $userTask && $userTask->status === \App\Models\UserTask::STATUS_TAKEN && $proof1Deadline): ?>
                <div x-data="{ 
                  deadline: new Date('<?php echo e($proof1Deadline->toIso8601String()); ?>').getTime(),
                  timeLeft: 0,
                  minutes: 0,
                  seconds: 0,
                  isExpired: false,
                  hasReloaded: false,
                  interval: null,

                  init() {
                    this.updateTimer();
                    if (this.timeLeft <= 0) {
                      window.location.reload();
                      return;
                    }
                    this.interval = setInterval(() => this.updateTimer(), 1000);
                  },

                  updateTimer() {
                    this.timeLeft = Math.max(0, Math.floor((this.deadline - Date.now()) / 1000));
                    this.minutes = Math.floor(this.timeLeft / 60);
                    this.seconds = this.timeLeft % 60;
                    this.isExpired = this.timeLeft === 0;
                    if (this.isExpired && !this.hasReloaded) {
                      this.hasReloaded = true;
                      clearInterval(this.interval);
                      setTimeout(() => window.location.reload(), 500);
                    }
                  },

                  formatTime() {
                    return this.minutes.toString().padStart(2, '0') + ':' + this.seconds.toString().padStart(2, '0');
                  }
                }"
                class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-300 rounded-xl p-5">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <div class="flex-shrink-0 text-yellow-600">
                        <svg class="w-6 h-6 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                      </div>
                      <div>
                        <h3 class="font-semibold text-yellow-900 text-lg">Waktu Submit Proof 1</h3>
                        <p class="text-yellow-700 text-sm">Segera upload bukti sebelum waktu habis!</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-3xl font-bold text-yellow-600 tabular-nums tracking-wider" x-text="formatTime()"></div>
                      <p class="text-xs text-yellow-600 mt-1">tersisa</p>
                    </div>
                  </div>
                </div>
              <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

              <!--[if BLOCK]><![endif]--><?php if($canSubmit): ?>
                <!--[if BLOCK]><![endif]--><?php if($rejectionMessage): ?>
                  <div class="bg-red-50 border border-red-200 rounded-xl p-5">
                    <div class="flex items-start gap-4">
                      <div class="flex-shrink-0 text-red-500"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg></div>
                      <div class="flex-1">
                        <h3 class="font-semibold text-red-900 text-lg">Catatan dari Admin</h3>
                        <p class="text-red-800 mt-1"><?php echo e($rejectionMessage); ?></p>
                        <p class="text-red-700 mt-2 text-sm">Silakan perbaiki dan kirim ulang.</p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <form wire:submit="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>" class="space-y-6" x-data="{ mode: 'upload' }">
                  <!-- Mode Toggles -->
                  <div class="flex p-1 bg-zinc-100 rounded-xl">
                    <button type="button" @click="mode = 'upload'" 
                            :class="mode === 'upload' ? 'bg-white shadow text-violet-600' : 'text-zinc-500 hover:text-zinc-700'" 
                            class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                      Upload Foto
                    </button>
                    <button type="button" @click="mode = 'link'"
                            :class="mode === 'link' ? 'bg-white shadow text-violet-600' : 'text-zinc-500 hover:text-zinc-700'" 
                            class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                      Kirim Link / Teks
                    </button>
                  </div>

                  <!-- Upload Mode -->
                  <div x-show="mode === 'upload'" 
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 translate-y-2"
                       x-transition:enter-end="opacity-100 translate-y-0"
                       class="space-y-4">
                    <div class="space-y-2">
                       <label class="block text-sm font-semibold text-gray-700">File Bukti (Gambar/Dokumen)</label>
                       <div x-data="{ isUploading: false, progress: 0, isDrag: false }"
                            x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                            class="relative">
                         <label 
                             for="<?php echo e($isStep2 ? 'proof1-files-2' : 'proof2-files-2'); ?>"
                             @dragover.prevent="isDrag = true"
                             @dragleave.prevent="isDrag = false"
                             @drop.prevent="isDrag = false"
                             :class="isDrag ? 'border-violet-500 bg-violet-50/70' : 'border-dashed border-zinc-200 bg-white'"
                             class="block cursor-pointer rounded-xl border px-4 py-6 text-sm text-zinc-600 transition-all duration-150 shadow-sm hover:shadow-md">
                           <div class="flex items-center gap-4">
                             <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow">
                               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v13h16V7M4 7l4-4h8l4 4M12 11v6"></path></svg>
                             </div>
                             <div class="flex-1">
                               <p class="font-semibold text-zinc-900">Tarik atau klik untuk memilih file</p>
                               <p class="text-xs text-zinc-500 mt-1">PNG, JPG, PDF, DOCX — Maks 10MB per file. Multiple file dapat dipilih.</p>
                             </div>
                             <div class="flex-shrink-0">
                               <span class="inline-flex px-3 py-2 rounded-lg bg-zinc-100 text-zinc-700 text-xs">Pilih</span>
                             </div>
                           </div>
                         </label>

                         <input id="<?php echo e($isStep2 ? 'proof1-files-2' : 'proof2-files-2'); ?>" type="file" wire:model="<?php echo e($isStep2 ? 'proof1Files' : 'proof2Files'); ?>" multiple accept="image/*,application/pdf,.doc,.docx" class="hidden" />

                         <!-- Upload Progress Overlay -->
                         <div x-show="isUploading" class="absolute inset-0 w-full h-full bg-white/80 backdrop-blur-sm rounded-lg flex items-center justify-center z-10">
                           <div class="w-full max-w-[80%] text-center">
                             <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                               <div class="bg-violet-600 h-2 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                             </div>
                             <p class="text-xs text-gray-600 font-medium" x-text="`Mengupload... ${progress}%`"></p>
                           </div>
                         </div>

                         <!-- Selected Files Preview -->
                         <!--[if BLOCK]><![endif]--><?php if($isStep2 ? (count($proof1Files ?? []) > 0) : (count($proof2Files ?? []) > 0)): ?>
                           <div class="mt-3 grid grid-cols-3 gap-3">
                             <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $isStep2 ? ($proof1Files ?? []) : ($proof2Files ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                               <div class="rounded-lg overflow-hidden bg-white border border-zinc-200 p-1">
                                 <!--[if BLOCK]><![endif]--><?php if(\Illuminate\Support\Str::startsWith($file->getClientMimeType(), 'image')): ?>
                                   <img src="<?php echo e($file->temporaryUrl()); ?>" class="w-full h-24 object-cover" alt="preview" />
                                 <?php else: ?>
                                   <div class="w-full h-24 flex items-center justify-center text-xs text-zinc-600"><?php echo e($file->getClientOriginalName()); ?></div>
                                 <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                               </div>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                           </div>
                         <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                       </div>
                       <!--[if BLOCK]><![endif]--><?php $__errorArgs = [$isStep2 ? 'proof1Files.*' : 'proof2Files.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-red-600 text-xs flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                  </div>

                  <!-- Link/Text Mode -->
                  <div x-show="mode === 'link'" 
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 translate-y-2"
                       x-transition:enter-end="opacity-100 translate-y-0"
                       class="space-y-4">
                    <div class="space-y-2">
                      <label for="description" class="block text-sm font-semibold text-gray-700">Link / Catatan</label>
                      <div class="relative">
                        <textarea wire:model="<?php echo e($isStep2 ? 'proof1Description' : 'proof2Description'); ?>" id="description" rows="4" placeholder="Paste link Google Drive, Imgur, atau catatan jika upload gagal..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all text-sm shadow-sm"></textarea>
                      </div>
                      <p class="text-xs text-amber-600 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Gunakan opsi ini jika Anda mengalami kendala saat upload file foto.
                      </p>
                      <!--[if BLOCK]><![endif]--><?php $__errorArgs = [$isStep2 ? 'proof1Description' : 'proof2Description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-red-600 text-xs flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                  </div>
                </form>
              <?php else: ?>
                <!-- Awaiting Admin Verification -->
                <!-- Awaiting Admin Verification -->
                <div class="text-center py-12 px-4 flex flex-col items-center">
                  <!-- Animated Status Icon -->
                  <div class="relative mb-6 group">
                    <div class="absolute inset-0 bg-orange-500/20 blur-xl rounded-full animate-pulse group-hover:bg-orange-500/30 transition-all duration-500"></div>
                    <div class="relative w-24 h-24 bg-white rounded-full shadow-2xl flex items-center justify-center border-4 border-orange-50 group-hover:scale-105 transition-transform duration-300">
                      <svg class="w-10 h-10 text-orange-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                      <!-- Checkmark overlay -->
                      <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                         <svg class="w-8 h-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path></svg>
                      </div>
                    </div>
                  </div>

                  <!-- Text Content -->
                  <h3 class="text-2xl font-bold text-zinc-900 mb-2">Menunggu Verifikasi</h3>
                  <p class="text-zinc-500 text-sm max-w-sm mx-auto leading-relaxed mb-6">
                    Bukti Anda telah berhasil terkirim. Tim admin kami sedang memeriksa kevalidan data Anda.
                  </p>

                  <!-- Status Badge -->
                  <div class="inline-flex items-center gap-2 px-4 py-2 bg-orange-50 border border-orange-100 rounded-full text-orange-700 text-sm font-medium mb-8">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-orange-500"></span>
                    </span>
                    Sedang Diverifikasi Admin
                  </div>

                  <!-- Tip Card -->
                  <div class="w-full max-w-lg bg-gradient-to-br from-violet-50 to-purple-50 border border-violet-100 rounded-2xl p-5 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                     <div class="absolute top-0 right-0 w-24 h-24 bg-violet-200/20 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-violet-200/30 transition-all"></div>
                     <div class="relative z-10 flex gap-4 items-start text-left">
                        <div class="flex-shrink-0 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-lg">💡</div>
                        <div class="flex-1">
                           <h4 class="font-bold text-zinc-800 text-sm mb-1">Manfaatkan Waktu Anda</h4>
                           <p class="text-zinc-600 text-xs leading-relaxed mb-3">
                             Proses verifikasi membutuhkan waktu. Anda bisa mengerjakan tugas lain untuk menambah penghasilan.
                           </p>
                           <a href="<?php echo e(route('user.dashboard')); ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-violet-600 hover:text-purple-700 transition-colors">
                             Cari Tugas Lain
                             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                           </a>
                        </div>
                     </div>
                  </div>
                </div>
              <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
          </div>
          <div class="bg-gray-50 px-6 sm:px-8 py-4 border-t border-zinc-200">
            <!--[if BLOCK]><![endif]--><?php if($canSubmit): ?>
              <div class="flex justify-end">
                <button 
                  wire:click="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>"
                  wire:loading.attr="disabled"
                  wire:target="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>"
                  class="inline-flex items-center gap-2 px-6 py-3 bg-violet-600 hover:bg-purple-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-violet-500/30 disabled:transform-none">
                  <svg wire:loading wire:target="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>" style="display: none;" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span wire:loading.remove wire:target="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>">Kirim untuk Verifikasi</span>
                  <span wire:loading wire:target="<?php echo e($isStep2 ? 'submitProof1' : 'submitProof2'); ?>" style="display: none;">Memproses...</span>
                </button>
              </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>        <?php else: ?>
            <!-- Step 4: Completed OR Failed -->
            <div class="p-6 sm:p-8">
              <!--[if BLOCK]><![endif]--><?php if($this->isTaskRejectedAndCancelled()): ?>
                <div class="text-center py-12">
                  <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                  </div>
                  <h2 class="text-3xl font-bold text-red-600 mb-2">Tugas Ditolak</h2>
                  <p class="text-lg text-zinc-600 mb-6 max-w-md mx-auto">Pengiriman Anda ditolak oleh admin dan tugas telah dikembalikan ke daftar tersedia.</p>
                  <div class="bg-red-50 border border-red-200 rounded-xl p-5 max-w-lg mx-auto">
                    <h3 class="font-semibold text-red-900 text-lg flex items-center gap-2">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      Catatan Admin:
                    </h3>
                    <p class="text-red-800 mt-2 text-left"><?php echo e($this->getRejectionFeedback() ?: 'Pengiriman Anda tidak memenuhi persyaratan.'); ?></p>
                  </div>
                  <div class="mt-8">
                    <a href="<?php echo e(route('user.dashboard')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-violet-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                      </svg>
                      Kembali ke Dashboard
                    </a>
                  </div>
                </div>
              <?php elseif($this->isTaskExpired()): ?>
                <!-- Expired / Deadline Passed — Read-only view (Desktop) -->
                <div class="text-center py-12">
                  <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  </div>
                  <h2 class="text-3xl font-bold text-orange-600 mb-2">⏰ Tugas Kadaluarsa</h2>
                  <p class="text-lg text-zinc-600 mb-6 max-w-md mx-auto">Tugas ini sudah melewati batas waktu dan tidak dapat dilanjutkan lagi. Anda masih bisa melihat riwayat di halaman ini.</p>
                  <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 max-w-lg mx-auto space-y-3">
                    <div class="flex justify-between items-center">
                      <span class="font-semibold text-orange-800">Status:</span>
                      <span class="px-3 py-1 bg-orange-200 text-orange-800 rounded-full text-sm font-medium">Kadaluarsa / Gagal</span>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if(optional($userTask)->cancelled_at): ?>
                      <div class="flex justify-between items-center">
                        <span class="font-semibold text-orange-800">Waktu Gugur:</span>
                        <span class="text-sm text-orange-700"><?php echo e($userTask->cancelled_at->format('d M Y, H:i')); ?></span>
                      </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                  </div>
                  <div class="mt-8 flex items-center justify-center gap-4">
                    <a href="<?php echo e(route('user.history')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors shadow-md">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                      Lihat Riwayat Tugas
                    </a>
                    <a href="<?php echo e(route('user.dashboard')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-violet-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                      Kembali ke Dashboard
                    </a>
                  </div>
                </div>
              <?php else: ?>
                <div class="text-center py-12">
                  <!--[if BLOCK]><![endif]--><?php if($this->isCompletedButAwaitingPayment()): ?>
                    <!-- Completed but awaiting payment -->
                    <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                      <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-bold text-yellow-600 mb-2">⏳ Tugas Selesai!</h2>
                    <p class="text-lg text-zinc-600 mb-6 max-w-md mx-auto">Pekerjaan Anda telah diverifikasi dan disetujui. Pembayaran sedang diproses.</p>
                  <?php else: ?>
                    <!-- Fully completed with payment -->
                    <div class="w-24 h-24 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-6">
                      <svg class="w-12 h-12 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h2 class="text-3xl font-bold text-zinc-900 mb-2">🎉 Tugas Selesai!</h2>
                    <p class="text-lg text-zinc-600 mb-6 max-w-md mx-auto">Selamat! Anda berhasil menyelesaikan tugas ini dan telah menerima pembayaran.</p>
                  <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                  <div class="bg-violet-50 border border-violet-200 rounded-xl p-5 max-w-lg mx-auto space-y-3">
                    <div class="flex justify-between items-center">
                      <span class="font-semibold text-purple-800">Status Akhir:</span>
                      <span class="px-3 py-1 bg-violet-200 text-purple-800 rounded-full text-sm font-medium"><?php echo e(\App\Models\UserTask::STATUSES[optional($userTask)->status] ?? 'Completed'); ?></span>
                    </div>
                    <?php if(optional($userTask)->payment_amount): ?>
                      <div class="flex justify-between items-center">
                        <span class="font-semibold text-purple-800">Hadiah:</span>
                        <span class="text-xl font-bold text-purple-700">Rp <?php echo e(number_format($userTask->payment_amount, 0, ',', '.')); ?></span>
                      </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php if(optional($userTask)->payment_amount): ?>
                      <div class="flex justify-between items-center">
                        <span class="font-semibold text-purple-800">Status Pembayaran:</span>
                        <?php if(optional($userTask)->payment_status === 'success'): ?>
                          <span class="px-3 py-1 bg-purple-200 text-purple-800 rounded-full text-sm font-medium flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Berhasil Dibayar
                          </span>
                        <?php elseif(optional($userTask)->payment_status === 'failed'): ?>
                          <span class="px-3 py-1 bg-red-200 text-red-800 rounded-full text-sm font-medium flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Pembayaran Gagal
                          </span>
                        <?php else: ?>
                          <span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm font-medium flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memproses Pembayaran
                          </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                      </div>
                      <?php if(optional($userTask)->payment_verified_at): ?>
                        <div class="text-center pt-2 border-t border-violet-200">
                          <p class="text-xs text-purple-700">
                            Pembayaran diverifikasi pada <?php echo e($userTask->payment_verified_at->format('d M Y \p\u\k\u\l H:i')); ?>

                          </p>
                        </div>
                      <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                  </div>
                </div>
              <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div class="bg-gray-50 px-6 sm:px-8 py-4 border-t border-zinc-200">
              <div class="flex justify-center">
                <a href="<?php echo e(route('user.dashboard')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-violet-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-violet-500/30">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                  Kembali ke Dashboard
                </a>
              </div>
            </div>
          <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
      </div>
    </div>
  </div>

  <!-- Floating Chat Widget - Only when user has active task -->
  <!--[if BLOCK]><![endif]--><?php if($userTask): ?>
    <div 
      x-data="{ 
        open: false,
        scrollChatToBottom() {
          const el = document.getElementById('chat-messages-<?php echo e($userTask->id); ?>');
          if (el) {
            setTimeout(() => {
              el.scrollTop = el.scrollHeight;
            }, 150);
          }
        }
      }" 
      x-init="
        $watch('open', value => {
          if (value) {
            scrollChatToBottom();
          }
        })
      "
      class="fixed bottom-24 sm:bottom-4 right-4 z-40"
    >
      <!-- Floating Chat Button -->
      <button 
        x-ref="toggleButton"
        @click="open = !open" 
        class="inline-flex items-center justify-center px-4 py-2 min-w-[120px] sm:min-w-[160px] bg-gradient-to-br from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
        :class="{ 'ring-4 ring-violet-300': open }"
        title="Chat dengan Admin"
        aria-label="Chat dengan Admin"
        :aria-expanded="open"
      >
        <div class="relative flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
          <!--[if BLOCK]><![endif]--><?php if($userTask->unreadMessagesForUser->count() > 0): ?>
            <span class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 rounded-full text-[10px] font-bold flex items-center justify-center animate-pulse shadow-lg">
              <?php echo e($userTask->unreadMessagesForUser->count()); ?>

            </span>
          <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
          <span class="text-sm font-semibold tracking-tight">Chat Admin</span>
        </div>
      </button>

      <!-- Chat Panel -->
      <div 
        x-show="open" 
        x-cloak
        @click.outside="if (!$refs.toggleButton.contains($event.target)) open = false"
        @close-chat.window="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="absolute bottom-16 right-0 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-zinc-200 overflow-hidden flex flex-col"
        style="height: 500px;"
      >
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('task-chat', ['userTask' => $userTask]);

$__html = app('livewire')->mount($__name, $__params, 'chat-floating-' . $userTask->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
      </div>
    </div>
  <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div>


<!--[if BLOCK]><![endif]--><?php if($userTask): ?>
  <script>
  (function() {
      const channelName = 'chat.<?php echo e($userTask->id); ?>';
      const userTaskId = <?php echo e($userTask->id); ?>;

      console.log('[Task Work Chat] Initializing Echo for:', channelName);

      let retries = 0;
      function trySubscribe() {
          if (typeof window.Echo === 'undefined') {
              retries++;
              if (retries < 50) setTimeout(trySubscribe, 200);
              return;
          }

          console.log('[Task Work Chat] ✅ Echo found! Subscribing to:', channelName);

          const channel = window.Echo.channel(channelName);

          // Bind to all events for debugging
          const pusherChannel = window.Echo.connector.pusher.channel(channelName);
          if (pusherChannel) {
              pusherChannel.bind_global(function(eventName, data) {
                  console.log('[Task Work Chat] 🔔 Event received:', eventName, data);
              });
          }

          console.log('[Task Work Chat] Setting up MessageSent listener...');

          // Try both with and without dot prefix
          channel.listen('.MessageSent', function(e) {
              console.log('[Task Work Chat] 📨 MESSAGE (with dot):', e);
              handleMessage(e);
          });

          channel.listen('MessageSent', function(e) {
              console.log('[Task Work Chat] 📨 MESSAGE (no dot):', e);
              handleMessage(e);
          });

          function handleMessage(e) {

              setTimeout(function() {
                  const el = document.getElementById('chat-messages-' + userTaskId);
                  console.log('[Task Work Chat] Looking for element:', el);
                  const rootEl = el ? el.closest('[wire\\:id]') : null;
                  console.log('[Task Work Chat] Root element:', rootEl);

                  if (rootEl && typeof Livewire !== 'undefined') {
                      const wireId = rootEl.getAttribute('wire:id');
                      const component = Livewire.find(wireId);

                      console.log('[Task Work Chat] Component found:', component);
                      console.log('[Task Work Chat] WireID:', wireId);

                      if (component) {
                          console.log('[Task Work Chat] ✅ About to call messageReceived with:', e);
                          component.call('messageReceived', e);
                          console.log('[Task Work Chat] ✅ Called messageReceived');
                          if (el) {
                              el.scrollTop = el.scrollHeight;
                              console.log('[Task Work Chat] Scrolled to bottom');
                          }
                      } else {
                          console.error('[Task Work Chat] ❌ Component is null!');
                      }
                  } else {
                      console.error('[Task Work Chat] ❌ Missing requirements:', {
                          rootEl: !!rootEl,
                          Livewire: typeof Livewire !== 'undefined'
                      });
                  }
              }, 100);
          }

          console.log('[Task Work Chat] ✅ Subscribed!');
      }

      trySubscribe();
  })();
  </script>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->

<script>
document.addEventListener('livewire:init', function () {
    Livewire.on('redirect-to-dashboard', function () {
        window.location.href = '<?php echo e(route('user.dashboard')); ?>';
    });
});
</script>
<?php /**PATH C:\laragon\www\baru\resources\views/livewire/task-work-wizard.blade.php ENDPATH**/ ?>