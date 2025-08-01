<?php

namespace App\Http\Controllers;

use App\Models\{ Renewal, Slot, Reservation };
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class RenewalPermitController extends Controller
{



public function index()
{
   $renewals = Renewal::with([
    'slot.cell.level.apartment',
    'deceased'

])->latest('id')->paginate(25);

    return view('renewals.index', compact('renewals'));

}

    public function store(Request $r)
    {
        $v = $r->validate([
            'reservation_id'   => 'required|exists:reservations,id',
            'slot_id'          => 'required|exists:slots,id',

            'date_applied'     => 'required|date',
            'renewal_start'    => 'required|date',
            'renewal_end'      => 'required|date|after:renewal_start',

            'requesting_party' => 'required|string|max:255',
            'applicant_address'=> 'required|string|max:255',
            'contact'          => 'required|string|max:55',
            'relationship_to_deceased' => 'nullable|string|max:100',
            'amount_as_per_ord'=> 'nullable|numeric|min:0',
            'verifiers_id'     => 'nullable|exists:verifiers,id',
            'remarks'          => 'nullable|string|max:500',
        ]);

        if (Renewal::where('slot_id',$v['slot_id'])->where('status','pending')->exists()) {
            return back()->with('error','A pending renewal already exists for this slot.');
        }

        DB::transaction(function () use ($v) {
            Slot::lockForUpdate()
                ->where('id',$v['slot_id'])
                ->update(['status'=>'renewal_pending']);

            Renewal::create($v);
        });

        return back()->with('success','Renewal request lodged!');
    }


   public function approve(Renewal $renewal)
{
    abort_if($renewal->status !== 'pending', 400, 'Already processed');

    DB::transaction(function () use ($renewal) {
        $renewal->update([
            'status'  => 'approved',
            'remarks' => 'Approved ' . now(),
        ]);

        $renewal->slot()->update(['status' => 'occupied']);
    });

    return back()->with('success', 'Renewal approved.');
}

    public function deny(Renewal $renewal)
    {
        abort_if($renewal->status!=='pending',400,'Already processed');

        DB::transaction(function () use ($renewal) {
            $renewal->update([
                'status'=>'denied',
                'remarks'=>'Denied '.now(),
            ]);

            $renewal->slot()->update(['status'=>'occupied']);
        });

        return back()->with('success','Renewal denied.');
    }





}
