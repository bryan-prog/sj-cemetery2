@extends('layouts.masterlayout')

<style>
  .container.mt-4 { max-width: 1680px !important; }
  body            { background-image:url(assets/img/bg_cemetery.png); }
  table           { border:1px solid #e9ecef !important; }

  thead.bg-default th { color:#fff !important; }
  thead.bg-default { text-align: center; }

  #renewalsTable th,
  #renewalsTable td {
      white-space: nowrap;
      vertical-align: middle;
  }
  .table          { color:black !important; }
  span.badge      { color:white !important; width: 100%; }
  .card .mt-3 .pagination { display:none !important; }
  .form-control { color:black !important;}
  .form-control-label { color:black !important; }

  .badge.bg-warning { background-color: #ffc107 !important; color: #fff !important; }
  .badge.bg-purple  { background-color: #6f42c1 !important; color: #fff !important; }

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
@php
  $status = $status ?? request('status','pending');
  $showValidityCol = ($status !== 'pending');
@endphp

<div class="container mt-4">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col d-flex align-items-center">
          <h3 class="mb-0">
            <img src="https://img.icons8.com/external-others-pike-picture/35/external-Renewal-Drivers-License-driving-others-pike-picture.png"/>
            LIST OF RENEWAL REQUESTS
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
        $renewals->loadMissing('slot.cell.level.apartment', 'deceased');

        $pendingCountsByCell = [];
        $cellLabels = [];

        foreach ($renewals as $row) {
          $cellId = optional($row->slot)->grave_cell_id;

          if ($cellId) {
            if (($row->status ?? '') === 'pending') {
              $pendingCountsByCell[$cellId] = ($pendingCountsByCell[$cellId] ?? 0) + 1;
            }

            if (!isset($cellLabels[$cellId])) {
              $cell   = optional($row->slot)->cell;
              $level  = optional($cell)->level;
              $apt    = optional($level)->apartment;
              $cellLabels[$cellId] = sprintf(
                '%s • L%s R%s C%s',
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
          $filters = [
            'pending'  => 'Pending',
            'renewed'  => 'Renewed',
            'all'      => 'All',
          ];
        @endphp
        @foreach($filters as $key => $label)
          <a href="{{ route('renewals.index', ['status' => $key]) }}"
             class="btn btn-sm {{ ($status === $key) ? 'btn-primary' : 'btn-outline-primary' }}">
            {{ $label }}
          </a>
        @endforeach
      </div>

      @if($renewals->isEmpty())
        <div class="alert alert-info">No renewal requests found.</div>
      @else
        <div class="table-responsive">
          <table id="renewalsTable" class="table table-flush align-middle mb-0">
            <thead class="bg-default">
              <tr>
                <th class="text-white">Applicant's Name</th>
                <th class="text-white">Relationship&nbsp;to&nbsp;Deceased</th>
                <th class="text-white">Name&nbsp;of&nbsp;Deceased</th>
                <th class="text-white">Sex</th>
                <th class="text-white">Buried&nbsp;At</th>
                <th class="text-white">Renewal&nbsp;Period</th>
                <th class="text-white">Status</th>
                @if($showValidityCol)
                  <th class="text-white">Validity</th>
                @endif
                <th class="text-white text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
            @php
              $now = \Carbon\Carbon::now();
              $expiryThresholdDays = 30;
            @endphp

            @foreach ($renewals as $r)
              @php
                $slot  = $r->slot;
                $cell  = $slot?->cell;
                $level = $cell?->level;
                $apt   = $level?->apartment;

                $location = $apt
                    ? $apt->name.' Level '.$level->level_no.' R'.$cell->row_no.' C'.$cell->col_no.' S'.$slot->slot_no
                    : '—';

                $rawStatus   = strtolower((string)$r->status);
                $statusLabel = ($rawStatus === 'approved') ? 'Renewed' : ucfirst($r->status);

                $badge = match ($rawStatus) {
                    'pending'  => 'bg-warning',
                    'approved' => 'bg-success',
                    'denied'   => 'bg-danger',
                    default    => 'bg-secondary',
                };

                $dec    = $r->deceased;
                $startY = $r->renewal_start?->format('Y') ?? '—';
                $endY   = $r->renewal_end?->format('Y')   ?? '—';

                $cellId        = optional($slot)->grave_cell_id;
                $pendingInCell = $cellId ? ($pendingCountsByCell[$cellId] ?? 1) : 1;
                $isMulti       = ($status === 'pending') && $pendingInCell > 1;
                $cellTitle     = $cellId ? ($cellLabels[$cellId] ?? '') : '';

                $validityText  = '';
                $validityClass = 'badge bg-secondary';
                if ($showValidityCol && $rawStatus !== 'pending' && $r->renewal_start && $r->renewal_end) {
                  $end = \Carbon\Carbon::parse($r->renewal_end);
                  if ($now->gt($end)) {
                    $validityText  = 'For Penalty';
                    $validityClass = 'badge bg-danger';
                  } else {
                    $daysLeft = $now->diffInDays($end, false);
                    if ($daysLeft <= $expiryThresholdDays && $daysLeft >= 0) {
                      $validityText  = 'For Expiry';
                      $validityClass = 'badge bg-warning';
                    } else {
                      $validityText  = 'Within Range';
                      $validityClass = 'badge bg-purple';
                    }
                  }
                }
              @endphp
              <tr data-cell="{{ $cellId }}" title="{{ $cellTitle }}">
                <td>{{ $r->requesting_party }}</td>
                <td>{{ $r->relationship_to_deceased ?? '—' }}</td>
                <td>{{ $dec?->name_of_deceased ?? '—' }}</td>
                <td>{{ $dec?->sex ?? '—' }}</td>
                <td>{{ $location }}</td>
                <td>{{ $startY }}-{{ $endY }}</td>
                <td><span class="badge {{ $badge }}">{{ $statusLabel }}</span></td>

                @if($showValidityCol)
                  <td>
                    @if($validityText !== '')
                      <span class="{{ $validityClass }}">{{ $validityText }}</span>
                    @endif
                  </td>
                @endif

                <td class="text-end">
                  <a href="{{ url('/renewals/'.$r->id.'/permit') }}" target="_blank" class="btn btn-sm btn-primary" title="Print Permit">
                    <i class="fa fa-print" aria-hidden="true"></i>
                  </a>


                  <button type="button"
                          class="btn btn-sm btn-info edit-renewal-btn"
                          data-id="{{ $r->id }}"
                          title="View / Edit">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                  </button>

                  @if ($r->status === 'pending')
                    @if(!$isMulti)
                      <button type="button"
                              class="btn btn-sm btn-success approve-btn"
                              data-toggle="modal"
                              data-target="#approveModal"
                              data-action="{{ route('renewals.approve', $r) }}"
                              title="Approve">
                        <i class="fa fa-check" aria-hidden="true"></i>
                      </button>
                    @else
                      <button type="button"
                              class="btn btn-sm btn-primary approve-batch-btn"
                              data-toggle="modal"
                              data-target="#approveBatchModal"
                              data-batch-action="{{ route('renewals.approveBatch', $r) }}"
                              title="Approve all pending renewals in this cell">
                        <i class="fa fa-check-double" aria-hidden="true"></i>
                      </button>
                    @endif

                    <form action="{{ route('renewals.deny', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Deny this request?');">
                      @csrf
                      <button class="btn btn-sm btn-danger" title="Deny"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </form>
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
          <h5 class="modal-title"><img class="mr-2" src="https://img.icons8.com/doodle/25/checked-checkbox.png"/> Approve Renewal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/25/check.png" /> OR No.</label>
            <input type="text" name="or_number" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/25/calendar--v2.png"/> Date Issued</label>
            <input type="date" name="or_issued_at" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save &amp; Approve</button>
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
          <h5 class="modal-title"><img class="mr-2" src="https://img.icons8.com/doodle/25/checked-checkbox.png"/> Approve All (Same Cell)</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/25/check.png" /> OR No.</label>
            <input type="text" name="or_number" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/25/calendar--v2.png"/> Date Issued</label>
            <input type="date" name="or_issued_at" class="form-control" required>
          </div>
          <div class="alert alert-info mb-0">
            This will approve <strong>all pending renewals</strong> from the <strong>same grave cell</strong>.
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save &amp; Approve All</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="editRenewalModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <form id="editRenewalForm" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="PATCH">

        <div class="modal-header">
          <h5 class="modal-title">Renewal Details</h5>
          <button type="button" class="close ml-2" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
        </div>

        <div class="modal-body">
          <div class="alert alert-danger d-none" id="editRenewalErrors"></div>


          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/test-account.png"> Applicant's Name</label>
              <input type="text" class="form-control er-field" name="requesting_party" required>
            </div>
            <div class="form-group col-md-6">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"> Applicant Address</label>
              <input type="text" class="form-control er-field" name="applicant_address" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-phone.png"> Contact</label>
              <input type="text" class="form-control er-field" name="contact" required>
            </div>
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/stickers/20/family.png"> Relationship to Deceased</label>
              <input type="text" class="form-control er-field" name="relationship_to_deceased">
            </div>
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/refund.png"> Amount (as per ordinance)</label>
              <input type="number" step="0.01" class="form-control er-field" name="amount_as_per_ord">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"> Date Applied</label>
              <input type="date" class="form-control er-field" name="date_applied" required>
            </div>
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"> Renewal Start</label>
              <input type="date" class="form-control er-field" name="renewal_start" required>
            </div>
            <div class="form-group col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"> Renewal End</label>
              <input type="date" class="form-control er-field" name="renewal_end" required>
            </div>
          </div>

          <h5 class="text-muted mb-2">Deceased Information & Location</h5>
          <hr class="mt-2 mb-3">

          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="form-control-label"><img src="https://img.icons8.com/plasticine/20/headstone.png"/> Name of Deceased</label>
              <input type="text" class="form-control er-deceased" name="dec_name" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label"><img src="https://img.icons8.com/stickers/20/gender.png"/> Sex</label>
              <input type="text" class="form-control er-deceased" name="dec_sex" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label"><img src="https://img.icons8.com/officel/20/cemetery.png"/> Buried at</label>
              <input type="text" class="form-control er-deceased" name="dec_buried_at" disabled>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" id="erEditSaveBtn" class="btn btn-danger" data-mode="view">Edit</button>
          <button type="button" id="erCancelBtn" class="btn btn-secondary">Cancel</button>
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
  window.SHOW_VALIDITY = {{ $showValidityCol ? 'true' : 'false' }};
</script>

<script>
$(function () {
  var table;
  if ($.fn && $.fn.DataTable) {
    table = $('#renewalsTable').DataTable({
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
      order        : [[5, 'desc']],
      language     : { paginate: { next:'>', previous:'<', first:'<<', last:'>>' } },
      columnDefs   : (window.SHOW_VALIDITY
                      ? [{ orderable:false, targets:[-1, -2] }]
                      : [{ orderable:false, targets:[-1] }])
    });

    $('#renewalsTable_length select').addClass('form-control form-control-sm');
    $('#renewalsTable_filter input')
      .addClass('form-control form-control-sm')
      .attr('placeholder', 'Search...');
  }


  $('#renewalsTable tbody').on('click', 'tr', function (e) {
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


  var $currentRow = null;
  var currentId   = null;
  var loadedData  = null;

  function setViewMode(isView) {
    var $fields = $('#editRenewalForm').find('input.er-field, textarea.er-field, select.er-field');
    $fields.prop('disabled', isView);
    $('#editRenewalForm').find('input.er-deceased').prop('disabled', true);

    var $editSave = $('#erEditSaveBtn');
    if (isView) {
      $editSave.text('Edit').attr('data-mode', 'view').removeClass('btn-success').addClass('btn-danger');
    } else {
      $editSave.text('Save changes').attr('data-mode', 'save').removeClass('btn-danger').addClass('btn-success');
    }
  }


  $(document).on('click', '.edit-renewal-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    $currentRow = $(this).closest('tr');
    currentId   = $(this).data('id');
    if (!currentId) return;

    $('#editRenewalErrors').addClass('d-none').empty();
    setViewMode(true);

    $.getJSON("{{ url('/renewals') }}/" + currentId, function (data) {
      loadedData = data;
      $('#editRenewalForm').attr('action', "{{ url('/renewals') }}/" + currentId);

      $('[name=requesting_party]').val(data.requesting_party || '');
      $('[name=applicant_address]').val(data.applicant_address || '');
      $('[name=contact]').val(data.contact || '');
      $('[name=relationship_to_deceased]').val(data.relationship_to_deceased || '');
      $('[name=date_applied]').val(data.date_applied || '');
      $('[name=amount_as_per_ord]').val(data.amount_as_per_ord || '');
      $('[name=renewal_start]').val(data.renewal_start || '');
      $('[name=renewal_end]').val(data.renewal_end || '');

      $('[name=dec_name]').val(data.deceased_name || '—');
      $('[name=dec_sex]').val(data.sex || '—');
      $('[name=dec_buried_at]').val(data.buried_at || '—');

      $('#erEditSaveBtn').show().text('Edit').attr('data-mode','view');
      $('#erCancelBtn').text('Cancel');

      $('#editRenewalModal').modal('show');
    });
  });


  $('#erEditSaveBtn').on('click', function() {
    var mode = $(this).attr('data-mode');
    if (mode === 'view') {
      setViewMode(false);
      $('[name=requesting_party]').trigger('focus');
    } else {
      $('#editRenewalForm').trigger('submit');
    }
  });


  $('#erCancelBtn').on('click', function () {
    var mode = $('#erEditSaveBtn').attr('data-mode');
    if (mode === 'save') {
      if (!loadedData) return;
      $('[name=requesting_party]').val(loadedData.requesting_party || '');
      $('[name=applicant_address]').val(loadedData.applicant_address || '');
      $('[name=contact]').val(loadedData.contact || '');
      $('[name=relationship_to_deceased]').val(loadedData.relationship_to_deceased || '');
      $('[name=date_applied]').val(loadedData.date_applied || '');
      $('[name=amount_as_per_ord]').val(loadedData.amount_as_per_ord || '');
      $('[name=renewal_start]').val(loadedData.renewal_start || '');
      $('[name=renewal_end]').val(loadedData.renewal_end || '');
      setViewMode(true);
    } else {
      $('#editRenewalModal').modal('hide');
    }
  });


  $('#editRenewalForm').on('submit', function (e) {
    e.preventDefault();
    if (!currentId) return;

    var $btn = $('#erEditSaveBtn').prop('disabled', true);
    var formData = $(this).serialize();

    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: formData,
      success: function (resp) {

        var startY = ($('[name=renewal_start]').val() || '').slice(0,4);
        var endY   = ($('[name=renewal_end]').val() || '').slice(0,4);
        var periodText = (startY && endY) ? (startY + '-' + endY)
                                          : (($('[name=renewal_start]').val() || '') + '-' + ($('[name=renewal_end]').val() || ''));

        $currentRow.find('td').eq(0).text($('[name=requesting_party]').val());
        $currentRow.find('td').eq(1).text($('[name=relationship_to_deceased]').val() || '—');

        if (resp.payload && resp.payload.deceased_name) {
          $currentRow.find('td').eq(2).text(resp.payload.deceased_name);
        }

        if (resp.payload && resp.payload.buried_at) {
          $currentRow.find('td').eq(4).text(resp.payload.buried_at);
          $('[name=dec_buried_at]').val(resp.payload.buried_at);
        }

        $currentRow.find('td').eq(5).text(periodText);

        if (table) {
          table.row($currentRow).invalidate().draw(false);
        }

        loadedData = $.extend({}, loadedData, {
          requesting_party: $('[name=requesting_party]').val(),
          applicant_address: $('[name=applicant_address]').val(),
          contact: $('[name=contact]').val(),
          relationship_to_deceased: $('[name=relationship_to_deceased]').val(),
          date_applied: $('[name=date_applied]').val(),
          amount_as_per_ord: $('[name=amount_as_per_ord]').val(),
          renewal_start: $('[name=renewal_start]').val(),
          renewal_end: $('[name=renewal_end]').val()
        });

        $('#editRenewalModal').one('hidden.bs.modal', function () {
          $('#saveSuccessText').text(resp.message || 'Saved successfully!');
          $('#saveSuccessModal').modal('show');
          setTimeout(function(){ $('#saveSuccessModal').modal('hide'); }, 1800);
        });
        $('#editRenewalModal').modal('hide');
      },
      error: function (xhr) {
        var $box = $('#editRenewalErrors').removeClass('d-none').empty();
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
