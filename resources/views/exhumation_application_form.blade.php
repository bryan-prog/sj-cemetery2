@extends('layouts.masterlayout')
@inject('carbon', 'Carbon\Carbon')
<style>
    .container.mt-3 {
        max-width: 1225px !important;
    }
    .form-control, .form-control-label{
        color:black !important; 
        text-transform:uppercase;
    }
    h4.text-red.text-uppercase{
        color: #ff0000 !important;
    }
    body{
        background-image: url(assets/img/bg_cemetery.png);
    }
</style>
@section('content')

<div class="container mt-3">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h3 class="mb-0"><img src="https://img.icons8.com/doodle/30/goodnotes.png" alt="goodnotes"/> REQUEST FOR EXHUMATION PERMIT</h3>
                </div>
                <div class="col d-flex justify-content-end">
                    <a href=""><button class="btn btn-success btn-sm mr-2">Save Request</button></a>
                    <a href="{{URL('/Generate_Exhumation_Permit')}}"><button class="btn btn-primary btn-sm">Print Permit</button></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h4 class="text-red text-uppercase mb-3"><u>Application Details</u></h4>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/external-flaticons-lineal-color-flat-icons/20/external-date-business-flaticons-lineal-color-flat-icons.png"/> Date Applied</label>
                        <input class="form-control" type="date" id="date_applied">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Requesting Party Name</label>
                        <input class="form-control" type="text" placeholder="requestor" id="requestor">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/marker--v1.png"/> Address</label>
                        <input class="form-control" type="text" placeholder="address" id="requestor_address">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-phone.png"/> Contact Number</label>
                        <input class="form-control" type="text" placeholder="contact no" id="contact_no">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/stickers/20/family.png"/> Relationship to the Deceased</label>
                        <input class="form-control" type="text" placeholder="relationship to deceased" id="relationship">
                    </div>
                </div>
            </div>
            <hr class="mt-2 mb-4">
            
            <h4 class="text-red text-uppercase my-3"><u>Burial Site Location</u></h4>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> Burial Site/Location</label>
                        <select class="form-control" id="Location">
                            <option>---</option>
                            <option>Restos (Above Apartment V)</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> Level</label>
                        <select class="form-control" id="level">
                            <option value="">---</option>

                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> Row</label>
                        <select class="form-control" id="row">
                            <option value="">---</option>

                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> Column Number</label>
                        <select class="form-control" id="column_number">
                            <option value="">---</option>

                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> Slot</label>
                        <select class="form-control" id="slot_number">
                            <option value="">---</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                        </select>
                    </div>
                </div> 
            </div>
            <hr class="mt-2 mb-4">

            <h4 class="text-red text-uppercase my-3"><u>Deceased Information</u></h4>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/plasticine/20/headstone.png"/> Name of the Deceased</label>
                        <input class="form-control" type="text" placeholder="name of the deceased" id="name_of_deceased">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/country.png"/> Transfer Location</label>
                        <input class="form-control" type="text" placeholder="location" id="transfer_location">
                    </div>
                </div>
            </div>
            <hr class="mt-2 mb-4">
            
            <h4 class="text-red text-uppercase my-3"><u>Payment / Assigned Personnel</u></h4>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/refund.png"/> Fee as per Ordinance No. 69 Series of 2022</label>
                        <input class="form-control" type="text" placeholder="fee" id="fee">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label"><img src="https://img.icons8.com/doodle/20/manager.png"/> Verifier</label>
                        <select name="" id="verifier" class="form-control">
                            <option value="">---</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection