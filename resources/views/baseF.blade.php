<!DOCTYPE html>
<html lang="en">

<head>
    <title>LibroLink</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="author" content="">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="../assets/img/libroLogo.png">
    
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/page-flip/dist/css/page-flip.min.css">
<script src="https://cdn.jsdelivr.net/npm/page-flip/dist/js/page-flip.browser.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/page-flip/dist/page-flip.css">
  <script src="https://unpkg.com/epubjs/dist/epub.min.js"></script>
<script src="https://unpkg.com/page-flip/dist/page-flip.min.js"></script>
  <script src="https://unpkg.com/pdfjs-dist/build/pdf.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/libroLogo.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('icomoon/icomoon.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">
    <link rel="stylesheet" href="{{ asset('style.css') }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL,GRAD@400,0,0"
    />
<!-- PDF.js + worker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';</script>

<!-- PageFlip -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/page-flip/dist/css/page-flip.min.css">
<script src="https://cdn.jsdelivr.net/npm/page-flip/dist/js/page-flip.browser.min.js"></script>
</head>

<body data-bs-spy="scroll" data-bs-target="#header" tabindex="0">

    <!-- Navbar -->
    @include("FrontOffice.navbar")

    <!-- Contenu principal -->
    @yield('content')
@yield('meta')
    <!-- Alertes -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3"
        role="alert" style="z-index: 9999;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3"
        role="alert" style="z-index: 9999;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Footer -->
    @include("FrontOffice.footer")

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-1.11.0.min.js') }}"></script>

    <!-- Bootstrap JS Bundle (inclut Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Template JS -->
    <script src="{{ asset('js/plugins.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>

    <!-- Dropdown custom pour profil -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.getElementById('profileDropdown');
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdownMenu = this.nextElementSibling;
                    if (dropdownMenu) {
                        dropdownMenu.classList.toggle('show');
                    }
                });
            }

            document.addEventListener('click', function(e) {
                const dropdown = document.querySelector('.dropdown-menu.show');
                if (dropdown && !e.target.closest('.dropdown')) {
                    dropdown.classList.remove('show');
                }
            });
        });
    </script>
    <script src="https://ucarecdn.com/libs/widget/3.x/uploadcare.full.min.js" charset="utf-8"></script>
</body>
</html>
