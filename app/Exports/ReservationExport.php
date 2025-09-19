<?php

namespace App\Exports;

use App\Models\Reservation;
use App\Models\BurialSite;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class ReservationExport implements FromView, WithEvents
{
    public function __construct(public int $siteId, public int $levelNo) {}

    public function view(): View
    {
        $site = BurialSite::findOrFail($this->siteId);

        $rows = Reservation::query()
            ->active()
            ->forSiteLevel($this->siteId, $this->levelNo)
            ->with(['deceased','slot.cell.level'])
            ->orderBy('id')
            ->get()
            ->map(function ($r) {
                $cell = optional($r->slot)->cell;


                $full  = trim((string) optional($r->deceased)->name_of_deceased);
                $parts = preg_split('/\s+/', $full, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $first = $parts[0] ?? '';
                $last  = count($parts) > 1 ? array_pop($parts) : '';
                $mid   = count($parts) ? strtoupper(mb_substr(trim(implode(' ', $parts)), 0, 1)) : '';


                $dob = optional($r->deceased)->date_of_birth
                    ? Carbon::parse($r->deceased->date_of_birth)->format('d/m/Y') : 'NO DATE';
                $dod = optional($r->deceased)->date_of_death
                    ? Carbon::parse($r->deceased->date_of_death)->format('d/m/Y') : 'NO DATE';

                $startYear = $r->renewal_start ? Carbon::parse($r->renewal_start)->format('Y') : null;
                $endYear   = $r->renewal_end   ? Carbon::parse($r->renewal_end)->format('Y')   : null;
                $renew     = $startYear && $endYear && $startYear !== $endYear
                                ? "{$startYear} - {$endYear}"
                                : ($endYear ?: ($startYear ?: ''));

                return [

                    'level'    => optional($r->level)->level_no ?? optional($cell?->level)->level_no,
                    'row_no'   => $cell?->row_no,
                    'col_no'   => $cell?->col_no,
                    'first'    => $first,
                    'mi'       => $mid,
                    'surname'  => $last,
                    'sex'      => optional($r->deceased)->sex,
                    'dob'      => $dob,
                    'dod'      => $dod,
                    'renew'    => $renew,
                    'contact1' => $r->applicant_name,
                    'contact2' => '',
                ];
                
            })
               ->sortBy('col_no') 
               ->sortBy('row_no') 
                    ->values(); 


        $firstRowNo = $rows->pluck('row_no')->filter()->min() ?: 1;

        $meta = [
            'title'        => 'SAN JUAN CITY CEMETERY DATABASE',
            'propertyType' => 'PUBLIC',
            'buildingType' => $site->name,
            'location'     => 'LEFT SIDE',
            'burialType'   => 'BONES/URN',
            'levelShort'   => $this->ordinalShort($this->levelNo),
            'rowShort'     => $this->ordinalShort($firstRowNo),
        ];

        return view('exports.cemetery_level', compact('rows','meta'));
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $e) {
                $s = $e->sheet->getDelegate();


                $s->mergeCells('A1:L1');
                $s->setCellValue('A1', 'SAN JUAN CITY CEMETERY DATABASE');
                $s->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $s->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);




                // $s->mergeCells('A9:A10');
                // $s->mergeCells('B9:B10');
                // $s->mergeCells('C9:C10');

                // $s->mergeCells('D9:F9');
                // $s->mergeCells('G9:G10');
                // $s->mergeCells('H9:H10');
                // $s->mergeCells('I9:I10');
                // $s->mergeCells('J9:J10');
                // $s->mergeCells('K9:K10');
                // $s->mergeCells('L9:L10');


                // $s->getStyle('A9:L10')->getFont()->setBold(true);
                // $s->getStyle('A9:L10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // $s->getStyle('A9:L10')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                // $s->getRowDimension(9)->setRowHeight(20);
                // $s->getRowDimension(10)->setRowHeight(18);


                $last = $s->getHighestRow();
                $s->getStyle("A5:K{$last}")
                  ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


                foreach ([
                    'A'=>15,
                    'B'=>15,
                    'C'=>20,
                    'D'=>15,
                    'E'=>22,
                    'F'=>22,
                    'G'=>15,
                    'H'=>16,
                    'I'=>16,
                    'J'=>30,
                    'K'=>24,
                    'L'=>24,
                ] as $col=>$w) {
                    $s->getColumnDimension($col)->setWidth($w);
                }
               
               $last = $s->getHighestRow();
               $style = $s->getStyle("A5:K{$last}");
               $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
               $startRow = 6;
               $lastRow = $s->getHighestRow();
            for ($row = $startRow; $row <= $lastRow; $row++) 
                {
                    $s->mergeCells("C{$row}:E{$row}");
                }        

            
            }
        ];
    }

    private function ordinalShort(int $n): string
    {
        $n = (int) $n;
        $suffix = 'th';
        if (($n % 100) < 11 || ($n % 100) > 13) {
            $map = [1=>'st',2=>'nd',3=>'rd'];
            $suffix = $map[$n % 10] ?? 'th';
        }
        return strtoupper($n.$suffix);
    }
}
