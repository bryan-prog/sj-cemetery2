@extends('layouts.masterlayout')

<style>
  body{background-color: #797979 !important;}
  .container.mt-6 { max-width: 1265px !important; }
  .form-control, .form-control-label { color:black !important; text-transform:uppercase; }
  .card-header h3 { margin: 0; }
  .search-results { max-height: 320px; overflow-y: auto; }
  .table thead th { background:#f3f4f6; }
  span{ font-weight:600}
  p, .table{color:black !important;}
  .modal-header{border-bottom: 1PX solid #e9ecef !important;}
</style>

@section('content')
<div class="container mt-6">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3>
        <img src="https://img.icons8.com/doodle/30/goodnotes.png" alt="goodnotes"/>
        Apply for Burial Permit
      </h3>
      <a href="{{ url('/Homepage') }}" class="btn btn-default btn-sm">Back to Home</a>
    </div>

    <div class="card-body pb-5 pt-2">
      <div class="row" style="align-items: center;">
        <div class="col-6 pl-4">
          <div class="d-flex justify-content-center mb-2">
            <img src="https://img.icons8.com/bubbles/120/verified-account.png"/>
          </div>
          <h2 class="mb-3 mt-2" style="text-align: center;">Verification of Applicant Record</h2>
          <p style="color:black; text-align: justify;">To help us locate your records and streamline your application process, please confirm whether you are an <span class="text-red">existing applicant</span> or a <span class="text-red">new applicant.</span></p>
          <small style="text-align: justify;">
            Note: An <strong>existing applicant</strong> is someone who has previously reserved and has a record in our system.
          </small>
          <button id="openGate" class="btn btn-primary btn-lg btn-block mt-4"><i class="fa fa-arrow-right mr-2"></i> Continue</button>
        </div>
        <div class="col">
          <img src="{{asset('assets/img/form-img.png')}}" width="100%" >
        </div>
      </div>

    </div>
  </div>
</div>


<div class="modal fade" id="applicantGateModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <img src="https://img.icons8.com/plasticine/30/opened-folder.png" alt="opened-folder"/> APPLICANT TYPE
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body py-5">


        <div id="stepChoice">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="border rounded p-3 h-100">
                <div class="d-flex justify-content-center mb-2">
                  <img src="https://img.icons8.com/bubbles/100/browse-folder.png"/>
                </div>
                <h4 style="text-align: center;">Existing Applicant</h4>
                <p class="mb-3" style="font-size: 13px !important;">
                  To proceed, please search for your record using the family name (LAST, FIRST) or a previous applicant's name.
                </p>
                <button class="btn btn-outline-default w-100" id="btnExisting">Search Record</button>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-3 h-100">
                <div class="d-flex justify-content-center mb-2">
                  <img src="https://img.icons8.com/bubbles/100/plus-2-math.png"/>
                </div>
                <h4 style="text-align: center;">New Applicant</h4>
                <p class="mb-3" style="font-size: 13px !important;">
                  If you do not have an existing record, please choose this option to initiate a new application.
                </p>
                <button class="btn btn-outline-default w-100" id="btnNew">Start New Application</button>
              </div>
            </div>
          </div>
        </div>


        <div id="stepExisting" class="d-none">
          <div class="d-flex justify-content-between align-items-end mb-2">
            <div class="flex-grow-1 pe-2">
              <label class="form-control-label mb-1">
                <img class="mr-2" src="https://img.icons8.com/doodle/20/search--v1.png"/> Search Existing Family / Applicant
              </label>
              <input id="familySearch" type="text" class="form-control" placeholder="Type at least 2 characters...">
            </div>
            <button class="btn btn-primary" id="backToChoice1">Back</button>
          </div>

          <div class="table-responsive border rounded">
            <table class="table table-hover mb-0" id="familiesTable">
              <thead>
                <tr>
                  <th style="width: 28%">Family (LAST, FIRST M SUFFIX)</th>
                  <th style="width: 15%">Contact No.</th>
                  <th>Address</th>
                  <th style="width: 10%">Active Graves</th>
                  <th style="width: 14%">Last Burial</th>
                  <th style="width: 19%">Lot Location</th>
                  <th style="width: 8%"></th>
                </tr>
              </thead>
              <tbody><!-- filled by JS --></tbody>
            </table>
          </div>
        </div>


        <div id="stepNew" class="d-none">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="text-red"><img class="mr-2" src="https://img.icons8.com/stickers/20/add-property.png" alt="add-property"/><u>CREATE NEW APPLICATION</u></h3 >
            <button class="btn btn-primary btn-sm" id="backToChoice2">Back</button>
          </div>

          <form id="newFamilyForm">
            @csrf
            <div class="row mb-3">
              <div class="col-md-3">
                <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Last Name <span class="text-danger">*</span></label>
                <input type="text" name="last_name" class="form-control" placeholder="e.g. CRUZ" required>
              </div>
              <div class="col-md-3">
                <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> First Name</label>
                <input type="text" name="first_name" class="form-control" placeholder="e.g. JUAN">
              </div>
              <div class="col-md-3">
                <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Middle Name</label>
                <input type="text" name="middle_name" class="form-control" placeholder="e.g. SANTOS">
              </div>
              <div class="col-md-3">
                <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Suffix</label>
                <input type="text" name="suffix" class="form-control" placeholder="JR, SR, III">
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-phone.png" /> Contact No.</label>
                <input type="text" name="contact_no" class="form-control" maxlength="11" placeholder="09XXXXXXXXX">
              </div>
              <div class="col-md-8">
                <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> Address</label>
                <input type="text" name="address" class="form-control">
              </div>
            </div>

            <div class="mt-3 d-flex justify-content-end">
              <button type="submit" class="btn btn-success">Save & Proceed</button>
            </div>
          </form>

          <div id="newFamilyAlert" class="alert alert-danger mt-3 d-none"></div>
        </div>

      </div>
      <div class="modal-footer pt-2">
        <small class="text-muted">You can update family details later if needed.</small>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="editFamilyModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          Edit Applicant
        </h5>
        <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>

      <div class="modal-body">
        <form id="editFamilyForm">
          @csrf
          <input type="hidden" name="id" id="ef_id" />
          <div class="row mb-3">
            <div class="col-md-3">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Last Name <span class="text-danger">*</span></label>
              <input type="text" name="last_name" id="ef_last_name" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> First Name</label>
              <input type="text" name="first_name" id="ef_first_name" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Middle Name</label>
              <input type="text" name="middle_name" id="ef_middle_name" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Suffix</label>
              <input type="text" name="suffix" id="ef_suffix" class="form-control" placeholder="JR, SR, III">
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-phone.png"/> Contact No.</label>
              <input type="text" name="contact_no" id="ef_contact_no" class="form-control" maxlength="11" placeholder="09XXXXXXXXX">
            </div>
            <div class="col-md-8">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> Address</label>
              <input type="text" name="address" id="ef_address" class="form-control">
            </div>
          </div>
        </form>

        <div id="editFamilyAlert" class="alert alert-danger mt-3 d-none"></div>
        <div id="editFamilySuccess" class="alert alert-success mt-3 d-none">Changes saved.</div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary close">Close</button>
        <button class="btn btn-success" id="saveEditFamily">Save changes</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="editSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Success</h6>
        <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body text-center py-4">
        <img src="https://img.icons8.com/color/96/ok--v1.png" alt="success" style="width:60px;height:60px;"/>
        <h5>Applicant Updated</h5>
        <div>Changes have been saved.</div>
      </div>
      <div class="modal-footer justify-content-center">
        <button id="es_ok_btn" type="button" class="btn btn-success">OK</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  });


  const gate = new bootstrap.Modal(document.getElementById('applicantGateModal'));
  $('#openGate').on('click', () => gate.show());

  $('#applicantGateModal .modal-header .close, #applicantGateModal .modal-footer .close').on('click', () => gate.hide());

  $('#btnExisting').on('click', () => {
    $('#stepChoice').addClass('d-none');
    $('#stepNew').addClass('d-none');
    $('#stepExisting').removeClass('d-none');
    setTimeout(()=> $('#familySearch').trigger('focus'), 200);
  });
  $('#btnNew').on('click', () => {
    $('#stepChoice').addClass('d-none');
    $('#stepExisting').addClass('d-none');
    $('#stepNew').removeClass('d-none');
  });
  $('#backToChoice1, #backToChoice2').on('click', () => {
    $('#stepExisting, #stepNew').addClass('d-none');
    $('#stepChoice').removeClass('d-none');
    $('#familiesTable tbody').empty();
    $('#familySearch').val('');
    $('#newFamilyAlert').addClass('d-none').text('');
    document.getElementById('newFamilyForm').reset();
  });


  let searchTimer = null;
  let currentQuery = '';
  $('#familySearch').on('input', function(){
    const q = $(this).val().trim();
    currentQuery = q;
    clearTimeout(searchTimer);
    if (q.length < 2) { $('#familiesTable tbody').empty(); return; }
    searchTimer = setTimeout(() => doSearch(q), 250);
  });

  function displayFamilyName(r){
    const ln = (r.last_name || '').toString().trim();
    const fn = (r.first_name || '').toString().trim();
    const mn = (r.middle_name || '').toString().trim();
    const sf = (r.suffix || '').toString().trim();

    let base = ln ? ln.toUpperCase() : '';
    if (fn) base += (base ? ', ' : '') + fn.toUpperCase();
    if (mn) base += ' ' + mn.toUpperCase();
    if (sf) base += ' ' + sf.toUpperCase();
    return base || '—';
  }

  function doSearch(q){
    $.get(`{{ url('/api/families/search') }}`, { q }, function(rows){
      const $tb = $('#familiesTable tbody').empty();
      if (!rows || !rows.length) {
        $tb.append('<tr><td colspan="7" class="text-center text-muted">No matches found.</td></tr>');
        return;
      }
      rows.forEach(function(r){
        const tr = `<tr>
          <td>${escapeHtml(displayFamilyName(r))}</td>
          <td>${escapeHtml(r.contact_no || '')}</td>
          <td>${escapeHtml(r.address || '')}</td>
          <td class="text-center">${Number(r.active_graves || 0)}</td>
          <td>${escapeHtml(r.last_burial_at || '—')}</td>
          <td>${escapeHtml(r.lot_location || '—')}</td>
          <td class="text-end">
            <button class="btn btn-sm btn-primary use-family"
                    data-id="${r.id}"
                    data-site="${r.default_site_id || ''}"
                    data-level="${r.default_level_id || ''}">
              Select
            </button>
            <button class="btn btn-sm btn-outline-secondary edit-family" data-id="${r.id}">
              Edit
            </button>
          </td>
        </tr>`;
        $tb.append(tr);
      });
    });
  }

  $(document).on('click', '.use-family', function(){
    const id    = $(this).data('id');
    const site  = $(this).data('site');
    const level = $(this).data('level');

    const params = new URLSearchParams({ family_id: id });
    if (site)  params.append('default_site_id', site);
    if (level) params.append('default_level_id', level);

    window.location = `{{ route('burial_application_form') }}?${params.toString()}`;
  });


  $('#newFamilyForm').on('submit', function(e){
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this).entries());

    if (!data.last_name || !data.last_name.trim()){
      $('#newFamilyAlert').removeClass('d-none').text('Last name is required.');
      return;
    }

    $('#newFamilyAlert').addClass('d-none').text('');

    $.post(`{{ url('/api/families') }}`, data, function(res){
      if (res && res.id) {
        window.location = `{{ route('burial_application_form') }}?family_id=${encodeURIComponent(res.id)}`;
      } else {
        $('#newFamilyAlert').removeClass('d-none').text('Unexpected response from server.');
      }
    }).fail(function(xhr){
      const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to create family.';
      $('#newFamilyAlert').removeClass('d-none').text(msg);
    });
  });


  const editModal    = new bootstrap.Modal(document.getElementById('editFamilyModal'));
  const successModal = new bootstrap.Modal(document.getElementById('editSuccessModal'));


  $('#editFamilyModal .modal-header .close, #editFamilyModal .modal-footer .close').on('click', () => editModal.hide());


  $('#editSuccessModal .modal-footer .btn-success, #editSuccessModal .modal-header .close').on('click', () => successModal.hide());


  $(document).on('click', '.edit-family', function(){
    const id = $(this).data('id');
    $('#editFamilyAlert').addClass('d-none').text('');
    $('#editFamilySuccess').addClass('d-none');

    $.get(`{{ url('/api/families') }}/${id}`, function(fam){
      $('#ef_id').val(fam.id);
      $('#ef_last_name').val(fam.last_name || '');
      $('#ef_first_name').val(fam.first_name || '');
      $('#ef_middle_name').val(fam.middle_name || '');
      $('#ef_suffix').val(fam.suffix || '');
      $('#ef_contact_no').val(fam.contact_no || '');
      $('#ef_address').val(fam.address || '');
      editModal.show();
    }).fail(function(){
      alert('Failed to load applicant details.');
    });
  });


  $('#saveEditFamily').on('click', function(){
    const id = $('#ef_id').val();
    const payload = {
      last_name:   $('#ef_last_name').val(),
      first_name:  $('#ef_first_name').val(),
      middle_name: $('#ef_middle_name').val(),
      suffix:      $('#ef_suffix').val(),
      contact_no:  $('#ef_contact_no').val(),
      address:     $('#ef_address').val(),
      _method:     'PUT'
    };

    if (!payload.last_name || !payload.last_name.trim()){
      $('#editFamilyAlert').removeClass('d-none').text('Last name is required.');
      return;
    }

    $('#editFamilyAlert').addClass('d-none').text('');

    $.ajax({
      url: `{{ url('/api/families') }}/${id}`,
      type: 'POST',
      data: payload
    }).done(function(){
      $('#editFamilySuccess').removeClass('d-none');


      if (currentQuery && currentQuery.length >= 2) {
        doSearch(currentQuery);
      }


      editModal.hide();
      setTimeout(() => successModal.show(), 250);
    }).fail(function(xhr){
      const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save changes.';
      $('#editFamilyAlert').removeClass('d-none').text(msg);
    });
  });

  function escapeHtml(s){
    return (s||'').toString().replace(/[&<>"'`=\/]/g, function (c) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[c];
    });
  }
});
</script>
@endsection
