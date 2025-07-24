<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','GameTicketHub')</title>
 @include('web.includes.css-plugins')
 @stack('custom-css')
 @livewireStyles
</head>
<body>

  <!-- Header -->
@include('web.includes.header')

 @yield('contents')
  <!-- Features Section -->
 @include('web.includes.feature-section')
  <!-- Footer -->
 @include('web.includes.footer')
   @stack('custom-js')
   @livewireScripts
</body>
</html>
