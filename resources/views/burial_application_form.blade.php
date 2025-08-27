@extends('layouts.masterlayout')
@inject('carbon','Carbon\Carbon')

<style>
    .container.mt-3 { max-width: 1225px !important; }
    .form-control, .form-control-label{ color:black !important; text-transform:uppercase; }
    h4.text-red.text-uppercase{ color: #ff0000 !important; }
    body{ background-image:url(assets/img/bg_cemetery.png); }
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

    <input type="hidden" name="family_id" value="{{ request('family_id') }}">

    <h4 class="text-red text-uppercase mb-3"><u>Application Details</u></h4>
    <div class="row">
        <div class="col">
           <label class="form-control-label">
             <img src="https://img.icons8.com/external-flaticons-lineal-color-flat-icons/20/external-date-business-flaticons-lineal-color-flat-icons.png"/> Date Applied
           </label>
           <input name="date_applied" type="date" class="form-control" value="{{ now()->toDateString() }}">
        </div>
        <div class="col">
           <label class="form-control-label">
             <img src="https://img.icons8.com/doodle/20/apple-calendar--v1.png"/> Internment Schedule
           </label>

           <input name="internment_sched" type="datetime-local" class="form-control">
        </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3"><u>Burial Site Location</u></h4>
    <div class="row">
        <div class="col">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> Burial Site</label>
            <select id="burial_site_id" name="burial_site_id" class="form-control">
                <option value="">-- Select burial site --</option>
                @foreach($burial_sites as $site)
                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> Level</label>
            <select id="level_id" name="level_id" class="form-control">
                <option value="">-- Select level --</option>
            </select>
        </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3"><u>Applicant Details</u></h4>
    <div class="row">
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> First Name</label>
            <input id="applicant_first_name" name="applicant_first_name" class="form-control" type="text">
        </div>
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Middle Name</label>
            <input id="applicant_middle_name" name="applicant_middle_name" class="form-control" type="text">
        </div>
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Last Name</label>
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
            <input id="applicant_address" name="applicant_address" class="form-control" type="text">
        </div>
        <div class="col">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-phone.png"/> Contact No.</label>
            <input id="applicant_contact_no" name="applicant_contact_no" class="form-control" type="text">
        </div>
        <div class="col">
            <label class="form-control-label"><img src="https://img.icons8.com/stickers/20/family.png"/> Relationship to Deceased</label>
            <input name="relationship_to_deceased" class="form-control" type="text">
        </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3"><u>Deceased Information</u></h4>
    <div class="row">
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> First Name</label>
            <input name="deceased_first_name" class="form-control" type="text">
        </div>
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Middle Name</label>
            <input name="deceased_middle_name" class="form-control" type="text">
        </div>
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Last Name</label>
            <input name="deceased_last_name" class="form-control" type="text">
        </div>
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/name.png"/> Suffix</label>
            <input name="deceased_suffix" class="form-control" type="text" placeholder="JR, SR, III">
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/marker--v1.png"/> Address Before Death</label>
            <input name="address_before_death" class="form-control" type="text">
        </div>
        <div class="col-md-2">
            <label class="form-control-label"><img src="https://img.icons8.com/stickers/20/gender.png"/> Sex</label>
            <select name="sex" class="form-control">
                <option value="">----</option>
                <option value="MALE">MALE</option>
                <option value="FEMALE">FEMALE</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"/> Birth Date</label>
            <input name="date_of_birth" class="form-control" type="date">
        </div>
        <div class="col-md-2">
            <label class="form-control-label"><img src="https://img.icons8.com/stickers/20/self-destruct-button.png"/> Death Date</label>
            <input name="date_of_death" class="form-control" type="date">
        </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3"><u>Payment / Miscellaneous</u></h4>
    <div class="row">
        <div class="col-md-4">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/refund.png"/> Amount as per Ordinance.</label>
            <input name="amount_as_per_ord" class="form-control" type="text">
        </div>
        <div class="col-md-4">
            <label class="form-control-label"><img src="https://img.icons8.com/plasticine/20/headstone.png"/> Funeral Service</label>
            <input name="funeral_service" class="form-control" type="text">
        </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3"><u>Assigned Personnel</u></h4>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/safety-hat.png"/> Grave Digger</label>
            <select name="grave_diggers_id" class="form-control">
               <option value="">----</option>
               @foreach($grave_diggers as $g)
                   <option value="{{ $g->id }}">{{ $g->name }}</option>
               @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/manager.png"/> Verifier</label>
            <select name="verifiers_id" class="form-control">
               <option value="">----</option>
               @foreach($verifiers as $v)
                   <option value="{{ $v->id }}">{{ $v->name_of_verifier }}</option>
               @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-control-label">Other Information</label>
        <textarea name="other_info" rows="3" class="form-control" style="resize: none;"></textarea>
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

    $('#chooseSlot').on('click', function () {
        const siteId  = $('#burial_site_id').val(),
              levelId = $('#level_id').val();

        if (!siteId)  { alert('Please choose a burial site.'); return; }
        if (!levelId) { alert('Please choose a level.');       return; }

        $('#burialForm')
          .attr('action', `{{ url('/') }}/levels/${levelId}/reserve`)
          .submit();
    });
});
</script>
@endsection
