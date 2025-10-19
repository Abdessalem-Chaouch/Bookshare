<div id="header-wrap">
    <div class="top-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Liens sociaux -->
                <div class="col-md-6">
                    <div class="social-links">
                        <ul>
                            <li><a href="#"><i class="icon icon-facebook"></i></a></li>
                            <li><a href="#"><i class="icon icon-twitter"></i></a></li>
                            <li><a href="#"><i class="icon icon-youtube-play"></i></a></li>
                            <li><a href="#"><i class="icon icon-behance-square"></i></a></li>
                        </ul>
                    </div>
                </div>

                <!-- Partie droite -->
                <div class="col-md-6">
                    <div class="right-element">

                        <!-- Panier -->
                        <a href="{{ route('cart.index') }}" class="for-buy position-relative">
                            <i class="icon icon-clipboard"></i>
                            <span>Cart</span>
                            <span id="cart-count" class="cart-badge">
                                {{ \App\Models\Cart::where('user_id', Auth::id())->sum('quantite') }}
                            </span>
                        </a>

                           <!-- Notification Dropdown -->
                       <div class="dropdown d-inline-block position-relative">
                            <a href="#" class="for-buy position-relative dropdown-toggle" id="notificationDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell" style="font-size: 1.2rem;"></i>
                                @if(Auth::user() && Auth::user()->unreadNotifications->count() > 0)
                                    <span id="notification-count" class="cart-badge">
                                        {{ Auth::user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end p-0 shadow"
                                aria-labelledby="notificationDropdown"
                                style="width: 300px; max-height: 400px; overflow-y: auto;">
                                
                                <!-- Header -->
                                <li class="px-3 py-2 border-bottom bg-light d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Notifications</span>
                                    <form action="{{ route('notifications.clear') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Clear All</button>
                                    </form>
                                </li>

                                <!-- Notifications list -->
                                @forelse($notifications as $notification)
                                    <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-start">
                                        <div>
                                            {{ $notification->data['message'] }}
                                            <br>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" class="ms-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">&times;</button>
                                        </form>
                                    </li>
                                @empty
                                    <li class="px-3 py-2"><span class="text-muted">Aucune notification</span></li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- CSS pour badge -->
                        <style>
                        .cart-badge {
                            position: absolute;
                            top: -5px;
                            right: -5px;
                            background: #9c9259; /* couleur primaire de ton projet */
                            color: #fff;
                            border-radius: 50%;
                            padding: 2px 6px;
                            font-size: 0.75rem;
                        }
                        </style>

                        @guest
                            <!-- Utilisateur non connecté -->
                            <a href="{{ route('login') }}" class="user-account for-buy">
                                <i class="icon icon-user"></i>&nbsp;&nbsp;&nbsp;<span>Login</span>
                            </a>
                        @endguest

                        @auth
                            @if(Auth::user()->role === 'auteur')
                                <!-- Dropdown Auteur -->
                                <div class="dropdown d-inline-block align-items-center">
                                    <a class="nav-link dropdown-toggle align-items-center" href="#" id="profileDropdownAuteur"
                                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="{{ auth()->user()->photo_profil
                                                    ? asset('storage/' . auth()->user()->photo_profil)
                                                    : asset('images/default-avatar.jpg') }}"
                                             alt="Profile"
                                             class="rounded-circle"
                                             style="width:40px; height:40px; object-fit:cover;">
                                        <span class="ms-2">{{ Auth::user()->name }}</span>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdownAuteur">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('profil.index') }}">
                                                <i class="bi bi-person"></i> Profil
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('dashboardAuteur') }}">
                                                <i class="bi bi-gear"></i> Dashboard
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                @csrf
                                                <button class="dropdown-item text-danger" type="submit">
                                                    <i class="bi bi-box-arrow-right"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                            @elseif(Auth::user()->role === 'admin')
                                <!-- Dropdown Admin -->
                                <div class="dropdown d-inline-block align-items-center">
                                    <a class="nav-link dropdown-toggle align-items-center" href="#" id="profileDropdownAdmin"
                                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="{{ auth()->user()->photo_profil
                                                    ? asset('storage/' . auth()->user()->photo_profil)
                                                    : asset('images/default-avatar.jpg') }}"
                                             alt="Profile"
                                             class="rounded-circle"
                                             style="width:40px; height:40px; object-fit:cover;">
                                        <span class="ms-2">{{ Auth::user()->name }}</span>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdownAdmin">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('profil.index') }}">
                                                <i class="bi bi-person"></i> Profil
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('dashboardAdmin') }}">
                                                <i class="bi bi-gear"></i> Dashboard
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                @csrf
                                                <button class="dropdown-item text-danger" type="submit">
                                                    <i class="bi bi-box-arrow-right"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                            @else
                                <!-- Dropdown Simple User -->
                                <div class="dropdown d-inline-block align-items-center">
                                    <a class="nav-link dropdown-toggle align-items-center" href="#" id="profileDropdownUser"
                                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="{{ auth()->user()->photo_profil
                                                    ? asset('storage/' . auth()->user()->photo_profil)
                                                    : asset('images/default-avatar.jpg') }}"
                                             alt="Profile"
                                             class="rounded-circle"
                                             style="width:40px; height:40px; object-fit:cover;">
                                        <span class="ms-2">{{ Auth::user()->name }}</span>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdownUser">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('profil.index') }}">
                                                <i class="bi bi-person"></i> Profil
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('myBooks') }}">
                                                <i class="bi bi-journal-bookmark"></i> My Books
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('borrows') }}">
                                                <i class="bi bi-book"></i> My Borrows
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('purchases') }}">
                                                <i class="bi bi-book"></i> My Purchases
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                @csrf
                                                <button class="dropdown-item text-danger" type="submit">
                                                    <i class="bi bi-box-arrow-right"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        @endauth

                        <!-- Search -->
                        <div class="action-menu">
                            <div class="search-bar">
                                <a href="#" class="search-button search-toggle" data-selector="#header-wrap">
                                    <i class="icon icon-search"></i>
                                </a>
                                <form role="search" method="get" class="search-box">
                                    <input class="search-field text search-input" placeholder="Search" type="search">
                                </form>
                            </div>
                        </div>
                    </div><!-- right-element -->
                </div>
            </div>
        </div>
    </div><!-- top-content -->

    <!-- Header principal -->
    <header id="header">
        <div class="container-fluid">
            <div class="row">
                <!-- Logo -->
                <div class="col-md-2">
                    <div class="main-logo">
                        <a href="{{ route('accueil') }}" class="logo-link d-flex align-items-center">
                            <img src="{{asset('assets/img/libroLogo.png')}}" alt="logo" style="width:50px; height:60px; margin-right:10px;">
                            <span class="logo-text">LibroLink</span>
                        </a>
                    </div>
                </div>

                <!-- Navbar -->
                <div class="col-md-10">
                    <nav id="navbar">
                        <div class="main-menu stellarnav">
                            <ul class="menu-list">
                                <li class="menu-item {{ request()->routeIs('accueil') ? 'active' : '' }}">
                                    <a href="{{ route('accueil') }}">Home</a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('front.categories') ? 'active' : '' }}">
                                    <a href="{{ route('front.categories') }}" class="nav-link">Categories</a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('livresf') ? 'active' : '' }}">
                                    <a href="{{ route('livresf') }}">Books</a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('articles') ? 'active' : '' }}">
                                    <a href="{{ route('articles') }}" class="nav-link">Blogs</a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('stores') ? 'active' : '' }}">
                                    <a href="{{ route('stores') }}" class="nav-link">Stores</a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('aboutus') ? 'active' : '' }}">
                                    <a href="{{ route('aboutus') }}" class="nav-link">About us</a>
                                </li>
                            </ul>

                            <div class="hamburger">
                                <span class="bar"></span>
                                <span class="bar"></span>
                                <span class="bar"></span>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
</div><!-- header-wrap -->
