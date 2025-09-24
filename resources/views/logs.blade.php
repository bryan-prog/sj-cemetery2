@extends('layouts.masterlayout')

@section('content')
<div class="container-fluid mt-3">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
                <img src="https://img.icons8.com/external-flaticons-lineal-color-flat-icons/28/external-logs-computer-programming-flaticons-lineal-color-flat-icons.png"/>
                Action Logs
            </h3>
            <div>
                <a href="{{ url('/Homepage') }}" class="btn btn-default btn-sm">Back to Home</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="table-responsive py-4">
                    <table id="logsTable" class="table table-flush w-100">
                        <thead class="bg-default">
                        <tr>
                            <th class="text-white">Action Log ID</th>
                            <th class="text-white">Account Used</th>
                            <th class="text-white">Deceased Name</th>
                            <th class="text-white">Location</th>
                            <th class="text-white">Remarks</th>
                            <th class="text-white">Module</th>
                            <th class="text-white">Transaction Date</th>
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
document.addEventListener('DOMContentLoaded', function () {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

  $('#logsTable').DataTable({
    processing: true,
    serverSide: true,
    deferRender: true,
    ajax: {
      url: '{{ route('logs.data') }}',
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf },
      error: function (xhr) {
        console.error('DataTables AJAX error:', {
          status: xhr.status,
          statusText: xhr.statusText,
          response: xhr.responseText
        });
        alert('Failed to load logs. See console and storage/logs/laravel.log.');
      }
    },
    lengthMenu: [[5, 10, 25, 100], [5, 10, 25, 100]],
    pageLength: 5,
    order: [[0, 'desc']],
    columns: [
      { data: 'log_id',      name: 'id' },
      { data: 'name',        name: 'name_display', orderable: false, searchable: false },
      { data: 'deceased',    name: 'details->deceased' },
      { data: 'location',    name: 'details->location' },
      { data: 'description', name: 'description',  orderable: false, searchable: false },
      { data: 'module',      name: 'module' },
      { data: 'happened_at', name: 'happened_at' }
    ],
    language: {
      emptyTable: 'No action logs yet.',
      paginate: { previous: '<<', next: '>>' }
    }
  });
});
</script>
@endpush
