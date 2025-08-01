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
                        <form action="{{ route('print_report') }}" method="GET">
                              <label for="qtr_select">Level</label>
                             <select id="level" name="level" class="form-control">
                                 <option value="1">1</option>
                                 <option value="2">2</option>
                                 <option value="3">3</option>
                                 <option value="4">4</option>
                                 <option value="5">5</option>
                                 <option value="6">6</option>
                                 <option value="7">7</option>
                                 <option value="8">8</option>
                                 <option value="9">9</option>
                             </select>
                               <button type="submit" class="btn btn-sm btn-success mt-2">
                            <i class="fa fa-download fa-fw"></i> Generate Report
                        </button>
                    </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <strong id="filter-count" class="text-primary float-right"></strong>
        </div>

    
    </div>

@endsection