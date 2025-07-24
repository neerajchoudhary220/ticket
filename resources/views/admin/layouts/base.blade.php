<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'MyTest')</title>
@include('admin.includes.css-plugins')
 @stack('custom-css')
 @livewireStyles
</head>
<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!--  App Topstrip -->
   @include('admin.includes.top-strip')
    <!-- Sidebar Start -->
    @include('admin.includes.sidebar')
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
     @include('admin.includes.header')
      <!--  Header End -->
      <div class="body-wrapper-inner">
        @yield('contents')
      </div>
    </div>
  </div>
@include('admin.includes.js-plguins')
 @stack('custom-js')
   @livewireScripts
</body>

</html>