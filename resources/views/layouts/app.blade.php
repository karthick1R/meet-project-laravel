<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Meeting Room Booking')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    @stack('styles')

    <style>
        body {
            font-family: "Inter", sans-serif;
            background: #f5f8ff;
            color: #1f2937;
        }

        /* TOP NAVBAR */
        .top-navbar {
            height: 60px;
            background: white;
            border-bottom: 1px solid #e3e8f0;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.05);
        }

        .top-navbar .navbar-brand {
            font-weight: 700;
            color: #3b7df0;
        }

        .top-navbar .nav-link {
            color: #374151;
            font-weight: 500;
        }

        .top-navbar .nav-link:hover {
            color: #3b7df0;
        }

        /* SIDEBAR */
        .sidebar {
            background: white;
            min-height: calc(100vh - 60px);
            border-right: 1px solid #e3e8f0;
            padding: 20px 0;
        }

        .sidebar .nav-link {
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 500;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 10px;
            margin-bottom: 6px;
            transition: 0.25s ease;
        }

        .sidebar .nav-link:hover {
            background: #eef4ff;
            color: #3b7df0;
        }

        .sidebar .nav-link.active {
            background: #3b7df0;
            color: white !important;
            box-shadow: 0px 4px 20px rgba(59, 125, 240, 0.25);
        }

        .sidebar .section-title {
            font-size: 12px;
            text-transform: uppercase;
            color: #9ca3af;
            padding: 10px 20px 6px;
            letter-spacing: 0.5px;
        }

        /* MAIN CONTENT */
        .main-content {
            padding: 2rem;
        }

        .alert {
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <!-- TOP NAVBAR -->
    <nav class="navbar navbar-expand-lg top-navbar px-4">
        <img src="{{ auth()->check() && auth()->user()->logo
    ? asset('storage/' . auth()->user()->logo)
    : asset('images/default-logo.png') }}" alt="Company Logo" style="width: 80px; height: 45px; object-fit: contain;"
            onerror="this.onerror=null; this.src='{{ asset('images/default-logo.png') }}';">







        <a class="navbar-brand" style="padding: 0.9%" href="{{ route('dashboard') }}">
            Meeting Room Booking
        </a>


        <div class="ms-auto d-flex">
            @auth
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        @if(auth()->user()->logo)
                            <!-- <img src="{{ asset('storage/' . auth()->user()->logo) }}" alt="Profile" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; margin-right: 8px;"> -->
                        @else
                            <i class="fa fa-user-circle me-2"></i>
                        @endif
                        {{ auth()->user()->name }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('bookings.index') }}">My Bookings</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">My Profile</a></li>

                        @if(auth()->user()->isAdmin())
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('admin.rooms.index') }}">Manage Rooms</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.bookings.index') }}">All Bookings</a></li>
                            @if(auth()->user()->isSuperAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Manage Users</a></li>
                            @endif
                        @endif

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">@csrf
                                <button class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a class="nav-link me-3" href="{{ route('login') }}">Login</a>
                <a class="nav-link" href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">

               @auth
        <div class="col-md-2 sidebar">
            <ul class="nav flex-column">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                       href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}" 
                       href="{{ route('rooms.index') }}">
                        <i class="fas fa-door-open"></i> Rooms
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('bookings.index') ? 'active' : '' }}" 
                       href="{{ route('bookings.index') }}">
                        <i class="fas fa-calendar-check"></i> My Bookings
                    </a>
                </li>

                {{-- NORMAL USERS ONLY --}}
                @if(auth()->user()->role === 'user')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('bookings.overall') ? 'active' : '' }}" 
                           href="{{ route('bookings.overall') }}">
                            <i class="fas fa-list-alt"></i> Overall Bookings
                        </a>
                    </li>
                @endif

                {{-- ADMIN SECTION --}}
                @if(auth()->user()->isAdmin())
                    <div class="section-title">Management</div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}" 
                           href="{{ route('admin.rooms.index') }}">
                            <i class="fas fa-door-open"></i> Manage Rooms
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" 
                           href="{{ route('admin.bookings.index') }}">
                            <i class="fas fa-calendar-check"></i> All Bookings
                        </a>
                    </li>

                    {{-- ✅ YOUR REQUESTED CONDITION ADDED HERE --}}
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                               href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </li>
                    @endif

                @endif

            </ul>
        </div>
        @endauth

            <!-- MAIN CONTENT -->
            <div class="@auth col-md-10 @else col-12 @endauth main-content">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            }
        });
    </script>

    @stack('scripts')

</body>

</html>