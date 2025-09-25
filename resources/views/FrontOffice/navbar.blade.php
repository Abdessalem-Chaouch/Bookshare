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
						
                        <a href="#" class="cart for-buy">
                            <i class="icon icon-clipboard"></i><span>Cart</span>
                        </a>

                        @guest
                            <!-- Utilisateur non connecté -->
                            <a href="{{ route('login') }}" class="user-account for-buy">
                                <i class="icon icon-user"></i>&nbsp;&nbsp;&nbsp;<span>Se connecter</span>
                            </a>
                        @endguest

                      @auth
						@if(Auth::user()->role === 'auteur')
							<!-- Dropdown Auteur -->
							<div class="nav-item dropdown d-inline-block align-items-center">
								<a class="nav-link dropdown-toggle align-items-center" href="#" id="profileDropdown"
								role="button" data-bs-toggle="dropdown" aria-expanded="false">
									<img src="{{ auth()->user()->photo_profil 
												? asset('storage/' . auth()->user()->photo_profil) 
												: asset('images/default-avatar.jpg') }}"
										alt="Profile"
										class="rounded-circle"
										style="width:40px; height:40px; object-fit:cover;">
									<span class="ms-2">{{ Auth::user()->name }}</span>
								</a>

								<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
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
							@else
							<!-- Dropdown Simple User -->
							<div class="nav-item dropdown d-inline-block align-items-center">
								<a class="nav-link dropdown-toggle align-items-center" href="#" id="profileDropdown"
								role="button" data-bs-toggle="dropdown" aria-expanded="false">
									<img src="{{ auth()->user()->photo_profil 
												? asset('storage/' . auth()->user()->photo_profil) 
												: asset('images/default-avatar.jpg') }}"
										alt="Profile"
										class="rounded-circle"
										style="width:40px; height:40px; object-fit:cover;">
									<span class="ms-2">{{ Auth::user()->name }}</span>
								</a>

								<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
									<li>
										<a class="dropdown-item" href="{{ route('profil.index') }}">
											<i class="bi bi-person"></i> Profil
										</a>
									</li>
									<li>
										<a class="dropdown-item" href="{{ route('profil.index') }}">
											<i class="bi bi-journal-bookmark"></i> My Books
										</a>
									</li>
									<li>
										<a class="dropdown-item" href="{{ route('profil.index') }}">
											<i class="bi bi-book"></i> Borrows
										</a>
									</li>
									<li>
										<a class="dropdown-item" href="{{ route('profil.index') }}">
											<i class="bi bi-clock-history"></i> Late Return
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
                                <li class="menu-item active"><a href="{{ route('accueil') }}">Home</a></li>
                                <li class="menu-item"><a href="{{ route('livres') }}" class="nav-link">Livres</a></li>
                                <li class="menu-item"><a href="{{ route('articles') }}" class="nav-link">Articles</a></li>
                                <li class="menu-item"><a href="{{ route('aboutus') }}" class="nav-link">About us</a></li>
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
