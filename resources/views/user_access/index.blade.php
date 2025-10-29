@extends('layouts.masterlayout')

@section('content')
<style>
  .container.mt-4 { max-width: 1480px !important; }
  .card { border: 0; box-shadow: 0 15px 35px rgba(50,50,93,.1), 0 5px 15px rgba(0,0,0,.07); }
  .card-header h4 { font-weight: 600; letter-spacing:.2px; color:#fff !important; }
  .card-subtitle { color:#cbd3dc; }
  .tableFixHead { max-height: 70vh; overflow-y: auto; }
  .tableFixHead thead th { position: sticky; top: 0; z-index: 2; }
  .table thead th { vertical-align: middle; }
  .table td, .table th { vertical-align: middle !important; height: 60px; }
  .table-hover tbody tr:hover { background:#f8f9fe; }
  .name-col { min-width: 300px; }
  .role-col { width: 150px; text-align:center; }
  .perm-col { width: 170px; text-align:center; }
  .save-col { width: 120px; text-align:center; }
  .d-none { display:none !important; }
  .avatar-dot{
    width: 34px; height: 34px; border-radius: 50%;
    color:#fff; display:inline-flex; align-items:center; justify-content:center;
    margin-right:.6rem; font-weight:700; font-size:.9rem;
    box-shadow: 0 2px 6px rgba(0,0,0,.15);
  }
  .role-pill { display:inline-block; padding:.35rem .6rem; border-radius:999px; font-size:.8rem; font-weight:600; letter-spacing:.2px; }
  .role-super { background:#ffe2e6; color:#b4232a; border:1px solid #ffc2ca; }
  .role-admin { background:#ddf6ff; color:#0b6b8a; border:1px solid #bdeaff; }
  .role-user  { background:#eef1f6; color:#3a4252; border:1px solid #d8dde6; }
  .pretty-check { display:inline-flex; align-items:center; justify-content:center; min-height:32px; }
  .custom-control.custom-checkbox.checkbox-md { padding-left: 2rem; display:inline-flex; align-items:center; justify-content:center; }
  .custom-checkbox.checkbox-md .custom-control-label::before{
    width: 22px; height: 22px; border-radius:.25rem;
    border:1px solid #cfd6df; background:#fff; left:-2rem; top:50%; transform:translateY(-50%);
  }
  .custom-checkbox.checkbox-md .custom-control-label::after{
    width: 22px; height: 22px; left:-2rem; top:50%; transform:translateY(-50%);
  }
  .custom-control-input:checked ~ .custom-control-label::before{ background:#2dce89; border-color:#2dce89; }
  .custom-control-input:focus   ~ .custom-control-label::before{ box-shadow: 0 0 0 .2rem rgba(45,206,137,.25); }
  .custom-control-input[disabled] ~ .custom-control-label{ opacity:.55; cursor:not-allowed; pointer-events:none; }
  .btn-save { border-radius: 999px; padding:.35rem 1rem; font-weight:600; display:inline-flex; align-items:center; justify-content:center; }
  .group-row .group-cell{
    background:#f1f4f9; color:#5b667a; font-weight:700; text-transform:uppercase;
    letter-spacing:.4px; padding:.55rem .9rem !important; border-top:1px solid #e9ecef;
  }
  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter { padding:.25rem 0 .75rem; }
  .dataTables_wrapper .dataTables_length label,
  .dataTables_wrapper .dataTables_filter label { font-weight:500; color:#3a4252; }
  .dataTables_wrapper .dataTables_length select {
    border:1px solid #e9ecef; border-radius:.375rem; padding:.375rem .5rem; outline:none; width: 80px; background:#fff;
  }
  .dataTables_wrapper .dataTables_filter input {
    border:1px solid #e9ecef; border-radius:.375rem; padding:.375rem .5rem; outline:none;
  }
  .dataTables_wrapper .dataTables_paginate { padding-top: .75rem; }
  .dataTables_wrapper .dataTables_paginate .paginate_button {
    border:1px solid #e9ecef !important; border-radius: 999px !important;
    padding:.35rem .65rem !important; margin:0 .15rem !important; background:#fff !important; color:#3a4252 !important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background:#5e72e4 !important; color:#fff !important; border-color:#5e72e4 !important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { opacity:.45 !important; cursor:not-allowed !important; }
</style>

@php
  function roleName($u) {
    $p = strtolower(trim($u->permission ?? ''));
    return $p === 'super admin' ? 'Super Admin' : ($p === 'admin' ? 'Admin' : 'User');
  }
  function roleSort($u) {
    return roleName($u) === 'Super Admin' ? 1 : (roleName($u) === 'Admin' ? 2 : 3);
  }
  function avatarColor($u) {
    $palette = [
      '#5e72e4', '#2dce89', '#11cdef', '#f5365c', '#fb6340', '#8965e0',
      '#f3a4b5', '#ffd600', '#2bffc6', '#ff9f89', '#66d1ff', '#172b4d'
    ];
    $seed = crc32(strtolower(($u->lname ?? '').'|'.($u->fname ?? '').'|'.($u->id ?? 0)));
    return $palette[$seed % count($palette)];
  }
@endphp

<div class="container mt-4">
  @if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
      <h4 class="mb-0">Manage User Account Restrictions</h4>
    </div>

    <div class="card-body">
      <div class="table-responsive tableFixHead">
        <table id="userAccessTable" class="table table-striped table-bordered table-hover mb-0">
          <thead class="bg-default text-white text-center">
            <tr>
              <th class="d-none">RoleSort</th>
              <th class="d-none">RoleName</th>
              <th class="name-col text-left">Name</th>
              <th class="role-col">Role</th>
              <th class="perm-col">Approval &amp; Denial</th>
              <th class="perm-col">View ActionLogs</th>
              <th class="perm-col">Edit Permits</th>
              <th class="perm-col">Print Permits</th>
              <th class="save-col">Save</th>
            </tr>
          </thead>

          <tbody>
            @foreach($users as $u)
              @php
                $formId  = 'form-'.$u->id;
                $rName   = roleName($u);
                $rSort   = roleSort($u);
                $isSuper = ($rName === 'Super Admin');
                $pillCls = $rName === 'Super Admin' ? 'role-super' : ($rName === 'Admin' ? 'role-admin' : 'role-user');
                $avatarBg = avatarColor($u);
              @endphp
              <tr data-role="{{ $rName }}">
                <td class="d-none">{{ $rSort }}</td>
                <td class="d-none">{{ $rName }}</td>

                <td class="text-left">
                  <div class="d-flex align-items-center">
                    <div class="avatar-dot" style="background: {{ $avatarBg }}">{{ strtoupper(substr($u->fname,0,1)) }}</div>
                    <div>
                      <div class="font-weight-600">{{ $u->lname }}, {{ $u->fname }}</div>
                      <div class="text-muted small">ID: {{ $u->id }}</div>
                    </div>
                  </div>
                </td>

                <td class="text-center">
                  <span class="role-pill {{ $pillCls }}">{{ $rName }}</span>
                </td>

                @foreach (['can_approve_deny','can_view_actionlogs','can_edit_permits','can_print_permits'] as $perm)
                  <td class="text-center">
                    <input type="hidden" form="{{ $formId }}" name="{{ $perm }}" value="0" {{ $isSuper ? 'disabled' : '' }}>
                    <div class="custom-control custom-checkbox checkbox-md pretty-check"
                         {!! $isSuper ? 'title="Locked for Super Admin" data-toggle="tooltip"' : '' !!}>
                      <input type="checkbox" class="custom-control-input"
                             id="{{ $perm }}-{{ $u->id }}"
                             form="{{ $formId }}"
                             name="{{ $perm }}" value="1"
                             {{ $isSuper ? 'checked disabled' : ($u->{$perm} ? 'checked' : '') }}
                             aria-checked="{{ $isSuper ? 'true' : ($u->{$perm} ? 'true' : 'false') }}">
                      <label class="custom-control-label" for="{{ $perm }}-{{ $u->id }}"></label>
                    </div>
                  </td>
                @endforeach

                <td class="text-center">
                  <form id="{{ $formId }}" method="POST" action="{{ route('user-access.update', $u) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary btn-save btn-sm" {{ $isSuper ? 'disabled' : '' }}>
                      Save
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
  var table = $('#userAccessTable').DataTable({
    pageLength: 25,
    autoWidth: false,
    order: [[0, 'asc'], [2, 'asc']],
    dom:
      '<"row mb-3"<"col-sm-6 d-flex align-items-center"l><"col-sm-6 text-sm-right"f>>' +
      'rt' +
      '<"row mt-3"<"col-sm-5 d-flex align-items-center"i><"col-sm-7 text-sm-right"p>>',
    columnDefs: [
      { targets: [0,1], visible:false, searchable:false },
      { targets: [4,5,6,7,8], orderable:false, searchable:false }
    ],
    drawCallback: function(settings){
      var api  = this.api();
      var rows = api.rows({page:'current'}).nodes();
      var last = null;

      api.column(1, {page:'current'}).data().each(function(group, i){
        if (last !== group) {
          $(rows).eq(i).before(
            '<tr class="group-row"><td colspan="9" class="group-cell">'+ group +'</td></tr>'
          );
          last = group;
        }
      });
    },
    language: {
      lengthMenu: "Show _MENU_ entries",
      search: "Search:",
      paginate: { previous: "&lt;", next: "&gt;" }
    }
  });

  $('[data-toggle="tooltip"], [title]').tooltip({ boundary: 'window' });
});
</script>
@endpush
