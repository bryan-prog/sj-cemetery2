@extends('layouts.masterlayout')
@inject('carbon','Carbon\Carbon')

@php
$matrix = [];
foreach ($level->cells as $cell) {
    $matrix[$cell->row_no][$cell->col_no] = $cell;
}

$rows = !empty($matrix) ? max(array_keys($matrix)) : 0;

$colsPerRow = [];
$maxCols = 0;
foreach ($matrix as $r => $colsArr) {
    $rowMax = !empty($colsArr) ? max(array_keys($colsArr)) : 0;
    $colsPerRow[$r] = $rowMax;
    if ($rowMax > $maxCols) $maxCols = $rowMax;
}

$activeFamilyId = request()->has('family_id') && request('family_id') !== ''
    ? (int) request('family_id')
    : null;

if (! function_exists('slotClass')) {
    function slotClass(string $s): string {
        return match ($s) {
            'available'             => 'slot-available',
            'occupied'              => 'slot-occupied',
            'reserved'              => 'slot-family-reserved',
            'exhumed'               => 'slot-exhumed',
            'for_renewal'           => 'slot-for-renewal',
            'renewal_pending'       => 'slot-renewal-pending',
            'exhumation_pending'    => 'slot-exhumation-pending',
            'for_penalty'           => 'slot-penalty',
            default                 => 'slot-exhumed',
        };
    }
}
@endphp

<style>
  .container.mt-3              { max-width: 1830px !important }
  .form-control,
  .form-control-label           { color:black!important;text-transform:none }
  th,td                         { border-color:black!important }
  .grid-head                    { background:#39597b!important }
  th.grid-head                  { color:#fff!important }
  .card .table th               { padding:0 2px!important }


  .slot-box {
    margin-top:5px;
    width:32px;
    height:50px;
    border-radius: 8px 8px 4px 4px;
    position: relative;
    display:flex;align-items:center;justify-content:center;
    font-size:12px;cursor:pointer;pointer-events:auto;
    z-index:2;
    transition: transform .08s ease, filter .12s ease, box-shadow .12s ease, background-color .12s ease;
  }

  .slot-box::before {
    content: '';
    position: absolute;
    top: 3px; left: 6px; right: 6px;
    height: 3px;
    border-radius: 2px;
    opacity: .35;
    background: currentColor;
    pointer-events: none;
  }


  .slot-available               { background:#c0c0c0; color:#111; font-weight:600; }
  .slot-family-reserved         { background:#2c3227; color:#ffffff; font-weight:700; }

  .slot-occupied                { background:#800000; color:#fff; font-weight:600; }
  .slot-exhumed                 { background:#c6f6d5; color:#22543d; font-weight:600; }
  .slot-renewal-pending         { background:#545AA7; color:#fff; font-weight:600; }
  .slot-for-renewal             { background:#AFDBF5; color:#0b0f12; font-weight:700; }
  .slot-exhumation-pending      { background:#fbd38d; color:#7b341e; font-weight:600; }
  .slot-penalty                 { background:#f87171; color:#fff; font-weight:600; }

  .slot-group                   { display:flex;flex-direction:column;align-items:center;gap:0 }
  .legend-box                   { width:18px;height:18px;border-radius:4px;display:inline-block }
  .legend-item                  { display:flex;align-items:center;gap:.35rem;margin-right:.75rem;font-size:.78rem }

  .slot-add {
    background: #10b981;
    color: #fff;
    font-weight: 700;
    border: 1px solid #0ea5a5;
  }
  .slot-add:hover { filter: brightness(0.95); }

  .slot-remove {
    background: #ef4444;
    color: #fff;
    font-weight: 700;
    border: 1px solid #dc2626;
  }
  .slot-remove:hover { filter: brightness(0.95); }

  .pulse { box-shadow: 0 0 0 0 rgba(13,110,253,.6); animation: pulse 1.6s infinite; }
  @keyframes pulse {
    0%   { box-shadow: 0 0 0 0 rgba(13,110,253,.6); }
    70%  { box-shadow: 0 0 0 12px rgba(13,110,253,0); }
    100% { box-shadow: 0 0 0 0 rgba(13,110,253,0); }
  }
  .dimmed { opacity: .25 !important; filter: grayscale(.1); }

  .find-panel {
    background:#f8fafc;
    border:1px solid #e9ecef;
    padding:.75rem;
    border-radius:.5rem;
  }
  #findMatches .list-group-item { cursor:pointer; }
  #findMatches .badge { font-weight:600; }

  td.cell-penalty { position: relative; }
  td.cell-penalty::after {
    content: "";
    position: absolute;
    inset: 1px;
    border: 1.5px solid #ef4444;
    border-radius: 1px;
    pointer-events: none;
  }


  .border-primary               { border:3px solid #0d6efd!important }
  th.grid-head                  { font-size: 1rem !important;}
</style>

@section('content')
<div class="container mt-3">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <a href="{{ url('/Homepage') }}" class="btn btn-default btn-sm">
        Back to Home
      </a>
      <div>
        <span style="color:black;">
          Apartment:&nbsp;<strong>{{ $level->apartment?->name ?? '—' }}</strong>
          &nbsp;|&nbsp;
          Level:&nbsp;<strong>{{ $level->level_no }}</strong>
        </span>
        @if($activeFamilyId)
          <span class="badge bg-info ms-2">APPLICANT ID: {{ $activeFamilyId }}</span>
        @endif
      </div>
    </div>

    <form method="POST" action="{{ route('reservations.store') }}">
      @csrf
      <input type="hidden" name="slot_id" id="slot_id">

      @foreach(request()->except(['slot_id','_token']) as $k=>$v)
        @if(is_array($v))
          @foreach($v as $x)<input type="hidden" name="{{ $k }}[]" value="{{ $x }}">@endforeach
        @else
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
      @endforeach

      <div class="card-body">
        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
          </div>
        @endif

        @if(request('transfer'))
          <div class="alert alert-info">
            Click a <strong>GREY</strong> slot for transfer destination.
          </div>
        @endif

        <div id="slotAlert" class="alert alert-warning alert-dismissible fade d-none" role="alert">
          <span class="message"></span>
          <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="d-flex justify-content-between flex-wrap mb-2">
          <div class="legend-item"><h5 class="mb-0">Select a Slot:</h5></div>
          <div class="d-flex flex-wrap">
            <div class="legend-item"><span class="legend-box" style="background:#b0b0b0;"></span>Available</div>
            <div class="legend-item"><span class="legend-box" style="background:#111111;"></span>Reserved</div>
            <div class="legend-item"><span class="legend-box slot-occupied"></span>Occupied</div>
            <div class="legend-item"><span class="legend-box slot-for-renewal"></span>For&nbsp;Renewal</div>
            <div class="legend-item"><span class="legend-box slot-renewal-pending"></span>Renewal&nbsp;Pending</div>
            <div class="legend-item"><span class="legend-box slot-exhumation-pending"></span>For&nbsp;Exhumation</div>
            <div class="legend-item"><span class="legend-box slot-penalty"></span>For&nbsp;Penalty</div>
          </div>
        </div>

        <div class="find-panel mb-2">
          <div class="row g-2 align-items-end">
            <div class="col-md-5">
              <label class="form-control-label">Search Deceased Name</label>
              <input id="findName" class="form-control" list="deceasedList">
              <datalist id="deceasedList"></datalist>
            </div>
            <div class="col-md-3">
              <label class="form-control-label">Date of Death (optional)</label>
              <input id="findDOD" type="date" class="form-control">
            </div>
            <div class="col-md-4 d-flex gap-2">
              <button type="button" id="btnFind" class="btn btn-primary">Search</button>
              <button type="button" id="btnFilter" class="btn btn-secondary" data-active="0">Dim non-matches</button>
              <button type="button" id="btnClear" class="btn btn-outline-dark">Clear</button>
            </div>
          </div>

          <div id="findMatches" class="list-group mt-2"></div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered text-center mb-0">
            <thead>
              <tr>
                <th class="grid-head">R\C</th>
                @for($c=1;$c<=$maxCols;$c++)
                  <th class="grid-head">{{ $c }}</th>
                @endfor
              </tr>
            </thead>
            <tbody>
              @for($r=1;$r<=$rows;$r++)
                <tr>
                  <th class="grid-head">{{ $r }}</th>
                  @for($c=1;$c<=$maxCols;$c++)
                    @php
                      $cell = ($colsPerRow[$r] ?? 0) >= $c ? ($matrix[$r][$c] ?? null) : null;
                    @endphp
                    @if($cell)
                      @php
                        $cellFamilyId = $cell->family_id ? (int) $cell->family_id : null;

                        $lockedCellForApplicant = $cellFamilyId
                            && (is_null($activeFamilyId) || $activeFamilyId !== $cellFamilyId);

                        $familyOwnsCell = $cellFamilyId && $activeFamilyId && ($activeFamilyId === $cellFamilyId);

                        $availableCount = $cell->slots->filter(fn($s) => $s->display_status === 'available')->count();
                        $canShowPlus  = $familyOwnsCell;
                        $canShowMinus = $familyOwnsCell && $availableCount > 0;

                        $nextNo = ($cell->slots->max('slot_no') ?? 0) + 1;

                        $hasPenaltySlot = $cell->slots->contains(fn($s) => $s->display_status === 'for_penalty');

                        $slotIdsForCell = $cell->slots->pluck('id');
                        $hasActiveRenewal = \App\Models\Renewal::whereIn('slot_id', $slotIdsForCell)
                            ->whereIn('status',['pending','approved'])
                            ->exists();

                        $cellPenalty = $hasPenaltySlot && !$hasActiveRenewal;
                      @endphp
                      <td class="{{ $cellPenalty ? 'cell-penalty' : '' }}" style="padding:4px;">
                        <div class="slot-group"
                             data-cell-family="{{ $cellFamilyId ?? '' }}"
                             data-locked-cell="{{ $lockedCellForApplicant ? 1 : 0 }}">

                          @foreach($cell->slots as $slot)
                            @php
                              $statusDb   = $slot->display_status;
                              $isAvailish = in_array($statusDb, ['available','exhumed'], true);
                              $statusUi   = ($lockedCellForApplicant && $isAvailish) ? 'reserved' : $statusDb;

                              $cls = slotClass($statusUi);

                              $res = optional($slot->reservation);
                              $dec = $res->deceased;
                              $ren = $slot->renewals->sortByDesc('id')->first();
                            @endphp

                            <div  class="slot-box {{ $cls }}"
                                  data-slot-id="{{ $slot->id }}"
                                  data-status="{{ $statusUi }}"
                                  data-locked="{{ $lockedCellForApplicant ? 1 : 0 }}"
                                  data-reservation="{{ $res?->id }}"
                                  data-row="{{ $r }}"
                                  data-col="{{ $c }}"
                                  data-apartment="{{ $level->apartment?->name }}"
                                  data-level="{{ $level->level_no }}"
                                  data-deceased="{{ $dec?->name_of_deceased }}"
                                  data-sex="{{ $dec?->sex }}"
                                  data-birthdate="{{ $dec?->date_of_birth }}"
                                  data-deathdate="{{ $dec?->dod_ymd ?? $dec?->date_of_death }}"
                                  data-applicant="{{ $res?->applicant_name }}"
                                  data-contact="{{ $res?->applicant_contact_no }}"
                                  data-internment="{{ $res?->internment_sched }}"
                                  data-renewal-start="{{ $ren?->renewal_start }}"
                                  data-renewal-end="{{ $ren?->renewal_end }}"
                                  data-renewal-status="{{ $ren?->status }}">
                              {{ $slot->slot_no }}
                            </div>
                          @endforeach

                          @if($canShowPlus)
                            <div class="slot-box slot-add add-slot"
                                 data-cell-id="{{ $cell->id }}"
                                 data-row="{{ $r }}"
                                 data-col="{{ $c }}"
                                 data-next="{{ $nextNo }}"
                                 title="Add one more slot for this cell">+
                            </div>
                          @endif

                          @if($canShowMinus)
                            <div class="slot-box slot-remove remove-slot"
                                 data-cell-id="{{ $cell->id }}"
                                 data-row="{{ $r }}"
                                 data-col="{{ $c }}"
                                 title="Remove the highest-numbered available slot">–
                            </div>
                          @endif
                        </div>
                      </td>
                    @else
                      <td style="padding:4px;"></td>
                    @endif
                  @endfor
                </tr>
              @endfor
            </tbody>
          </table>
        </div>

        <button class="btn btn-success mt-3"
                type="submit"
                onclick="return $('#slot_id').val()?true:alert('Choose a slot first!')">
          Save Reservation
        </button>
      </div>
    </form>
  </div>
</div>

@include('modals.renewal-request')
@include('modals.exhume-request')
@include('modals.slot-details')
@include('modals.choose-site')


<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom:0;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img src="https://img.icons8.com/bubbles/150/verified-account.png" alt="ok"/>
        <p id="successMessage" class="mt-2 mb-0" style="color:black;font-weight:500;text-transform:uppercase;">
          Request submitted successfully.
        </p>
      </div>
      <div class="modal-footer" style="border-top:0;">
        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
const ADD_SLOT_URL_TPL = @json(route('cells.slots.store',   ['cell' => '__CELL__']));
const DEL_SLOT_URL_TPL = @json(route('cells.slots.destroy', ['cell' => '__CELL__']));
</script>

<script>
$(function () {
  const slotM   = new bootstrap.Modal(document.getElementById('slotModal'));
  const chooseM = new bootstrap.Modal(document.getElementById('chooseSiteModal'));
  const formM   = new bootstrap.Modal(document.getElementById('exhumReqModal'), {
    backdrop: 'static',
    keyboard: false
  });
  const renewalM= new bootstrap.Modal(document.getElementById('renewalModal'));
  const successM= new bootstrap.Modal(document.getElementById('successModal'));


  document.addEventListener('show.bs.modal', (e) => {
  document.querySelectorAll('.modal.show').forEach(m => {
    if (m !== e.target) bootstrap.Modal.getInstance(m)?.hide();
  });
});

  function showSuccess(msg){
    $('#successMessage').text(msg || 'Request submitted successfully.');
    successM.show();
  }

  let picking       = false;
  let fromSlotId    = null;
  let reservationId = null;

  const isAvailable = st => (st === 'available' || st === 'exhumed');

  const flash = msg => {
    const $a = $('#slotAlert');
    $a.find('.message').text(msg);
    $a.removeClass('d-none').addClass('show');
    setTimeout(() => bootstrap.Alert.getOrCreateInstance($a[0]).close(), 2500);
  };

  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
  });


  $(document).on('click', '.slot-box', function () {
    if ($(this).hasClass('add-slot') || $(this).hasClass('remove-slot')) return;

    const $box   = $(this);
    const status = ($box.data('status') || '').toString();
    const locked = String($box.data('locked')) === '1';

    if (locked && isAvailable(status)) { flash('This slot is reserved for another family.'); return; }

    if (picking) {
      if (!isAvailable(status)) {
        flash('Pick a GREY slot.');
        return;
      }

      $('.slot-box[data-picked-transfer="1"]')
        .removeClass('border-warning pulse')
        .removeAttr('data-picked-transfer');

      $box.addClass('border-warning pulse')
          .attr('data-picked-transfer','1');

      $('#exhumationForm input[name=to_slot_id]').val($box.data('slot-id'));
      const destLoc = `${$box.data('apartment')} • L${$box.data('level')} R${$box.data('row')} C${$box.data('col')} S${$.trim($box.text())}`;
      $('#current_location_field').val(destLoc).prop('readonly', true);

      picking = false;
      formM.show();
      return;
    }

    if (!isAvailable(status)) {
      buildDetails($box);
      slotM.show();
      return;
    }


    $('.border-primary').removeClass('border-primary');
    $box.addClass('border-primary');
    $('#slot_id').val($box.data('slot-id'));
  });

  function buildDetails($b) {
    const st          = ($b.data('status') || '').toString();
    const renStart    = ($b.data('renewalStart')  || '').toString();
    const renEnd      = ($b.data('renewalEnd')    || '').toString();
    const renStatus   = ($b.data('renewalStatus') || '').toString();
    const statusLabel = st.toUpperCase();

    let body = `
      <div class="row mb-3">
        <div class="col">
          <label class="form-control-label">
            <img src="https://img.icons8.com/doodle/20/ok.png"/> STATUS
          </label>
          <input class="form-control" value="${statusLabel}"
                 style="color:red!important;font-weight:500;" readonly>
        </div>
        <div class="col">
          <label class="form-control-label">
            <img src="https://img.icons8.com/doodle/20/city-buildings.png"/> APARTMENT
          </label>
          <input class="form-control" value="${$b.data('apartment') || ''}" readonly>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> LEVEL</label>
          <input class="form-control" value="${$b.data('level') || ''}" readonly>
        </div>
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> ROW</label>
          <input class="form-control" value="${$b.data('row') || ''}" readonly>
        </div>
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> COLUMN</label>
          <input class="form-control" value="${$b.data('col') || ''}" readonly>
        </div>
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> SLOT</label>
          <input class="form-control" value="${$b.text().trim()}" readonly>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/plasticine/20/headstone.png"/> DECEASED NAME</label>
          <input class="form-control" value="${$b.data('deceased') || ''}" readonly>
        </div>
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/stickers/20/gender.png"/> SEX</label>
          <input class="form-control" value="${$b.data('sex') || ''}" readonly>
        </div>
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"/> BIRTHDATE</label>
          <input class="form-control" value="${$b.data('birthdate') || ''}" readonly>
        </div>
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/stickers/20/self-destruct-button.png"/> DEATH DATE</label>
          <input class="form-control" value="${$b.data('deathdate') || ''}" readonly>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/test-account.png"/> CONTACT PERSON</label>
          <input class="form-control" value="${$b.data('applicant') || ''}" readonly>
        </div>
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-phone.png"/> CONTACT NUMBER</label>
          <input class="form-control" value="${$b.data('contact') || ''}" readonly>
        </div>
        <div class="col">
          <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-calendar--v1.png"/> INTERNMENT</label>
          <input class="form-control" value="${$b.data('internment') || ''}" readonly>
        </div>
      </div>
    `;

    if (renStatus) {
      const badge = { pending:'warning', approved:'success', denied:'danger' }[renStatus.toLowerCase()] || 'secondary';
      body += `
        <div class="row mb-3">
          <div class="col">
            <label class="form-control-label">
              <img src="https://img.icons8.com/doodle/20/available-updates.png"/> RENEWAL PERIOD
            </label>
            <input class="form-control" value="${renStart} → ${renEnd}" readonly>
          </div>
          <div class="col">
            <label class="form-control-label">
              <img src="https://img.icons8.com/plasticine/20/info-squared.png"/> RENEWAL STATUS
            </label>
            <div class="form-control bg-light">
              <span class="badge bg-${badge}" style="font-size:0.85rem;" readonly>
                ${renStatus.toUpperCase()}
              </span>
            </div>
          </div>
        </div>
      `;
    }

    const statusRaw       = (renStatus || '').toString().toLowerCase();
    const slotUiStatus    = (st || '').toString().toLowerCase();
    const isPenalty       = (slotUiStatus === 'for_penalty');
    const isForRenewal    = (slotUiStatus === 'for_renewal');
    const hasPendingRen   = (statusRaw === 'pending');


    const canRequestRenewal    = (!hasPendingRen && (isPenalty || isForRenewal || statusRaw === 'denied'));
    const canRequestExhumation = (statusRaw !== 'pending');

    if (['occupied','reserved','renewal_pending','exhumation_pending','for_penalty','for_renewal'].includes(slotUiStatus)) {
      body += `
        ${canRequestExhumation ? `
          <button type="button"
                  class="btn btn-danger me-2 mt-2 open-exhum-form"
                  data-reservation="${$b.data('reservation') || ''}"
                  data-from-slot="${$b.data('slot-id') || ''}">
            Request Exhumation
          </button>` : ''}

        ${canRequestRenewal ? `
          <button type="button"
                  class="btn btn-warning mt-2 open-renewal-form"
                  data-slot="${$b.data('slot-id') || ''}">
            Request Renewal
          </button>` : ''}
      `;
    }

    $('#slotModal .modal-body').html(body);
  }


  $(document).on('click', '.add-slot', function () {
    const $btn   = $(this);
    const cellId = $btn.data('cell-id');
    const familyId = {{ $activeFamilyId ? (int)$activeFamilyId : 'null' }};
    if (!familyId) { flash('No family selected.'); return; }

    $btn.prop('disabled', true);

    $.post(ADD_SLOT_URL_TPL.replace('__CELL__', cellId), { count: 1, family_id: familyId })
      .done(function (resp) {
        if (!resp.created || !resp.created.length) return;

        const s = resp.created[0];
        const html = `
          <div class="slot-box slot-available"
               data-slot-id="${s.id}"
               data-status="available"
               data-locked="0"
               data-row="${$btn.data('row')}"
               data-col="${$btn.data('col')}"
               data-apartment="{{ $level->apartment?->name }}"
               data-level="{{ $level->level_no }}">
            ${s.slot_no}
          </div>
        `;
        $btn.before(html);

        $('.border-primary').removeClass('border-primary');
        $btn.prev('.slot-box').addClass('border-primary');
        $('#slot_id').val(s.id);

        const n = parseInt($btn.attr('data-next'), 10) + 1;
        $btn.attr('data-next', n).prop('disabled', false);
      })
      .fail(function (xhr) {
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message
                  : 'Cannot add slot for this cell.';
        flash(msg);
        $btn.prop('disabled', false);
      });
  });

  $(document).on('click', '.remove-slot', function () {
    const $btn   = $(this);
    const cellId = $btn.data('cell-id');
    const familyId = {{ $activeFamilyId ? (int)$activeFamilyId : 'null' }};
    if (!familyId) { flash('No family selected.'); return; }

    $btn.prop('disabled', true);

    $.ajax({
      url: DEL_SLOT_URL_TPL.replace('__CELL__', cellId),
      method: 'DELETE',
      data: { count: 1, family_id: familyId },
      success: function (resp) {
        if (!resp.deleted || !resp.deleted.length) { $btn.prop('disabled', false); return; }

        resp.deleted.forEach(d => {
          const $gone = $(`.slot-box[data-slot-id="${d.id}"]`);
          if ($gone.hasClass('border-primary')) {
            $('#slot_id').val('');
          }
          $gone.remove();
        });

        $btn.prop('disabled', false);
      },
      error: function (xhr) {
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message
                  : 'Cannot remove slot from this cell.';
        flash(msg);
        $btn.prop('disabled', false);
      }
    });
  });

  $(document).on('click', '.open-exhum-form', function () {
    const fromSlot = $(this).data('from-slot');
    fromSlotId    = fromSlot;
    reservationId = $(this).data('reservation');

    const $srcBox = $(`.slot-box[data-slot-id="${fromSlot}"]`);
    const currLoc = `Apartment: ${$srcBox.data('apartment')} • `
                  + `L${$srcBox.data('level')} R${$srcBox.data('row')} `
                  + `C${$srcBox.data('col')} S${$.trim($srcBox.text())}`;

    $('#exhum_current_location').val(currLoc).prop('readonly', true);
    $('#exhum_deceased_name').val($srcBox.data('deceased') || '').prop('readonly', true);
    $('#exhum_date_of_death').val(($srcBox.data('deathdate') || '').toString().substring(0,10)).prop('readonly', true);
    $('#exhum_contact').val('');

    slotM.hide();

    if (confirm(
        'Would you like to transfer remains inside the cemetery location?\n\n'
        + 'OK = Choose burial site & level\nCancel = Specify transfer location')
    ) {
      $('#modal_site').val('');
      $('#modal_level').html('<option value="">-- Select level --</option>');
      $('#loadGridBtn').prop('disabled', true);
      chooseM.show();
    } else {
      openExhumationForm('', '', false);
    }
  });

  $('#modal_site').on('change', function () {
    const id = $(this).val();
    $('#modal_level').html('<option value="">-- Select level --</option>');
    $('#loadGridBtn').prop('disabled', true);
    if (!id) return;

    $.get(`{{ url('/') }}/api/burial-sites/${id}/levels`, levels => {
      levels.forEach(l =>
        $('#modal_level').append(`<option value="${l.id}">${l.level_no}</option>`));
    });
  });

  $('#modal_level').on('change', () =>
    $('#loadGridBtn').prop('disabled', !$('#modal_level').val()));

  $('#loadGridBtn').on('click', function () {
    const lvl = $('#modal_level').val();
    if (!lvl) return;

    const $srcBox = $(`.slot-box[data-slot-id="${fromSlotId}"]`);
    const decName = ($srcBox.data('deceased')  || '').toString();
    const decDod  = ($srcBox.data('deathdate') || '').toString().substring(0,10);

    const qs = $.param({
      transfer:       1,
      reservation_id: reservationId,
      from_slot_id:   fromSlotId,
      curr_loc:       $('#exhum_current_location').val(),
      exhum_dec_name: decName,
      exhum_dod:      decDod
    });
    window.location = `{{ url('/') }}/levels/${lvl}/grid?` + qs;
  });

  @if(request('transfer'))
    picking = true;
    $('#exhumationForm input[name=reservation_id]').val("{{ request('reservation_id') }}");
    $('#exhumationForm input[name=from_slot_id]').val("{{ request('from_slot_id') }}");
  @endif

 $(document).on('click', '.open-renewal-form', function () {
  const slotId = $(this).data('slot');
  const $box   = $(`.slot-box[data-slot-id="${slotId}"]`);
  const resId  = $box.data('reservation');

  $box.closest('td').removeClass('cell-penalty');

  $('#renewalForm input[name=slot_id]').val(slotId);
  $('#renewalForm input[name=reservation_id]').val(resId);

  const today = new Date().toISOString().substring(0,10);
  $('#renewal_start').val(today);
  $('#renewal_end').val(`${parseInt(today.substring(0,4))+5}${today.substring(4)}`);


  slotM.hide();

  renewalM.show();
});

  $('#renewal_start').on('change', function () {
    const [y,m,d] = this.value.split('-').map(Number);
    if (!y) return;
    $('#renewal_end').val(`${y+5}-${String(m).padStart(2,'0')}-${String(d).padStart(2,'0')}`);
  });

  function openExhumationForm(destId, locLabel = '', readOnly = true) {
    $('#outsideFields').toggleClass('d-none', readOnly).toggle(!readOnly);

    $('#exhumationForm input[name=reservation_id]').val(reservationId);
    $('#exhumationForm input[name=from_slot_id]').val(fromSlotId);
    $('#exhumationForm input[name=to_slot_id]').val(destId);

    $('#current_location_field').val(locLabel).prop('readonly', readOnly);

    if (readOnly) {
      $('#exhum_deceased_name, #exhum_date_of_death').val('');
    }

    formM.show();
  }

  @if(session('renewal_success'))
    showSuccess(@json(session('renewal_success')));
  @endif
  @if(session('exhumation_success'))
    showSuccess(@json(session('exhumation_success')));
  @endif
  @if(session('success'))
    showSuccess(@json(session('success')));
  @endif

  const qs = new URLSearchParams(window.location.search);
  if(qs.get('renewal') === 'success'){
    showSuccess('Renewal request submitted successfully.');
  }
  if(qs.get('exhumation') === 'success'){
    showSuccess('Exhumation request submitted successfully.');
  }

  const allSlots = [];
  const nameSet = new Set();

  function norm(s) {
    return (s || '')
      .toString()
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
      .replace(/ñ/gi, 'n')
      .trim().toLowerCase();
  }

  $('.slot-box').each(function(){
    const $b = $(this);
    if ($b.hasClass('add-slot') || $b.hasClass('remove-slot')) return;

    const id   = $b.data('slot-id');
    const name = ($b.data('deceased') || '').toString().trim();
    const dod  = ($b.data('deathdate') || '').toString().trim();

    const item = {
      id,
      name,
      nameN: norm(name),
      dod,
      apt: ($b.data('apartment') || '').toString().trim(),
      lvl: ($b.data('level') || '').toString().trim(),
      row: ($b.data('row') || '').toString().trim(),
      col: ($b.data('col') || '').toString().trim(),
      slotNo: $.trim($b.text()),
      status: ($b.data('status') || '').toString().trim()
    };

    if (item.name) {
      allSlots.push(item);
      nameSet.add(item.name);

      $b.attr({
        'title': item.name,
        'data-bs-toggle': 'tooltip',
        'data-bs-placement': 'top'
      });
    } else {
      $b.removeAttr('title data-bs-toggle data-bs-placement');
    }
  });

  [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => {
    new bootstrap.Tooltip(el, { trigger: 'hover focus' });
  });

  const $dl = $('#deceasedList');
  [...nameSet].sort((a,b)=>a.localeCompare(b)).forEach(n => {
    $dl.append(`<option value="${$('<div/>').text(n).html()}"></option>`);
  });

  const $results = $('#findMatches');
  let lastMatchedIds = new Set();

  function clearHighlights() {
    $('.slot-box').removeClass('pulse border-warning');
    $('.slot-box').removeClass('dimmed');
    lastMatchedIds.clear();
  }

  function labelOf(item) {
    return `${item.apt} • L${item.lvl} R${item.row} C${item.col} S${item.slotNo}`;
  }

  function renderResults(matches) {
    $results.empty();

    if (!matches.length) {
      $results.append(`<div class="list-group-item text-muted">No matches found.</div>`);
      return;
    }

    matches.slice(0, 100).forEach(m => {
      $results.append(`
        <div class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>${$('<div/>').text(m.name).html()}</strong>
            ${m.dod ? `<span class="badge bg-light text-dark ms-1">DOD: ${$('<div/>').text(m.dod).html()}</span>` : ''}
            <div class="text-muted small">${$('<div/>').text(labelOf(m)).html()}</div>
          </div>
        </div>
      `);
    });
  }

  function doFind() {
    clearHighlights();

    const qName = norm($('#findName').val());
    const qDoD  = ($('#findDOD').val() || '').trim();

    if (!qName && !qDoD) {
      $results.empty().append(`<div class="list-group-item text-muted">Type a name (and/or pick a date) then press Search.</div>`);
      return;
    }

    const matches = allSlots.filter(s => {
      const nameOk = qName ? s.nameN.includes(qName) : true;
      const dodOk  = qDoD ? (s.dod || '').startsWith(qDoD) : true;
      return nameOk && dodOk;
    });

    renderResults(matches);
    matches.forEach(m => lastMatchedIds.add(m.id));
  }

  function applyDimming(active) {
    $('.slot-box').each(function(){
      const $b = $(this);
      if ($b.hasClass('add-slot') || $b.hasClass('remove-slot')) return;

      const id = $b.data('slot-id');
      const shouldDim = active && lastMatchedIds.size > 0 && !lastMatchedIds.has(id);
      $b.toggleClass('dimmed', !!shouldDim);
    });
  }

  $('#btnFind').on('click', doFind);
  $('#findName').on('keyup', e => { if (e.key === 'Enter') doFind(); });
  $('#findDOD').on('change', doFind);

  $('#btnFilter').on('click', function(){
    const isActive = $(this).attr('data-active') === '1';
    const next = isActive ? '0' : '1';
    $(this).attr('data-active', next);
    $(this).text(next === '1' ? 'Undim non-matches' : 'Dim non-matches');
    applyDimming(next === '1');
  });

  $('#btnClear').on('click', function(){
    $('#findName').val('');
    $('#findDOD').val('');
    $results.empty();
    $('#btnFilter').attr('data-active','0').text('Dim non-matches');
    clearHighlights();
  });

  const urlQS = new URLSearchParams(window.location.search);
  const focusId = urlQS.get('focus_slot_id');
  const findNameQS = urlQS.get('find');

  if (focusId) {
    const $box = $(`.slot-box[data-slot-id="${focusId}"]`);
    if ($box.length) {
      $('.border-primary').removeClass('border-primary');
      $box.addClass('border-primary pulse');
      $box[0].scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
      const st = ($box.data('status') || '').toString();
      if (st !== 'available' && st !== 'exhumed') {
        buildDetails($box);
        slotM.show();
      }
    }
  } else if (findNameQS) {
    $('#findName').val(findNameQS);
    doFind();
    $('#btnFilter').click();
  }
});
</script>

@if(request('curr_loc'))
<script>
$(function () {
  $('#exhum_current_location')
    .val(@json(request('curr_loc')))
    .prop('readonly', true);
});
</script>
@endif

@if(request('transfer'))
<script>
$(function () {
  const decName = @json(request('exhum_dec_name'));
  const decDod  = @json(request('exhum_dod'));

  if (decName) $('#exhum_deceased_name').val(decName).prop('readonly', true);
  if (decDod)  $('#exhum_date_of_death').val(String(decDod).substring(0,10)).prop('readonly', true);
});
</script>
@endif
@endsection
