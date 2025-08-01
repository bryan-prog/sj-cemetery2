@extends('layouts.masterlayout')


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<style>

    #renewalsTable th,
    #renewalsTable td {
        white-space: nowrap;
        vertical-align: middle;
    }
</style>

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-3">Renewal Requests</h3>

            <div class="table-responsive">
                <table id="renewalsTable" class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Applicant's Name</th>
                            <th>Relationship&nbsp;to&nbsp;Deceased</th>
                            <th>Name&nbsp;of&nbsp;Deceased</th>
                            <th>Sex</th>
                            <th>Buried&nbsp;At</th>
                            <th>Renewal&nbsp;Period</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
@foreach ($renewals as $r)
@php
    $slot  = $r->slot;
    $cell  = $slot?->cell;
    $level = $cell?->level;
    $apt   = $level?->apartment;

    $location = $apt
        ? $apt->name.' L'.$level->level_no.' R'.$cell->row_no.'C'.$cell->col_no.'S'.$slot->slot_no
        : '—';

    $badge = match ($r->status) {
        'pending'  => 'bg-warning',
        'approved' => 'bg-success',
        'denied'   => 'bg-danger',
        default    => 'bg-secondary',
    };

    $dec = $r->deceased;
@endphp
<tr>
    <td>{{ $r->requesting_party }}</td>
    <td>{{ $r->relationship_to_deceased ?? '—' }}</td>
    <td>{{ $dec?->name_of_deceased ?? '—' }}</td>
    <td>{{ $dec?->sex ?? '—' }}</td>
    <td>{{ $location }}</td>
    <td>{{ $r->renewal_start->year }}-{{ $r->renewal_end->year }}</td>
    <td><span class="badge {{ $badge }}">{{ ucfirst($r->status) }}</span></td>
    <td class="text-end">
        @if ($r->status === 'pending')
            <form action="{{ route('renewals.approve',$r) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-success">Approve</button>
            </form>

            <form action="{{ route('renewals.deny',$r) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-danger">Deny</button>
            </form>
        @endif
    </td>
</tr>
@endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $renewals->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function () {
    $('#renewalsTable').DataTable({
        paging : false,
        order  : [[5, 'desc']],
        info   : false
    });
});
</script>
