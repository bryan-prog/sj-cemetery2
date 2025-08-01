
<div class="modal fade" id="renewalModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="renewalForm" method="POST" action="{{ route('renewals.store') }}">
        @csrf
        <input type="hidden" name="reservation_id">
        <input type="hidden" name="slot_id">

        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white mb-0">
            <img src="https://img.icons8.com/dusk/30/renew-subscription.png"/>
            RENEWAL&nbsp;REQUEST
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"> Date&nbsp;Applied</label>
              <input  type="date" name="date_applied" class="form-control"
                      value="{{ now()->toDateString() }}">
            </div>

            <div class="col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"> Renewal&nbsp;Start</label>
              <input  type="date" name="renewal_start" id="renewal_start"
                      class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-control-label"><img src="https://img.icons8.com/arcade/20/birth-date.png"> Renewal&nbsp;End</label>
              <input  type="date" name="renewal_end" id="renewal_end"
                      class="form-control">
            </div>

            <div class="col-md-8 mt-3">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/test-account.png"> Requesting&nbsp;Party</label>
              <input type="text" name="requesting_party" class="form-control"
                     style="text-transform:uppercase;">
            </div>

            <div class="col-md-4 mt-3">
              <label class="form-control-label"><img src="https://img.icons8.com/stickers/20/family.png"> Relationship&nbsp;to&nbsp;Deceased</label>
              <input type="text" name="relationship_to_deceased"
                     class="form-control" style="text-transform:uppercase;">
            </div>

            <div class="col-12 mt-3">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/city-buildings.png"> Applicant&nbsp;Address&nbsp;<small class="text-muted">(optional)</small></label>
              <input type="text" name="applicant_address" class="form-control"
                     style="text-transform:uppercase;">
            </div>

              <div class="col-12 mt-3">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/city-buildings.png"> Contact<small class="text-muted">(optional)</small></label>
              <input type="text" name="contact" class="form-control"
                     style="text-transform:uppercase;">
            </div>

            <div class="col-md-6 mt-3">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/refund.png"> Payment&nbsp;as&nbsp;per&nbsp;Ord.</label>
              <input type="number" step="0.01" name="amount_as_per_ord"
                     class="form-control">
            </div>

            <div class="col-md-6 mt-3">
              <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/manager.png"> Verifier</label>
              <select name="verifiers_id" class="form-control">
                <option value="">----</option>
                @foreach($verifiers as $v)
                  <option value="{{ $v->id }}">{{ $v->name_of_verifier }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 mt-3">
              <label class="form-control-label">Remarks&nbsp;<small class="text-muted">(optional)</small></label>
              <textarea name="remarks" class="form-control" rows="2"
                        style="text-transform:uppercase;"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-warning" type="submit">Submit&nbsp;Renewal</button>
          <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
