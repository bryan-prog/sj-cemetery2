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
                                <th class="text-white">Username</th>
                                <th class="text-white">Name</th>
                                <th class="text-white">Description</th>
                                <th class="text-white">Module</th>
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