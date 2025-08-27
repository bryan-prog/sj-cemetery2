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
                    <h6 class="h2 mb-0"><i class="fa fa-user-plus mr-2" aria-hidden="true"></i> Update Profile</h6>
                </div>
                <div class="card-body" style="padding:2rem;">
                 <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="fname" class="col-md-3 col-form-label text-md-end"><i class="fa fa-user mr-2" aria-hidden="true"></i>First Name</label>
                            <div class="col">
                                <input id="fname" type="text" class="form-control" name="fname" value="{{ old('fname', auth()->user()->fname) }}" style="color:black" placeholder="Your first name" required autocomplete="fname" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="mname" class="col-md-3 col-form-label text-md-end"><i class="fa fa-user mr-2" aria-hidden="true"></i>Middle Name</label>
                            <div class="col">
                                <input id="mname" type="text" class="form-control" name="mname" value="{{ old('mname', auth()->user()->mname) }}" style="color:black" placeholder="Your middle name" autocomplete="mname" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="lname" class="col-md-3 col-form-label text-md-end"><i class="fa fa-user mr-2" aria-hidden="true"></i>Last Name</label>
                            <div class="col">
                                <input id="lname" type="text" class="form-control" name="lname" value="{{ old('lname', auth()->user()->lname) }}" style="color:black" placeholder="Your last name" required autocomplete="lname" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="suffix" class="col-md-3 col-form-label text-md-end"><i class="fa fa-user mr-2" aria-hidden="true"></i>Suffix</label>

                            <div class="col">
                                <input id="suffix" type="text" class="form-control" name="suffix" value="{{ old('suffix', auth()->user()->suffix) }}" style="color:black"placeholder="Your suffix" autocomplete="suffix">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="designation" class="col-md-3 col-form-label text-md-end"><i class="fa fa-id-card mr-2" aria-hidden="true"></i>Designation</label>
                            <div class="col">
                                <input id="designation" type="text" class="form-control" name="designation" style="color:black" value="{{ old('designation', auth()->user()->designation) }}" placeholder="Office/Center Designation" required autocomplete="designation" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="username" class="col-md-3 col-form-label text-md-end"><i class="fa fa-id-card mr-2" aria-hidden="true"></i>Username</label>
                            <div class="col">
                                <input id="username" type="text" class="form-control" name="username" style="color:black" value="{{ old('username', auth()->user()->username) }}" placeholder="Your desired user name" required autocomplete="username" autofocus>
                            </div>
                        </div>

              

                        <div class="row mb-0">
                            <div class="col">
                                <button type="submit" class="btn btn-default" style="float:right;">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection