@extends('layouts.masterlayout')
@inject('carbon', 'Carbon\Carbon')

<style>
  .container.mt-3 { max-width: 1830px !important; }
  .form-control{ color:black !important; }
  body{ background-image: url(assets/img/bg_cemetery.png); }
  table{ border:1px solid #e9ecef !important; color:black !important;}
</style>

@section('content')
<div class="container mt-3">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h3 class="mb-0"><img src="https://img.icons8.com/doodle/28/cloud-folder.png"/> San Juan City Cemetery Database</h3>
      <div>
        <a href="{{URL('/Homepage')}}"><button class="btn btn-default btn-sm">Back to Home</button></a>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col">
          <p style="color:black; margin-bottom: 1.5px;"><b><img src="https://img.icons8.com/doodle/20/building--v1.png"/> Property Type <span style="position:relative; left:15px;">:</span></b> <span style="position:relative; left:27px;">Public</span></p>
          <p style="color:black; margin-bottom: 1.5px;"><b><img src="https://img.icons8.com/doodle/20/apartment.png"/> Building Type <span style="position:relative; left:18px;">:</span></b> <span style="position:relative; left:30px;">Restos</span></p>
          <p style="color:black; margin-bottom: 1.5px;"><b><img src="https://img.icons8.com/doodle/20/marker--v1.png"/> Location <span style="position:relative; left:57px;">:</span></b> <span style="position:relative; left:69px;">Left Side</span></p>
          <p style="color:black; margin-bottom: 1.5px;"><b><img src="https://img.icons8.com/color/21/headstone--v1.png"/> Burial Type <span style="position:relative; left:37px;">:</span></b> <span style="position:relative; left:49px;">Bones/Urn</span></p>
        </div>


        <div class="col">
          <div class="form-group">
            <label class="form-control-label" for="burial_site_id">
              <img src="https://img.icons8.com/office/20/stairs-up.png"/> Select Apartment
            </label>
            <select class="form-control" id="burial_site_id">
              <option value="">---</option>

            </select>
          </div>
        </div>

        <div class="col">
          <div class="form-group">
            <label class="form-control-label" for="level_no">
              <img src="https://img.icons8.com/color/20/add-row.png"/> Select Level
            </label>
            <select class="form-control" id="level_no">
              <option value="">---</option>

            </select>
          </div>
          <button id="apply-filter" class="btn btn-sm btn-danger">Apply Filter</button>
          <button id="export-excel" class="btn btn-success btn-sm">Export (Excel)</button>

        </div>

      </div>


</a>
      <div class="row mt-4">
        <div class="table-responsive py-4">
          <table class="table table-flush" id="reservations-table">
            <thead class="bg-default">
              <tr>
                <th class="text-white">Buried At</th>
                <th class="text-white">Name of Deceased</th>
                <th class="text-white">Sex</th>
                <th class="text-white">Date of Birth</th>
                <th class="text-white">Date of Death</th>
                <th class="text-white">Year of Renewal</th>
                <th class="text-white">Contact Person</th>

              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
$(function () {
  const $site  = $('#burial_site_id');
  const $level = $('#level_no');


  $.getJSON('{{ url('/api/burial-sites') }}', function (sites) {
    $site.empty().append('<option value="">---</option>');
    sites.forEach(s => $site.append('<option value="'+s.id+'">'+s.name.toUpperCase()+'</option>'));
  });


  $site.on('change', function () {
    const id = $(this).val();
    $level.empty().append('<option value="">---</option>');
    if (!id) return;
    $.getJSON('{{ url('/api/burial-sites') }}/' + id + '/levels', function (levels) {
      levels.forEach(l => $level.append('<option value="'+l.level_no+'">Level '+l.level_no+'</option>'));
    });
  });


  const table = $('#reservations-table').DataTable({
    serverSide: false,
    processing: true,
    searching: true,
    lengthChange: false,
    pageLength: 25,
    order: [[0, 'asc']],
    ajax: {
      url: '{{ route('reservations.client.list') }}',
      type: 'GET',
      dataType: 'json',
      headers: { 'Accept': 'application/json' },
      data: function (d) {
        d.burial_site_id = $site.val();
        d.level_no       = $level.val();
      },
      dataSrc: 'data'
    },
    columns: [
      { data: 'buried_at',      defaultContent: '—' },
      { data: 'name_of_deceased',  defaultContent: '—' },
      { data: 'sex',            defaultContent: '—' },
      { data: 'date_of_birth',  defaultContent: '—' },
      { data: 'date_of_death',  defaultContent: '—' },
      { data: 'renewal_period', defaultContent: '—' },
      { data: 'contact_person', defaultContent: '—' },

    ],
    language: { paginate: { next: '>', previous: '<', first: '<<', last: '>>' } },
    pagingType: 'full_numbers'
  });


  $('#apply-filter').on('click', function () {
    if (!$site.val() || !$level.val()) {
      alert('Please select apartment and level first.');
      return;
    }
    table.ajax.reload(null, true);


  });

   $('#export-excel').on('click', function () {
      const siteId  = $('#burial_site_id').val();
      const levelNo = $('#level_no').val();
      if (!siteId || !levelNo) {
          alert('Please select Apartment and Level first.');
          return;
      }
      const url = `{{ route('reservations.export') }}?burial_site_id=${siteId}&level_no=${levelNo}`;
      window.location.href = url;
  });

});

</script>
@endpush
