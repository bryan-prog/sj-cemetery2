
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
    .toggle-group { display: flex; gap: 12px; flex-wrap: wrap; }
    .inline-toggle input[type="checkbox"] { margin-right: 8px; }

    .gd-wrapper{ position:relative; }
    .gd-input.form-control{ cursor:pointer; min-height:38px; display:flex; align-items:center; gap:6px; padding-right:36px; }
    .gd-tags{ display:flex; gap:6px; overflow-x:auto; white-space:nowrap; scrollbar-width: thin; }
    .gd-tag{ display:inline-flex; align-items:center; gap:6px; padding:.15rem .5rem; border-radius:999px; background:#eef2f7; font-size:.875rem; border:1px solid #dbe1ea; }
    .gd-tag .gd-x{ font-weight:bold; border:none; background:transparent; line-height:1; cursor:pointer; }
    .gd-caret{ pointer-events:none; position:absolute; right:12px; top:50%; transform:translateY(-50%); }
    .gd-dropdown{ position:absolute; left:0; right:0; z-index:1000; background:#fff; border:1px solid #dee2e6; border-top:none; border-radius:0 0 .375rem .375rem; overflow:hidden; }
    .gd-dropdown .gd-list{ max-height:240px; overflow:auto; }
    .gd-item{ display:flex; align-items:center; gap:8px; padding:.4rem .5rem; }
    .gd-item:hover{ background:#f8f9fa; }
    .gd-search{ position:sticky; top:0; background:#fff; padding:.5rem; border-bottom:1px solid #eee; }


    .ind-modal { position: fixed; inset: 0; background: rgba(0,0,0,.45); display:flex; align-items:center; justify-content:center; z-index: 2000; }
    .ind-box   { background:#fff; border-radius:.5rem; width:420px; max-width:95vw; box-shadow:0 10px 30px rgba(0,0,0,.2); overflow:hidden; }
    .ind-head  { padding:.85rem 1rem; font-weight:600; border-bottom:1px solid #e9ecef; }
    .ind-body  { padding:1rem; }
    .ind-foot  { padding:.75rem 1rem; border-top:1px solid #e9ecef; display:flex; gap:.5rem; justify-content:flex-end; }
    .ind-error { color:#e3342f; font-size:.875rem; display:none; }

    @media screen and (max-width: 764px){
      .row{ display: flex !important; flex-direction: column !important; }
      .card-header.d-flex.justify-content-between { flex-wrap: wrap !important; }
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

    <div class="row mt-3">
      <div class="col-md-6">
        <label class="form-control-label">
          <img src="https://img.icons8.com/doodle/20/newsletter.png"/> Email Address
        </label>
        <input id="applicant_email"
               name="applicant_email"
               type="email"
               class="form-control"
               value="{{ old('applicant_email', request('prefill_applicant_email')) }}"
               placeholder="sample@gmail.com">
      </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3 d-flex align-items-center justify-content-between">
      <span><u>Deceased Information</u></span>
      <div class="toggle-group">
        <label class="mb-0 d-flex align-items-center inline-toggle">
          <input type="checkbox" id="sameAddressToggle"> Same Address
        </label>
      </div>
    </h4>

    <div class="row">
        <div class="col-md-3">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/name.png"/> First Name</label>
            <input id="deceased_first_name" name="deceased_first_name" class="form-control" type="text">
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
            <input id="address_before_death" name="address_before_death" class="form-control" type="text">
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
            <div class="d-flex align-items-center justify-content-between">
              <label class="form-control-label mb-0">
                <img src="https://img.icons8.com/doodle/20/refund.png"/> Amount as per Ordinance.
              </label>
            </div>
            <div class="input-group">
              <input id="amount_as_per_ord" name="amount_as_per_ord" class="form-control" type="text" placeholder="0.00" disabled>
            </div>
            <small id="oopNote" class="text-muted d-none">Filled via Order of Payment.</small>

            {{-- <small id="indigentNote" class="text-success d-none d-block mt-1">
              Indigent discount applied: ₱<span id="indigentNoteAmt">0.00</span>
            </small>
            <small id="waivedNote" class="text-danger d-none d-block mt-1">
              Amount is waived; base set to ₱0.00.
            </small> --}}


            <input type="hidden" id="is_indigent" name="is_indigent" value="0">
            <input type="hidden" id="indigent_discount" name="indigent_discount" value="0.00">
            <input type="hidden" id="is_waived" name="is_waived" value="0">
            <input type="hidden" id="misc_transfer_fee_hidden" name="misc_transfer_fee" value="0">
            <input type="hidden" id="misc_review_dc_hidden" name="misc_review_dc" value="0">
        </div>

        <div class="col-md-4">
            <label class="form-control-label"><img src="https://img.icons8.com/plasticine/20/headstone.png"/> Funeral Service</label>
            <input id="funeral_service" name="funeral_service" class="form-control" type="text">
        </div>

        <div class="col-md-4">
            <label class="form-control-label">
              <img src="https://img.icons8.com/doodle/20/money.png"/> Miscellaneous (plus fees)
            </label>
            <div class="border rounded p-2">
              <label class="inline-toggle d-flex align-items-center mb-1">
                <input type="checkbox" id="misc_transfer_fee"> Transfer Fee <span class="ml-1">(₱200)</span>
              </label>
              <label class="inline-toggle d-flex align-items-center mb-1">
                <input type="checkbox" id="misc_review_dc"> Review of Death Certificate <span class="ml-1">(₱100)</span>
              </label>

              <div class="d-flex align-items-center">
                <label class="inline-toggle d-flex align-items-center mb-0 mr-3">
                  <input type="checkbox" id="misc_indigent"> Indigent (Discount)
                </label>
                <button type="button" id="btnEditIndigent" class="btn btn-link btn-sm p-0 d-none">Edit amount</button>
              </div>

              <div class="d-flex align-items-center mt-1">
                <label class="inline-toggle d-flex align-items-center mb-0">
                  <input type="checkbox" id="misc_waived"> Waived
                </label>
              </div>

              <small class="text-muted d-block mt-1">
                Checked items are added on top of the base amount.
              </small>
            </div>
        </div>
    </div>


    <div id="indigentModal" class="ind-modal d-none" role="dialog" aria-modal="true" aria-labelledby="indigentTitle">
      <div class="ind-box">
        <div class="ind-head" id="indigentTitle">Indigent Discount</div>
        <div class="ind-body">
          <p class="mb-2">Enter the amount to discount from the base ordinance amount.</p>
          <div class="form-group mb-2">
            <label class="form-control-label">Discount Amount (₱)</label>
            <input type="number" min="0" step="0.01" class="form-control" id="indigentInput" placeholder="0.00">
          </div>
          <div class="ind-error" id="indigentError">Please enter a valid amount (not more than the base amount).</div>
        </div>
        <div class="ind-foot">
          <button type="button" class="btn btn-secondary btn-sm" id="indigentCancel">Cancel</button>
          <button type="button" class="btn btn-primary btn-sm" id="indigentSave">Save</button>
        </div>
      </div>
    </div>

    <hr class="mt-4 mb-4">
    <h4 class="text-red text-uppercase my-3"><u>Assigned Personnel</u></h4>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-control-label required"><img src="https://img.icons8.com/doodle/20/safety-hat.png"/> Grave Diggers</label>
           <select id="grave_diggers_id" name="grave_diggers_id[]" class="form-control d-none" multiple>
               <option value="" disabled>--Select can be Multiple--</option>
               @foreach($grave_diggers as $g)
                   <option value="{{ $g->id }}">{{ $g->name }}</option>
               @endforeach
            </select>
            <div id="gd-ui" class="gd-wrapper">
              <div class="gd-input form-control">
                <div class="gd-tags"></div>
                <span class="gd-caret">▾</span>
              </div>
              <div class="gd-dropdown d-none">
                <div class="gd-search">
                  <input type="text" class="form-control form-control-sm" placeholder="Search grave diggers…">
                  <small class="text-muted d-block mt-2">Select up to 5.</small>
                </div>
                <div class="gd-list"></div>
              </div>
            </div>
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

  <form id="saveReservationForm"
        action="{{ route('reservations.store') }}"
        method="POST"
        class="d-none">
    @csrf
  </form>
 </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(function () {

    const FEE_TRANSFER = 200;
    const FEE_REVIEW   = 100;

    const LS_TRANSFER      = 'burial.misc.transfer';
    const LS_REVIEW        = 'burial.misc.review';
    const LS_INDIGENT      = 'burial.misc.indigent';
    const LS_INDIGENT_AMT  = 'burial.misc.indigent_amt';
    const LS_WAIVED        = 'burial.misc.waived';

    let preselectLevelId = null;
    let baseAmount = null;
    const $amt = $('#amount_as_per_ord');

    let indigentOn       = (localStorage.getItem(LS_INDIGENT) === '1');
    let indigentDiscount = parseFloat(localStorage.getItem(LS_INDIGENT_AMT) || '0') || 0;
    let waivedOn         = (localStorage.getItem(LS_WAIVED) === '1');

    const $indigentCheckbox = $('#misc_indigent');
    const $btnEditIndigent  = $('#btnEditIndigent');
    const $indigentModal    = $('#indigentModal');
    const $indigentInput    = $('#indigentInput');
    const $indigentError    = $('#indigentError');
    const $indigentNote     = $('#indigentNote');
    const $indigentNoteAmt  = $('#indigentNoteAmt');
    const $isIndigent       = $('#is_indigent');
    const $indigentHidden   = $('#indigent_discount');

    const $waivedCheckbox   = $('#misc_waived');
    const $waivedNote       = $('#waivedNote');
    const $isWaived         = $('#is_waived');

    const $transferHidden   = $('#misc_transfer_fee_hidden');
    const $reviewHidden     = $('#misc_review_dc_hidden');

    function setBaseAmount(val){
      const n = Number(val);
      baseAmount = Number.isFinite(n) ? n : null;
    }
    function extrasTotal(){
      let sum = 0;
      if ($('#misc_transfer_fee').is(':checked')) sum += FEE_TRANSFER;
      if ($('#misc_review_dc').is(':checked'))   sum += FEE_REVIEW;
      return sum;
    }
    function formatMoney(n){ return Number(n).toFixed(2); }

    function updateIndigentUI(){
      const disabledByWaive = waivedOn;
      const showNote = indigentOn && indigentDiscount > 0 && !disabledByWaive;

      $indigentCheckbox.prop('disabled', disabledByWaive)
                       .closest('label').css('opacity', disabledByWaive ? .6 : 1);
      $btnEditIndigent.prop('disabled', disabledByWaive)
                      .toggleClass('d-none', !indigentOn || disabledByWaive);
      $indigentNote.toggleClass('d-none', !showNote);
      $indigentNoteAmt.text(formatMoney(indigentDiscount));

      $isIndigent.val(indigentOn ? '1' : '0');
      $indigentHidden.val(formatMoney(indigentDiscount));
    }

    function updateWaivedUI(){
      $waivedNote.toggleClass('d-none', !waivedOn);
      $isWaived.val(waivedOn ? '1' : '0');

      if (waivedOn) {
        if (indigentOn || indigentDiscount) {
          indigentOn = false;
          indigentDiscount = 0;
          $indigentCheckbox.prop('checked', false);
          localStorage.setItem(LS_INDIGENT, '0');
          localStorage.setItem(LS_INDIGENT_AMT, '0');
        }
      }
      updateIndigentUI();
    }

    function recomputeTotal(){
      const extras   = extrasTotal();
      const baseRaw  = Number.isFinite(baseAmount) ? baseAmount : 0;
      const baseEff  = waivedOn ? 0 : baseRaw;
      const discount = (indigentOn && !waivedOn) ? Math.min(indigentDiscount, baseEff) : 0;
      const total    = Math.max(0, (baseEff - discount)) + extras;

      $amt.val(formatMoney(total));
      updateWaivedUI();
    }


    $('#misc_transfer_fee').prop('checked', localStorage.getItem(LS_TRANSFER) === '1');
    $('#misc_review_dc').prop('checked',   localStorage.getItem(LS_REVIEW) === '1');
    $transferHidden.val($('#misc_transfer_fee').is(':checked') ? '1' : '0');
    $reviewHidden.val($('#misc_review_dc').is(':checked') ? '1' : '0');

    $('#misc_transfer_fee').on('change', function(){
      localStorage.setItem(LS_TRANSFER, this.checked ? '1' : '0');
      $transferHidden.val(this.checked ? '1' : '0');
      recomputeTotal();
    });
    $('#misc_review_dc').on('change', function(){
      localStorage.setItem(LS_REVIEW, this.checked ? '1' : '0');
      $reviewHidden.val(this.checked ? '1' : '0');
      recomputeTotal();
    });


    $indigentCheckbox.prop('checked', indigentOn);
    updateIndigentUI();

    $waivedCheckbox.prop('checked', waivedOn);
    updateWaivedUI();

    function openIndigentModal(){
      $indigentError.hide();
      $indigentInput.val(indigentDiscount ? formatMoney(indigentDiscount) : '');
      $indigentModal.removeClass('d-none');
    }
    function closeIndigentModal(){ $indigentModal.addClass('d-none'); }

    $indigentCheckbox.on('change', function(){
      if (this.checked) {
        indigentOn = true;
        openIndigentModal();
      } else {
        indigentOn = false;
        indigentDiscount = 0;
        localStorage.setItem(LS_INDIGENT, '0');
        localStorage.setItem(LS_INDIGENT_AMT, '0');
        updateIndigentUI();
        recomputeTotal();
      }
    });
    $btnEditIndigent.on('click', function(e){
      e.preventDefault();
      openIndigentModal();
    });

    $('#indigentCancel').on('click', function(){
      if (indigentOn && !indigentDiscount) {
        $indigentCheckbox.prop('checked', false);
        indigentOn = false;
        localStorage.setItem(LS_INDIGENT, '0');
      }
      closeIndigentModal();
      updateIndigentUI();
    });

    $('#indigentSave').on('click', function(){
      const raw = $indigentInput.val();
      let val = parseFloat(raw);
      if (!Number.isFinite(val) || val < 0) {
        $indigentError.text('Please enter a valid, non-negative amount.').show();
        return;
      }
      const baseRaw = Number.isFinite(baseAmount) ? baseAmount : 0;
      if (val > baseRaw) {
        $indigentError.text('Discount cannot be greater than the base amount.').show();
        return;
      }
      $indigentError.hide();

      indigentOn = true;
      indigentDiscount = val;
      localStorage.setItem(LS_INDIGENT, '1');
      localStorage.setItem(LS_INDIGENT_AMT, String(indigentDiscount));

      updateIndigentUI();
      recomputeTotal();
      closeIndigentModal();
    });

    $indigentModal.on('click', function(e){
      if (e.target === this) $('#indigentCancel').trigger('click');
    });

    $waivedCheckbox.on('change', function(){
      waivedOn = this.checked;
      localStorage.setItem(LS_WAIVED, waivedOn ? '1' : '0');
      updateWaivedUI();
      recomputeTotal();
    });


    $('#burial_site_id').on('change', function () {
        const siteId = $(this).val();
        const $lvl   = $('#level_id').empty().append('<option value="">-- Select level --</option>');
        if (!siteId) return;
        $.get(`{{ url('/') }}/api/burial-sites/${siteId}/levels`, function (levels) {
            levels.forEach(lvl => $lvl.append('<option value="'+lvl.id+'">'+lvl.level_no+'</option>'));
            if (preselectLevelId) {
                $('#level_id').val(preselectLevelId);
                preselectLevelId = null;
            }
        });
    });

    const urlParams      = new URLSearchParams(window.location.search);
    const defaultSiteId  = urlParams.get('default_site_id');
    const defaultLevelId = urlParams.get('default_level_id');
    const carriedSlotId  = urlParams.get('selected_slot_id');

    if (defaultSiteId) {
        preselectLevelId = defaultLevelId || null;
        $('#burial_site_id').val(defaultSiteId).trigger('change');
    }
    if (carriedSlotId) {
        $('#chooseSlot').text('Save Reservation').removeClass('btn-info').addClass('btn-success');
    }

    const oopTotal = urlParams.get('oop_total');
    const oopSel   = urlParams.get('oop_sel');
    if (oopTotal) {
        const formatted = Number(oopTotal).toFixed(2);
        $amt.val(formatted).prop('disabled', true).addClass('bg-light');
        $('#oopNote').removeClass('d-none');
        setBaseAmount(Number(oopTotal));
        recomputeTotal();
    }
    if ($('#btnOOP').length) {
      $('#btnOOP').on('click', function (e) {
          e.preventDefault();
          const currentUrl = window.location.href;
          let target = '{{ route('order_of_payment') }}' + '?return_to=' + encodeURIComponent(currentUrl);
          if (oopSel) target += '&selected=' + encodeURIComponent(oopSel);
          window.location.href = target;
      });
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
    $('#applicant_address').on('input', function(){ syncDeceasedAddressFromApplicant(); });

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
    $('#noLapidaToggle').on('change', function () { setNoLapida($(this).is(':checked')); });

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
        list.forEach(msg => $ul.append('<li>'+msg+'</li>'));
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


        recomputeTotal();

        const carriedSlotId = new URLSearchParams(window.location.search).get('selected_slot_id');
        if (carriedSlotId) {
            const $post = $('#saveReservationForm');
            $post.find('input:not([name="_token"])').remove();
            const add = (name, val) => {
                if (Array.isArray(val)) {
                    val.forEach(v => $post.append($('<input>', { type:'hidden', name: name+'[]', value: v })));
                } else {
                    $post.append($('<input>', { type:'hidden', name, value: val ?? '' }));
                }
            };


            add('no_lapida',            $('#no_lapida').val());
            add('deceased_first_name',  $('#deceased_first_name').val());
            add('deceased_middle_name', $('#deceased_middle_name').val());
            add('deceased_last_name',   $('#deceased_last_name').val());
            add('deceased_suffix',      $('#deceased_suffix').val());
            add('address_before_death', $('#address_before_death').val());
            add('date_of_birth',        $('#date_of_birth').val());
            add('date_of_death',        $('#date_of_death').val());
            add('sex',                  $('#sex').val());
            add('level_id',             $('#level_id').val());
            add('slot_id',              carriedSlotId);
            add('grave_diggers_id',     $('#grave_diggers_id').val());
            add('verifiers_id',         $('#verifiers_id').val());
            add('burial_site_id',       $('#burial_site_id').val());
            add('date_applied',         $('#date_applied').val());
            add('internment_sched',     $('#internment_sched').val());
            add('applicant_first_name', $('#applicant_first_name').val());
            add('applicant_middle_name',$('#applicant_middle_name').val());
            add('applicant_last_name',  $('#applicant_last_name').val());
            add('applicant_suffix',     $('#applicant_suffix').val());
            add('applicant_email',      $('#applicant_email').val());
            add('family_id',               $('input[name="family_id"]').val());
            add('applicant_address',       $('#applicant_address').val());
            add('applicant_contact_no',    $('#applicant_contact_no').val());
            add('relationship_to_deceased',$('#relationship_to_deceased').val());
            add('amount_as_per_ord',       $('#amount_as_per_ord').val());
            add('funeral_service',         $('#funeral_service').val());
            add('other_info',              $('#other_info').val());


            add('misc_transfer_fee', $('#misc_transfer_fee').is(':checked') ? 1 : 0);
            add('misc_review_dc',    $('#misc_review_dc').is(':checked') ? 1 : 0);
            add('is_indigent',       $('#is_indigent').val());
            add('indigent_discount', $('#indigent_discount').val());
            add('is_waived',         $('#is_waived').val());

            $post.trigger('submit');
            return;
        }
        const levelId = $('#level_id').val();
        $('#burialForm').attr('action', `{{ url('/') }}/levels/${levelId}/reserve`).submit();
    });


    const MAX_GD = 5;
    const $native = $('#grave_diggers_id');
    const $ui     = $('#gd-ui');
    const $input  = $ui.find('.gd-input');
    const $tags   = $ui.find('.gd-tags');
    const $menu   = $ui.find('.gd-dropdown');
    const $search = $ui.find('.gd-search input');
    const $list   = $ui.find('.gd-list');

    const OPTIONS = [];
    $native.find('option').each(function(){
      const v = $(this).val(); const t = $(this).text();
      if (!v || $(this).is(':disabled')) return;
      OPTIONS.push({ id: String(v), name: t });
    });

    function makeItemRow(opt){
      const row = $('<label class="gd-item"><input type="checkbox" value="'+opt.id+'"/><span>'+opt.name+'</span></label>');
      return row;
    }
    function renderList(filtered = OPTIONS){
      $list.empty();
      filtered.forEach(opt => {
        const $row = makeItemRow(opt);
        const selected = ($native.val() || []).map(String);
        if (selected.includes(String(opt.id))) {
          $row.find('input[type="checkbox"]').prop('checked', true);
        }
        $list.append($row);
      });
    }
    function renderTags(){
      $tags.empty();
      const selected = ($native.val() || []).map(String);
      selected.forEach(id => {
        const opt = OPTIONS.find(o => o.id === id);
        if (!opt) return;
        const $chip = $('<span class="gd-tag" data-id="'+opt.id+'"><span>'+opt.name+'</span><button type="button" class="gd-x" aria-label="Remove">&times;</button></span>');
        $tags.append($chip);
      });
    }
    function openMenu(){
      $menu.removeClass('d-none');
      $input.addClass('show');
      $search.val('').trigger('input').focus();
    }
    function closeMenu(){
      $menu.addClass('d-none');
      $input.removeClass('show');
    }
    $input.on('click', function(e){
      e.stopPropagation();
      $menu.hasClass('d-none') ? openMenu() : closeMenu();
    });
    $(document).on('click', function(){ closeMenu(); });
    $menu.on('click', function(e){ e.stopPropagation(); });
    $search.on('input', function(){
      const q = $(this).val().toString().toLowerCase().trim();
      if (!q) { renderList(OPTIONS); return; }
      const filtered = OPTIONS.filter(o => o.name.toLowerCase().includes(q));
      renderList(filtered);
    });
    $list.on('change', 'input[type="checkbox"]', function(){
      const id = $(this).val();
      let selected = ($native.val() || []).map(String);
      if (this.checked) {
        if (selected.length >= MAX_GD) {
          this.checked = false;
          $input.addClass('is-invalid');
          setTimeout(() => $input.removeClass('is-invalid'), 600);
          return;
        }
        if (!selected.includes(id)) selected.push(id);
      } else {
        selected = selected.filter(v => v !== id);
      }
      $native.val(selected).trigger('change');
      renderTags();
    });
    $tags.on('click', '.gd-x', function(e){
      e.stopPropagation();
      const id = $(this).closest('.gd-tag').data('id').toString();
      let selected = ($native.val() || []).map(String).filter(v => v !== id);
      $native.val(selected).trigger('change');
      renderTags();
      $list.find('input[type="checkbox"][value="'+id+'"]').prop('checked', false);
    });
    renderList(OPTIONS);
    renderTags();

    const multiSelectWithoutCtrl = ( elemSelector ) => {
      let options = [].slice.call(document.querySelectorAll(elemSelector + ' option'));
      options.forEach(function (element) {
          element.addEventListener('mousedown', function (e) {
                  e.preventDefault();
                  element.parentElement.focus();
                  this.selected = !this.selected;
                  return false;
              }, false );
      });
    }
    multiSelectWithoutCtrl('#grave_diggers_id');


    (function autoFillBurialAmount(){
      const $site = $('#burial_site_id');
      const oopTotal = new URLSearchParams(window.location.search).get('oop_total');
      const hasOOP = !!oopTotal;
      function computeDefaultAmountBySiteName(nameText){

        const total = 3000 + 500;
        return total;
      }
      function shouldFill() { return !hasOOP; }
      function maybeFill() {
        if (!shouldFill()) { recomputeTotal(); return; }
        const selectedText = ($site.find('option:selected').text() || '').trim();
        if (!selectedText) { setBaseAmount(null); recomputeTotal(); return; }
        const val = computeDefaultAmountBySiteName(selectedText);
        setBaseAmount(val);
        recomputeTotal();
      }
      $site.on('change', maybeFill);
      setTimeout(maybeFill, 0);
    })();

    setTimeout(recomputeTotal, 0);
});
</script>
@endsection
