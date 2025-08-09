<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>@lang('translation.menu')</span></li>

                <li class="nav-item">
                    <a href="index" class="nav-link"><i data-feather="home" class="icon-dual"></i> <span>@lang('translation.ecommerce')</span></a>
                </li>


                <li class="menu-title"><i class="ri-more-fill"></i> <span>@lang('translation.pages')</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarCategories" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCategories">
                        <i data-feather="grid" class="icon-dual"></i> <span>@lang('admin.categories.categories')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarCategories">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('dashboard.categories.index')}}" class="nav-link">@lang('admin.categories.categories')</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('dashboard.categories.trashes')}}" class="nav-link">@lang('admin.categories.categories_deleted')</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end categories Menu -->

                <li class="menu-title"><span>@lang('translation.menu_general')</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarCountries" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCountries">
                        <i data-feather="grid" class="icon-dual"></i> <span>@lang('admin.countries.countries_and_regions')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarCountries">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('dashboard.countries.index')}}" class="nav-link">@lang('admin.countries.countries')</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('dashboard.regions.index')}}" class="nav-link">@lang('admin.regions.regions')</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('dashboard.cities.index')}}" class="nav-link">@lang('admin.cities.cities')</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end categories Menu -->

                <li class="nav-item">
                    <a class="nav-link" href="{{route('dashboard.currencies.index')}}">
                        <i data-feather="grid" class="icon-dual"></i> <span>@lang('admin.currencies.currencies')</span>
                    </a>
                </li> <!-- end categories Menu -->
                <li class="nav-item">
                    <a class="nav-link" href="{{route('dashboard.languages.index')}}">
                        <i data-feather="grid" class="icon-dual"></i> <span>@lang('admin.languages.languages')</span>
                    </a>
                </li> <!-- end languages Menu -->


            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
