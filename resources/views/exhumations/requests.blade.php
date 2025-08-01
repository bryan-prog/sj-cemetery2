@extends('layouts.masterlayout')
<style>
    .container.mt-4   { max-width: 1830px !important }
</style>
@section('content')
<div class="container mt-4">

  <div class="card">
    <div class="card-body">
      <h3 class="mb-3">Exhumation Requests</h3>
      @foreach (['success','info','error'] as $f)
          @if(session($f))
            <div class="alert alert-{{ $f === 'error' ? 'danger' : $f }}">
              {{ session($f) }}
            </div>
          @endif
      @endforeach

      @if($exhumations->isEmpty())
          <div class="alert alert-info">No exhumation requests found.</div>
      @else
        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
              <tr>
                <th>Requestor</th>
                <th>Relationship</th>
                <th>Date&nbsp;Requested</th>
                <th>Deceased&nbsp;Name</th>
                <th>Date&nbsp;of&nbsp;Death</th>
                <th>For&nbsp;Transfer</th>
                <th class="text-center" style="width:180px;">Actions</th>
              </tr>
            </thead>

            <tbody>
            @foreach($exhumations as $ex)
              @php

                $dec = $ex->reservation->deceased;
              @endphp
              <tr>
                <td>{{ $ex->requesting_party }}</td>
                <td>{{ $ex->relationship_to_deceased }}</td>


                <td>{{ \Illuminate\Support\Carbon::parse($ex->date_applied)->format('Y-m-d') }}</td>

                <td>{{ $dec?->name_of_deceased ?? '—' }}</td>


                <td>
                  {{ $dec?->date_of_death
                        ? \Illuminate\Support\Carbon::parse($dec->date_of_death)->format('Y-m-d')
                        : '—' }}
                </td>

                <td>{{ $ex->destination_label }}</td>

                <td class="text-center">
                  @if($ex->status === 'pending')

                    <form method="POST"
                          action="{{ route('exhumations.approve', $ex) }}"
                          class="d-inline"
                          onsubmit="return confirm('Approve this request?');">
                      @csrf
                      <button class="btn btn-sm btn-success">Approve</button>
                    </form>


                    <form method="POST"
                          action="{{ route('exhumations.deny', $ex) }}"
                          class="d-inline"
                          onsubmit="return confirm('Deny this request?');">
                      @csrf
                      <button class="btn btn-sm btn-danger">Deny</button>
                    </form>
                  @else
                    <span class="badge bg-secondary">{{ ucfirst($ex->status) }}</span>
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>


        {{ $exhumations->links() }}
      @endif
    </div>
  </div>

</div>
@endsection
