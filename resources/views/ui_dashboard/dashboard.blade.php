<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF token -->
    <title>@yield('title')</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    {{-- <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    {{-- <style>
        /* Custom styling for the sidebar custom container */
        .sidebar-custom {
            position: absolute;
            /* Position the custom area */
            bottom: 0;
            /* Stick it to the bottom of the sidebar */
            width: 100%;
            /* Ensure it spans the width of the sidebar */
            padding: 10px;
            /* Add some padding for spacing */
            z-index: 1000;
            /* Make sure it's above other sidebar elements */
            background-color: #343a40;
            /* Match the AdminLTE sidebar color */
            display: flex;
            /* Use flexbox to arrange buttons */
            justify-content: space-between;
            /* Space the buttons */
        }

        /* Styling for the right-positioned button */
        .sidebar-custom .pos-right {
            margin-left: auto;
            /* Push the 'Help' button to the right */
        }

        /* Handle sidebar collapse, hide the 'Help' button */
        .sidebar-mini.sidebar-collapse .sidebar-custom .hide-on-collapse {
            display: none;
            /* Hide 'Help' button when sidebar is collapsed */
        }

        /* Optional: adjust button size/spacing */
        .sidebar-custom .btn {
            padding: 5px 10px;
            font-size: 14px;
        }
    </style> --}}
    @yield('css')
    @yield('headScript')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('ui_dashboard.dbSidebar')
        @include('ui_dashboard.dbNavbar')
        <div class="content-wrapper" style="@yield('pageSize')">
            @include('ui_dashboard.dbHeader')
            @yield('content')
        </div>
        {{-- @include('ui_dashboard.dbFooter') --}}
    </div>
    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    @yield('script')
</body>

</html>
