<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" href="{{asset('assets/img/sjc.png')}}" type="image/png">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SJ CEMETERY SYSTEM</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- CSS Files -->
    <link href="{{asset('css/login.css')}}" rel="stylesheet" />
    <!-- Argon CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/argon.css?v=1.1.0')}}" type="text/css">


    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700;900&display=swap');

    body{
        background-image: url(assets/img/cemetery_bg.png);
        overflow:hidden;
        font-family: 'Poppins', sans-serif !important;
        font-weight: 400;
    }
    .form-control{
        color:black !important;
    }
    button.npc {
        position: relative;
        left: 1610px;
        top: 60px;
        border: none;
        padding: 10px;
        background: #0d135b;
        border-radius: 72px;
        box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgb(0 0 0 / 25%) 0px 32px 16px;;
    }
    button.npc:focus{
        outline:none;
    }
    .modal-header.npc {
        padding: 10px !important;
    }
    .show {
        opacity: 1;
        display:block;
    }
    h4{
    font-size:1.5rem !important;
    }
    .container {
        max-width: 729px;
    }
</style>

</head>
<body>
    <div class="container mt-8">
        <div class="row justify-content-center">
            <img src="{{asset('assets/img/sjc.png')}}" style="width: 15%;">
            <img src="{{asset('assets/img/mayor.png')}}" style="width: 15%;">
        </div>
        <div class="row justify-content-center">
        <h1 style="font-size:2.5rem; margin-top:10px;color:white; -webkit-text-stroke: 1px black;">SJ CEMETERY SYSTEM</h1>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="section text-center">
                            <h4 class="mb-4 pb-2 pt-2">Log In</h4>
                        </div>
                        <form  action="{{ route('login') }}" method="POST">
                            @csrf
                               @if ($errors->any())
                          <div class="alert alert-danger col-md-12">
                                      {{ $errors->first() }}
                                    </div>
                            @endif

                            <div class="form-group mb-4">
                                <label for="username" class="form-control-label" style="color:black; font-weight:500;">Username</label>
                                <input class="form-control @error('username') is-invalid @enderror" type="text" value="{{ old('username') }}" id="username"   name="username" placeholder="Username"  required autocomplete="username" autofocus>
                              @error('username')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                            </div>


                            <div class="form-group mb-2">
                                <label for="password" class="form-control-label" style="color:black; font-weight:500;">Password</label>
                                <input class="form-control @error('password') is-invalid @enderror" type="password" id="password"  name="password" placeholder="Password"  required autocomplete="current-password">
                              @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div>


                            <div class="row justify-content-center mb-2">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="npc">
        <button class="npc" id="npc" data-toggle="modal" data-target="#npcmodal"><img src="{{asset('assets/img/npc-2.png')}}" class="rounded-circle" style="width: 120px;"></button>
    </div>


    <div class="modal fade" id="npcmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header npc">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="{{asset('assets/img/npc_2025.png')}}" style="width: 100%;">
                </div>
            </div>
        </div>
    </div>

</body>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</html>
