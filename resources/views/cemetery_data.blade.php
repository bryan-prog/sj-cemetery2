@extends('layouts.masterlayout')
@inject('carbon', 'Carbon\Carbon')
<style>
    .container.mt-3 {
        max-width: 1830px !important;
    }
    .form-control{
        color:black !important;
    }
    body{
        background-image: url(assets/img/bg_cemetery.png);
    }
</style>
@section('content')
<div class="container mt-3">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0"><img src="https://img.icons8.com/doodle/28/cloud-folder.png"/> San Juan City Cemetery Database</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <p style="color:black; margin-bottom: 1.5px;"><b><img src="https://img.icons8.com/doodle/20/building--v1.png"/> Property Type <span style="position:relative; left:15px;">:</span></b> <span style="position:relative; left:27px;">Public</span></p>
                    <p style="color:black; margin-bottom: 1.5px;"><b><img src="https://img.icons8.com/doodle/20/apartment.png"/> Building Type <span style="position:relative; left:18px;">:</span></b> <span style="position:relative; left:30px;">Restos</span></p>
                    <p style="color:black; margin-bottom: 1.5px;"><b><img src="https://img.icons8.com/doodle/20/marker--v1.png"/> Location <span style="position:relative; left:57px;">:</span></b> <span style="position:relative; left:69px;">Left Side</span></p>
                    <p style="color:black; margin-bottom: 1.5px;"><b><img src="https://img.icons8.com/color/21/headstone--v1.png"/> Burial Type <span style="position:relative; left:37px;">:</span></b> <span style="position:relative; left:49px;">Bones/Urn</span></p>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-control-label" for="level"><img src="https://img.icons8.com/office/20/stairs-up.png"/> Select Level</label>
                        <select class="form-control" id="level">
                            <option value="---">---</option>
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3</option>
                            <option value="4">Level 4</option>
                            <option value="5">Level 5</option>
                            <option value="6">Level 6</option>
                            <option value="7">Level 7</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-control-label" for="row"><img src="https://img.icons8.com/color/20/add-row.png"/> Select Row</label>
                        <select class="form-control" id="row">
                            <option value="---">---</option>
                            <option value="1">Row 1</option>
                            <option value="2">Row 2</option>
                            <option value="3">Row 3</option>
                            <option value="4">Row 4</option>
                            <option value="5">Row 5</option>
                            <option value="6">Row 6</option>
                            <option value="7">Row 7</option>
                            <option value="8">Row 8</option>
                        </select>
                    </div>
                    <button class="btn btn-sm btn-danger">Apply Filter</button>
                </div>
            </div>
            <div class="row mt-4">
                <div class="table-responsive">
                    <table class="table table-flush" id="datatable-basic">
                        <thead class="bg-default">
                            <tr>
                                <th class="text-white">Column Number</th>
                                <th class="text-white">Name of Deceased</th>
                                <th class="text-white">Sex</th>
                                <th class="text-white">Date of Birth</th>
                                <th class="text-white">Date of Death</th>
                                <th class="text-white">Year of Renewal</th>
                                <th class="text-white">Contact Person</th>
                            </tr>
                        </thead>
                        <tbody>
                
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection