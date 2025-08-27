<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
            <meta name="author" content="Creative Tim">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <title>SJ CEMETERY SYSTEM</title>
            <!-- Favicon -->
            <link rel="icon" href="{{asset('assets/img/sjc.png')}}" type="image/png">
            <!-- Fonts -->
            <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700"> -->
            <!-- Icons -->
            <link rel="stylesheet" href="{{asset('assets/vendor/nucleo/css/nucleo.css')}}" type="text/css">
            <link rel="stylesheet" href="{{asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css')}}" type="text/css">
            <!--Datatables-->
            <!-- Page plugins -->
            <link rel="stylesheet" href="{{asset('assets/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}">
            <link rel="stylesheet" href="{{asset('assets/vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}">
            <link rel="stylesheet" href="{{asset('assets/vendor/datatables.net-select-bs4/css/select.bootstrap4.min.css')}}">
            <!-- Argon CSS -->
            <link rel="stylesheet" href="{{asset('assets/css/argon.css?v=1.1.0')}}" type="text/css">

            <!-- <script src="{{asset('assets/js/jquery-3.3.1.min.js')}}"></script> -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700;900&display=swap');
                body{
                    font-family: 'Poppins', sans-serif !important;
                    font-weight: 400;
                }
                .modal-header{border-bottom: 1PX solid #e9ecef !important;}
                 .form-control[readonly] {
                    opacity: 1;
                    color: #000000 !important;
                    background-color: #e5e5e57a !important;
                }
                @media only screen and (max-width: 600px) {
                    .navbar-collapse.collapsing, .navbar-collapse.show {
                        padding: 1.5rem;
                        border-radius: 0.25rem;
                        background: #FFF;
                        -webkit-box-shadow: 0 50px 100px rgba(50, 50, 93, 0.1), 0 15px 35px rgba(50, 50, 93, 0.15), 0 5px 15px rgba(0, 0, 0, 0.1);
                        box-shadow: 0 50px 100px rgba(50, 50, 93, 0.1), 0 15px 35px rgba(50, 50, 93, 0.15), 0 5px 15px rgba(0, 0, 0, 0.1);
                        -webkit-animation: show-navbar-collapse .2s ease forwards;
                        animation: show-navbar-collapse .2s ease forwards;
                    }
                    /* .nav-link {
                        color: black !important;
                    } */
                    img.sjc {
                        width: 50px;
                    }
                    .navbar-collapse .navbar-collapse-header {
                        display: block;
                        padding-bottom: 1rem;
                        margin-bottom: 1rem;
                        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                    }
                    li.nav-item.dropdown {
                        margin-bottom: .5rem;
                    }
                }
        </style>
        </head>

    <body>
        @stack('scripts')
    <div class="main-content" id="panel">
        <!-- Header -->
        <nav class="navbar navbar-expand-lg bg-dark fix-top shadow" id="fixed-top" style="border-color: #A2A6B0 !important;">
            <div class="container col-12">
                <a class="navbar-brand" href="{{URL('/Homepage')}}">
                    <img class="sjc" src="{{asset('assets/img/sjc.png')}}" width="50px">
                    <img src="{{asset('assets/img/mayor.png')}}" width="50px;">
                </a>
                <button class="navbar-toggler navbar-dark bgcolor fix-top  shadow" type="button" data-toggle="collapse" data-target="#navbar-default" aria-controls="navbar-default" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar-default">
                    <div class="navbar-collapse-header">
                        <div class="row">
                            <div class="col-6 collapse-brand">
                            <a href="">
                                <img src="{{asset('assets/img/sjc.png')}}" width="35%">
                                <!-- <img src="{{asset('../assets/img/mayor.png')}}" width="8%"> -->
                            </a>
                            </div>
                            <div class="col-6 collapse-close">
                            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar-default" aria-controls="navbar-default" aria-expanded="false" aria-label="Toggle navigation">
                                <span></span>
                                <span></span>
                            </button>
                            </div>
                        </div>
                    </div>
                    <ul class="navbar-nav ml-lg-auto">
                        <li>
                            <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="media align-items-center">
                                    <div class="media-body ml-2 d-none d-lg-block">
                                        <span class="mb-0 text-sm  font-weight-bold text-white">  {{ Auth::user()->fname }} {{ Auth::user()->lname }}</span>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome!</h6>
                                </div>
                                <a href="{{URL('/my_profile')}}" class="dropdown-item">
                                    <i class="ni ni-single-02"></i>
                                    <span>My profile</span>
                                </a>
                                <a href="{{URL('/list_of_users')}}" class="dropdown-item">
                                    <i class="ni ni-settings-gear-65"></i>
                                    <span>List of Users</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ni ni-user-run"></i>
                                    <span>Logout</span>
                                </a>
                                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <section class="content">
            @yield('content')
        </section>
    </div>



    <!-- Core -->
    <script src="{{asset('/assets/vendor/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/vendor/js-cookie/js.cookie.js')}}"></script>
    <script src="{{asset('assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js')}}"></script>
    <script src="{{asset('assets/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js')}}"></script>
    <!--Datatable JS-->
    <script src="{{asset('assets/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-buttons/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-select/js/dataTables.select.min.js')}}"></script>

    <!-- Argon JS -->
    <script src="{{asset('assets/js/argon.js?v=1.1.0')}}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            window.setTimeout(function () {
                $(".alert").fadeTo(500, 0).slideUp(500, function () {
                    $(this).remove();
                });
            }, 3000);

            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                event.preventDefault();
                return false;
                }
            });
        });
    </script>



    </body>
</html>
