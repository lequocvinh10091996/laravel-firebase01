<!DOCTYPE html>
<html lang="en" ng-app="laravel-app">
<head>
<title>Laravel - @yield('title')</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="{{ asset('matriz-admin/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('matriz-admin/css/bootstrap-responsive.min.css') }}" />
<link rel="stylesheet" href="{{ asset('matriz-admin/css/uniform.css') }}" />
<link rel="stylesheet" href="{{ asset('matriz-admin/css/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('matriz-admin/css/fullcalendar.css') }}" />
<link rel="stylesheet" href="{{ asset('matriz-admin/css/matrix-style.css') }}" />
<link rel="stylesheet" href="{{ asset('matriz-admin/css/matrix-media.css') }}" />
<link rel="stylesheet" href="{{ asset('matriz-admin/font-awesome/css/font-awesome.css') }}"/>
<link rel="stylesheet" href="{{ asset('matriz-admin/css/jquery.gritter.css') }}" />
<link rel="stylesheet" href="{{ asset('matriz-admin/font-awesome/css/css-family.css') }}" />
<link rel="stylesheet" href="{{ asset('matriz-admin/alertify/css/alertify.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/myapp.css') }}" />

<script src="{{ asset('matriz-admin/js/jquery.min.js') }}"></script> 
<script src="{{ asset('matriz-admin/js/jquery.ui.custom.js') }}"></script> 
<script src="{{ asset('matriz-admin/js/bootstrap.min.js') }}"></script> 
<script src="{{ asset('matriz-admin/js/jquery.uniform.js') }}"></script> 
<script src="{{ asset('matriz-admin/js/select2.min.js') }}"></script> 
<script src="{{ asset('matriz-admin/js/jquery.validate.js') }}"></script> 
<script src="{{ asset('matriz-admin/js/jquery.dataTables.min.js') }}"></script> 
<script src="{{ asset('matriz-admin/js/matrix.js') }}"></script> 
<script src="{{ asset('matriz-admin/js/matrix.form_validation.js') }}"></script>
<script src="{{ asset('matriz-admin/alertify/alertify.min.js') }}"></script>

<script src="{{ asset('js/angular.min.js') }}"></script>
<script src="{{ asset('js/dirPaginate.js') }}"></script>

<script>

  var appName = angular.module('laravel-app', ['angularUtils.directives.dirPagination']);

  appName.config(
    function($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
  });
  
  var pathArray = window.location.pathname.split('/');
  var partLocalhost = window.location.host;
  if(partLocalhost == 'localhost'){
      var newPathname = "";
      for(i = 1; i < pathArray.length; i++){
          newPathname += "/";
          newPathname += pathArray[i];
          if(pathArray[i] == 'public'){
              partLocalhost += newPathname;
          }
      }
  }
  
  appName.constant('MainUrl', window.location.protocol + "//" + partLocalhost)

</script>
@if(!session('acc_username'))
<script>window.location.href = window.location.protocol + "//" + partLocalhost;</script>
@endif
</head>
<body ng-controller="{{ $controllername }}">

<!--Header-part-->
<div id="header">
  <h2>IT Dic</h2>
</div>
<!--close-Header-part--> 


<!--top-Header-menu-->
<div id="user-nav" class="navbar navbar-inverse">
  <ul class="nav">
      <li class=""><a href="#" onclick="togglePage()"><i class="icon icon-list"></i></a></li>
      <li class=""><a title="" href="#"><i class="icon icon-user"></i> <span class="text">Welcome: <b style="color: #49CCED;">{{session('acc_username')}}</b></span></a></li>
      <li class=""><a title="" href="#" onclick="window.location.href = window.location.protocol + '//' + partLocalhost +'/logout';"><i class="icon icon-share-alt"></i> <span class="text">Logout</span></a></li>
  </ul>
</div>
<!--close-top-Header-menu-->
<!--sidebar-menu-->
<div id="sidebar">
    <ul>
        <li class="@if($controllername == 'AccountController' ){{'active'}}@endif">
            <a href="{{ url('/account/index') }}"><i class="icon icon-home"></i> <span>Account</span></a> 
        </li>
        <li class="@if($controllername == 'TerminologyController' ){{'active'}}@endif">
            <a href="{{ url('/terminology/index') }}"><i class="icon icon-th"></i> <span>Terminology</span></a> 
        </li>
        <li class="@if($controllername == 'SectionController' ){{'active'}}@endif">
            <a href="{{ url('/section/index') }}"><i class="icon icon-inbox"></i> <span>Section</span></a> 
        </li>
        <li class="@if($controllername == 'TopicController' ){{'active'}}@endif">
            <a href="{{ url('/topic/index') }}"><i class="icon icon-book"></i> <span>Topic</span></a>
        </li>
    </ul>
</div>
<!--sidebar-menu-->

<!--main-container-part-->
<div id="content">
    <!--breadcrumbs-->
    <div id="content-header">
     <div id="breadcrumb"> <a href="#" title="@yield('title')" class="tip-bottom"><i class="icon-home"></i> @yield('title')</a> <a href="#" class="current">@yield('title-current')</a> </div>
    </div>
    <!--End-breadcrumbs-->
 <div class="container-fluid">

     <div class="widget-box">
         @yield('content')
     </div>
 </div>

@yield('modal-content')
</div>

<!--end-main-container-part-->

<!--Footer-part-->
<div class="row-fluid">
  <div id="footer" class="span12"> 2019 &copy; Laravel </div>
</div>
<!--end-Footer-part-->

</body>
</html>
<script>
    $(document).ready(function () {
        $("#content").css("margin-left", "40px");
        $("#sidebar ul").css("border-bottom", "0px");
        $("#sidebar ul").css("width", "40px");
        $("#sidebar ul li").css("border-top", "0px");
        $("#sidebar ul li").css("border-bottom", "0px");
        $('#sidebar ul li span').addClass('hidden');
    });
    function togglePage() {
        if($("#content").css("margin-left") == "220px"){
            $("#content").css("margin-left", "40px");
            $("#sidebar ul").css("border-bottom", "0px");
            $("#sidebar ul").css("width", "40px");
            $("#sidebar ul li").css("border-top", "0px");
            $("#sidebar ul li").css("border-bottom", "0px");
            $('#sidebar ul li span').addClass('hidden');
        } else {
            $("#content").css("margin-left", "220px");
            $("#sidebar ul").css("border-bottom", "1px solid #37414b");
            $("#sidebar ul").css("width", "220px");
            $("#sidebar ul li").css("border-top", "1px solid #37414b");
            $("#sidebar ul li").css("border-bottom", "1px solid #37414b");
            $('#sidebar ul li span').removeClass('hidden');
        }
    }
</script>
