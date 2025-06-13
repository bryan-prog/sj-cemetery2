@extends('layouts.masterlayout')
<style>
    label{
        font-size: 15px !important;
        color:black !important;
        font-weight:500 !important;
    }
    .form-control{
        color:black !important;
        border: 1px solid rgb(109, 109, 109) !important;
        font-weight:700 !important;
        text-transform:uppercase !important;
    }
</style>
@section('content')
<div class="container mt-5">
    <!-- Table -->
    <div class="row">
        <div class="col">
            <div class="card">
                <!-- Card header -->
                <div class="card-header mb-0">
                    <h6 class="h2 mb-0"><i class="fa fa-user-plus mr-2" aria-hidden="true"></i> Register New User</h6>
                </div>
                <div class="card-body" style="padding:2rem;">
                    <form method="" action="">
                        <div class="row mb-3">
                            <label for="fname" class="col-md-3 col-form-label text-md-end"><i class="fa fa-user mr-2" aria-hidden="true"></i>First Name</label>
                            <div class="col">
                                <input id="fname" type="text" class="form-control" name="fname" value="" style="color:black" placeholder="Your first name" required autocomplete="fname" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="mname" class="col-md-3 col-form-label text-md-end"><i class="fa fa-user mr-2" aria-hidden="true"></i>Middle Name</label>
                            <div class="col">
                                <input id="mname" type="text" class="form-control" name="mname" value="" style="color:black" placeholder="Your middle name" autocomplete="mname" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="lname" class="col-md-3 col-form-label text-md-end"><i class="fa fa-user mr-2" aria-hidden="true"></i>Last Name</label>
                            <div class="col">
                                <input id="lname" type="text" class="form-control" name="lname" value="" style="color:black" placeholder="Your last name" required autocomplete="lname" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="suffix" class="col-md-3 col-form-label text-md-end"><i class="fa fa-user mr-2" aria-hidden="true"></i>Suffix</label>

                            <div class="col">
                                <input id="suffix" type="text" class="form-control" name="suffix" value="" style="color:black"placeholder="Your suffix" autocomplete="suffix">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="designation" class="col-md-3 col-form-label text-md-end"><i class="fa fa-id-card mr-2" aria-hidden="true"></i>Designation</label>
                            <div class="col">
                                <input id="designation" type="text" class="form-control" name="designation" style="color:black" value="" placeholder="Office/Center Designation" required autocomplete="designation" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="username" class="col-md-3 col-form-label text-md-end"><i class="fa fa-id-card mr-2" aria-hidden="true"></i>Username</label>
                            <div class="col">
                                <input id="username" type="text" class="form-control" name="username" style="color:black" value="" placeholder="Your desired user name" required autocomplete="username" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="permission" class="col-md-3 col-form-label text-md-end"><i class="fa fa-info-circle mr-2" aria-hidden="true"></i>User Level</label>
                            <div class="col">                        
                                <select class="form-control " id="permission" name="permission" style="color:black" value="" autocomplete="permission" required autofocus>
                                    <option>----</option>
                                    <option value="Super Admin">Super Admin</option>
                                    <option value="Admin">Admin</option>
                                    <option value="User">User</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-3 col-form-label text-md-end"><i class="fa fa-unlock-alt mr-2" aria-hidden="true"></i>Password</label>
                            <div class="col">
                                <input id="password" type="password" class="form-control" name="password" style="color:black" placeholder="password" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-3 col-form-label text-md-end"><i class="fa fa-unlock-alt mr-2" aria-hidden="true"></i>Confirm Password</label>
                            <div class="col">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" style="color:black" placeholder="re-enter password" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col">
                                <button type="submit" class="btn btn-default" style="float:right;">Register</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection