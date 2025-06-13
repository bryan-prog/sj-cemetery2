@extends('layouts.masterlayout')
@inject('carbon', 'Carbon\Carbon')
<style>
    .form-control{
        color:black !important;
    }
</style>
@section('content')

<div class="container mt-3">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h3>APPLICATION FOR BURIAL PERMIT</h3>
                </div>
                <div class="col d-flex justify-content-end">
                    <button class="btn btn-success btn-sm">Save Application</button>
                    <button class="btn btn-primary btn-sm">Print Application</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Date Applied</label>
                        <input class="form-control" type="date" id="date_applied">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Applicant's Name</label>
                        <input class="form-control" type="text" placeholder="contact person" id="contact_person">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Address</label>
                        <input class="form-control" type="text" placeholder="address" id="contact_person_address">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Contact Number</label>
                        <input class="form-control" type="text" placeholder="contact number" id="contact_number">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Relationship to the Deceased</label>
                        <input class="form-control" type="text" placeholder="relationship" id="relationship">
                    </div>
                </div>
            </div>
            <hr class="mt-2 mb-4">
            <div class="row">
                <div class="col">
                    <h4 class="text-red">DECEASED INFORMATION</h4>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Name of the Deceased</label>
                        <input class="form-control" type="text" placeholder="name of deceased" id="name_of_deceased">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Address of the Deceased before Death</label>
                        <input class="form-control" type="text" placeholder="name of deceased" id="address_of_deceased">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Date of Birth</label>
                        <input class="form-control" type="date" id="deceased_birthdate">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Date of Death</label>
                        <input class="form-control" type="date" id="deceased_Deathdate">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Burial Site/Location</label>
                        <select class="form-control" id="Location">
                            <option>---</option>
                            <option>Restos (Above Apartment V)</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Row</label>
                        <select class="form-control" id="level_row">
                            <option value="">---</option>
                            <option>R1</option>
                            <option>R2</option>
                            <option>R3</option>
                            <option>R4</option>
                            <option>R5</option>
                            <option>R6</option>
                            <option>R7</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Column Number</label>
                        <select class="form-control" id="column_number">
                            <option value="">---</option>
                            <option>R1_1</option>
                            <option>R1_2</option>
                            <option>R1_3</option>
                            <option>R1_4</option>
                            <option>R1_5</option>
                            <option>R1_6</option>
                            <option>R1_7</option>
                        </select>
                    </div>
                </div>
            </div>
            <hr class="mt-2 mb-4">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Amount to be Paid</label>
                        <input class="form-control" type="text" placeholder="" id="amount">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Funeral Service</label>
                        <input class="form-control" type="text" placeholder="name of deceased" id="funeral_service">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Internment Schedule</label>
                        <input class="form-control" type="datetime-local" id="internment_sched">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="exampleFormControlSelect1" class="form-control-label">Assigned Grave Digger</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                    <label for="exampleFormControlSelect1" class="form-control-label">Select Verifier</label>
                    <select class="form-control" id="exampleFormControlSelect1">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="example-text-input" class="form-control-label">Other Pertinent Information, if any</label>
                    <textarea class="form-control" id="other_info" rows="2" style="resize: none;"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection