<div class="modal fade" id="chooseSiteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mb-0"><img src="https://img.icons8.com/doodle/30/checked-checkbox.png"/> Choose Transfer Location</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"> Burial Site / Apartment</label>
          <select id="modal_site" class="form-control">
            <option value="">-- Select site --</option>
            @foreach($burial_sites as $s)
              <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-control-label"><img src="https://img.icons8.com/doodle/20/address.png"> Level</label>
          <select id="modal_level" class="form-control">
            <option value="">-- Select level --</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button id="loadGridBtn" class="btn btn-primary" disabled>Load Grid</button>
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
