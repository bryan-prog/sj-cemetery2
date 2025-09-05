@extends('layouts.masterlayout')
@inject('carbon','Carbon\Carbon')

<style>
    .container.mt-3 { max-width: 1225px !important; }

    .form-control { color:black !important; text-transform:none !important; }
    input.form-control, textarea.form-control, select.form-control { text-transform:none !important; }
    .form-control::placeholder { text-transform:none !important; }

    .form-control-label{ color:black !important; text-transform:uppercase; }

    h4.text-red.text-uppercase{ color: #ff0000 !important; }
    body{ background-image:url(assets/img/bg_cemetery.png); }


    .is-invalid { border-color: #e3342f !important; box-shadow: 0 0 0 .2rem rgba(227,52,47,.15) !important; }
    label.required::after { content:" *"; color:#e3342f; }


    .inline-toggle { font-weight:normal; font-size:.95rem; }

    .toggle-group {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.inline-toggle input[type="checkbox"] {
  margin-right: 8px;
}
</style>

@section('content')
<div class="container mt-3">
 <div class="card">
  <div class="card-header d-flex justify-content-between">
     <h3 class="mb-0">
        <img src="https://img.icons8.com/doodle/30/goodnotes.png" alt="goodnotes"/>
        APPLICATION FOR BURIAL PERMIT
     </h3>
     <div class="d-flex gap-2">
        @if(request('family_id'))
          <span class="badge badge-default badge-lg align-self-center mr-2">FAMILY ID: {{ request('family_id') }}</span>
        @endif
        <a href="{{ url('/apply/burial') }}" class="btn btn-outline-default btn-sm">Change Applicant</a>
        <a href="{{ URL('/Homepage') }}" class="btn btn-default btn-sm">Back to Home</a>
     </div>
  </div>

  <form id="burialForm" method="GET">
   <div class="card-body">

    @if(session('success'))
       <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <div id="clientErrors" class="alert alert-danger d-none">
        <strong>Please review the following:</strong>
        <ul class="mb-0" id="clientErrorsList"></ul>
    </div>

    <input type="hidden" name="family_id" value="{{ request('family_id') }}">
    <input type="hidden" name="no_lapida" id="no_lapida" value="0">

    <h4 class="text-red text-uppercase mb-3"><u>Application Details</u></h4>
    <div class="row">
        <div class="col">
           <label class="form-control-label required">
             <img src="https://img.icons8.com/external-flaticons-lineal-color-flat-icons/20/external-date-business-flaticons-lineal-color-flat-icons.png"/> Date Applied
           </label>
           <input name="date_applied" id="date_applied" type="date" class="form-control" value="{{ now()->toDateString() }}">
        </div>
        <div class="col">
           <label class="form-control-label required">
             <img src="https://img.icons8.com/doodle/20/apple-calendar--v1.png"/> Internment Schedule
           </label>
           <input name="internment_sched" id="internment_sched" type="datetime-local" class="form-control">
        </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3"><u>Burial Site Location</u></h4>
    <div class="row">
        <div class="col">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/address.png"/> Burial Site</label>
            <select id="burial_site_id" name="burial_site_id" class="form-control">
                <option value="">-- Select burial site --</option>
                @foreach($burial_sites as $site)
                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/address.png"/> Level</label>
            <select id="level_id" name="level_id" class="form-control">
                <option value="">-- Select level --</option>
            </select>
        </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3"><u>Applicant Details</u></h4>
    <div class="row">
        <div class="col-md-3">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/name.png"/> First Name</label>
            <input id="applicant_first_name" name="applicant_first_name" class="form-control" type="text">
        </div>
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Middle Name</label>
            <input id="applicant_middle_name" name="applicant_middle_name" class="form-control" type="text">
        </div>
        <div class="col-md-3">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/name.png"/> Last Name</label>
            <input id="applicant_last_name" name="applicant_last_name" class="form-control" type="text">
        </div>
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Suffix</label>
            <input id="applicant_suffix" name="applicant_suffix" class="form-control" type="text" placeholder="JR, SR, III">
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/marker--v1.png"/> Address</label>
            <input id="applicant_address" name="applicant_address" class="form-control" type="text" placeholder="Applicant's Address">
        </div>
        <div class="col">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-phone.png"/> Contact No.</label>
            <input id="applicant_contact_no" name="applicant_contact_no" class="form-control" type="text">
        </div>
        <div class="col">
            <label class="form-control-label required"><img src="https://img.icons8.com/stickers/20/family.png"/> Relationship to Deceased</label>
            <input id="relationship_to_deceased" name="relationship_to_deceased" class="form-control" type="text">
        </div>
    </div>

    <hr class="mt-4 mb-4">
  <h4 class="text-red text-uppercase my-3 d-flex align-items-center justify-content-between">
  <span><u>Deceased Information</u></span>
  <div class="toggle-group">
    <label class="mb-0 d-flex align-items-center inline-toggle">
      <input type="checkbox" id="sameAddressToggle">
      Same Address
    </label>
    <label class="mb-0 d-flex align-items-center inline-toggle">
      <input type="checkbox" id="noLapidaToggle">
      No Lapida
    </label>
  </div>
</h4>

    <div class="row">
        <div class="col-md-3">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/name.png"/> First Name</label>
            <input id="deceased_first_name" name="deceased_first_name" class="form-control" type="text" placeholder="">
        </div>
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Middle Name</label>
            <input id="deceased_middle_name" name="deceased_middle_name" class="form-control" type="text">
        </div>
        <div class="col-md-3">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/name.png"/> Last Name</label>
            <input id="deceased_last_name" name="deceased_last_name" class="form-control" type="text">
        </div>
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Suffix</label>
            <input id="deceased_suffix" name="deceased_suffix" class="form-control" type="text" placeholder="JR, SR, III">
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-5">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/marker--v1.png"/> Address Before Death</label>
            <input id="address_before_death" name="address_before_death" class="form-control" type="text" placeholder="">
        </div>
        <div class="col-md-2">
            <label class="form-control-label required"><img src="https://img.icons8.com/stickers/20/gender.png"/> Sex</label>
            <select id="sex" name="sex" class="form-control">
                <option value="">----</option>
                <option value="MALE">MALE</option>
                <option value="FEMALE">FEMALE</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-control-label required"><img src="https://img.icons8.com/arcade/20/birth-date.png"/> Birth Date</label>
            <input id="date_of_birth" name="date_of_birth" class="form-control" type="date">
        </div>
        <div class="col-md-2">
            <label class="form-control-label required"><img src="https://img.icons8.com/stickers/20/self-destruct-button.png"/> Death Date</label>
            <input id="date_of_death" name="date_of_death" class="form-control" type="date">
        </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3"><u>Payment / Miscellaneous</u></h4>
    <div class="row">
        <div class="col-md-4">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/refund.png"/> Amount as per Ordinance.</label>
            <input id="amount_as_per_ord" name="amount_as_per_ord" class="form-control" type="text">
        </div>
        <div class="col-md-4">
            <label class="form-control-label"><img src="https://img.icons8.com/plasticine/20/headstone.png"/> Funeral Service</label>
            <input id="funeral_service" name="funeral_service" class="form-control" type="text">
        </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3"><u>Assigned Personnel</u></h4>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/safety-hat.png"/> Grave Digger</label>
            <select id="grave_diggers_id" name="grave_diggers_id" class="form-control">
               <option value="">----</option>
               @foreach($grave_diggers as $g)
                   <option value="{{ $g->id }}">{{ $g->name }}</option>
               @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/manager.png"/> Verifier</label>
            <select id="verifiers_id" name="verifiers_id" class="form-control">
               <option value="">----</option>
               @foreach($verifiers as $v)
                   <option value="{{ $v->id }}">{{ $v->name_of_verifier }}</option>
               @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-control-label">Other Information</label>
        <textarea id="other_info" name="other_info" rows="3" class="form-control" style="resize: none;"></textarea>
    </div>

    <div class="d-flex justify-content-end">
       <button type="button" id="chooseSlot" class="btn btn-info">Proceed to Location</button>
    </div>
   </div>
  </form>
 </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(function () {
    let preselectLevelId = null;


    $('#burial_site_id').on('change', function () {
        const siteId = $(this).val();
        const $lvl   = $('#level_id').empty().append('<option value="">-- Select level --</option>');
        if (!siteId) return;
        $.get(`{{ url('/') }}/api/burial-sites/${siteId}/levels`, function (levels) {
            levels.forEach(lvl => $lvl.append(`<option value="${lvl.id}">${lvl.level_no}</option>`));
            if (preselectLevelId) {
                $('#level_id').val(preselectLevelId);
                preselectLevelId = null;
            }
        });
    });


    const urlParams       = new URLSearchParams(window.location.search);
    const defaultSiteId   = urlParams.get('default_site_id');
    const defaultLevelId  = urlParams.get('default_level_id');

    if (defaultSiteId) {
        preselectLevelId = defaultLevelId || null;
        $('#burial_site_id').val(defaultSiteId).trigger('change');
    }

    @if(request('family_id'))
      $.get(`{{ url('/api/families/search') }}`, { id: '{{ request('family_id') }}' }, function(rows){
        const f = Array.isArray(rows) && rows.length ? rows[0] : null;
        if (f) {
          $('#applicant_first_name').val(f.first_name || '');
          $('#applicant_middle_name').val(f.middle_name || '');
          $('#applicant_last_name').val(f.last_name || '');
          $('#applicant_suffix').val(f.suffix || '');

          if (f.address) $('#applicant_address').val(f.address);
          if (f.contact_no) $('#applicant_contact_no').val(f.contact_no);

          if (!defaultSiteId && f.default_site_id) {
              preselectLevelId = f.default_level_id || null;
              $('#burial_site_id').val(f.default_site_id).trigger('change');
          }
        }
      });
    @endif


    function syncDeceasedAddressFromApplicant() {
        if ($('#sameAddressToggle').is(':checked')) {
            const addr = $('#applicant_address').val() || '';
            $('#address_before_death').val(addr).prop('readonly', true);
        }
    }
    $('#sameAddressToggle').on('change', function(){
        if ($(this).is(':checked')) {
            syncDeceasedAddressFromApplicant();
        } else {
            $('#address_before_death').prop('readonly', false).val('');
        }
        hideClientErrors();
    });

    $('#applicant_address').on('input', function(){
        syncDeceasedAddressFromApplicant();
    });


    function setNoLapida(on) {
        const val = on ? '1' : '0';
        $('#no_lapida').val(val);

        const $first  = $('#deceased_first_name');
        const $middle = $('#deceased_middle_name');
        const $last   = $('#deceased_last_name');
        const $suffix = $('#deceased_suffix');
        const $addr   = $('#address_before_death');
        const $sex    = $('#sex');
        const $dob    = $('#date_of_birth');
        const $dod    = $('#date_of_death');

        if (on) {

            $first.val('NO LAPIDA').prop('readonly', true);
            [$middle,$last,$suffix].forEach($i => $i.prop('readonly', true).val(''));

            [$sex,$dob,$dod].forEach($i => $i.prop('disabled', true).val(''));

            $addr.prop('readonly', true);
        } else {

            [$first,$middle,$last,$suffix].forEach($i => $i.prop('readonly', false).val(''));
            $addr.prop('readonly', $('#sameAddressToggle').is(':checked'));
            if (!$('#sameAddressToggle').is(':checked')) $addr.val('');
            [$sex,$dob,$dod].forEach($i => $i.prop('disabled', false).val(''));
        }

        hideClientErrors();
    }
    $('#noLapidaToggle').on('change', function () {
        setNoLapida($(this).is(':checked'));
    });


    function clearInvalids() { $('.is-invalid').removeClass('is-invalid'); }
    function markInvalid(selector) { $(selector).addClass('is-invalid'); }

    function hideClientErrors() {
        $('#clientErrors').addClass('d-none');
        $('#clientErrorsList').empty();
        clearInvalids();
    }
    function showClientErrors(list) {
        const $box = $('#clientErrors');
        const $ul  = $('#clientErrorsList').empty();
        list.forEach(msg => $ul.append(`<li>${msg}</li>`));
        $box.removeClass('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateFormBeforeProceed() {
        const errors = [];
        const noLapida = $('#no_lapida').val() === '1';


        if (!$('#date_applied').val()) { errors.push('Date Applied is required.'); markInvalid('#date_applied'); }
        if (!$('#internment_sched').val()) { errors.push('Internment Schedule is required (YYYY-MM-DDThh:mm).'); markInvalid('#internment_sched'); }


        if (!$('#burial_site_id').val()) { errors.push('Burial Site is required.'); markInvalid('#burial_site_id'); }
        if (!$('#level_id').val()) { errors.push('Level is required.'); markInvalid('#level_id'); }


        if (!$('#applicant_first_name').val()?.trim()) { errors.push('Applicant First Name is required.'); markInvalid('#applicant_first_name'); }
        if (!$('#applicant_last_name').val()?.trim())  { errors.push('Applicant Last Name is required.'); markInvalid('#applicant_last_name'); }
        if (!$('#relationship_to_deceased').val()?.trim()) { errors.push('Relationship to Deceased is required.'); markInvalid('#relationship_to_deceased'); }


        if (!$('#grave_diggers_id').val()) { errors.push('Grave Digger is required.'); markInvalid('#grave_diggers_id'); }
        if (!$('#verifiers_id').val())     { errors.push('Verifier is required.');     markInvalid('#verifiers_id'); }


        if (!noLapida) {
            if (!$('#deceased_first_name').val()?.trim()) { errors.push('Deceased First Name is required (or toggle No Lapida).'); markInvalid('#deceased_first_name'); }
            if (!$('#deceased_last_name').val()?.trim())  { errors.push('Deceased Last Name is required (or toggle No Lapida).');  markInvalid('#deceased_last_name'); }

            if (!$('#address_before_death').val()?.trim()) { errors.push('Address Before Death is required (or toggle No Lapida).'); markInvalid('#address_before_death'); }
            const dob = $('#date_of_birth').val(), dod = $('#date_of_death').val();
            if (!dob) { errors.push('Birth Date is required (or toggle No Lapida).'); markInvalid('#date_of_birth'); }
            if (!dod) { errors.push('Death Date is required (or toggle No Lapida).'); markInvalid('#date_of_death'); }
            const sex = $('#sex').val();
            if (!sex) { errors.push('Sex is required (or toggle No Lapida).'); markInvalid('#sex'); }
            if (dob && dod && dob > dod) {
                errors.push('Death Date must be after or equal to Birth Date.');
                markInvalid('#date_of_birth'); markInvalid('#date_of_death');
            }
        } else {

            if (!$('#deceased_first_name').val()) $('#deceased_first_name').val('NO LAPIDA');
        }

        if (errors.length) {
            const $first = $('.is-invalid').first();
            if ($first.length) { $first[0].scrollIntoView({ behavior:'smooth', block:'center' }); $first.focus(); }
        }
        return errors;
    }

    $('#chooseSlot').on('click', function () {
        hideClientErrors();
        const errors = validateFormBeforeProceed();
        if (errors.length) { showClientErrors(errors); return; }


        const levelId = $('#level_id').val();
        $('#burialForm')
          .attr('action', `{{ url('/') }}/levels/${levelId}/reserve`)
          .submit();
    });
});
</script>
@endsection
