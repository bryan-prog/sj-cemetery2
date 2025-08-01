{{-- @extends('layouts.masterlayout')

@push('styles')
<style>
    .parent{
        display:grid;
        grid-template-columns:repeat(36,1fr);
        grid-column-gap:0;
        grid-row-gap:10px;
    }
    .parent button{width:50px;height:100px;font-size:11px}
    .taken      {border:2px solid #e90000;background:#ffc1c1}
    .available  {border:2px solid #057f05;background:#76ff76}
    .no_lapida  {border:2px solid #000000;background:#8f8f8f}
</style>
@endpush

@section('content')
<div class="container mt-3 mb-3">
    <h3 class="text-center">LEVEL {{ $levelNo }} APARTMENT</h3>

    <div class="parent">

        @for($r=$rows; $r>=1; $r--)
            @for($c=1; $c<= $cols; $c++)
                @php $class = $grid[$r][$c] ?? 'available'; @endphp
                <button class="{{ $class }}">
                    R{{ $r }}_{{ $c }}
                </button>
            @endfor
        @endfor
    </div>
</div>
@endsection --}}
