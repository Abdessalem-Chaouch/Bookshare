<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
     <title>LibroLink</title>
  
    <link rel="icon" type="image/png" href="../assets/img/libroLogo.png">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/icomoon/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  </head>

  <body>

 <div class="d-lg-flex half">
   <div class="bg order-1 order-md-2" style="background-image: url('{{ asset('assets/img/bg_1.jpg') }}');;"></div> <div class="contents order-2 order-md-1">

        <div class="container">
          <div class="row align-items-center justify-content-center">
            <div class="col-md-7">
              <div class="comfort-header">
                <div class="gentle-logo">
                    <div class="logo-circle">
                        <div class="comfort-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <path d="M16 2C8.3 2 2 8.3 2 16s6.3 14 14 14 14-6.3 14-14S23.7 2 16 2z" fill="none" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M12 16a4 4 0 108 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                                <circle cx="20" cy="12" r="1.5" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="gentle-glow"></div>
                    </div>
                </div>
                <h1 class="comfort-title">Welcome back</h1>
                <p class="gentle-subtitle">Sign in to your peaceful space</p>
            </div>

              <!-- FORMULAIRE LARAVEL -->
              <form method="POST" action="{{ route('login') }}" class="comfort-form" novalidate>
                @csrf

                <!-- Email -->
                <div class="soft-field">
                  <div class="field-container">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    <label for="email">Email address</label>
                    <div class="field-accent"></div>
                  </div>
                  <x-input-error :messages="$errors->get('email')" class="gentle-error" />
                </div>

                <!-- Password -->
                <div class="soft-field">
                  <div class="field-container">
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                    <label for="password">Password</label>
                    <button type="button" class="gentle-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                            <div class="toggle-icon">
                                <svg class="eye-open" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M10 3c-4.5 0-8.3 3.8-9 7 .7 3.2 4.5 7 9 7s8.3-3.8 9-7c-.7-3.2-4.5-7-9-7z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                </svg>
                                <svg class="eye-closed" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M3 3l14 14M8.5 8.5a3 3 0 004 4m2.5-2.5C15 10 12.5 7 10 7c-.5 0-1 .1-1.5.3M10 13c-2.5 0-4.5-2-5-3 .3-.6.7-1.2 1.2-1.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </button>
                        <div class="field-accent"></div>
                  </div>
                  <x-input-error :messages="$errors->get('password')" class="gentle-error" />
                </div>

                <!-- Remember + Forgot -->
                <div class="comfort-options">
                   <label class="gentle-checkbox">
                        <input type="checkbox" id="remember" name="remember">
                        <span class="checkbox-soft">
                            <div class="check-circle"></div>
                            <svg class="check-mark" width="12" height="10" viewBox="0 0 12 10" fill="none">
                                <path d="M1 5l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    <span class="checkbox-text">Remember me</span>
                  </label>
                  @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="comfort-link">Forgot password?</a>
                  @endif
                </div>

                <!-- Bouton login -->
                  <button type="submit" class="comfort-button">
                    <div class="button-background"></div>
                    <span class="button-text">Sign in</span>
                    <div class="button-loader">
                        <div class="gentle-spinner">
                            <div class="spinner-circle"></div>
                        </div>
                    </div>
                    <div class="button-glow"></div>
                </button>
              </form>

              <!-- Social logins -->
            <div class="gentle-divider">
                <div class="divider-line"></div>
                <span class="divider-text">or continue with</span>
                <div class="divider-line"></div>
            </div>

              <div class="comfort-social">
                <a href="{{ route('google.login') }}" class="social-soft me-2">
            <div class="social-background"></div>
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <path d="M9 7.4v3.2h4.6c-.2 1-.8 1.8-1.6 2.4v2h2.6c1.5-1.4 2.4-3.4 2.4-5.8 0-.6 0-1.1-.1-1.6H9z" fill="#4285F4"/>
                <path d="M9 17c2.2 0 4-0.7 5.4-1.9l-2.6-2c-.7.5-1.6.8-2.8.8-2.1 0-3.9-1.4-4.6-3.4H1.7v2.1C3.1 15.2 5.8 17 9 17z" fill="#34A853"/>
                <path d="M4.4 10.5c-.2-.5-.2-1.1 0-1.6V6.8H1.7c-.6 1.2-.6 2.6 0 3.8l2.7-2.1z" fill="#FBBC04"/>
                <path d="M9 4.2c1.2 0 2.3.4 3.1 1.2l2.3-2.3C12.9 1.8 11.1 1 9 1 5.8 1 3.1 2.8 1.7 5.4l2.7 2.1C5.1 5.6 6.9 4.2 9 4.2z" fill="#EA4335"/>
            </svg>
            <span>Google</span>
            <div class="social-glow"></div>
        </a>

        <a href="{{ route('facebook.login') }}" class="social-soft">
            <div class="social-background"></div>
            <svg width="18" height="18" viewBox="0 0 18 18" fill="#1877F2">
                <path d="M18 9C18 4.03 13.97 0 9 0S0 4.03 0 9c0 4.49 3.29 8.21 7.59 9v-6.37H5.31V9h2.28V7.02c0-2.25 1.34-3.49 3.39-3.49.98 0 2.01.18 2.01.18v2.21h-1.13c-1.11 0-1.46.69-1.46 1.4V9h2.49l-.4 2.63H10.4V18C14.71 17.21 18 13.49 18 9z"/>
            </svg>
            <span>Facebook</span>
            <div class="social-glow"></div>
        </a>   </div>

              <div class="comfort-signup">
                <span class="signup-text">Don't have an account?</span>
                <a href="{{ route('register') }}" class="comfort-link signup-link">Sign up</a>
              </div>

              <div class="gentle-success" id="successMessage">
                <div class="success-bloom">
                    <div class="bloom-rings">
                        <div class="bloom-ring ring-1"></div>
                        <div class="bloom-ring ring-2"></div>
                        <div class="bloom-ring ring-3"></div>
                    </div>
                    <div class="success-icon">
                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                            <path d="M8 14l5 5 11-11" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <h3 class="success-title">Welcome!</h3>
                <p class="success-desc">Taking you to your account...</p>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>

<<<<<<< HEAD
        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Social Login -->
        <div class="mt-4 space-y-3">
            <!-- Facebook Login -->
            <a href="{{ route('facebook.login') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Continue with Facebook
            </a>
            
            <!-- Google Login -->
            <a href="{{ route('google.login') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Continue with Google
            </a>
        </div>

        <div class="flex items-center justify-center mt-4">
            <span class="text-sm text-gray-600 dark:text-gray-400">or</span>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
            @endif
            &nbsp;&nbsp;&nbsp;
            <a href="{{ route('register') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __("Sign up") }}
            </a>

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
=======
    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/mainLogin.js') }}"></script>
  </body>
</html>
>>>>>>> origin/main
