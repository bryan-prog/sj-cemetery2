@extends('layouts.masterlayout')
@inject('carbon', 'Carbon\Carbon')

<style>
    .container.mt-5 { max-width: 1830px !important; }
    body            { background-image: url(assets/img/bg_cemetery.png); }
    .form-control-label, .form-control{ color:black !important;}
</style>

@section('content')
<div class="container mt-5">

  <div class="row mb-3 d-flex justify-content-end">
      <a href="{{ route('renewals.index') }}"><button class="btn btn-success mr-2">See Renewal Requests</button></a>
      <a href="{{ url('/exhumations/requests') }}"><button class="btn btn-primary mr-2">See Exhumation Requests</button></a>
      <a href="{{ route('burial.apply.gate') }}" class="btn btn-danger mr-2">Apply for Burial Permit</a>
      <a href="{{ url('/cemetery_data') }}"><button class="btn btn-info">See Database</button></a>
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
          <h5 class="h3 mb-0">Slots on Restos per Level</h5>
        </div>
        <div class="card-body">
         <ul class="list-group list-group-flush list my--3">
  @for ($lvl = 1; $lvl <= 7; $lvl++)
    @php
      $p = $levelProgress[$lvl] ?? ['percent'=>0,'busy'=>0,'total'=>0,'level_id'=>null];
      $colors = [
        1=>'bg-orange', 2=>'bg-success', 3=>'bg-primary',
        4=>'bg-yellow', 5=>'bg-default', 6=>'bg-danger', 7=>'bg-info'
      ];
    @endphp

    <li class="list-group-item px-0">
      <div class="row align-items-center">
        <div class="col">
          <h5 class="mb-0">Level {{ $lvl }}</h5>
          <small class="text-muted">
            {{ number_format($p['busy']) }} / {{ number_format($p['total']) }}
          </small>
        </div>

        <div class="col-8">
          <div class="progress progress-xs mb-0" title="{{ $p['percent'] }}% ">
            <div
              class="progress-bar {{ $colors[$lvl] ?? 'bg-primary' }}"
              role="progressbar"
              style="width: {{ $p['percent'] }}%;"
              aria-valuenow="{{ $p['percent'] }}"
              aria-valuemin="0"
              aria-valuemax="100">
            </div>
          </div>
        </div>

        <div class="col">
          <a href="{{ route('level.show', $p['level_id'] ?? $lvl) }}">
            <button class="btn btn-sm btn-success">View</button>
          </a>
        </div>
      </div>
    </li>
  @endfor
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
              @forelse ($renewals as $renewal)
                <tr data-id="{{ $renewal->id }}">
                  <th scope="row">{{ $renewal->requesting_party }}</th>
                  <td>{{ $renewal->relationship_to_deceased ?? '—' }}</td>
                  <td>{{ $renewal->deceased_attrs['name_of_deceased'] ?? '—' }}</td>
                  <td>{{ $renewal->buried_at ?? '—' }}</td>
                  <td>
                    {{ optional($renewal->renewal_start)->format('Y') }}
                    –
                    {{ optional($renewal->renewal_end)->format('Y') }}
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center">No pending renewals.</td></tr>
              @endforelse
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

          {{-- <div class="form-group">
            <label class="form-control-label">Remarks</label>
            <textarea class="form-control er-field" name="remarks" rows="2"></textarea>
          </div> --}}

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
        paginate: { first: '<<', last: '>>', previous: '<', next: '>' }
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
      $('[name=dec_buried_at]').val(data.buried_at || ($currentRow.find('td').eq(2).text() || '—'));

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


  $('#erCancelBtn').on('click', function() {
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
      $('[name=remarks]').val(loadedData.remarks || '');
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

        var periodText =
          ($('[name=renewal_start]').val() || '').slice(0,4)
          + ' – ' +
          ($('[name=renewal_end]').val() || '').slice(0,4);

        $currentRow.find('th[scope="row"]').text($('[name=requesting_party]').val());
        $currentRow.find('td').eq(0).text($('[name=relationship_to_deceased]').val() || '—');

        if (resp.payload && resp.payload.deceased_name) {
          $currentRow.find('td').eq(1).text(resp.payload.deceased_name);
        }
        if (resp.payload && resp.payload.buried_at) {
          $currentRow.find('td').eq(2).text(resp.payload.buried_at);
          $('[name=dec_buried_at]').val(resp.payload.buried_at);
        }
        $currentRow.find('td').eq(3).text(periodText);

        table.row($currentRow).invalidate().draw(false);


        loadedData = $.extend({}, loadedData, {
          requesting_party: $('[name=requesting_party]').val(),
          applicant_address: $('[name=applicant_address]').val(),
          contact: $('[name=contact]').val(),
          relationship_to_deceased: $('[name=relationship_to_deceased]').val(),
          date_applied: $('[name=date_applied]').val(),
          amount_as_per_ord: $('[name=amount_as_per_ord]').val(),
          renewal_start: $('[name=renewal_start]').val(),
          renewal_end: $('[name=renewal_end]').val(),
          remarks: $('[name=remarks]').val()
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
