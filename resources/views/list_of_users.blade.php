@extends('layouts.masterlayout')
<style>
    .container.reg {
        max-width: 1500px !important;
    }
    .form-control-label{
        font-size: 15px !important;
        color:black !important;
        font-weight:400 !important;
    }
    .form-control{
        color:black !important;
        border: 1px solid rgb(61, 61, 61) !important;
        font-weight:700 !important;
    }
    table{
        border:1px solid #e9ecef !important;
    }
</style>

@section('content')

<div class="container reg mt-5">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h3 class="my-0"><i class="fa fa-users mr-2" aria-hidden="true"></i> Users List</h3>
                </div>
                <div class="col d-flex justify-content-end">
                    <a type="button" class="btn btn-default btn-sm" href="{{URL('/register')}}">Add New User</a>
                </div>
            </div>
        </div>
        <div class="card-body pt-3 px-5 ">
            <div class="row">
                <div class="table-responsive py-4">
                    <table class="table table-flush" id="datatable-basic">
                        <thead class="">
                            <tr>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Designation</th>
                                <th>Permission</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                             @foreach($users as $user)
                                <tr id="{{$user->id}}">
                                    <td>{{$user->lname}} </td>
                                    <td>{{$user->fname}} </td>
                                    <td>{{$user->mname}}</td>
                                    <td>{{$user->designation}}</td>
                                    <td>{{$user->permission}}</td>
                                    <td>{{$user->active}}</td>
                                    <td><button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#dataModal">Edit</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">User Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('change_user_info',)}}" id="edit_item_form">
                    @csrf
                       <input type="hidden" id="info_id" name="info_id" readonly>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="form-control-label" for="last_name"><i class="fa fa-user mr-2" aria-hidden="true"></i>Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"  >
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="form-control-label" for="first_name"><i class="fa fa-user mr-2" aria-hidden="true"></i>First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" >
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="form-control-label" for="middle_name"><i class="fa fa-user mr-2" aria-hidden="true"></i>Middle Name</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name" >
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="form-control-label" for="suffix"><i class="fa fa-user mr-2" aria-hidden="true"></i>Suffix</label>
                                <input type="text" class="form-control" id="suffix" name="suffix" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="form-control-label" for="designation"><i class="fa fa-user mr-2" aria-hidden="true"></i>Designation</label>
                                <input type="text" class="form-control" id="designation" name="designation" >
                            </div>
                        </div>

                       @if(Auth::user()->permission == 'Super Admin')
                        <div class="col">
                            <div class="form-group">
                                <label class="form-control-label" for="permission"><i class="fa fa-info mr-2" aria-hidden="true"></i>Permission</label>
                                <select class="form-control" id="permission" name="permission" style="color:black" required >
                                    <option>----</option>
                                    <option value="Super Admin">Super Admin</option>
                                    <option value="Admin">Admin</option>
                                    <option value="User">User</option>
                                </select>
                            </div>
                        </div>
                        @else
                        <div class="col">
                            <div class="form-group">
                                <label class="form-control-label" for="permission"><i class="fa fa-info mr-2" aria-hidden="true"></i>Permission</label>
                                <select class="form-control" id="permission" name="permission" style="color:black" required >
                                    <option>----</option>
                                    <option value="Admin">Admin</option>
                                    <option value="User">User</option>
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col">
                            <div class="form-group">
                                <label class="form-control-label" for="username"><i class="fa fa-info mr-2" aria-hidden="true"></i>Username</label>
                                <input type="text" class="form-control" id="username" name="username" min="0" required readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="form-control-label" for="username"><i class="fa fa-info mr-2" aria-hidden="true"></i>Status</label>
                                <select class="form-control" id="permission" name="permission" style="color:black" required >
                                    <option>----</option>
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-flex justify-content-center">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary" id="btnSaveStockEdit">Edit User Info</button>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
                <form method="" action="">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="form-control-label" for="change_password"><i class="fa fa-unlock-alt mr-2" aria-hidden="true"></i>Change Password</label>
                                <input type="password" class="form-control" id="change_password" name="change_password" min="0" required >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-default" id="changePassBtn">Change Password</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>

$(document).ready(function(){
 var table = $('#datatable-basic').DataTable();

$('#datatable-basic tbody').on('dblclick', 'tr', function () {
    var thisData = table.row(this).data();
    console.log(thisData);
    if ( $(this).hasClass('selected') )
    {
        $(this).removeClass('selected');
    }
    else
    {
        table.$('tr.selected').removeClass('selected');
        $(this).addClass('selected');
    }

    $('#info_id').val(thisData['DT_RowId']);
    $('#change_pass_id').val(thisData['DT_RowId']);

    $.get("{{url('user_details')}}/"+thisData['DT_RowId'], function(data){
        console.log(data);
        $('#first_name').val(data.fname);
        $('#middle_name').val(data.mname);
        $('#last_name').val(data.lname);
        $('#suffix').val(data.suffix);
        $('#designation').val(data.designation);
        $('#permission').val(data.permission);
        $('#username').val(data.username);
        $('#active').val(data.active);
    });

    $('#dataModal').modal({
        backdrop: 'static',
        keyboard: true
    });
});

});

</script>

@endsection
