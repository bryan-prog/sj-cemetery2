
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
          <h5 class="modal-title">
            <img class="mr-2" src="https://img.icons8.com/doodle/30/information.png"/>
            EXHUMATION&nbsp;REQUEST
          </h5>

          {{-- <button type="button" class="btn-close exhum-close" aria-label="Close"></button> --}}
          <button type="button" class="exhum-close"
                  aria-label="Close"
                  style="background:transparent;border:0;padding:0;margin-left:.25rem;font-size:1.25rem;line-height:1;">
            &times;
          </button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/test-account.png">
                Name&nbsp;of&nbsp;Deceased
              </label>
              <input type="text" id="exhum_deceased_name" class="form-control" readonly>
            </div>
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/external-xnimrodx-lineal-color-xnimrodx/20/external-grave-calendar-xnimrodx-lineal-color-xnimrodx.png"/>
                Date&nbsp;of&nbsp;Death
              </label>
              <input type="date" id="exhum_date_of_death" class="form-control" readonly>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/arcade/20/birth-date.png">
                Date&nbsp;Applied
              </label>
              <input type="date" name="date_applied" class="form-control" value="{{ now()->toDateString() }}">
            </div>
            <div class="col-md-8">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/test-account.png">
                Requesting&nbsp;Party&nbsp;Name
              </label>
              <input type="text" name="requesting_party" class="form-control" style="text-transform:uppercase;">
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-12">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/address.png">
                Address
              </label>
              <input type="text" name="address" id="exhum_address" class="form-control" style="text-transform:uppercase;">
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-12">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/address.png">
                Current&nbsp;Location
              </label>
              <input type="text" id="exhum_current_location" class="form-control" readonly>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-8">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/address.png">
                Transfer&nbsp;Location
              </label>
              <input type="text" name="current_location" id="current_location_field" class="form-control" readonly>
            </div>
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/stickers/20/family.png">
                Relationship&nbsp;to&nbsp;Deceased
              </label>
              <input type="text" name="relationship_to_deceased" class="form-control" style="text-transform:uppercase;">
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/apple-phone.png">
                Contact&nbsp;Number
              </label>
              <input type="text" name="contact" id="exhum_contact" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/refund.png">
                Payment&nbsp;as&nbsp;per&nbsp;Ord.
              </label>
              <input type="number" step="0.01" name="amount_as_per_ord" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/manager.png">
                Verifier
              </label>
              <select name="verifiers_id" class="form-control">
                <option value="">----</option>
                @foreach($verifiers as $v)
                  <option value="{{ $v->id }}">{{ $v->name_of_verifier }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <small class="text-muted">Leave destination blank if no transfer is needed.</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-danger" type="submit">Submit&nbsp;Request</button>
          <button class="btn btn-secondary exhum-close" type="button">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
(function(){
  var el = document.getElementById('exhumReqModal');
  if (!el) return;


  var bs5 = (window.bootstrap && bootstrap.Modal)
    ? bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static', keyboard: false })
    : null;


  if (window.jQuery && jQuery.fn && jQuery.fn.modal) {
    try { jQuery(el).modal({ backdrop: 'static', keyboard: false, show: false }); } catch(e){}
  }

  function hardCloseExhum() {

    try { bs5 && bs5.hide(); } catch(e){}

    try { if (window.jQuery && jQuery(el).modal) jQuery(el).modal('hide'); } catch(e){}

    try {
      el.classList.remove('show');
      el.setAttribute('aria-hidden','true');
      el.style.display = 'none';
      document.querySelectorAll('.modal-backdrop').forEach(function(b){ b.remove(); });
      document.body.classList.remove('modal-open');
      document.body.style.removeProperty('padding-right');
    } catch(e){}
  }

  el.querySelectorAll('.exhum-close, [data-bs-dismiss="modal"], [data-dismiss="modal"]').forEach(function(btn){
    btn.addEventListener('click', function(ev){
      ev.preventDefault();
      hardCloseExhum();
    });
  });


  el.addEventListener('keydown', function(ev){
    if (ev.key === 'Escape' || ev.keyCode === 27) { ev.preventDefault(); ev.stopPropagation(); }
  });
  el.addEventListener('click', function(ev){

    if (ev.target === el) { ev.preventDefault(); ev.stopPropagation(); }
  });
})();
</script>
