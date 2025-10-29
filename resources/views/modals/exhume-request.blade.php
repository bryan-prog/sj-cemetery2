<div class="modal fade" id="exhumReqModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="exhumationForm" method="POST" action="{{ route('exhumations.store') }}">
        @csrf
        <input type="hidden" name="reservation_id">
        <input type="hidden" name="from_slot_id">
        <input type="hidden" name="to_slot_id">

        <div class="modal-header">
          <h5 class="modal-title mb-0" style="display:flex;align-items:center;gap:.5rem;">
            <img class="mr-2" src="https://img.icons8.com/doodle/30/information.png" alt="i"/>
            EXHUMATION&nbsp;REQUEST
          </h5>
          <button type="button"
                  class="close exhum-close"
                  data-dismiss="modal"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                  style="background:transparent;border:0;padding:0;margin-left:.25rem;font-size:1.25rem;line-height:1;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/test-account.png" alt="deceased">
                Name&nbsp;of&nbsp;Deceased
              </label>
              <input type="text" id="exhum_deceased_name" class="form-control" readonly>
            </div>
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/external-xnimrodx-lineal-color-xnimrodx/20/external-grave-calendar-xnimrodx-lineal-color-xnimrodx.png" alt="dod"/>
                Date&nbsp;of&nbsp;Death
              </label>
              <input type="date" id="exhum_date_of_death" class="form-control" readonly>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/arcade/20/birth-date.png" alt="date">
                Date&nbsp;Applied
              </label>
              <input type="date" name="date_applied" class="form-control" value="{{ now()->toDateString() }}">
            </div>
            <div class="col-md-8">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/test-account.png" alt="party">
                Requesting&nbsp;Party&nbsp;Name
              </label>
              <input type="text" name="requesting_party" class="form-control" style="text-transform:uppercase;">
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-12">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/address.png" alt="addr">
                Address
              </label>
              <input type="text" name="address" id="exhum_address" class="form-control" style="text-transform:uppercase;">
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-12">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/address.png" alt="loc">
                Current&nbsp;Location
              </label>
              <input type="text" id="exhum_current_location" class="form-control" readonly>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="exhum_for_cremation" name="for_cremation" value="1">
                <label class="form-check-label" for="exhum_for_cremation" style="font-weight:600;">For Cremation</label>
              </div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-8">
              <label id="transfer_location_label" class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/address.png" alt="to">
                Transfer&nbsp;Location
              </label>
              <input type="text" name="current_location" id="current_location_field" class="form-control" readonly>
            </div>
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/stickers/20/family.png" alt="rel">
                Relationship&nbsp;to&nbsp;Deceased
              </label>
              <input type="text" name="relationship_to_deceased" class="form-control" style="text-transform:uppercase;">
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/apple-phone.png" alt="phone">
                Contact&nbsp;Number
              </label>
              <input type="text" name="contact" id="exhum_contact" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/refund.png" alt="fee">
                Payment&nbsp;as&nbsp;per&nbsp;Ord.
              </label>
              <input
                type="number"
                name="amount_as_per_ord"
                id="exhum_amount_as_per_ord"
                class="form-control"
                value="3500"
                min="3500"
                max="3500"
                step="1"
                readonly
                onwheel="return false"
                onkeydown="return false"
              >
              <small class="text-muted d-block mt-1">
                Fixed amount as per ordinance.
              </small>
            </div>
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/manager.png" alt="verifier">
                Verifier
              </label>
              <select name="verifiers_id" class="form-control">
                <option value="">----</option>
                @foreach($verifiers as $v)
                  <option value="{{ $v->id }}">{{ $v->name_of_verifier }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-danger" type="submit">Submit&nbsp;Request</button>
          <button type="button"
                  class="btn btn-secondary exhum-close"
                  data-dismiss="modal"
                  data-bs-dismiss="modal">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(function(){
  const $cb = $('#exhum_for_cremation');
  const $label = $('#transfer_location_label');
  const $loc   = $('#current_location_field');

  function syncCremationUI(){
    if ($cb.is(':checked')) {
      $label.html('<img src="https://img.icons8.com/doodle/20/address.png" alt="to"> Cremation&nbsp;Location');
      if ($loc.is('[readonly]')) {
      } else {
        $loc.attr('placeholder','e.g., Crematorium / Address');
      }
    } else {
      $label.html('<img src="https://img.icons8.com/doodle/20/address.png" alt="to"> Transfer&nbsp;Location');
      $loc.attr('placeholder','');
    }
  }

  $cb.on('change', syncCremationUI);
  syncCremationUI();
});
</script>
