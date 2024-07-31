<!doctype html>
<html lang="en" class="color-sidebar sidebarcolor1">

<head>
    @include('includes.meta')
    @stack('before-style')
    @include('includes.style')
    @stack('after-style')
    <title>Dashbaord</title>
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        @include('includes.sidebar')
        <!--end sidebar wrapper -->
        <!--start header -->
        @include('includes.header')
        <!--end header -->
        <!--start page wrapper -->
        @yield('content')
        <!--end page wrapper -->
        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        @include('includes.footer')

    </div>
    <!--end wrapper-->
    @stack('before-js')
    @include('includes.js')
    @stack('after-js')

</body>

</html>
