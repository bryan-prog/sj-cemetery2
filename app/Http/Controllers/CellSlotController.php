<?php

namespace App\Http\Controllers;

use App\Models\{GraveCell, Reservation, Slot};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CellSlotController extends Controller
{

    public function store(Request $request, GraveCell $cell)
    {
        $data = $request->validate([
            'count'     => 'sometimes|integer|min:1|max:6',
            'family_id' => 'required|integer|exists:families,id',
        ]);

        $count    = $data['count'] ?? 1;
        $familyId = (int) $data['family_id'];


        if (!$cell->family_id || (int)$cell->family_id !== $familyId) {
            return response()->json([
                'message' => 'Only the owning family can add slots to this cell.'
            ], 403);
        }

        return DB::transaction(function () use ($cell, $count) {
            $start   = (int) ($cell->slots()->max('slot_no') ?? 0);
            $created = [];

            for ($i = 1; $i <= $count; $i++) {
                $created[] = $cell->slots()->create([
                    'slot_no' => $start + $i,
                    'status'  => 'available',
                ]);
            }


            $cell->update(['max_slots' => max((int)$cell->max_slots, $start + $count)]);

            return response()->json([
                'created' => collect($created)->map(fn($s) => [
                    'id'      => $s->id,
                    'slot_no' => $s->slot_no,
                    'status'  => $s->display_status,
                ])->all(),
            ], 201);
        });
    }


    public function destroy(Request $request, GraveCell $cell)
    {
        $data = $request->validate([
            'count'     => 'sometimes|integer|min:1|max:6',
            'family_id' => 'required|integer|exists:families,id',
        ]);

        $count    = $data['count'] ?? 1;
        $familyId = (int) $data['family_id'];

        if (!$cell->family_id || (int)$cell->family_id !== $familyId) {
            return response()->json([
                'message' => 'Only the owning family can remove slots from this cell.'
            ], 403);
        }


        $available = $cell->slots()
            ->where('status', 'available')
            ->orderByDesc('slot_no')
            ->limit($count)
            ->get();

        if ($available->isEmpty()) {
            return response()->json(['message' => 'No removable (available) slots found.'], 422);
        }

        return DB::transaction(function () use ($cell, $available) {
            $deleted = [];
            foreach ($available as $slot) {
                $deleted[] = ['id' => $slot->id, 'slot_no' => $slot->slot_no];
                $slot->delete();
            }


            $cell->update(['max_slots' => (int) ($cell->slots()->max('slot_no') ?? 0)]);

            return response()->json(['deleted' => $deleted], 200);
        });
    }
}
