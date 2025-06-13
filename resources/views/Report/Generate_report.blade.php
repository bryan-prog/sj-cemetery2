@extends('layouts.masterlayout')
@section('content')
    <!-- Header -->
    <div class="header pb-6">
        <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
                <h6 class="h2 d-inline-block mb-0">Report</h6>
            </div>
            <div class="col-lg-6 col-5 text-right">
                <a href="" class="btn btn-sm btn-success"><i class="fa fa-download fa-fw"></i>Generate Report</a>
            </div>
            </div>
        </div>
        </div>
    </div>
    <!-- /Header -->

    <!-- Page content -->
     
    <div class="container-fluid mt--6">

        @if(\Session::has('success'))
            {{-- session ung nilagay mo sa return ng controller --}}
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="ni ni-check-bold"></i></span>
                <span class="alert-text"><strong>Success!</strong> {{ \Session::get('success')}}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                              <label for="qtr_select">Level</label>
                              <select class="form-control" id="lvl_select" name="lvl_select">
                                  <option>----</option>
                                  <option value="1">1st</option>
                                  <option value="2">2nd</option>
                                  <option value="3">3rd</option>
                              </select>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <strong id="filter-count" class="text-primary float-right"></strong>
        </div>

    </div>
    
@endsection