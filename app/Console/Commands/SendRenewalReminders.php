<?php

namespace App\Console\Commands;

use App\Mail\BurialSlotRenewalNotice;
use App\Models\ActionLog;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendRenewalReminders extends Command
{
    protected $signature = 'renewals:send-reminders {--dry-run : Show what would be emailed without sending}';
    protected $description = 'Email applicants when renewal expiry is within 6 months.';

    public function handle(): int
    {
        $tz   = 'Asia/Manila';
        $now  = Carbon::now($tz)->startOfDay();
        $edge = $now->copy()->addMonthsNoOverflow(6)->endOfDay();


        $q = Reservation::active()
            ->with(['slot.cell.level.apartment', 'latestApprovedRenewal', 'deceased'])
            ->whereNotNull('applicant_email');

        $sent = 0;

        $q->chunkById(200, function ($reservations) use ($now, $edge, $tz, &$sent) {
            foreach ($reservations as $res) {
                $coverageEnd = $this->coverageEndForReservation($res, $tz);
                if (!$coverageEnd) continue;


                if ($coverageEnd->lt($now) || $coverageEnd->gt($edge)) continue;


                $alreadySent = ActionLog::where('action', 'renewal.reminder_sent')
                    ->where('target_type', \App\Models\Reservation::class)
                    ->where('target_id', $res->id)
                    ->where('happened_at', '>=', $now->copy()->subDays(200))
                    ->exists();
                if ($alreadySent) continue;


                [$deceasedList, $slotRef] = $this->namesOrSlotRef($res);
                $client = trim($res->applicant_name) ?: 'Applicant';
                $expStr = $coverageEnd->format('m/d/Y');

              if ($this->option('dry-run')) {
    $this->line("- DRY: would email {$res->applicant_email} (Res #{$res->id}) | {$client} | who='".($deceasedList ? implode(', ', $deceasedList) : $slotRef)."' | expires={$expStr}");
    $sent++;
    continue;
}

                try {
                    Mail::to($res->applicant_email)
                        ->send(new BurialSlotRenewalNotice($client, $deceasedList, $slotRef, $expStr));


                    ActionLog::create([
                        'user_id'     => null,
                        'username'    => 'system',
                        'action'      => 'renewal.reminder_sent',
                        'target_type' => \App\Models\Reservation::class,
                        'target_id'   => $res->id,
                        'happened_at' => now($tz),
                        'details'     => [
                            'coverage_end' => $coverageEnd->toDateString(),
                            'email'        => $res->applicant_email,
                        ],
                    ]);

                    $sent++;
                } catch (\Throwable $e) {
                    $this->error("Failed to email {$res->applicant_email} for reservation {$res->id}: ".$e->getMessage());
                }
            }
        });

        $this->info($this->option('dry-run')
            ? "DRY RUN finished. {$sent} emails would be sent."
            : "Sent {$sent} renewal reminder email(s).");

        return Command::SUCCESS;
    }


    private function coverageEndForReservation(Reservation $res, string $tz): ?Carbon
    {
        if ($res->latestApprovedRenewal && $res->latestApprovedRenewal->renewal_end) {
            return Carbon::parse($res->latestApprovedRenewal->renewal_end, $tz)->startOfDay();
        }
        if ($res->internment_sched) {
            return Carbon::parse($res->internment_sched, $tz)->startOfDay()->addYears(5);
        }
        $slot = $res->slot;
        if ($slot && $slot->occupancy_start) {
            return Carbon::parse($slot->occupancy_start, $tz)->startOfDay()->addYears(5);
        }
        return null;
    }


    private function namesOrSlotRef(Reservation $res): array
    {
        $slot   = $res->slot;
        $cellId = $slot?->grave_cell_id;

        $names = [];
        if ($cellId) {
            $siblings = Reservation::active()
                ->whereHas('slot', fn($q) => $q->where('grave_cell_id', $cellId))
                ->with('deceased')
                ->get();

            foreach ($siblings as $r) {
                $d = $r->deceased;
                if (!$d) continue;
                $last   = strtoupper(trim((string)$d->last_name));
                $first  = strtoupper(trim((string)$d->first_name));
                $middle = strtoupper(trim((string)$d->middle_name));
                $suffix = strtoupper(trim((string)$d->suffix));
                $full   = $last ?: '';
                if ($first)  $full .= ($full ? ', ' : '') . $first;
                if ($middle) $full .= ' ' . $middle;
                if ($suffix) $full .= ' ' . $suffix;
                if ($full !== '') $names[] = $full;
            }
            $names = array_values(array_unique($names));
        }

        $slotRef = $slot?->location_label;
        return [$names, $slotRef];
    }
}
