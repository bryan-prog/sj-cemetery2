<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BURIAL PERMIT</title>
    <link rel="icon" href="{{asset('/assets/img/sjc.png')}}" type="image/png">
    @inject('carbon', 'Carbon\Carbon')
    <style>
        body{
            font-family:Arial, Helvetica, sans-serif;
        }
        p,span, li, th, td{
            font-family: Arial, Helvetica, sans-serif;
            font-size:0.84em;
        }
        .headline{
            text-align:Center;
        }
        hr.thick {
            height: 1px;
            background-color: black;
            border: none;
        }
        li{
            margin-bottom:5px;
        }
    </style>
</head>
<body>
@php
    function pdf_src(string $relPath): ?string {
        $abs = public_path($relPath);
        if (!is_file($abs)) return null;
        $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
        $map = ['jpg'=>'jpeg','jpeg'=>'jpeg','png'=>'png','gif'=>'gif','svg'=>'svg+xml','webp'=>'webp'];
        $sub = $map[$ext] ?? $ext;
        $mime = ($ext === 'svg') ? 'image/svg+xml' : "image/{$sub}";
        return "data:{$mime};base64," . base64_encode(file_get_contents($abs));
    }

    function ordinal_int(int $n): string {
        $v = $n % 100;
        if ($v >= 11 && $v <= 13) return $n.'th';
        $suf = ['th','st','nd','rd','th','th','th','th','th','th'][$n % 10];
        return $n . $suf;
    }


    function fmt_date($val): string {
        if (!$val || $val === '—') return '—';
        try {
            $c = $val instanceof \Carbon\Carbon ? $val : \Carbon\Carbon::parse($val);
            return $c->format('F j, Y');
        } catch (\Throwable $e) {
            return (string)$val;
        }
    }


    function fmt_internment($val): string {
        if (!$val || $val === '—') return '—';
        try {
            $c = $val instanceof \Carbon\Carbon ? $val : \Carbon\Carbon::parse($val);
            return $c->format('F j, Y l \@ g:ia');
        } catch (\Throwable $e) {
            return (string)$val;
        }
    }

    $DATE_APPLIED = fmt_date($date_applied ?? $r->date_applied ?? ($burial->date_applied ?? null));
    $DOB          = fmt_date($dob ?? $r->deceased?->date_of_birth ?? $r->deceased?->dob_ymd ?? null);
    $DOD          = fmt_date($deathDate ?? $dod ?? $r->deceased?->date_of_death ?? $r->deceased?->dod_ymd ?? null);


    $INTERNMENT   = fmt_internment($internment ?? $r->internment_sched ?? null);

    $APPLICANT_NAME = $applicant_name
        ?? ($burial->requesting_party ?? null)
        ?? trim(collect([$r->applicant_first_name,$r->applicant_middle_name,$r->applicant_last_name,$r->applicant_suffix])->filter()->implode(' '))
        ?: '—';

    $APPLICANT_ADDR    = $applicant_addr    ?? ($burial->applicant_address ?? $r->applicant_address ?? '—');
    $APPLICANT_CONTACT = $applicant_contact ?? ($burial->contact ?? $r->applicant_contact_no ?? '—');
    $RELATIONSHIP      = $relationship      ?? ($burial->relationship_to_deceased ?? $r->relationship_to_deceased ?? '—');

    $DECEASED_NAME = $deceasedName
        ?? trim(collect([
            $r->deceased?->first_name,
            $r->deceased?->middle_name,
            $r->deceased?->last_name,
            $r->deceased?->suffix,
        ])->filter()->implode(' '))
        ?: '—';

    $DECEASED_ADDR = $deceased_addr ?? ($r->deceased?->address_before_death ?? '—');

    if (!isset($burialLocation) || !$burialLocation) {
        $levelObj = $r->slot?->cell?->level ?? $r->level;
        $aptName  = $levelObj?->apartment?->name ?? $r->burialSite?->name;
        $lvlNo    = (int)($levelObj?->level_no ?? 0);
        $aptLevel = $aptName ? ($aptName . ($lvlNo ? (', ' . ordinal_int($lvlNo) . ' Level') : '')) : null;
        $precise  = $r->slot?->location_label;
        $burialLocation = $precise ?: ($aptLevel ?: ($r->burialSite?->name ?? '—'));
    }

    $FEE        = $feeNumeric ?? (is_null($r->amount_as_per_ord) ? '—' : number_format((float)$r->amount_as_per_ord, 2));
    $FUNERAL    = $funeral_service ?? ($r->funeral_service ?? '—');
    $GRAVE_DGR  = $grave_digger    ?? ($r->grave_diggers?->name ?? '—');
    $OTHER_INFO = $other_info      ?? ($r->other_info ?? '—');

    $VERIFIER_NAME = $verifier ?? ($r->verifiers?->name_of_verifier ?? '—');
    $VERIFIER_POS  = $r->verifiers?->position ?? '—';

    $img_bgp   = pdf_src('assets/img/bgp.png');
    $img_sjc   = pdf_src('assets/img/sjc.png');
    $img_mayor = pdf_src('assets/img/mayor.png');
@endphp


    <div class="container">
       <div class="row" style="text-align: center;">
            @if($img_bgp)   <img src="{{ $img_bgp }}" style="width: 12%"> @endif
            @if($img_sjc)   <img src="{{ $img_sjc }}" style="width: 12%"> @endif
            @if($img_mayor) <img src="{{ $img_mayor }}" style="width: 12%"> @endif
        </div>
        <div class="headline">
            <span><b>REPUBLIC OF THE PHILIPPINES</b></span><br>
            <span><b>CITY OF SAN JUAN, METRO MANILA</b></span><br>
            <span><b>-oOo-</b></span><br>
            <span><b>CITY CEMETERY OFFICE</b></span>
        </div>
    </div>
    <div class="row" style="text-align:center">
        <h4 style="margin-bottom:0 !important">APPLICATION FOR BURIAL PERMIT</h4>
        <span style="font-size: 0.7em !important">(Under section 5E.05 Article E, Chapter V of Ordinance No. 47, Series of 2003 San Juan Municipal Revenue Code of 2002)</span>
    </div>
    <div class="row" style="margin-bottom:-15px !important">
        <ul>
            <li>DATE APPLIED
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $DATE_APPLIED }}</b></span>
            </li>
            <li>APPLICANT'S NAME
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $APPLICANT_NAME }}</b></span>
            </li>
            <li>ADDRESS
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $APPLICANT_ADDR }}</b></span>
            </li>
            <li>CONTACT NUMBER
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $APPLICANT_CONTACT }}</b></span>
            </li>
            <li>RELATIONSHIP TO THE PERSON TO BE BURIED
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $RELATIONSHIP }}</b></span>
            </li>
            <li>NAME OF THE DEAD PERSON
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $DECEASED_NAME }}</b></span>
            </li>
            <li>ADDRESS OF THE DEAD PERSON <br>BEFORE DEATH
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $DECEASED_ADDR }}</b></span>
            </li>
            <li>DATE OF BIRTH
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $DOB }}</b></span>
            </li>
            <li>DATE OF DEATH
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $DOD }}</b></span>
            </li>
            <li>BURIAL SITE
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;">
                    <b>{{ $burialLocation ?? '—' }}</b>
                </span>
            </li>
            <li>AMOUNT TO BE PAID as per <br>Ordinance No. 69 Series of 2022
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $FEE }}</b></span>
            </li>
            <li>FUNERAL SERVICE
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $FUNERAL }}</b></span>
            </li>
            <li>ASSIGNED GRAVE DIGGER
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $GRAVE_DGR }}</b></span>
            </li>
            <li>INTERNMENT SCHEDULE
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $INTERNMENT }}</b></span>
            </li>
            <li>Other pertinent information, if Any
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $OTHER_INFO }}</b></span>
            </li>
        </ul>
    </div>
    <div class="row">
        <table style="width:100%;">
            <tr style="text-align:center;">
                <th><h5>LOCATION CONFIRMATION/VERIFICATION:</h5></th>
                <th><h5>RECOMMENDING APPROVAL :</h5></th>
            </tr>
            <tr>
                <td style="height:15px;"></td>
            </tr>
            <tr style="text-align:center;">

                <th>{{ $VERIFIER_NAME }}</th>
                <th>DRA. MITZI TANCHOCO, MD, MMHoa</th>
            </tr>
            <tr style="text-align:center;">

                <td>{{ $VERIFIER_POS }}</td>
                <td>City Health Officer II</td>
            </tr>
        </table>
    </div>
    <div class="row" style="text-align:center;">
        <span><b>APPROVED:</b></span>
        <br><br><br>
        <p><b>FRANCISCO JAVIER M. ZAMORA</b></p>
        <p style="position:relative; top:-10px;">CITY MAYOR</p>
        <br>
        <span>BY THE AUTHORITY OF THE MAYOR</span>
        <br><br><br>
        <p><b>ENGR. GILBERT O. LABRADORES</b></p>
        <p style="position:relative; top:-5px;">Supervising Administrative Officer</p>
        <p style="position:relative; top:-10px;">San Juan City Cemetery Office</p>
    </div>
</body>
</html>
