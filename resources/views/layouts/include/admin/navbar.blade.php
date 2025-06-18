@php($authUser = auth()->user())
@php($nav = $nav ?? null)
@if (@$authUser && !$nav)
    <nav class="navbar main_nav d-none navbar-expand-md w-100 navbar-light bg-white shadow-sm position-absolute">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Route::is('admin.setting') ? 'active' : '' }}"
                            href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Setting
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item {{ Route::is('admin.setting.index') ? 'active' : '' }}"
                                    href="{{ route('admin.setting.index') }}">GHL Setting</a></li>
                            <li><a class="dropdown-item {{ Route::is('admin.setting.noloco') ? 'active' : '' }}"
                                    href="{{ route('admin.setting.noloco') }}">Noloco Setting</a></li>
                            <li><a class="dropdown-item {{ Route::is('admin.setting.mapping') ? 'active' : '' }}"
                                    href="{{ route('admin.setting.mapping', 'deals') }}">Mapping Setting</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.dropdown.matchables.index') ? 'active' : '' }}"
                            href="{{ route('admin.dropdown.matchables.index') }}">Matchables</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.scripts.index') ? 'active' : '' }}"
                            href="{{ route('admin.scripts.index') }}">Scripts</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Route::is('admin.setting.crud') ? 'active' : '' }}"
                            href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Crud Settings
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            @php($currentParam = request()->route('id'))
                            <li>
                                <a class="dropdown-item {{ $currentParam === 'sources' ? 'active' : '' }}"
                                    href="{{ route('admin.setting.crud', 'sources') }}">
                                    Sources
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $currentParam === 'id_type' ? 'active' : '' }}"
                                    href="{{ route('admin.setting.crud', 'id_type') }}">
                                    Id Type
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $currentParam === 'state_id' ? 'active' : '' }}"
                                    href="{{ route('admin.setting.crud', 'state_id') }}">
                                    State Id
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $currentParam === 'residence_type' ? 'active' : '' }}"
                                    href="{{ route('admin.setting.crud', 'residence_type') }}">
                                    Residence Type
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $currentParam === 'previous_residence_type' ? 'active' : '' }}"
                                    href="{{ route('admin.setting.crud', 'previous_residence_type') }}">
                                    Previous Residence Type
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $currentParam === 'employment_status' ? 'active' : '' }}"
                                    href="{{ route('admin.setting.crud', 'employment_status') }}">
                                    Employment Status
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $currentParam === 'previous_employment_status' ? 'active' : '' }}"
                                    href="{{ route('admin.setting.crud', 'previous_employment_status') }}">
                                    Previous Employment Status
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $currentParam === 'income_frequency' ? 'active' : '' }}"
                                    href="{{ route('admin.setting.crud', 'income_frequency') }}">
                                    Income Frequency
                                </a>
                            </li>
                        </ul>

                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Route::is('admin.mappings.custom') ? 'active' : '' }}"
                            href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Mapping
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item {{ Route::is('admin.mappings.custom.index') ? 'active' : '' }}"
                                    href="{{ route('admin.mappings.custom.index') }}">Extention</a></li>
                            <li><a class="dropdown-item {{ Route::is('admin.mappings.customer.form') ? 'active' : '' }}"
                                    href="{{ route('admin.mappings.customer.form') }}">Customer</a></li>
                            <li><a class="dropdown-item {{ Route::is('admin.mappings.coborrower.form') ? 'active' : '' }}"
                                    href="{{ route('admin.mappings.coborrower.form') }}">Coborrower</a></li>
                            <li><a class="dropdown-item {{ Route::is('admin.mappings.ghl.form') ? 'active' : '' }}"
                                    href="{{ route('admin.mappings.deals.form') }}">Deals</a></li>

                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Route::is('admin.mappings.csv.index') ? 'active' : '' }}"
                            href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            CSV Setting
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item {{ Route::is('admin.mappings.csv.index') ? 'active' : '' }}"
                                    href="{{ route('admin.mappings.csv.index') }}">Inbounding</a></li>
                            <li><a class="dropdown-item {{ Route::is('admin.mappings.csv.outbound.index') ? 'active' : '' }}"
                                    href="{{ route('admin.mappings.csv.outbound.index') }}">Outbonding</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.logs.history') ? 'active' : '' }}"
                            href="{{ route('admin.logs.history', 'deals') }}">Logs History</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.scriptings.index') ? 'active' : '' }}"
                            href="{{ route('admin.scriptings.index') }}">Scriptings</a>
                    </li>
                </ul>



                <ul class="navbar-nav ms-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                </li>
                            </ul>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
@endif
