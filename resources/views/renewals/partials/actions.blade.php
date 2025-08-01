{{-- tiny partial just in case you want server-side rendering --}}
<div class="btn-group btn-group-sm">
    <a href="{{ url("renewals/$r->id") }}" class="btn btn-primary">View</a>
    <form action="{{ route('renewals.approve', $r) }}" method="POST">
        @csrf
        <button class="btn btn-success" onclick="return confirm('Approve this renewal?')">
            Approve
        </button>
    </form>
    <form action="{{ route('renewals.deny', $r) }}" method="POST">
        @csrf
        <button class="btn btn-danger" onclick="return confirm('Deny this renewal?')">
            Deny
        </button>
    </form>
</div>
