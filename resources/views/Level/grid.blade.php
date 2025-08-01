
@extends('layouts.masterlayout')
@inject('carbon','Carbon\Carbon')

@php

$matrix = [];
foreach ($level->cells as $cell) {
    $matrix[$cell->row_no][$cell->col_no] = $cell;
}
$rows = max(array_keys($matrix));
$cols = max(array_map(fn ($r) => max(array_keys($r)), $matrix));

if (! function_exists('slotClass')) {
    function slotClass(string $s): string {
        return match ($s) {
            'available'             => 'slot-available',
            'occupied','reserved'   => 'slot-occupied',
            'exhumed'               => 'slot-exhumed',
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
    .form-control-label           { color:black!important;text-transform:uppercase }
    th,td                         { border-color:black!important }
    .grid-head                    { background:#39597b!important }
    th.grid-head                  { color:#fff!important }
    .card .table th               { padding:0 2px!important }
    .slot-box                     { margin-top:5px;width:32px;height:32px;border-radius:4px;
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:12px;cursor:pointer;pointer-events:auto;
                                    position:relative;z-index:2 }
    .slot-available               { background:#c6f6d5;color: black;font-weight: 500;}
    .slot-occupied                { background: #6495ED;cursor:help;color: white;font-weight: 600;}
    .slot-exhumed                 { background: #c6f6d5;cursor:help;color: white;font-weight: 600; }
    .slot-renewal-pending         { background: #faf089;cursor:help;color: white;font-weight: 600; }
    .slot-exhumation-pending      { background: #fbd38d;cursor:help;color: white;font-weight: 600; }
    .slot-group                   { display:flex;flex-direction:column;align-items:center;gap:0 }
    .legend-box                   { width:18px;height:18px;border-radius:4px;display:inline-block }
    .slot-penalty                 { background: #f87171; cursor:help;color: white;font-weight: 600;}
    .legend-item                  { display:flex;align-items:center;gap:.35rem;margin-right:.75rem;font-size:.78rem }
    .border-primary               { border:3px solid #0d6efd!important }
    th.grid-head                  { font-size: 1rem !important;}
</style>

@section('content')
<div class="container mt-3">
  <div class="card">
    <div class="card-header">
      <a href="{{ url('/Homepage') }}" class="btn btn-secondary btn-sm">
        Back to form
      </a>
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
            Click a <strong>GREEN</strong> slot for transfer destination.
          </div>
        @endif

        <div id="slotAlert" class="alert alert-warning alert-dismissible fade d-none" role="alert">
          <span class="message"></span>
          <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="d-flex justify-content-between flex-wrap mb-2">
          <div class="mb-2">
            <span style="color:black;">
            Apartment:&nbsp;<strong>{{ $level->apartment?->name ?? '—' }}</strong>
            &nbsp;|&nbsp;
            Level:&nbsp;<strong>{{ $level->level_no }}</strong>
            </span>
          </div>
          <div class="d-flex flex-wrap">
            <div class="legend-item"><h5 class="mb-0">Select a Slot:</h5></div>
            <div class="legend-item"><span class="legend-box slot-available"></span>Available</div>
            <div class="legend-item"><span class="legend-box slot-occupied"></span>Occupied</div>
            <div class="legend-item"><span class="legend-box slot-renewal-pending"></span>For&nbsp;Renewal</div>
            <div class="legend-item"><span class="legend-box slot-exhumation-pending"></span>For&nbsp;Exhumation</div>
             <div class="legend-item"><span class="legend-box slot-penalty"></span>For&nbsp;Penalty</div> {{-- NEW --}}
          </div>
        </div>


        <div class="table-responsive">
          <table class="table table-bordered text-center mb-0">
            <thead>
              <tr>
                <th class="grid-head">R\C</th>
                @for($c=1;$c<=$cols;$c++)
                  <th class="grid-head">{{ $c }}</th>
                @endfor
              </tr>
            </thead>
            <tbody>
              @for($r=1;$r<=$rows;$r++)
                <tr>
                  <th class="grid-head">{{ $r }}</th>
                  @for($c=1;$c<=$cols;$c++)
                    @php $cell = $matrix[$r][$c] ?? null; @endphp
                    <td style="padding:4px;">
                      @if($cell)
                        <div class="slot-group">
                          @foreach($cell->slots as $slot)
                            @php
                              $status = $slot->display_status;
                              $cls = slotClass($status);
                              $res = optional($slot->reservation);
                              $dec = $res->deceased;
                              $ren = $slot->renewals->first();
                            @endphp
                            <div  class="slot-box {{ $cls }}"
                                  data-slot-id="{{ $slot->id }}"
                                  data-status="{{ $status }}"
                                  data-reservation="{{ $res?->id }}"
                                  data-row="{{ $r }}"
                                  data-col="{{ $c }}"
                                  data-apartment="{{ $level->apartment?->name }}"
                                  data-level="{{ $level->level_no }}"
                                  data-deceased="{{ $dec?->name_of_deceased }}"
                                  data-sex="{{ $dec?->sex }}"
                                  data-birthdate="{{ $dec?->date_of_birth }}"
                                  data-deathdate="{{ $dec?->date_of_death }}"
                                  data-applicant="{{ $res?->applicant_name }}"
                                  data-contact="{{ $res?->applicant_contact_no }}"
                                  data-internment="{{ $res?->internment_sched }}"
                                  data-renewal-start="{{ $ren?->renewal_start }}"
                                  data-renewal-end="{{ $ren?->renewal_end }}"
                                  data-renewal-status="{{ $ren?->status }}">
                              {{ $slot->slot_no }}
                            </div>
                          @endforeach
                        </div>
                      @endif
                    </td>
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
@include('modals.renewal-request')


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function () {


  const slotM   = new bootstrap.Modal(document.getElementById('slotModal'));
  const chooseM = new bootstrap.Modal(document.getElementById('chooseSiteModal'));
  const formM   = new bootstrap.Modal(document.getElementById('exhumReqModal'));
  const renewalM= new bootstrap.Modal(document.getElementById('renewalModal'));

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


  $(document).on('click', '.slot-box', function () {
    const $box   = $(this);
    const status = $box.data('status');

    if (picking) {
      if (!isAvailable(status)) { flash('Pick a GREEN slot.'); return; }

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
    const st = $b.data('status') ?? '';
    const renStart  = $b.data('renewalStart')  ?? '';
    const renEnd    = $b.data('renewalEnd')    ?? '';
    const renStatus = $b.data('renewalStatus') ?? '';
    const statusLabel = st.toString().toUpperCase();

    let body = `
      <div class="row mb-2">
        <div class="col">
          <label class="form-control-label">
            <img src="https://img.icons8.com/doodle/20/ok.png"/> STATUS
          </label>
          <input class="form-control" value="${statusLabel}"
                 style="color:red!important;font-weight:500;">
        </div>
        <div class="col">
          <label class="form-control-label">
            <img src="https://img.icons8.com/doodle/20/city-buildings.png"/> APARTMENT
          </label>
          <input class="form-control" value="${$b.data('apartment')}">
        </div>
      </div>

      <div class="row mb-2">
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> LEVEL</label><input class="form-control" value="${$b.data('level')}"></div>
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> ROW</label><input class="form-control" value="${$b.data('row')}"></div>
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> COLUMN</label><input class="form-control" value="${$b.data('col')}"></div>
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"/> SLOT</label><input class="form-control" value="${$b.text().trim()}"></div>
      </div>

      <div class="row mb-2">
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/plasticine/20/headstone.png"/> DECEASED NAME</label><input class="form-control" value="${$b.data('deceased')}"></div>
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/stickers/20/gender.png"/> SEX</label><input class="form-control" value="${$b.data('sex')}"></div>
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"/> BIRTHDATE</label><input class="form-control" value="${$b.data('birthdate')}"></div>
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/stickers/20/self-destruct-button.png"/> DEATH DATE</label><input class="form-control" value="${$b.data('deathdate')}"></div>
      </div>

      <div class="row mb-2">
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/doodle/20/test-account.png"/> CONTACT PERSON</label><input class="form-control" value="${$b.data('applicant')}"></div>
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-phone.png"/> CONTACT NUMBER</label><input class="form-control" value="${$b.data('contact')}"></div>
        <div class="col"><label class="form-control-label"><img src="https://img.icons8.com/doodle/20/apple-calendar--v1.png"/> INTERNMENT</label><input class="form-control" value="${$b.data('internment')}"></div>
      </div>
    `;

    if (renStatus) {
      const badge = {pending:'warning', approved:'success', denied:'danger'}[renStatus] ?? 'secondary';
      body += `
        <div class="row mb-2">
          <div class="col">
            <label class="form-control-label">
              <img src="https://img.icons8.com/fluency/20/renew.png"/> RENEWAL PERIOD
            </label>
            <input class="form-control" value="${renStart} → ${renEnd}">
          </div>
          <div class="col">
            <label class="form-control-label">
              <img src="https://img.icons8.com/color/20/combo-chart--v1.png"/> RENEWAL STATUS
            </label>
            <div class="form-control bg-light">
              <span class="badge bg-${badge}" style="font-size:0.85rem;">
                ${renStatus.toUpperCase()}
              </span>
            </div>
          </div>
        </div>
      `;
    }

  const canRequestRenewal   = renStatus !== 'pending';
  const canRequestExhumation = renStatus !== 'pending';

  if (['occupied','reserved','renewal_pending','exhumation_pending', 'for_penalty'].includes(st)) {
    body += `
      ${canRequestExhumation ? `
        <button type="button"
                class="btn btn-danger me-2 mt-2 open-exhum-form"
                data-reservation="${$b.data('reservation')}"
                data-from-slot="${$b.data('slot-id')}">
          Request Exhumation
        </button>` : ''}

      ${canRequestRenewal ? `
        <button type="button"
                class="btn btn-warning mt-2 open-renewal-form"
                data-slot="${$b.data('slot-id')}">
          Request Renewal
        </button>` : ''}
    `;
  }
    $('#slotModal .modal-body').html(body);
  }


  $(document).on('click', '.open-exhum-form', function () {
    fromSlotId    = $(this).data('from-slot');
    reservationId = $(this).data('reservation');

    const $srcBox = $(`.slot-box[data-slot-id="${fromSlotId}"]`);
    const currLoc = `Apartment: ${$srcBox.data('apartment')} • `
                  + `L${$srcBox.data('level')} R${$srcBox.data('row')} `
                  + `C${$srcBox.data('col')} S${$.trim($srcBox.text())}`;

    $('#exhum_current_location').val(currLoc).prop('readonly', true);
    $('#exhum_deceased_name').val($srcBox.data('deceased') || '').prop('readonly', true);
    $('#exhum_date_of_death').val($srcBox.data('deathdate') || '').prop('readonly', true);
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

    const qs = $.param({
      transfer:       1,
      reservation_id: reservationId,
      from_slot_id:   fromSlotId,
      curr_loc:       $('#exhum_current_location').val()
    });
    window.location = `{{ url('/') }}/levels/${lvl}/grid?` + qs;
  });


  @if(request('transfer'))
    picking = true;
    $('#exhumationForm input[name=reservation_id]').val("{{ request('reservation_id') }}");
    $('#exhumationForm input[name=from_slot_id]').val("{{ request('from_slot_id') }}");
  @endif

  $(document).on('click', '.open-renewal-form', function () {

    slotM.hide();
    const slotId = $(this).data('slot');
    const $box   = $(`.slot-box[data-slot-id="${slotId}"]`);
    const resId  = $box.data('reservation');

    $('#renewalForm input[name=slot_id]').val(slotId);
    $('#renewalForm input[name=reservation_id]').val(resId);

    const today = new Date().toISOString().substring(0,10);
    $('#renewal_start').val(today);
    $('#renewal_end').val(`${parseInt(today.substring(0,4))+5}${today.substring(4)}`);

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
@endsection
