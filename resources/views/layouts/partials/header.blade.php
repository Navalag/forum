@include('layouts.partials.nav_mobile')

<header id="tt-header">
    <div class="container">
        <div class="row tt-row no-gutters">
            <div class="col-auto">
                <!-- toggle mobile menu -->
                <a class="toggle-mobile-menu" href="#">
                    <img class="tt-icon" src="{{ asset('images/svg-sprite/icon-menu_icon.svg') }}" alt="">
                </a>
                <!-- /toggle mobile menu -->
                <!-- logo -->
                <div class="tt-logo">
                    <a href="{{ LaravelLocalization::localizeUrl('/threads') }}"><img src="{{ asset('images/logo.png') }}" alt=""></a>
                </div>
                <!-- /logo -->
                <!-- desctop menu -->
                @include('layouts.partials.nav')
                <!-- /desctop menu -->
                <!-- tt-search -->
                @include('layouts.partials.search')
                <!-- /tt-search -->
            </div>
            <div class="col-auto ml-auto">
                <!-- Authentication Links -->
                @guest
                    <div class="tt-account-btn">
                        <a href="{{ LaravelLocalization::localizeUrl(route('login')) }}" class="btn btn-primary">@lang('auth.login')</a>
                        @if (Route::has('register'))
                            <a href="{{ LaravelLocalization::localizeUrl(route('register')) }}" class="btn btn-secondary">@lang('auth.sign_up')</a>
                        @endif
                    </div>
                @else
                    <div class="tt-user-info d-flex justify-content-center">
                        <user-notifications></user-notifications>
                        <div class="tt-avatar-icon tt-size-md">
                            <i class="tt-icon"><img src="{{ auth()->user()->avatar_path }}" alt="{{ auth()->user()->name }}"></i>
                        </div>
                        <div class="custom-select-01">
                            <a id="navbarDropdown" class="nav-link" href="#" title="{{ auth()->user()->name }}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Str::limit(auth()->user()->name, 7, '') }}<i class="tt-icon"><svg><use xlink:href="#icon-arrow_below"></use></svg></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ LaravelLocalization::localizeUrl(route('profile', auth()->user())) }}">
                                    @lang('header.my_profile')
                                </a>
                                <a class="dropdown-item" href="{{ LaravelLocalization::localizeUrl(route('logout')) }}"
                                   onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">
                                    @lang('header.logout')
                                </a>

                                <form id="logout-form" action="{{ LaravelLocalization::localizeUrl(route('logout')) }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</header>
