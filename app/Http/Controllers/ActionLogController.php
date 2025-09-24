<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Models\Renewal;
use App\Models\Exhumation;
use App\Models\Reservation;
use App\Models\Slot;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ActionLogController extends Controller
{
    public function index()
    {
        return view('logs');
    }

    public function data(Request $request)
    {
        $q = ActionLog::with([
                'user:id,username,fname,lname',
                'target' => function ($morphTo) {
                    if (method_exists($morphTo, 'morphWith')) {
                        $morphTo->morphWith([
                            Renewal::class     => ['deceased','slot.cell.level.apartment'],
                            Exhumation::class  => ['reservation.deceased','fromSlot.cell.level.apartment','toSlot.cell.level.apartment'],
                            Reservation::class => ['deceased','slot.cell.level.apartment','burialSite','level.apartment'],
                        ]);
                    }
                },
            ])
            ->select('action_logs.*');

        return DataTables::of($q)
            ->addColumn('log_id', fn(ActionLog $log) => $log->id)
            ->addColumn('name', function (ActionLog $log) {
                $u = $log->user;
                $full = trim(($u->fname ?? '') . ' ' . ($u->lname ?? ''));
                return $full !== '' ? $full : '—';
            })
            ->addColumn('deceased', function (ActionLog $log) {
                $d = $log->details['deceased'] ?? null;
                if (is_string($d) && trim($d) !== '') return $d;
                return $this->inferDeceasedName($log) ?? '—';
            })
            ->addColumn('location', function (ActionLog $log) {
                $loc = $log->details['location']
                    ?? $log->details['from_label']
                    ?? $log->details['buried_at']
                    ?? null;
                if (is_string($loc) && trim($loc) !== '') return $loc;
                return $this->inferLocation($log) ?? '—';
            })
            ->addColumn('description', fn(ActionLog $log) => $log->action_label)
            ->addColumn('module', fn(ActionLog $log) => $log->module)
            ->addColumn('happened_at', fn(ActionLog $log) =>
                optional($log->happened_at)->format('Y-m-d H:i') ?: '—'
            )
            ->filterColumn('module', function ($query, $keyword) {
                $query->where('target_type', 'like', "%{$keyword}%");
            })
            ->toJson();
    }

    private function inferDeceasedName(ActionLog $log): ?string
    {
        $target = $log->target;

        if ($target instanceof Renewal) {
            $d = $target->deceased ?: $target->deceased()->first();
            return $this->formatDeceased($d);
        }
        if ($target instanceof Exhumation) {
            $res = $target->reservation ?: $target->reservation()->first();
            $d = $res?->deceased ?: ($res ? $res->deceased()->first() : null);
            return $this->formatDeceased($d);
        }
        if ($target instanceof Reservation) {
            $d = $target->deceased ?: $target->deceased()->first();
            return $this->formatDeceased($d);
        }
        return null;
    }

    private function inferLocation(ActionLog $log): ?string
    {
        $target = $log->target;

        if ($target instanceof Renewal) {
            return $target->buried_at ?: $this->slotLabel($target->slot);
        }
        if ($target instanceof Exhumation) {
            $from = $this->slotLabel($target->fromSlot);
            if ($from) return $from;
            $to = $this->slotLabel($target->toSlot);
            return $to ?: ($target->current_location ?: null);
        }
        if ($target instanceof Reservation) {

            return $target->location_or_apt_level ?: $this->slotLabel($target->slot);
        }
        return null;
    }

    private function formatDeceased($d): ?string
    {
        if (!$d) return null;
        if (isset($d->full_name) && $d->full_name) return $d->full_name;

        $ln = $d->last_name ?? null;
        $fn = $d->first_name ?? null;
        $mn = $d->middle_name ?? null;
        $sx = $d->suffix ?? null;

        $parts = [];
        if ($ln) $parts[] = strtoupper($ln).',';
        if ($fn) $parts[] = strtoupper($fn);
        if ($mn) $parts[] = strtoupper($mn);
        if ($sx) $parts[] = strtoupper($sx);

        $name = trim(implode(' ', $parts));
        return $name !== '' ? $name : null;
    }

    private function slotLabel(?Slot $slot): ?string
    {
        if (!$slot || !$slot->cell || !$slot->cell->level) return null;
        $cell  = $slot->cell;
        $level = $cell->level;
        $site  = $level->apartment;

        return sprintf('%s • Level %s R%s C%s S%s',
            $site?->name, $level->level_no, $cell->row_no, $cell->col_no, $slot->slot_no
        );
    }
}
