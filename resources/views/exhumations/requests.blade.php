@extends('layouts.masterlayout')

<style>
  .container.mt-4 { max-width: 1680px !important; }
  body { background-image: url("{{ secure_asset('assets/img/bg_cemetery.png') }}"); }
  table { border:1px solid #e9ecef !important; }
  thead.bg-default th { color:#fff !important; text-align:center; }

  #exhumationsTable th, #exhumationsTable td { white-space: nowrap; vertical-align: middle; }
  .table { color:black !important; }
  span.badge { color:white !important; width:100%; }
  .card .mt-3 .pagination { display:none !important; }
  .form-control, .form-control-label { color:black !important; }

  .badge.bg-warning { background-color: #ffc107 !important; color: #fff !important; }
  .badge.bg-success { background-color: #28a745 !important; color: #fff !important; }
  .badge.bg-secondary { background-color: #6c757d !important; color:#fff !important; }

  .dataTables_wrapper .dt-header { margin-bottom: .5rem; }
  .dataTables_wrapper .dataTables_length label,
  .dataTables_wrapper .dataTables_filter label { margin-bottom: 0; }
  .dataTables_wrapper .dataTables_length select {
    width: auto; display: inline-block; margin: 0 .5rem;
  }
  .dataTables_wrapper .dataTables_filter input {
    width: 220px; display: inline-block; margin-left: .5rem;
  }
  @media (max-width: 576px) {
    .dataTables_wrapper .dataTables_filter input { width: 150px; }
  }
</style>

@section('content')
<div class="container mt-4">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col d-flex align-items-center">
          <h3 class="mb-0">
            <img src="https://img.icons8.com/external-icongeek26-linear-colour-icongeek26/35/external-remove-documents-icongeek26-linear-colour-icongeek26-1.png"/>
            LIST OF EXHUMATION REQUESTS
          </h3>
        </div>
        <div class="col d-flex justify-content-end">
          <a href="{{ url('/Homepage') }}"><button class="btn btn-default btn-sm">Back to Home</button></a>
        </div>
      </div>
    </div>

    <div class="card-body">
      @foreach (['success','info','error'] as $f)
        @if(session($f))
          <div class="alert alert-{{ $f === 'error' ? 'danger' : $f }}">{{ session($f) }}</div>
        @endif
      @endforeach

      @php
        $exhumations->loadMissing('fromSlot.cell.level.apartment', 'toSlot.cell.level.apartment', 'reservation.deceased');
        $status = $status ?? request('status','pending');
        $showORCol = ($status !== 'pending');

        $cellCounts = [];
        $cellLabels = [];
        foreach ($exhumations as $e) {
            $cellId = optional($e->fromSlot)->grave_cell_id;
            if ($cellId) {
                $cellCounts[$cellId] = ($cellCounts[$cellId] ?? 0) + 1;

                if (!isset($cellLabels[$cellId])) {
                    $cell   = optional($e->fromSlot)->cell;
                    $level  = optional($cell)->level;
                    $apt    = optional($level)->apartment;
                    $cellLabels[$cellId] = sprintf(
                        '%s • Level %s R%s C%s',
                        $apt->name ?? '—',
                        $level->level_no ?? '—',
                        $cell->row_no ?? '—',
                        $cell->col_no ?? '—'
                    );
                }
            }
        }
      @endphp

      <div class="mb-3 d-flex flex-wrap gap-2">
        @php
          $filters = ['pending' => 'Pending','exhumed' => 'Exhumed','all' => 'All'];
        @endphp
        @foreach($filters as $key => $label)
          <a href="{{ route('exhumations.requests', ['status' => $key]) }}"
             class="btn btn-sm {{ ($status === $key) ? 'btn-primary' : 'btn-outline-primary' }}">
            {{ $label }}
          </a>
        @endforeach
      </div>

      @if($exhumations->isEmpty())
        <div class="alert alert-info">No exhumation requests found.</div>
      @else
        <div class="table-responsive">
          <table id="exhumationsTable" class="table table-flush align-middle mb-0">
            <thead class="bg-default">
              <tr>
                <th class="text-white">Requestor</th>
                <th class="text-white">Relationship&nbsp;to&nbsp;Deceased</th>
                <th class="text-white">Date&nbsp;Requested</th>
                <th class="text-white">Name&nbsp;of&nbsp;Deceased</th>
                <th class="text-white">Date&nbsp;of&nbsp;Death</th>
                <th class="text-white">For&nbsp;Transfer</th>
                @if($showORCol)
                  <th class="text-white">OR&nbsp;No.</th>
                @endif
                <th class="text-white">Status</th>
                <th class="text-white text-end">Actions</th>
              </tr>
            </thead>

            <tbody>
            @foreach($exhumations as $ex)
              @php
                $dec = $ex->reservation->deceased ?? null;

                $badge = match ($ex->status) {
                  'pending'             => 'bg-warning',
                  'approved', 'exhumed' => 'bg-success',
                  default               => 'bg-secondary',
                };

                $statusText = $ex->status === 'approved' ? 'Exhumed' : ucfirst($ex->status);

                $dateRequested = $ex->date_applied
                  ? \Illuminate\Support\Carbon::parse($ex->date_applied)->format('Y-m-d')
                  : '—';

                $dod = $dec?->date_of_death
                  ? \Illuminate\Support\Carbon::parse($dec->date_of_death)->format('Y-m-d')
                  : '—';

                $forTransfer = $ex->toSlot
                  ? (app(\App\Http\Controllers\ExhumationPermitController::class)->locationLabel($ex->toSlot) ?? '—')
                  : ($ex->current_location ?? '—');

                $originCellId   = optional($ex->fromSlot)->grave_cell_id;
                $originIsBulk   = $originCellId ? (($cellCounts[$originCellId] ?? 0) > 1) : false;
                $originCellText = $originCellId ? ($cellLabels[$originCellId] ?? '') : '';
              @endphp

              <tr data-cell="{{ $originCellId }}" title="{{ $originCellText }}">
                <td>{{ $ex->requesting_party }}</td>
                <td>{{ $ex->relationship_to_deceased ?? '—' }}</td>
                <td>{{ $dateRequested }}</td>
                <td>{{ $dec?->name_of_deceased ?? '—' }}</td>
                <td>{{ $dod }}</td>
                <td>{{ $forTransfer }}</td>

                @if($showORCol)
                  <td>
                    {{ in_array($ex->status, ['approved','exhumed'], true) ? ($ex->or_number ?? '—') : '—' }}
                  </td>
                @endif

                <td><span class="badge {{ $badge }}">{{ $statusText }}</span></td>

                <td class="text-end">
                  <a href="{{ route('exhumations.permit', $ex) }}"
                     target="_blank"
                     class="btn btn-sm btn-primary"
                     title="Print Permit">
                    <i class="fa fa-print" aria-hidden="true"></i>
                  </a>

                  {{-- SHOW Edit button only if NOT approved/exhumed --}}
                  @if (!in_array($ex->status, ['approved','exhumed'], true))
                  <button type="button"
                          class="btn btn-sm btn-info edit-exh-btn"
                          data-id="{{ $ex->id }}"
                          title="View / Edit">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                  </button>
                  @endif

                  @if($ex->status === 'pending')
                    @if($originIsBulk)

                      <button type="button"
                              class="btn btn-sm btn-primary approve-batch-btn"
                              title="Mark all pending from this origin cell as Exhumed"
                              data-toggle="modal"
                              data-target="#approveBatchModal"
                              data-batch-action="{{ route('exhumations.approveBatch', $ex) }}">
                        <i class="fa fa-check-double" aria-hidden="true"></i>
                      </button>

                      <button type="button"
                              class="btn btn-sm btn-danger deny-batch-btn"
                              title="Deny all pending from this origin cell"
                              data-toggle="modal"
                              data-target="#denyBatchModal"
                              data-batch-action="{{ route('exhumations.denyBatch', $ex) }}">
                        <i class="fa fa-times-circle" aria-hidden="true"></i>
                      </button>
                    @else

                      <button type="button"
                              class="btn btn-sm btn-success approve-btn"
                              data-toggle="modal"
                              data-target="#approveModal"
                              data-action="{{ route('exhumations.approve', $ex) }}"
                              title="Mark Exhumed">
                        <i class="fa fa-check" aria-hidden="true"></i>
                      </button>

                      <form method="POST"
                            action="{{ route('exhumations.deny', $ex) }}"
                            class="d-inline"
                            onsubmit="return confirm('Deny this request?');">
                        @csrf
                        <button class="btn btn-sm btn-danger" title="Deny">
                          <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                      </form>
                    @endif
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>



<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="approveForm" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">
            <img class="mr-2" src="https://img.icons8.com/doodle/25/checked-checkbox.png"/> Mark Exhumed
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label class="form-control-label">
              <img src="https://img.icons8.com/doodle/25/check.png" /> OR No.
            </label>
            <input type="text" name="or_number" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-control-label">
              <img src="https://img.icons8.com/doodle/25/calendar--v2.png"/> Date Issued
            </label>
            <input type="date" name="or_issued_at" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save &amp; Mark Exhumed</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="approveBatchModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="approveBatchForm" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">
            <img class="mr-2" src="https://img.icons8.com/doodle/25/checked-checkbox.png"/> Mark All Exhumed (Same Cell)
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label class="form-control-label">
              <img src="https://img.icons8.com/doodle/25/check.png" /> OR No.
            </label>
            <input type="text" name="or_number" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-control-label">
              <img src="https://img.icons8.com/doodle/25/calendar--v2.png"/> Date Issued
            </label>
            <input type="date" name="or_issued_at" class="form-control" required>
          </div>
          <div class="alert alert-info mb-0">
            This will mark <strong>all pending exhumations</strong> from the <strong>same grave cell</strong> as Exhumed.
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save &amp; Mark All Exhumed</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="denyBatchModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="denyBatchForm" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">
           Deny All (Same Cell)
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <p class="mb-0">Are you sure you want to proceed?</p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No, keep pending</button>
          <button type="submit" class="btn btn-danger">Yes, Deny All</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="editExhModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <form id="editExhForm" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="PATCH">

        <div class="modal-header">
          <h5 class="modal-title">Exhumation Details</h5>
          <button type="button" class="close ml-2" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
        </div>

        <div class="modal-body">
          <div class="alert alert-danger d-none" id="editExhErrors"></div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/test-account.png"> Requesting Party</label>
              <input type="text" class="form-control ex-field" name="requesting_party" required>
            </div>
            <div class="form-group col-md-6">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"> Address</label>
              <input type="text" class="form-control ex-field" name="address">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-phone.png"> Contact</label>
              <input type="text" class="form-control ex-field" name="contact">
            </div>
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/stickers/20/family.png"> Relationship to Deceased</label>
              <input type="text" class="form-control ex-field" name="relationship_to_deceased">
            </div>
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/refund.png"> Amount (fee as per ordinance)</label>
              <input type="number" step="0.01" class="form-control ex-field" name="amount_as_per_ord">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"> Date Requested</label>
              <input type="date" class="form-control ex-field" name="date_applied" required>
            </div>
            <div class="form-group col-md-8">
              <label class="form-control-label"><img src="https://img.icons8.com/officel/20/cemetery.png"/>Transfer Location</label>
              <input type="text" class="form-control ex-ro" name="current_location" disabled>
            </div>
          </div>

          <h4 class="text-muted mb-2">Deceased Information & Location</h4>
          <hr class="mt-2 mb-3">

          <div class="form-row">
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/plasticine/20/headstone.png"/> Name of Deceased</label>
              <input type="text" class="form-control ex-ro" name="dec_name" disabled>
            </div>
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"/> Date of Death</label>
              <input type="text" class="form-control ex-ro" name="dec_dod" disabled>
            </div>
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/officel/20/cemetery.png"/> From (Origin Tomb)</label>
              <input type="text" class="form-control ex-ro" name="from_label" disabled>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" id="exEditSaveBtn" class="btn btn-danger" data-mode="view">Edit</button>
          <button type="button" id="exCancelBtn" class="btn btn-secondary">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="saveSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body text-center">
        <div class="mb-2">
          <img src="https://img.icons8.com/doodle/48/checked-checkbox.png" alt="Success"/>
        </div>
        <h5 class="mb-0" id="saveSuccessText">Saved successfully!</h5>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-success btn-sm" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2LcEk3A2KlFwgag7W/sJtrrUpAI8Q4K3sF86dIHNDz"
        crossorigin="anonymous"></script>

<script>
  const EXH_SHOW_URL_TPL           = @json(route('exhumations.show',   ['exhumation' => '__ID__']));
  const EXH_UPDATE_URL_TPL         = @json(route('exhumations.update', ['exhumation' => '__ID__']));
  const EXH_APPROVE_URL_TPL        = @json(route('exhumations.approve', ['exhumation' => '__ID__']));
  const EXH_APPROVE_BATCH_URL_TPL  = @json(route('exhumations.approveBatch', ['exhumation' => '__ID__']));
  const EXH_DENY_URL_TPL           = @json(route('exhumations.deny', ['exhumation' => '__ID__']));
  const EXH_DENY_BATCH_URL_TPL     = @json(route('exhumations.denyBatch', ['exhumation' => '__ID__']));
  const EXH_PERMIT_URL_TPL         = @json(route('exhumations.permit', ['exhumation' => '__ID__']));
</script>

<script>
$(function () {
  var table;
  if ($.fn && $.fn.DataTable) {
    table = $('#exhumationsTable').DataTable({
      processing   : true,
      serverSide   : false,
      searching    : true,
      lengthChange : true,
      pageLength   : 10,
      lengthMenu   : [[5,10,25,50,100],[5,10,25,50,100]],
      dom          : '<"dt-header row align-items-center mb-2"'
                   +   '<"col-sm-6 d-flex align-items-center"l>'
                   +   '<"col-sm-6 d-flex justify-content-end"f>'
                   + '>'
                   + 'rt'
                   + '<"dt-footer row align-items-center mt-2"'
                   +   '<"col-sm-6"i>'
                   +   '<"col-sm-6 d-flex justify-content-end"p>'
                   + '>',
      pagingType   : 'full_numbers',
      order        : [[2, 'desc']],
      language     : { paginate: { next:'>', previous:'<', first:'<<', last:'>>' } },
      columnDefs   : [{ orderable:false, targets:[-1] }]
    });

    $('#exhumationsTable_length select').addClass('form-control form-control-sm');
    $('#exhumationsTable_filter input')
      .addClass('form-control form-control-sm')
      .attr('placeholder', 'Search...');
  }

  $('#exhumationsTable tbody').on('click', 'tr', function (e) {
    if ($(e.target).closest('a,button,input,select,textarea,label').length) return;
    if (table) table.$('tr.table-active').removeClass('table-active');
    $(this).addClass('table-active');
  });

  $(document).on('click', '.approve-btn', function () {
    $('#approveForm').attr('action', $(this).data('action'));
    $('#approveForm')[0]?.reset();
  });

  $(document).on('click', '.approve-batch-btn', function () {
    $('#approveBatchForm').attr('action', $(this).data('batch-action'));
    $('#approveBatchForm')[0]?.reset();
  });

  $(document).on('click', '.deny-batch-btn', function () {
    $('#denyBatchForm').attr('action', $(this).data('batch-action'));
  });

  var $currentRow = null;
  var currentId   = null;
  var loadedData  = null;

  function setViewMode(isView) {
    var $fields = $('#editExhForm').find('input.ex-field, textarea.ex-field, select.ex-field');
    $fields.prop('disabled', isView);
    $('#editExhForm').find('input.ex-ro').prop('disabled', true);

    var $editSave = $('#exEditSaveBtn');
    if (isView) {
      $editSave.text('Edit').attr('data-mode', 'view').removeClass('btn-success').addClass('btn-danger');
    } else {
      $editSave.text('Save changes').attr('data-mode', 'save').removeClass('btn-danger').addClass('btn-success');
    }
  }

  $(document).on('click', '.edit-exh-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    $currentRow = $(this).closest('tr');
    currentId   = $(this).data('id');
    if (!currentId) return;

    $('#editExhErrors').addClass('d-none').empty().text('');
    $('#editExhForm')[0].reset();
    setViewMode(true);

    $('[name=dec_name]').val('Loading…');
    $('[name=dec_dod]').val('');
    $('[name=from_label]').val('');

    $('#editExhModal').modal('show');

    $('#editExhForm').attr('action', EXH_UPDATE_URL_TPL.replace('__ID__', currentId));

    $.getJSON(EXH_SHOW_URL_TPL.replace('__ID__', currentId))
      .done(function (data) {
        loadedData = data;

        $('[name=requesting_party]').val(data.requesting_party || '');
        $('[name=address]').val(data.address || '');
        $('[name=contact]').val(data.contact || '');
        $('[name=relationship_to_deceased]').val(data.relationship_to_deceased || '');
        $('[name=amount_as_per_ord]').val(data.amount_as_per_ord || '');
        $('[name=date_applied]').val(data.date_applied || '');

        var displayLoc = data.current_location || data.to_label || '';
        $('[name=current_location]').val(displayLoc);

        $('[name=dec_name]').val(data.deceased_name || '—');
        $('[name=dec_dod]').val(data.date_of_death || '—');
        $('[name=from_label]').val(data.from_label || '—');

        $('#exEditSaveBtn').show().text('Edit').attr('data-mode','view');
        $('#exCancelBtn').text('Cancel');
      })
      .fail(function (xhr) {
        var msg = 'Unable to load request details.';
        if (xhr.status === 404) msg = 'Record not found (404). Check if the route exhumations.show exists and ID is valid.';
        if (xhr.status === 419) msg = 'Session expired (419). Please refresh and try again.';
        $('#editExhErrors').removeClass('d-none').text(msg);
        $('[name=dec_name]').val('—');
      });
  });

  $('#exEditSaveBtn').on('click', function() {
    var mode = $(this).attr('data-mode');
    if (mode === 'view') {
      setViewMode(false);
      $('[name=requesting_party]').trigger('focus');
    } else {
      $('#editExhForm').trigger('submit');
    }
  });

  $('#exCancelBtn').on('click', function () {
    var mode = $('#exEditSaveBtn').attr('data-mode');
    if (mode === 'save') {
      if (!loadedData) return;
      $('[name=requesting_party]').val(loadedData.requesting_party || '');
      $('[name=address]').val(loadedData.address || '');
      $('[name=contact]').val(loadedData.contact || '');
      $('[name=relationship_to_deceased]').val(loadedData.relationship_to_deceased || '');
      $('[name=amount_as_per_ord]').val(loadedData.amount_as_per_ord || '');
      $('[name=date_applied]').val(loadedData.date_applied || '');
      $('[name=current_location]').val(loadedData.current_location || loadedData.to_label || '');
      setViewMode(true);
    } else {
      $('#editExhModal').modal('hide');
    }
  });

  $('#editExhForm').on('submit', function (e) {
    e.preventDefault();
    if (!currentId) return;

    var $btn = $('#exEditSaveBtn').prop('disabled', true);
    var formData = $(this).serialize();

    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: formData,
      success: function (resp) {
        $currentRow.find('td').eq(0).text($('[name=requesting_party]').val());
        $currentRow.find('td').eq(1).text($('[name=relationship_to_deceased]').val() || '—');
        $currentRow.find('td').eq(2).text($('[name=date_applied]').val() || '—');

        var locText = (resp.payload && resp.payload.for_transfer)
                        ? resp.payload.for_transfer
                        : ($('[name=current_location]').val() || $currentRow.find('td').eq(5).text());
        $currentRow.find('td').eq(5).text(locText);

        if (table) table.row($currentRow).invalidate().draw(false);

        loadedData = $.extend({}, loadedData, {
          requesting_party: $('[name=requesting_party]').val(),
          address: $('[name=address]').val(),
          contact: $('[name=contact]').val(),
          relationship_to_deceased: $('[name=relationship_to_deceased]').val(),
          amount_as_per_ord: $('[name=amount_as_per_ord]').val(),
          date_applied: $('[name=date_applied]').val(),
          current_location: $('[name=current_location]').val()
        });

        $('#editExhModal').one('hidden.bs.modal', function () {
          $('#saveSuccessText').text(resp.message || 'Saved successfully!');
          $('#saveSuccessModal').modal('show');
          setTimeout(function(){ $('#saveSuccessModal').modal('hide'); }, 1800);
        });
        $('#editExhModal').modal('hide');
      },
      error: function (xhr) {
        var $box = $('#editExhErrors').removeClass('d-none').empty();
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          $.each(xhr.responseJSON.errors, function (k, arr) {
            $box.append('<div>' + arr.join('<br>') + '</div>');
          });
        } else {
          $box.text('Something went wrong. Please try again.');
        }
      },
      complete: function () {
        $btn.prop('disabled', false);
        setViewMode(true);
      }
    });
  });
});
</script>
@endpush
