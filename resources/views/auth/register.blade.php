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
                <p class="gentle-subtitle">Sign Up to your peaceful space</p>
            </div>

    <form method="POST" action="{{ route('register') }}" class="comfort-form" novalidate>
        @csrf

        <!-- Name -->
        <div class="soft-field">
            <div class="field-container">
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                <label for="name">Full Name</label>
                <div class="field-accent"></div>
            </div>
            <x-input-error :messages="$errors->get('name')" class="gentle-error" />
        </div>

        <!-- Email -->
        <div class="soft-field">
            <div class="field-container">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                <label for="email">Email address</label>
                <div class="field-accent"></div>
            </div>
            <x-input-error :messages="$errors->get('email')" class="gentle-error" />
        </div>

        <!-- Password -->
        <div class="soft-field">
            <div class="field-container">
                <input id="password" type="password" name="password" required autocomplete="new-password">
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

        <!-- Confirm Password -->
        <div class="soft-field">
            <div class="field-container">
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                <label for="password_confirmation">Confirm Password</label>
                <div class="field-accent"></div>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="gentle-error" />
        </div>

        <!-- Register Button -->
        <button type="submit" class="comfort-button mt-4">
                    <div class="button-background"></div>
                    <span class="button-text">Register</span>
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
                <span class="signup-text">Already Register?</span>
                <a href="{{ route('login') }}" class="comfort-link signup-link">Sign In</a>
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

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/mainLogin.js') }}"></script>
  </body>
</html>
