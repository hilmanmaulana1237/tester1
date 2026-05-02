<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enhanced Wizard Step with Tailwind CSS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#eff6ff',
              100: '#dbeafe',
              500: '#3b82f6',
              600: '#2563eb',
              700: '#1d4ed8',
            }
          },
          animation: {
            'fade-in': 'fadeIn 0.5s ease-in-out',
            'slide-up': 'slideUp 0.3s ease-out',
          },
          keyframes: {
            fadeIn: {
              '0%': { opacity: '0' },
              '100%': { opacity: '1' },
            },
            slideUp: {
              '0%': { transform: 'translateY(10px)', opacity: '0' },
              '100%': { transform: 'translateY(0)', opacity: '1' },
            }
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <header class="mb-10 text-center">
      <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Enhanced Wizard Form</h1>
      <p class="text-gray-600 dark:text-gray-300">Complete your profile in just a few steps</p>
    </header>

    <!-- Main Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
      <div class="p-6 md:p-8">
        <!-- Progress Bar -->
        <div class="mb-8">
          <div class="flex justify-between mb-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300" id="progress-text">33%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
            <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-500 ease-in-out" id="progress-bar" style="width: 33%"></div>
          </div>
        </div>

        <!-- Enhanced Stepper -->
        <div data-hs-stepper='{"isCompleted": false}' id="stepper">
          <!-- Stepper Nav -->
          <ul class="relative flex flex-row gap-x-2 mb-8">
            <!-- Item 1 -->
            <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group" data-hs-stepper-nav-item='{"index": 1, "isCompleted": false}'>
              <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                <span class="size-7 flex justify-center items-center shrink-0 bg-gray-100 font-medium text-gray-800 rounded-full transition-all duration-300 group-hover:bg-gray-200 hs-stepper-active:bg-primary-600 hs-stepper-active:text-white hs-stepper-success:bg-green-500 hs-stepper-success:text-white hs-stepper-completed:bg-green-500 hs-stepper-completed:group-hover:bg-green-600 dark:bg-gray-700 dark:text-white dark:group-hover:bg-gray-600 dark:hs-stepper-active:bg-primary-500 dark:hs-stepper-success:bg-green-500 dark:hs-stepper-completed:bg-green-500">
                  <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">1</span>
                  <svg class="hidden shrink-0 size-3 hs-stepper-success:block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </span>
                <span class="ms-2 text-sm font-medium text-gray-800 group-hover:text-gray-600 dark:text-white dark:group-hover:text-gray-300">
                  Account
                </span>
              </span>
              <div class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-green-500 hs-stepper-completed:bg-green-500 dark:bg-gray-700 dark:hs-stepper-success:bg-green-500 dark:hs-stepper-completed:bg-green-500"></div>
            </li>
            <!-- End Item 1 -->
            
            <!-- Item 2 -->
            <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group" data-hs-stepper-nav-item='{"index": 2, "isCompleted": false}'>
              <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                <span class="size-7 flex justify-center items-center shrink-0 bg-gray-100 font-medium text-gray-800 rounded-full transition-all duration-300 group-hover:bg-gray-200 hs-stepper-active:bg-primary-600 hs-stepper-active:text-white hs-stepper-success:bg-green-500 hs-stepper-success:text-white hs-stepper-completed:bg-green-500 hs-stepper-completed:group-hover:bg-green-600 dark:bg-gray-700 dark:text-white dark:group-hover:bg-gray-600 dark:hs-stepper-active:bg-primary-500 dark:hs-stepper-success:bg-green-500 dark:hs-stepper-completed:bg-green-500 dark:hs-stepper-completed:group-hover:bg-green-600">
                  <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">2</span>
                  <svg class="hidden shrink-0 size-3 hs-stepper-success:block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </span>
                <span class="ms-2 text-sm font-medium text-gray-800 group-hover:text-gray-600 dark:text-white dark:group-hover:text-gray-300">
                  Personal
                </span>
              </span>
              <div class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-green-500 hs-stepper-completed:bg-green-500 dark:bg-gray-700 dark:hs-stepper-success:bg-green-500 dark:hs-stepper-completed:bg-green-500"></div>
            </li>
            <!-- End Item 2 -->
            
            <!-- Item 3 -->
            <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group" data-hs-stepper-nav-item='{"index": 3, "isCompleted": false}'>
              <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                <span class="size-7 flex justify-center items-center shrink-0 bg-gray-100 font-medium text-gray-800 rounded-full transition-all duration-300 group-hover:bg-gray-200 hs-stepper-active:bg-primary-600 hs-stepper-active:text-white hs-stepper-success:bg-green-500 hs-stepper-success:text-white hs-stepper-completed:bg-green-500 hs-stepper-completed:group-hover:bg-green-600 dark:bg-gray-700 dark:text-white dark:group-hover:bg-gray-600 dark:hs-stepper-active:bg-primary-500 dark:hs-stepper-success:bg-green-500 dark:hs-stepper-completed:bg-green-500 dark:hs-stepper-completed:group-hover:bg-green-600">
                  <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">3</span>
                  <svg class="hidden shrink-0 size-3 hs-stepper-success:block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </span>
                <span class="ms-2 text-sm font-medium text-gray-800 group-hover:text-gray-600 dark:text-white dark:group-hover:text-gray-300">
                  Preferences
                </span>
              </span>
              <div class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-green-500 hs-stepper-completed:bg-green-500 dark:bg-gray-700 dark:hs-stepper-success:bg-green-500 dark:hs-stepper-completed:bg-green-500"></div>
            </li>
            <!-- End Item 3 -->
          </ul>
          <!-- End Stepper Nav -->

          <!-- Stepper Content -->
          <div class="mt-5 sm:mt-8">
            <!-- First Content - Account Setup -->
            <div data-hs-stepper-content-item='{"index": 1, "isCompleted": false}' class="animate-fade-in">
              <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Account Setup</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Create your account credentials to get started</p>
                
                <div class="space-y-4">
                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                      </div>
                      <input type="email" id="email" class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="your.email@example.com">
                    </div>
                  </div>
                  
                  <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                      </div>
                      <input type="password" id="password" class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="••••••••">
                    </div>
                  </div>
                  
                  <div>
                    <label for="confirm-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                      </div>
                      <input type="password" id="confirm-password" class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="••••••••">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End First Content -->

            <!-- Second Content - Personal Information -->
            <div data-hs-stepper-content-item='{"index": 2, "isCompleted": false}' class="animate-fade-in" style="display: none;">
              <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Personal Information</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Tell us a bit about yourself</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="first-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
                    <input type="text" id="first-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="John">
                  </div>
                  
                  <div>
                    <label for="last-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
                    <input type="text" id="last-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Doe">
                  </div>
                  
                  <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                    <input type="tel" id="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="+1 (555) 123-4567">
                  </div>
                  
                  <div>
                    <label for="birthdate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Birth Date</label>
                    <input type="date" id="birthdate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                  </div>
                  
                  <div class="md:col-span-2">
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bio</label>
                    <textarea id="bio" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Tell us a little about yourself..."></textarea>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Second Content -->

            <!-- Third Content - Preferences -->
            <div data-hs-stepper-content-item='{"index": 3, "isCompleted": false}' class="animate-fade-in" style="display: none;">
              <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Preferences</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Customize your experience</p>
                
                <div class="space-y-6">
                  <div>
                    <h4 class="text-md font-medium text-gray-800 dark:text-white mb-3">Notification Preferences</h4>
                    <div class="space-y-2">
                      <div class="flex items-center">
                        <input id="email-notifications" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="email-notifications" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Email notifications</label>
                      </div>
                      <div class="flex items-center">
                        <input id="sms-notifications" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="sms-notifications" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">SMS notifications</label>
                      </div>
                      <div class="flex items-center">
                        <input id="push-notifications" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="push-notifications" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Push notifications</label>
                      </div>
                    </div>
                  </div>
                  
                  <div>
                    <h4 class="text-md font-medium text-gray-800 dark:text-white mb-3">Language</h4>
                    <select id="language" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                      <option value="en">English</option>
                      <option value="es">Spanish</option>
                      <option value="fr">French</option>
                      <option value="de">German</option>
                      <option value="ja">Japanese</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Third Content -->

            <!-- Final Content -->
            <div data-hs-stepper-content-item='{"isFinal": true}' class="animate-fade-in" style="display: none;">
              <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                <div class="text-center py-8">
                  <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="h-10 w-10 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <h3 class="mt-4 text-xl font-semibold text-gray-800 dark:text-white">Setup Complete!</h3>
                  <p class="mt-2 text-gray-600 dark:text-gray-300">Your account has been successfully created. Welcome aboard!</p>
                  <div class="mt-6">
                    <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      Go to Dashboard
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Final Content -->

            <!-- Stepper Controls -->
            <div class="mt-8 flex justify-between items-center gap-x-2">
              <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600 dark:focus:bg-gray-600 transition-colors" data-hs-stepper-back-btn="" style="display: none;">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m15 18-6-6 6-6"></path>
                </svg>
                Back
              </button>
              
              <div class="flex space-x-2">
                <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary-600 text-white shadow-sm hover:bg-primary-700 focus:outline-hidden focus:bg-primary-700 disabled:opacity-50 disabled:pointer-events-none transition-colors" data-hs-stepper-next-btn="">
                  Next
                  <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6"></path>
                  </svg>
                </button>
                
                <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary-600 text-white shadow-sm hover:bg-primary-700 focus:outline-hidden focus:bg-primary-700 disabled:opacity-50 disabled:pointer-events-none transition-colors" data-hs-stepper-finish-btn="" style="display: none;">
                  Finish
                </button>
              </div>
              
              <button type="reset" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-200 text-gray-800 shadow-sm hover:bg-gray-300 focus:outline-hidden focus:bg-gray-300 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:bg-gray-600 transition-colors" data-hs-stepper-reset-btn="">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                  <path d="M3 3v5h5"></path>
                </svg>
                Reset
              </button>
            </div>
          </div>
          <!-- End Stepper Content -->
        </div>
        <!-- End Enhanced Stepper -->
      </div>
    </div>

    <!-- Footer -->
    <footer class="mt-8 text-center text-gray-500 dark:text-gray-400 text-sm">
      <p>© 2023 Enhanced Wizard Form. All rights reserved.</p>
    </footer>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize stepper
      const stepper = document.querySelector('#stepper');
      const progressBar = document.getElementById('progress-bar');
      const progressText = document.getElementById('progress-text');
      
      // Update progress bar
      function updateProgress() {
        const totalSteps = 3;
        const currentStep = parseInt(stepper.querySelector('.hs-stepper-active').closest('[data-hs-stepper-nav-item]').dataset.hsStepperNavItem.index);
        const progress = (currentStep / totalSteps) * 100;
        
        progressBar.style.width = `${progress}%`;
        progressText.textContent = `${Math.round(progress)}%`;
      }
      
      // Add event listeners to stepper navigation
      document.querySelectorAll('[data-hs-stepper-nav-item]').forEach(item => {
        item.addEventListener('click', function() {
          setTimeout(updateProgress, 100);
        });
      });
      
      // Add event listeners to stepper buttons
      document.querySelector('[data-hs-stepper-next-btn]').addEventListener('click', function() {
        setTimeout(updateProgress, 100);
      });
      
      document.querySelector('[data-hs-stepper-back-btn]').addEventListener('click', function() {
        setTimeout(updateProgress, 100);
      });
      
      // Form validation
      document.querySelector('[data-hs-stepper-next-btn]').addEventListener('click', function(e) {
        const currentStep = parseInt(stepper.querySelector('.hs-stepper-active').closest('[data-hs-stepper-nav-item]').dataset.hsStepperNavItem.index);
        let isValid = true;
        
        if (currentStep === 1) {
          const email = document.getElementById('email').value;
          const password = document.getElementById('password').value;
          const confirmPassword = document.getElementById('confirm-password').value;
          
          if (!email || !password || !confirmPassword) {
            isValid = false;
            alert('Please fill in all fields');
          } else if (password !== confirmPassword) {
            isValid = false;
            alert('Passwords do not match');
          }
        } else if (currentStep === 2) {
          const firstName = document.getElementById('first-name').value;
          const lastName = document.getElementById('last-name').value;
          
          if (!firstName || !lastName) {
            isValid = false;
            alert('Please fill in your name');
          }
        }
        
        if (!isValid) {
          e.preventDefault();
        }
      });
    });
  </script>
</body>
</html>