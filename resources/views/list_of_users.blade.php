@extends('layouts.masterlayout')

<style>
  .container.reg { max-width: 1500px !important; }
  .form-control-label{ font-size: 15px !important; color:black !important; font-weight:500 !important; }
  .form-control{ color:black !important; border: 1px solid rgb(61, 61, 61) !important; }
  table{ border:1px solid #e9ecef !important; }
  .badge-lock { font-size:.8rem; }
</style>

@section('content')
<div class="container reg mt-5">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col">
          <h3 class="my-0"><i class="fa fa-users mr-2"></i> Users List</h3>
        </div>
        <div class="col d-flex justify-content-end">
          <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#dataModal">Edit</button>
          <a type="button" class="btn btn-default btn-sm ml-2" href="{{ URL('/register') }}">Add New User</a>
        </div>
      </div>
    </div>

    <div class="card-body pt-3 px-5 ">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="row">
        <div class="table-responsive py-4">
          <table class="table table-flush" id="user_table">
            <thead class="thead">
              <tr>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Designation</th>
                <th>Permission</th>
                <th>Active</th>
                @if(auth()->user()->can('manage-user-access') || auth()->user()->permission === 'Super Admin')
                  <th style="min-width:160px;">Actions</th>
                @endif
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- EDIT USER MODAL (unchanged) --}}
<div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">User Information</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="Test_edit_user">
          @csrf
          <input type="hidden" name="info_id" id="info_id" readonly>
          <div class="row">
            <div class="col">
              <div class="form-group">
                <label class="form-control-label" for="last_name"><i class="fa fa-user mr-2"></i>Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name">
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label class="form-control-label" for="first_name"><i class="fa fa-user mr-2"></i>First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name">
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label class="form-control-label" for="middle_name"><i class="fa fa-user mr-2"></i>Middle Name</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name">
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label class="form-control-label" for="suffix"><i class="fa fa-user mr-2"></i>Suffix</label>
                <input type="text" class="form-control" id="suffix" name="suffix">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label class="form-control-label" for="designation"><i class="fa fa-user mr-2"></i>Designation</label>
                <input type="text" class="form-control" id="designation" name="designation">
              </div>
            </div>

            @if(Auth::user()->permission == 'Super Admin')
            <div class="col">
              <div class="form-group">
                <label class="form-control-label" for="permission"><i class="fa fa-info mr-2"></i>Permission</label>
                <select class="form-control" id="permission" name="permission" style="color:black" required>
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
                <label class="form-control-label" for="permission"><i class="fa fa-info mr-2"></i>Permission</label>
                <select class="form-control" id="permission" name="permission" style="color:black" required>
                  <option>----</option>
                  <option value="Admin">Admin</option>
                  <option value="User">User</option>
                </select>
              </div>
            </div>
            @endif

            <div class="col">
              <div class="form-group">
                <label class="form-control-label" for="username"><i class="fa fa-info mr-2"></i>Username</label>
                <input type="text" class="form-control" id="username" name="username" required readonly>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label class="form-control-label" for="status"><i class="fa fa-info mr-2"></i>Status</label>
                <select class="form-control" id="status" name="status" style="color:black" required>
                  <option>----</option>
                  <option value="1">Active</option>
                  <option value="2">Inactive</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row"><div class="col d-flex justify-content-center"><div class="form-group mb-0">
            <button type="submit" class="btn btn-primary" id="btnSaveStockEdit">Edit User Info</button>
          </div></div></div>
        </form>

        <hr>

        <form method="POST" action="{{ route('home.change_password') }}">
          @csrf
          <input type="hidden" name="info_id" id="info_id_pass" readonly>
          <div class="row">
            <div class="col">
              <div class="form-group">
                <label class="form-control-label" for="change_password"><i class="fa fa-unlock-alt mr-2"></i>Change Password</label>
                <input type="password" class="form-control" id="change_password" name="change_password" required>
              </div>
            </div>
          </div>
          <div class="row"><div class="col"><div class="form-group mb-0">
            <button type="submit" class="btn btn-default" id="changePassBtn">Change Password</button>
          </div></div></div>
        </form>
      </div>
    </div>
  </div>
</div>


@if(auth()->user()->can('manage-user-access') || auth()->user()->permission === 'Super Admin')
<div class="modal fade" id="accessModal" tabindex="-1" role="dialog" aria-labelledby="accessLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <form id="accessForm" method="POST">
      @csrf
      @method('PATCH')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Manage Access — <span id="accessUserName">...</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>

        <div class="modal-body">
          <div id="accessLockedNote" class="alert alert-info d-none">
            <strong>Note:</strong> Super Admin permissions are locked and always enabled.
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <input type="hidden" name="can_approve_deny" value="0">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="access_can_approve_deny" name="can_approve_deny" value="1">
                <label class="custom-control-label" for="access_can_approve_deny">Approval &amp; Denial</label>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <input type="hidden" name="can_view_actionlogs" value="0">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="access_can_view_actionlogs" name="can_view_actionlogs" value="1">
                <label class="custom-control-label" for="access_can_view_actionlogs">View ActionLogs</label>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <input type="hidden" name="can_edit_permits" value="0">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="access_can_edit_permits" name="can_edit_permits" value="1">
                <label class="custom-control-label" for="access_can_edit_permits">Edit Permits</label>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <input type="hidden" name="can_print_permits" value="0">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="access_can_print_permits" name="can_print_permits" value="1">
                <label class="custom-control-label" for="access_can_print_permits">Print Permits</label>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script>
  window.setTimeout(function(){
    $(".alert").fadeTo(500,0).slideUp(500,function(){ $(this).remove(); });
  },3000);

  $(function(){
    const canManageAccess = @json(auth()->user()->can('manage-user-access') || auth()->user()->permission === 'Super Admin');

    const columns = [
      {data:'lname',       name:'lname'},
      {data:'fname',       name:'fname'},
      {data:'mname',       name:'mname'},
      {data:'designation', name:'designation'},
      {data:'permission',  name:'permission'},
      {data:'active',      name:'active'},
    ];

    if (canManageAccess) {
      columns.push({
        data:null, orderable:false, searchable:false,
        render:function(data,type,row){
          const name = `${row.lname}, ${row.fname}`;
          return `
            <button type="button"
              class="btn btn-primary btn-sm access-btn"
              data-id="${row.id}" data-name="${name}">
              Manage Access
            </button>
          `;
        }
      });
    }

    const table = $('#user_table').DataTable({
      ajax: '{{ url('test_list_of_users') }}',
      processing: true,
      serverSide: true,
      select: true,
      lengthChange: false,
      pageLength: 10,
      order: [[0,'asc']],
      language: { paginate: { next:'>', previous:'<', first:'<<', last:'>>' } },
      columns: columns,
      pagingType: "full_numbers",
    });


    $('#user_table tbody').on('click', 'tr', function(){
      const data = table.row(this).data();
      if (!data) return;
      fillEditFields(data);
      $(this).addClass('selected').siblings().removeClass('selected');
    });

    function fillEditFields(data){
      $('#info_id').val(data['id']);
      $('#info_id_pass').val(data['id']);
      $('#last_name').val(data['lname']);
      $('#first_name').val(data['fname']);
      $('#middle_name').val(data['mname']);
      $('#permission').val(data['permission']);
      $('#status').val(data['active']);
      $('#username').val(data['username']);
      $('#designation').val(data['designation']);
    }


    $('#user_table tbody').on('click','.access-btn', function(e){
      e.stopPropagation();
      const userId = $(this).data('id');
      const userName = $(this).data('name');

      $('#accessUserName').text(userName);
      $('#accessLockedNote').addClass('d-none');
      $('#accessForm')[0].reset();

      const updateAction = @json(route('user-access.update', ['user' => '__ID__'])).replace('__ID__', userId);
      $('#accessForm').attr('action', updateAction);

      const showUrl = @json(route('user-access.show', ['user' => '__ID__'])).replace('__ID__', userId);
      $.get(showUrl, function(res){
        $('#access_can_approve_deny').prop('checked', !!res.can_approve_deny);
        $('#access_can_view_actionlogs').prop('checked', !!res.can_view_actionlogs);
        $('#access_can_edit_permits').prop('checked', !!res.can_edit_permits);
        $('#access_can_print_permits').prop('checked', !!res.can_print_permits);

        const isSuper = (res.permission || '').toLowerCase() === 'super admin';
        if (isSuper) {
          $('#accessLockedNote').removeClass('d-none');
          $('#accessForm input[type=checkbox]').prop('checked', true).prop('disabled', true);
        } else {
          $('#accessLockedNote').addClass('d-none');
          $('#accessForm input[type=checkbox]').prop('disabled', false);
        }

        $('#accessModal').modal('show');
      });
    });
  });
</script>
@endpush
