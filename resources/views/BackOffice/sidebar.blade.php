<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboardAdmin') }}" class="app-brand-link">

            <span class="app-brand-logo demo">
                <img alt="icon" src="{{asset('assets/img/libroLogo.png')}}" style="width:40px; height:40px; margin-right:10px;">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">LibroLink</span>

        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- Dashboard (visible pour admin OU auteur) -->
        @auth
        {{-- Si l'utilisateur est admin --}}
        @if(auth()->user()->role === 'admin')
        <li class="menu-item {{ request()->routeIs('dashboardAdmin') ? 'active' : '' }}">
            <a href="{{ route('dashboardAdmin') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Admin Dashboard</div>
            </a>
        </li>
        @endif

        {{-- Si l'utilisateur est auteur --}}
        @if(auth()->user()->role === 'auteur')
        <li class="menu-item {{ request()->routeIs('dashboardAuteur') ? 'active' : '' }}">
            <a href="{{ route('dashboardAuteur') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Author Dashboard</div>
            </a>
        </li>
        @endif
        @endauth

        <!-- ✅ Partie visible UNIQUEMENT pour ADMIN -->
        @if(auth()->user()->isAdmin())
        <!-- Gestion des utilisateurs -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Accounts Managements</span></li>
        <li class="menu-item {{ request()->routeIs('AjouterUtilisateur', 'listeUtilisateur') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Layouts">Users</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('AjouterUtilisateur') ? 'active' : '' }}">
                    <a href="{{ route('AjouterUtilisateur') }}" class="menu-link">
                        <div data-i18n="Without menu">Add User</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('listeUtilisateur') ? 'active' : '' }}">
                    <a href="{{ route('listeUtilisateur') }}" class="menu-link">
                        <div data-i18n="Without navbar">Users List</div>
                    </a>
                </li>
            </ul>
        </li>


        <!-- Gestion des magasins -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Stores Managements</span></li>
        <li class="menu-item {{ request()->routeIs('AjouterMagasin', 'listeMagasin') ? 'open active' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-store"></i>
                <div data-i18n="User interface">Stores</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('AjouterMagasin') ? 'active' : '' }}">
                    <a href="{{ route('AjouterMagasin') }}" class="menu-link">
                        <div data-i18n="Accordion">Add Store</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('listeMagasin') ? 'active' : '' }}">
                    <a href="{{ route('listeMagasin') }}" class="menu-link">
                        <div data-i18n="Alerts">Stores List</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Gestion des Blogs -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Blogs Managements</span></li>
                <li class="menu-item {{ request()->routeIs('categoryBlog.create', 'categoryBlog.index') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-copy"></i>
                <div data-i18n="Form Elements">Category Blogs</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('categoryBlog.create') ? 'active' : '' }}">
                    <a href="{{ route('categoryBlog.create') }}" class="menu-link">
                        <div data-i18n="Basic Inputs">Add Category Blog</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('categoryBlog.index') ? 'active' : '' }}">
                    <a href="{{ route('categoryBlog.index') }}" class="menu-link">
                        <div data-i18n="Input groups">Category Blog List</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('AjouterBlog', 'listeBlog') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-news"></i>
                <div data-i18n="Form Elements">Blogs</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('AjouterBlog') ? 'active' : '' }}">
                    <a href="{{ route('AjouterBlog') }}" class="menu-link">
                        <div data-i18n="Basic Inputs">Add Blog</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('listeBlog') ? 'active' : '' }}">
                    <a href="{{ route('listeBlog') }}" class="menu-link">
                        <div data-i18n="Input groups">Blogs List</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <!-- ✅ Partie visible pour ADMIN ET AUTEUR : Gestion des Livres -->
        @if(auth()->user()->isAdmin())
        <!-- Gestion des catégories -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Books Managements</span></li>
        <li class="menu-item {{ request()->routeIs('AjouterCategorie', 'listeCategorie') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-category"></i>
                <div data-i18n="Misc">Categories</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('AjouterCategorie') ? 'active' : '' }}">
                    <a href="{{ route('AjouterCategorie') }}" class="menu-link">
                        <div data-i18n="Error">Add Categorie </div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('listeCategorie') ? 'active' : '' }}">
                    <a href="{{ route('listeCategorie') }}" class="menu-link">
                        <div data-i18n="Under Maintenance">Categories List</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Gestion des livres -->
        <li class="menu-item {{ request()->routeIs('AjouterLivre', 'listeLivre') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Account Settings">Books</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('AjouterLivre') ? 'active' : '' }}">
                    <a href="{{ route('AjouterLivre') }}" class="menu-link">
                        <div data-i18n="Account">Add Book</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('listeLivre') ? 'active' : '' }}">
                    <a href="{{ route('listeLivre') }}" class="menu-link">
                        <div data-i18n="Notifications">Books List</div>
                    </a>
                </li>




            </ul>
        </li>


        <li class="menu-header small text-uppercase"><span class="menu-header-text">Payments</span></li>

        <li class="menu-item {{ request()->routeIs('transaction') ? 'active' : '' }}">
            <a href="{{ route('transactions') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Account Settings">Transactions</div>
            </a>
        </li>

        @endif

        @if(auth()->user()->isAuteur())



    <!-- Gestion des livres -->
    <li class="menu-item {{ request()->routeIs('AjouterLivre', 'listeLivre', 'mesLivres') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-book"></i>
            <div data-i18n="Account Settings">Books</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('AjouterLivre') ? 'active' : '' }}">
                <a href="{{ route('AjouterLivre') }}" class="menu-link">
                    <div data-i18n="Account">Add Book</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('listeLivre') ? 'active' : '' }}">
                <a href="{{ route('listeLivre') }}" class="menu-link">
                    <div data-i18n="Notifications">Books List</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('mesLivres') ? 'active' : '' }}">
                <a href="{{ route('mesLivres') }}" class="menu-link">
                    <div data-i18n="Profile">My Books</div>
                </a>
            </li>
        </ul>
    </li>
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Payments</span></li>

        <li class="menu-item {{ request()->routeIs('transaction') ? 'active' : '' }}">
            <a href="{{ route('transactions') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Account Settings">Transactions</div>
            </a>
        </li>

        @endif

    </ul>
</aside>
<!-- / Menu -->
