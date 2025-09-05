{{-- resources/views/homepage.blade.php --}}
@extends('layouts.masterlayout')
@inject('carbon', 'Carbon\Carbon')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.10/css/dataTables.bootstrap4.min.css">
@endpush

<style>
  .container.mt-5 { max-width: 1830px !important; }
  body            { background-image: url(assets/img/bg_cemetery.png); }
  .form-control-label, .form-control, .form-select { color:black !important; }


  .progress.progress-xs { height: 8px; }
  .list-levels { margin-top: .85rem; }
  .list-levels .list-group-item { transition: background .2s ease; padding-top:.75rem; padding-bottom:.75rem; } /* more vertical room */
  .list-levels .list-group-item:hover { background: #f8f9fa; }
  .list-levels h6 { margin:0; font-weight:600; }
  .list-levels small { color:#6c757d; display:inline-block; margin-top:.15rem; }

  .card-header .title-wrap { display:flex; align-items:center; gap:.5rem; }
  .title-dot { width:10px; height:10px; border-radius:50%; background:#3b82f6; display:inline-block; }


  .apt-picker { position: relative; margin-bottom: .9rem; }
  .apt-picker .select-apt {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 9999px;
    padding: .62rem 2.8rem .62rem 2.2rem;
    background-color: #fff;
    font-weight: 600;
    letter-spacing: .2px;
    box-shadow: 0 2px 6px rgba(0,0,0,.06);
    transition: box-shadow .15s ease, border-color .15s ease, transform .05s ease;
    color: #111827;
    background-image:
      url("data:image/svg+xml,%3Csvg width='16' height='16' xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .8rem center;
    background-size: 16px 16px;
  }
  .apt-picker .select-apt:hover {
    border-color: #c1c9d0;
    box-shadow: 0 3px 10px rgba(0,0,0,.08);
  }
  .apt-picker .select-apt:focus {
    outline: none;
    border-color: #94b4ff;
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
  }
  .apt-picker .left-icon {
    position: absolute; left: .8rem; top: 50%; transform: translateY(-50%);
    opacity: .9;
  }
  .apt-picker .left-icon svg { display:block; }
  .apt-picker .clear-btn {
    position: absolute; right: 2.2rem; top: 50%; transform: translateY(-50%);
    border-radius: 9999px;
    padding: 2px 7px;
    font-size: .75rem;
    line-height: 1rem;
  }
  .apt-hint { font-size: .8rem; color:#6b7280; margin-top: .35rem; }


  .inline-spinner {
    display: inline-flex; align-items: center; gap:.35rem; margin-left: .4rem;
  }


  .row.align-items-center .col-8 .progress { margin-top: .35rem; }
</style>

@section('content')
<div class="container mt-5">


  <div class="row mb-3 d-flex justify-content-end">
    <a href="{{ route('renewals.index') }}"><button class="btn btn-success mr-2">See Renewal Requests</button></a>
    <a href="{{ url('/exhumations/requests') }}"><button class="btn btn-primary mr-2">See Exhumation Requests</button></a>
    <a href="{{ route('burial.apply.gate') }}" class="btn btn-danger mr-2">Apply for Burial Permit</a>
    <a href="{{ route('reservations.index') }}"><button class="btn btn-info">See Database</button></a>
  </div>

  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Total</h5>
              <span class="h2 font-weight-bold mb-0">{{ number_format($overallTotal) }}</span>
            </div>
            <div class="col-auto">
              <img src="https://img.icons8.com/bubbles/70/folder-invoices.png" alt="folder-invoices"/>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Reservations</h5>
              <span class="h2 font-weight-bold mb-0">{{ number_format($reservationTotal) }}</span>
            </div>
            <div class="col-auto">
              <img src="https://img.icons8.com/bubbles/70/windows-explorer.png" alt="windows-explorer"/>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">For Renewal</h5>
              <span class="h2 font-weight-bold mb-0">{{ number_format($renewalPending) }}</span>
            </div>
            <div class="col-auto">
              <img src="https://img.icons8.com/bubbles/70/restart.png" alt="restart"/>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">For Exhumation</h5>
              <span class="h2 font-weight-bold mb-0">{{ number_format($exhumationPending) }}</span>
            </div>
            <div class="col-auto">
              <img src="https://img.icons8.com/bubbles/70/cancel--v2.png" alt="cancel--v2"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="row">


    <div class="col-xl-4">
      <div class="card">
        <div class="card-header">
          <div class="title-wrap">
            <span class="title-dot"></span>
            <h5 class="h3 mb-0">Slots per Apartment</h5>
          </div>
        </div>

        <div class="card-body">

          <div class="form-group apt-picker">
            <label class="form-control-label mb-1">Apartment</label>

            <span class="left-icon" aria-hidden="true">

              <svg width="18" height="18" viewBox="0 0 24 24" stroke="#6b7280" fill="none" stroke-width="2">
                <path d="M12 21s-6-5.686-6-10a6 6 0 1 1 12 0c0 4.314-6 10-6 10z"/>
                <circle cx="12" cy="11" r="2.5"/>
              </svg>
            </span>

            <select id="apartmentSelect" class="select-apt">
              <option value="">— Select apartment —</option>
            </select>

            <button type="button" id="aptClearBtn" class="btn btn-outline-secondary clear-btn btn-sm" title="Clear">
              &times;
            </button>

            <span id="aptLoading" class="inline-spinner d-none" aria-live="polite">
              <span class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true"></span>
              <small class="text-muted">Loading…</small>
            </span>

            <div class="apt-hint">Pick an apartment to see its levels.</div>
          </div>


          <ul id="levelsList" class="list-group list-group-flush list list-levels">

          </ul>
        </div>
      </div>
    </div>


    <div class="col-xl-8">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col"><h3 class="mb-0">For Renewal</h3></div>
            <div class="col text-right">
              <a href="{{ route('renewals.index') }}" class="btn btn-sm btn-primary">See all</a>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table align-items-center table-flush" id="renewals-table">
            <thead class="thead-light">
              <tr>
                <th>Applicant's&nbsp;Name</th>
                <th>Relationship</th>
                <th>Name&nbsp;of&nbsp;Deceased</th>
                <th>Buried&nbsp;at</th>
                <th>Renewal&nbsp;Period</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($renewals as $renewal)
                @php
                  $periodText =
                    optional($renewal->renewal_start)->format('Y')
                    . ' – ' .
                    optional($renewal->renewal_end)->format('Y');
                @endphp
                <tr data-id="{{ $renewal->id }}">
                  <td class="cell-applicant">{{ $renewal->requesting_party }}</td>
                  <td class="cell-rel">{{ $renewal->relationship_to_deceased ?? '—' }}</td>
                  <td class="cell-deceased">{{ $renewal->deceased_attrs['name_of_deceased'] ?? '—' }}</td>
                  <td class="cell-buried">{{ $renewal->buried_at ?? '—' }}</td>
                  <td class="cell-period">{{ $periodText }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>
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

          <h6 class="text-muted mb-2">Deceased Details</h6>
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.10/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.10/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function () {

  var table = $('#renewals-table').DataTable({
    pageLength   : 7,
    lengthChange : true,
    lengthMenu   : [[5,10,25,50,100],[5,10,25,50,100]],
    pagingType   : 'full_numbers',
    info         : true,
    order        : [],
    language     : {
      search: "_INPUT_",
      searchPlaceholder: "Search…",
      paginate: { first: '<<', last: '>>', previous: '<', next: '>' },
      emptyTable: "No pending renewals."
    },
    dom:
      '<"row align-items-center my-2"'
        + '<"col-sm-12 col-md-6"l>'
        + '<"col-sm-12 col-md-6 d-flex justify-content-end"f>'
      + '>'
      + 't'
      + '<"row align-items-center my-2"'
        + '<"col-sm-12 col-md-6 d-flex align-items-center"i>'
        + '<"col-sm-12 col-md-6 d-flex justify-content-end"p>'
      + '>'
  });

  let $currentRow = null, currentId = null, loadedData = null;

  function setViewMode(isView) {
    const $fields = $('#editRenewalForm').find('input.er-field, textarea.er-field, select.er-field');
    $fields.prop('disabled', isView);
    $('#editRenewalForm').find('input.er-deceased').prop('disabled', true);

    const $btn = $('#erEditSaveBtn');
    $btn.text(isView ? 'Edit' : 'Save changes')
        .attr('data-mode', isView ? 'view' : 'save')
        .toggleClass('btn-danger', isView)
        .toggleClass('btn-success', !isView);
  }

  $('#renewals-table tbody').on('click', 'tr', function (e) {
    if ($(e.target).closest('a,button,input,select,textarea,label').length) return;

    table.$('tr.table-active').removeClass('table-active');
    $(this).addClass('table-active');

    $currentRow = $(this);
    currentId   = $currentRow.data('id');
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
      $('[name=remarks]').val(data.remarks || '');

      $('[name=dec_name]').val(data.deceased_name || '—');
      $('[name=dec_sex]').val(data.sex || '—');
      $('[name=dec_buried_at]').val(data.buried_at || ($currentRow.find('.cell-buried').text() || '—'));

      $('#editRenewalModal').modal('show');
    });
  });

  $('#erEditSaveBtn').on('click', function() {
    const mode = $(this).attr('data-mode');
    if (mode === 'view') {
      setViewMode(false);
      $('[name=requesting_party]').trigger('focus');
    } else {
      $('#editRenewalForm').trigger('submit');
    }
  });

  $('#erCancelBtn').on('click', function() {
    const mode = $('#erEditSaveBtn').attr('data-mode');
    if (mode === 'save' && loadedData) {
      $('[name=requesting_party]').val(loadedData.requesting_party || '');
      $('[name=applicant_address]').val(loadedData.applicant_address || '');
      $('[name=contact]').val(loadedData.contact || '');
      $('[name=relationship_to_deceased]').val(loadedData.relationship_to_deceased || '');
      $('[name=date_applied]').val(loadedData.date_applied || '');
      $('[name=amount_as_per_ord]').val(loadedData.amount_as_per_ord || '');
      $('[name=renewal_start]').val(loadedData.renewal_start || '');
      $('[name=renewal_end]').val(loadedData.renewal_end || '');
      $('[name=remarks]').val(loadedData.remarks || '');
      setViewMode(true);
    } else {
      $('#editRenewalModal').modal('hide');
    }
  });

  $('#editRenewalForm').on('submit', function (e) {
    e.preventDefault();
    if (!currentId) return;

    const $btn = $('#erEditSaveBtn').prop('disabled', true);
    const formData = $(this).serialize();

    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: formData,
      success: function (resp) {
        const periodText =
          ($('[name=renewal_start]').val() || '').slice(0,4) + ' – ' +
          ($('[name=renewal_end]').val() || '').slice(0,4);

        $currentRow.find('.cell-applicant').text($('[name=requesting_party]').val());
        $currentRow.find('.cell-rel').text($('[name=relationship_to_deceased]').val() || '—');

        if (resp.payload && resp.payload.deceased_name) {
          $currentRow.find('.cell-deceased').text(resp.payload.deceased_name);
        }
        if (resp.payload && resp.payload.buried_at) {
          $currentRow.find('.cell-buried').text(resp.payload.buried_at);
          $('[name=dec_buried_at]').val(resp.payload.buried_at);
        }
        $currentRow.find('.cell-period').text(periodText);

        table.row($currentRow).invalidate().draw(false);

        $('#editRenewalModal').one('hidden.bs.modal', function () {
          $('#saveSuccessText').text(resp.message || 'Saved successfully!');
          $('#saveSuccessModal').modal('show');
          setTimeout(function(){ $('#saveSuccessModal').modal('hide'); }, 1800);
        });
        $('#editRenewalModal').modal('hide');
      },
      error: function (xhr) {
        const $box = $('#editRenewalErrors').removeClass('d-none').empty();
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



  const $aptSel    = $('#apartmentSelect');
  const $aptClear  = $('#aptClearBtn');
  const $aptLoad   = $('#aptLoading');
  const $list      = $('#levelsList');
  const barColors  = ['bg-primary','bg-success','bg-info','bg-warning','bg-danger','bg-default','bg-orange','bg-yellow'];

  function setLoading(on) {
    $aptLoad.toggleClass('d-none', !on);
  }


  setLoading(true);
  $.getJSON(`{{ url('/') }}/api/burial-sites`, function(items){
    items.forEach(i => $aptSel.append(`<option value="${i.id}">${$('<div/>').text(i.name).html()}</option>`));
  }).always(function(){ setLoading(false); });


  $aptClear.on('click', function(){
    $aptSel.val('');
    $list.empty();
  });


  $aptSel.on('change', function(){
    const id = $(this).val();
    $list.empty();
    if (!id) return;

    setLoading(true);
    $.getJSON(`{{ url('/') }}/api/burial-sites/${id}/levels`, function(levels){
      if (!levels.length) {
        $list.append(`<li class="list-group-item text-muted px-0">No levels for this apartment.</li>`);
        return;
      }


      levels.sort((a,b) => (a.level_no||0)-(b.level_no||0)).forEach((l, idx) => {
        const color = barColors[idx % barColors.length];
        $list.append(`
          <li class="list-group-item px-0" data-level-id="${l.id}">
            <div class="row align-items-center">
              <div class="col">
                <h6>Level ${l.level_no}</h6>
                <small class="js-counts">—</small>
              </div>
              <div class="col-8">
                <div class="progress progress-xs mb-0" title="0%">
                  <div class="progress-bar ${color} js-bar" role="progressbar"
                       style="width:0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <div class="col text-end">
                <a class="btn btn-sm btn-success" href="{{ url('/levels') }}/${l.id}/grid">View</a>
              </div>
            </div>
          </li>
        `);
      });


      const requests = $list.find('li').map(function(){
        const $li = $(this);
        const lvlId = $li.data('level-id');
        return $.getJSON(`{{ url('/') }}/api/levels/${lvlId}/slots-progress`, function(sum){
          const total   = parseInt(sum.total || 0, 10);
          const busy    = parseInt(sum.busy || 0, 10);
          const percent = total ? Math.round((busy/total)*100) : 0;

          $li.find('.js-counts').text(`${busy.toLocaleString()} / ${total.toLocaleString()}`);
          $li.find('.js-bar')
             .css('width', percent + '%')
             .attr('aria-valuenow', percent);
          $li.find('.progress').attr('title', percent + '%');
        });
      }).get();

      $.when.apply($, requests).always(function(){ setLoading(false); });
    }).fail(function(){ setLoading(false); });
  });

});
</script>
@endpush
