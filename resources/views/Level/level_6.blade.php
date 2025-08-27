@extends('layouts.masterlayout')
@inject('carbon', 'Carbon\Carbon')
<style>
    .parent {
        display: grid;
        grid-template-columns: repeat(36, 1fr);
        grid-template-rows: repeat(8, 1fr);
        grid-column-gap: 0px;
        grid-row-gap: 10px;
    }
    .container{
        max-width: 1890px !important;
    }
    button.taken {
        border: 2px solid #e90000;
        background: #ffc1c1;
        width: 50px;
        font-size: 11px;
        height: 100px;
    }
    button.available {
        border: 2px solid #057f05;
        background: #76ff76;
        width: 50px;
        font-size: 11px;
        height: 100px;
    }
    button.no_lapida {
        border: 2px solid rgb(0, 0, 0);
        background:rgb(143, 143, 143);
        width: 50px;
        font-size: 11px;
        height: 100px;
    }
    .modal-title, .form-control{
        color:black !important;
    }
    .a#tabs-icons-text-2-tab, .a#tabs-icons-text-3-tab{
        color: black !important;
    }
    .nav-link {
        color: black !important;
    }
    .nav-link.active {
        color: white !important;
    }
</style>
@section('content')
<div class="container mt-3 mb-3">
    <div class="row">
        <div class="col-10 d-flex justify-content-center">
            <h3 style="margin-left:20%;">LEVEL 6 APARTMENT</h3>
        </div>
        <div class="col">
        <span class="badge badge-secondary">LEGEND: </span>
        <span class="badge badge-success">Available</span>
        <span class="badge badge-danger">Taken</span>
        <span class="badge badge-dark">No Lapida</span>
        </div>

    </div>
    <div class="parent">
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_1</div></button>
    </div>
</div>


<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">[Column Number] Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="nav-wrapper">
                    <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-toggle="tab" href="#tabs-icons-text-1" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true"><i class="ni ni-cloud-upload-96 mr-2"></i>1st Slot</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-toggle="tab" href="#tabs-icons-text-2" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>2nd Slot</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-3-tab" data-toggle="tab" href="#tabs-icons-text-3" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false"><i class="ni ni-calendar-grid-58 mr-2"></i>3rd Slot</a>
                        </li>
                    </ul>
                </div>
                <div class="card shadow">
                    <div class="card-body">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Location</label>
                                            <input class="form-control" type="text" placeholder="location" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Name of Deceased</label>
                                            <input class="form-control" type="text" placeholder="name of deceased" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Sex</label>
                                            <input class="form-control" type="text" placeholder="sex" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Birth</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Death</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Contact Person</label>
                                            <input class="form-control" type="text" placeholder="contact person" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Year of Renewal</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Location</label>
                                            <input class="form-control" type="text" placeholder="location" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Name of Deceased</label>
                                            <input class="form-control" type="text" placeholder="name of deceased" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Sex</label>
                                            <input class="form-control" type="text" placeholder="sex" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Birth</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Death</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Contact Person</label>
                                            <input class="form-control" type="text" placeholder="contact person" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Year of Renewal</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tabs-icons-text-3" role="tabpanel" aria-labelledby="tabs-icons-text-3-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Location</label>
                                            <input class="form-control" type="text" placeholder="location" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Name of Deceased</label>
                                            <input class="form-control" type="text" placeholder="name of deceased" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Sex</label>
                                            <input class="form-control" type="text" placeholder="sex" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Birth</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Death</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Contact Person</label>
                                            <input class="form-control" type="text" placeholder="contact person" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Year of Renewal</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
