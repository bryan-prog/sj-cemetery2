@extends('layouts.masterlayout')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

<style>
  .container-fluid.mt-3 { max-width: 90% !important; width: 90% !important; }
  .form-control { color:black !important; }
  body { background-image: url(assets/img/bg_cemetery.png); }
  table { border:1px solid #e9ecef !important; color:black !important; }
  thead.bg-default th { color:#fff !important; }
  .form-control-label img { position: relative; top: -1px; margin-right: 6px; }
  .card-header h3 img { margin-right: 6px; }
  #reservationsTable td .btn + .btn { margin-left: .375rem; }
  .modal .form-control-label { color:black !important; }
  .modal .form-row { margin-left: 0; margin-right: 0; }
  .badge-readonly { font-size: .78rem; }
</style>

@section('content')
<div class="container-fluid mt-3">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="mb-0">
        <img src="https://img.icons8.com/doodle/28/cloud-folder.png"/>
        San Juan City Cemetery Database
      </h3>
      <div>
        <a href="{{ url('/Homepage') }}" class="btn btn-default btn-sm">Back to Home</a>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col">
          <p style="color:black; margin-bottom: 1.5px;">
            <b><img src="https://img.icons8.com/doodle/20/building--v1.png"/> Property Type :</b>
            <span id="propTypeText">Public</span>
          </p>
          <p style="color:black; margin-bottom: 1.5px;">
            <b><img src="https://img.icons8.com/doodle/20/apartment.png"/> Building Type :</b>
            <span id="bldgTypeText">—</span>
          </p>
          <p style="color:black; margin-bottom: 1.5px;">
            <b><img src="https://img.icons8.com/doodle/20/marker--v1.png"/> Location :</b>
            <span id="locationText">—</span>
          </p>
          <p style="color:black; margin-bottom: 1.5px;">
            <b><img src="https://img.icons8.com/color/21/headstone--v1.png"/> Burial Type :</b>
            <span id="burialTypeText">—</span>
          </p>
        </div>

        <div class="col">
          <div class="form-group">
            <label class="form-control-label" for="filterSite">
              <img src="https://img.icons8.com/office/20/stairs-up.png"/> Select Apartment
            </label>
            <select id="filterSite" class="form-control">
              <option value="">— Select Apartment —</option>
              @foreach($burialSites as $site)
                <option value="{{ $site->id }}">{{ $site->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col">
          <div class="form-group">
            <label class="form-control-label" for="filterLevel">
              <img src="https://img.icons8.com/color/20/add-row.png"/> Select Level
            </label>
            <select id="filterLevel" class="form-control">
              <option value="">— Select Level —</option>
            </select>
          </div>

          <div class="d-flex gap-2">
            <button id="btnFilter" class="btn btn-sm btn-danger" disabled>Apply Filter</button>
            <button id="btnExport" class="btn btn-success btn-sm" disabled>Export (Excel)</button>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="table-responsive py-4">
          <table id="reservationsTable" class="table table-flush w-100">
            <thead class="bg-default">
              <tr>
                <th class="text-white">Date Applied</th>
                <th class="text-white">Date of Internment</th>
                <th class="text-white">Years Buried</th>
                <th class="text-white">Applicant</th>
                <th class="text-white">Deceased</th>
                <th class="text-white">Apartment</th>
                <th class="text-white">Level</th>
                <th class="text-white">Location</th>
                <th class="text-white">Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>


<div class="modal fade" id="reservationViewModal" tabindex="-1" role="dialog" aria-hidden="true" data-id="">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          Reservation Details
        </h5>
        <button type="button" class="close ml-2" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
      </div>

      <div class="modal-body">
        <form id="rvForm" autocomplete="off">
          @csrf
          @method('PUT')

          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="form-control-label">
                <img src="https://img.icons8.com/arcade/20/birth-date.png"/> Date Applied
              </label>
              <input id="rvDateApplied" name="date_applied" type="date" class="form-control" disabled>
            </div>
            <div class="form-group col-md-6">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/calendar--v2.png"/> Internment
              </label>
              <input id="rvInternment" name="internment_sched" type="datetime-local" class="form-control" disabled>
            </div>
          </div>

          <h5 class="text-muted mb-2">Applicant's Information</h5>
          <hr class="mt-0 mb-3">
          <div class="form-row">
            <div class="form-group col-md-3">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/test-account.png"/> First Name
              </label>
              <input id="rvApplicantFirst" name="applicant_first_name" class="form-control" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label">Middle Name</label>
              <input id="rvApplicantMiddle" name="applicant_middle_name" class="form-control" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label">Last Name</label>
              <input id="rvApplicantLast" name="applicant_last_name" class="form-control" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label">Suffix</label>
              <input id="rvApplicantSuffix" name="applicant_suffix" class="form-control" disabled>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/address.png"/> Address
              </label>
              <input id="rvAddress" name="applicant_address" class="form-control" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/apple-phone.png"/> Contact
              </label>
              <input id="rvContact" name="applicant_contact_no" class="form-control" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label">
                <img src="https://img.icons8.com/stickers/20/family.png"/> Relationship
              </label>
              <input id="rvRelation" name="relationship_to_deceased" class="form-control" disabled>
            </div>
          </div>

          <h5 class="text-muted mb-2">Deceased Details</h5>
          <hr class="mt-0 mb-3">
          <div class="form-row">
            <div class="form-group col-md-3">
              <label class="form-control-label">
                <img src="https://img.icons8.com/plasticine/20/headstone.png"/> First Name
              </label>
              <input id="rvDecFirst" name="deceased_first_name" class="form-control" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label">Middle Name</label>
              <input id="rvDecMiddle" name="deceased_middle_name" class="form-control" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label">Last Name</label>
              <input id="rvDecLast" name="deceased_last_name" class="form-control" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label">Suffix</label>
              <input id="rvDecSuffix" name="deceased_suffix" class="form-control" disabled>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-3">
              <label class="form-control-label">
                <img src="https://img.icons8.com/arcade/20/birth-date.png"/> Date of Birth
              </label>
              <input id="rvDob" name="date_of_birth" type="date" class="form-control" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label">
                <img src="https://img.icons8.com/arcade/20/birth-date.png"/> Date of Death
              </label>
              <input id="rvDod" name="date_of_death" type="date" class="form-control" disabled>
            </div>
          </div>

          <h5 class="text-muted mb-2">Location</h5>
          <hr class="mt-0 mb-3">
          <div class="form-row">
            <div class="form-group col-12">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/marker--v1.png"/> Location
              </label>
              <input id="rvLocationLabel" class="form-control" disabled>
            </div>
          </div>

          <h5 class="text-muted mb-2">Other</h5>
          <hr class="mt-0 mb-3">
          <div class="form-row">
            <div class="form-group col-md-4">
              <label class="form-control-label">
                <img src="https://img.icons8.com/plasticine/20/headstone.png"/> Funeral Service
              </label>
              <input id="rvFuneral" name="funeral_service" class="form-control" disabled>
            </div>
            <div class="form-group col-md-3">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/refund.png"/> Amount(as per ordinance.)
              </label>
              <input id="rvAmount" name="amount_as_per_ord" class="form-control" disabled>
            </div>
            <div class="form-group col-md-5">
              <label class="form-control-label">
                <img src="https://img.icons8.com/doodle/20/note.png"/> Other Information
              </label>
              <input id="rvNotes" name="other_info" class="form-control" disabled>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button id="rvBtnEdit" type="button" class="btn btn-warning">
          <i class="fa fa-edit" aria-hidden="true"></i> Edit
        </button>
        <button id="rvBtnSave" type="button" class="btn btn-success d-none">
          <i class="fa fa-save" aria-hidden="true"></i> Save Changes
        </button>
        <button id="rvBtnCancel" type="button" class="btn btn-light d-none">Cancel</button>

        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="updateSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body text-center p-4">
        <div class="mb-2">
          <img src="https://img.icons8.com/fluency/48/ok.png" alt="success"/>
        </div>
        <h5 class="mb-1">Reservation Updated</h5>
        <p class="mb-0">Your changes were saved successfully.</p>
        <button type="button" class="btn btn-success mt-3" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  (function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const $site      = $('#filterSite');
    const $level     = $('#filterLevel');
    const $btnFilter = $('#btnFilter');
    const $btnExport = $('#btnExport');


    const $bldgType   = $('#bldgTypeText');
    const $location   = $('#locationText');
    const $burialType = $('#burialTypeText');

    const BASE_LEVELS_URL = @json(url('/api/burial-sites'));
    const LIST_URL        = @json(route('reservations.list'));
    const EXPORT_URL      = @json(route('reservations.export'));
    const SHOW_URL_TPL    = @json(route('reservations.show', ['reservation' => '__ID__']));
    const UPDATE_URL_TPL  = @json(route('reservations.update', ['reservation' => '__ID__']));
    const PERMIT_URL_TPL  = @json(route('reservations.permit.pdf', ['reservation' => '__ID__']));


    function inferSide(nameRaw) {
      const name = String(nameRaw || '').toLowerCase();
      if (name.includes('left side'))  return 'Left Side';
      if (name.includes('right side')) return 'Right Side';
      return '—';
    }


    function burialTypeFor(nameRaw) {
      const n = String(nameRaw || '').toLowerCase().replace(/\s+/g, ' ').trim();


      if (/left side.*restos/.test(n)) return 'Bones/Urn';

      if (/left side.*apartment\s*(2|ii)\b/.test(n)) return '—';
      if (/left side.*apartment\s*(3|iii)\b/.test(n)) return 'Bones/Urn/Whole Casket';
      if (/left side.*apartment\s*(4|iv)\b/.test(n)) return 'Whole Casket';
      if (/left side.*veterans/.test(n))            return 'Whole Casket';
      if (/left side.*entrance/.test(n))            return 'Bones/Urn/Whole Casket';


      if (/right side.*apartment\s*(1-a|i-a)\b/.test(n)) return '—';
      if (/right side.*apartment\s*(1-b|i-b)\b/.test(n)) return '—';
      if (/right side.*apartment\s*5\b/.test(n))         return 'Bones/Urn/Whole Casket';
      if (/right side.*entrance/.test(n))                return 'Bones/Urn/Whole Casket';


      return '—';
    }


    function syncHeaderFromSelectedSite() {
      const $opt = $site.find('option:selected');
      const name = $opt.val() ? $opt.text() : '';
      $bldgType.text(name || '—');
      $location.text(inferSide(name));
      $burialType.text(burialTypeFor(name));
    }

    function yearsNumberSince(dateStr) {
      if (!dateStr) return '—';
      const dt = new Date(String(dateStr).replace(' ', 'T'));
      if (isNaN(dt.getTime())) return '—';
      const today = new Date();
      let y = today.getFullYear() - dt.getFullYear();
      const hadAnniv =
        (today.getMonth() > dt.getMonth()) ||
        (today.getMonth() === dt.getMonth() && today.getDate() >= dt.getDate());
      if (!hadAnniv) y--;
      return y < 0 ? '—' : String(y);
    }

    function toDateInput(val) {
      if (!val) return '';
      const d = new Date(String(val).replace(' ', 'T'));
      if (Number.isNaN(d.getTime())) return '';
      const m = String(d.getMonth()+1).padStart(2,'0');
      const day = String(d.getDate()).padStart(2,'0');
      return `${d.getFullYear()}-${m}-${day}`;
    }
    function toDateTimeLocalInput(val) {
      if (!val) return '';
      const d = new Date(String(val).replace(' ', 'T'));
      if (Number.isNaN(d.getTime())) return '';
      const m = String(d.getMonth()+1).padStart(2,'0');
      const day = String(d.getDate()).padStart(2,'0');
      const hh = String(d.getHours()).padStart(2,'0');
      const mm = String(d.getMinutes()).padStart(2,'0');
      return `${d.getFullYear()}-${m}-${day}T${hh}:${mm}`;
    }

    const table = $('#reservationsTable').DataTable({
      processing: true,
      serverSide: false,
      deferLoading: 0,
      pageLength: 25,
      columns: [
        { data: 'date_applied' },
        { data: 'internment_sched' },
        { data: 'internment_sched', render: function(value) { return yearsNumberSince(value); } },
        { data: 'applicant_name' },
        { data: 'deceased_name' },
        { data: 'burial_site' },
        { data: 'level_no' },
        { data: 'location' },
        {
          data: null,
          orderable: false,
          searchable: false,
          render: function (row) {
            const viewUrl = SHOW_URL_TPL.replace('__ID__', row.id);
            const pdfUrl  = PERMIT_URL_TPL.replace('__ID__', row.id) + '?force=1';
            return `
              <div class="text-nowrap">
                <button type="button"
                        class="btn btn-sm btn-info btn-view"
                        data-id="${row.id}"
                        data-view-url="${viewUrl}"
                        title="View / Edit">
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </button>
                <a href="${pdfUrl}"
                   target="_blank"
                   class="btn btn-sm btn-primary"
                   title="Print Permit">
                  <i class="fa fa-print" aria-hidden="true"></i>
                </a>
              </div>
            `;
          }
        }
      ],
      language: { paginate: { next: '>', previous: '<', first: '<<', last: '>>' } },
      pagingType: 'full_numbers'
    });

    function syncButtons() {
      const ok = $site.val() && $level.val();
      $btnFilter.prop('disabled', !ok);
      $btnExport.prop('disabled', !ok);
    }

    function resetLevels() {
      $level.html('<option value="">— Select Level —</option>');
      syncButtons();
    }

    $site.on('change', function() {

      syncHeaderFromSelectedSite();


      const siteId = $(this).val();
      resetLevels();
      if (!siteId) return;

      $level.append('<option value="" disabled>Loading levels…</option>');
      const url = `${BASE_LEVELS_URL}/${siteId}/levels`;

      $.getJSON(url)
        .done(function(levels) {
          resetLevels();
          if (Array.isArray(levels) && levels.length) {
            levels.forEach(l => $level.append(`<option value="${l.level_no}">Level ${l.level_no}</option>`));
          } else {
            $level.append('<option value="" disabled>(No levels found)</option>');
          }
          syncButtons();
        })
        .fail(function(xhr) {
          resetLevels();
          console.error('Levels request failed:', xhr.status, xhr.responseText);
          alert('Could not load levels for the selected apartment.');
        });
    });

    $level.on('change', syncButtons);
    $site.on('change', syncButtons);

    $btnFilter.on('click', function() {
      const siteId  = $site.val();
      const levelNo = $level.val();
      if (!siteId || !levelNo) return;

      const listUrl = `${LIST_URL}?burial_site_id=${siteId}&level_no=${levelNo}`;

      $.getJSON(listUrl)
        .done(function(resp) {
          table.clear();
          table.rows.add(resp.data || []);
          table.draw();
        })
        .fail(function() {
          table.clear().draw();
          alert('Failed to load reservations. Check server logs.');
        });
    });

    $btnExport.on('click', function() {
      const siteId  = $site.val();
      const levelNo = $level.val();
      if (!siteId || !levelNo) {
        alert('Please select Apartment and Level first.');
        return;
      }
      const url = `${EXPORT_URL}?burial_site_id=${siteId}&level_no=${levelNo}`;
      window.location.href = url;
    });

    const $modal = $('#reservationViewModal');
    const $form  = $('#rvForm');

    const $btnEdit   = $('#rvBtnEdit');
    const $btnSave   = $('#rvBtnSave');
    const $btnCancel = $('#rvBtnCancel');

    const $inputs = $form.find('input');
    const $loc    = $('#rvLocationLabel');

    let currentPayload = null;

    function setEditMode(on) {
      $btnEdit.toggleClass('d-none', on);
      $btnSave.toggleClass('d-none', !on);
      $btnCancel.toggleClass('d-none', !on);

      $inputs.prop('disabled', !on);
      $loc.prop('disabled', true);
    }

    function fillModal(r) {
      currentPayload = JSON.parse(JSON.stringify(r));
      $modal.attr('data-id', r.id);

      $('#rvDateApplied').val(toDateInput(r.date_applied));
      $('#rvInternment').val(toDateTimeLocalInput(r.internment_sched));

      $('#rvApplicantFirst').val(r.applicant_first_name || '');
      $('#rvApplicantMiddle').val(r.applicant_middle_name || '');
      $('#rvApplicantLast').val(r.applicant_last_name || '');
      $('#rvApplicantSuffix').val(r.applicant_suffix || '');
      $('#rvAddress').val(r.applicant_address || '');
      $('#rvContact').val(r.applicant_contact_no || '');
      $('#rvRelation').val(r.relationship_to_deceased || '');

      $('#rvDecFirst').val(r.deceased_first_name || '');
      $('#rvDecMiddle').val(r.deceased_middle_name || '');
      $('#rvDecLast').val(r.deceased_last_name || '');
      $('#rvDecSuffix').val(r.deceased_suffix || '');
      $('#rvDob').val(toDateInput(r.date_of_birth));
      $('#rvDod').val(toDateInput(r.date_of_death));

      $('#rvLocationLabel').val(r.location || '');

      $('#rvFuneral').val(r.funeral_service || '');
      $('#rvAmount').val(r.amount_as_per_ord || '');
      $('#rvNotes').val(r.other_info || '');

      setEditMode(false);
    }

    function revertModal() {
      if (currentPayload) fillModal(currentPayload);
      setEditMode(false);
    }

    $(document).on('click', '.btn-view', function () {
      const viewUrl = $(this).data('view-url');
      const id      = $(this).data('id');
      if (!viewUrl || !id) return;

      $form[0].reset();
      setEditMode(false);

      $.getJSON(viewUrl)
        .done(function (r) {
          fillModal(r);
          $modal.modal('show');
        })
        .fail(function (xhr) {
          console.error('Could not load reservation details', xhr.status, xhr.responseText);
          alert('Could not load reservation details.');
        });
    });

    $btnEdit.on('click', function() { setEditMode(true); });
    $btnCancel.on('click', function() { revertModal(); });

    $btnSave.on('click', function() {
      const id        = $modal.attr('data-id');
      const updateUrl = UPDATE_URL_TPL.replace('__ID__', id);

      const payload = {
        _method: 'PUT',
        date_applied: $('#rvDateApplied').val(),
        internment_sched: $('#rvInternment').val(),

        applicant_first_name: $('#rvApplicantFirst').val(),
        applicant_middle_name: $('#rvApplicantMiddle').val(),
        applicant_last_name: $('#rvApplicantLast').val(),
        applicant_suffix: $('#rvApplicantSuffix').val(),
        applicant_address: $('#rvAddress').val(),
        applicant_contact_no: $('#rvContact').val(),
        relationship_to_deceased: $('#rvRelation').val(),

        deceased_first_name: $('#rvDecFirst').val(),
        deceased_middle_name: $('#rvDecMiddle').val(),
        deceased_last_name: $('#rvDecLast').val(),
        deceased_suffix: $('#rvDecSuffix').val(),
        date_of_birth: $('#rvDob').val(),
        date_of_death: $('#rvDod').val(),

        funeral_service: $('#rvFuneral').val(),
        amount_as_per_ord: $('#rvAmount').val(),
        other_info: $('#rvNotes').val(),
      };

      if (!payload.date_applied || !payload.internment_sched) {
        alert('Please complete Date Applied and Internment.');
        return;
      }

      $btnSave.prop('disabled', true).text('Saving…');

      $.ajax({
        url: updateUrl,
        type: 'POST',
        data: payload,
        dataType: 'json',
      })
      .done(function (r) {

        const rows = table.rows().indexes().toArray();
        for (const idx of rows) {
          const data = table.row(idx).data();
          if (String(data.id) === String(r.id)) {
            table.row(idx).data(r).draw(false);
            break;
          }
        }

        fillModal(r);
        setEditMode(false);

        $('#reservationViewModal').one('hidden.bs.modal', function() {
          $('#updateSuccessModal').modal('show');
        });
        $('#reservationViewModal').modal('hide');
      })
      .fail(function (xhr) {
        console.error('Update failed', xhr.status, xhr.responseText);
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          const firstErr = Object.values(xhr.responseJSON.errors)[0][0] || 'Validation error.';
          alert(firstErr);
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          alert(xhr.responseJSON.message);
        } else {
          alert('Failed to save changes. Please check inputs.');
        }
      })
      .always(function () {
        $btnSave.prop('disabled', false).html('<i class="fa fa-save" aria-hidden="true"></i> Save Changes');
      });
    });


    syncHeaderFromSelectedSite();
  })();
</script>
@endsection
