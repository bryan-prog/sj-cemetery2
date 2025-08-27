<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RENEWAL PERMIT</title>
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
    </style>
</head>
<body>
@php

    $renewal     = $renewal ?? null;
    $dec         = $dec      ?? ($renewal?->deceased);
    $dateApplied = $dateApplied
                   ?? ($renewal?->date_applied ? $renewal->date_applied->format('F d, Y') : '—');
    $dod         = $dod
                   ?? ($dec?->date_of_death ? \Carbon\Carbon::parse($dec->date_of_death)->format('F d, Y') : '—');

    $cell        = $renewal?->slot?->cell;
    $level       = $cell?->level;
    $apt         = $level?->apartment;

    $aptName     = $aptName ?? ($apt?->name ?? '—');
    $location    = $location ?? ($renewal?->buried_at);

    $periodLabel = $periodLabel
                   ?? (($renewal?->renewal_start && $renewal?->renewal_end)
                        ? $renewal->renewal_start->format('F d, Y').' – '.$renewal->renewal_end->format('F d, Y')
                        : '—');

   $feeNumeric = is_null($renewal->amount_as_per_ord) ? null : number_format((float)$renewal->amount_as_per_ord, 2);

@endphp

    <div class="container">
       <div class="row" style="text-align: center;">
            <img src="{{public_path('assets/img/bgp.png')}}" style="width: 12%">
            <img src="{{public_path('assets/img/sjc.png')}}" style="width: 12%">
            <img src="{{public_path('assets/img/mayor.png')}}" style="width: 12%">
        </div>
        <div class="headline">
            <span><b>REPUBLIC OF THE PHILIPPINES</b></span><br>
            <span><b>CITY OF SAN JUAN, METRO MANILA</b></span><br>
            <span><b>-oOo-</b></span><br>
            <span><b>CITY CEMETERY OFFICE</b></span>
        </div>
    </div>
    <div class="row">
        <h5>INTER-OFFICE LETTER</h5>
    </div>
    <div class="row" style="margin-bottom: 5%;">
        <div class="col-6">
            <p style="position: absolute; left: 0%;">TO</p>
            <p style="position: absolute; left: 10%;">:</p>
        </div>
        <div class="col-6">
            <p style="position: absolute; left: 15%;"><b>ATTY. DENNIS ALBERT S. PAMINTUAN</b></p><br>
            <p style="position: absolute; left: 15%;line-height:2;font-size:0.78em;">CITY ADMINISTRATOR</p>
        </div>
    </div>
    <div class="row" style="margin-bottom: 5%;">
        <div class="col-6">
            <p style="position: absolute; left: 0%;">FROM</p>
            <p style="position: absolute; left: 10%;">:</p>
        </div>
        <div class="col-6">
            <p style="position: absolute; left: 15%;"><b>MR. GILBERT O. LABRADORES</b></p><br>
            <p style="position: absolute; left: 15%;line-height:2;font-size:0.78em;">ICO-SAN JUAN CITY CEMETERY</p>
        </div>
    </div>
    <div class="row" style="margin-bottom: 2%;">
        <div class="col-6">
            <p style="position: absolute; left: 0%;">DATE</p>
            <p style="position: absolute; left: 10%;">:</p>
        </div>
        <div class="col-6">
            <p style="position: absolute; left: 15%; text-transform:uppercase;"><b>{{ $dateApplied }}</b></p><br>
        </div>
    </div>
    <div class="row" style="margin-bottom: 5%;">
        <div class="col-6">
            <p style="position: absolute; left: 0%;">SUBJECT</p>
            <p style="position: absolute; left: 10%;">:</p>
        </div>
        <div class="col-6">
            <p style="position: absolute; left: 15%;"><b>REQUEST FOR VERIFICATION / RENEWAL</b></p><br>
        </div>
    </div>
    <hr class="thick my-4">
    <div class="row" style="margin-bottom: -10px;">
        <p style="text-indent: 30px;">Upon inspection and verification conducted by this office on the availability of a certain lot area requested by:</p>
    </div>
    <div class="row" style="margin-bottom: -10px;margin-left:10%;">
        <ul>
            <li>Applicant's Name <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%; border-bottom:1px solid black; width:48%;"><b>{{ $renewal?->requesting_party ?? '—' }}</b></span></li>
            <li>Address <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%; border-bottom:1px solid black; width:48%;"><b>{{ $renewal?->applicant_address ?? '—' }}</b></span></li>
            <li>Contact Number <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%; border-bottom:1px solid black; width:48%;"><b>{{ $renewal?->contact ?? '—' }}</b></span></li>
            <li>Relationship to the Deceased <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%; border-bottom:1px solid black; width:48%;"><b>{{ $renewal?->relationship_to_deceased ?? '—' }}</b></span></li>
        </ul>
    </div>
    <div class="row" style="margin-bottom: -10px;">
        <p style="text-indent: 30px;">We hereby confirm that the late</p>
    </div>
    <div class="row" style="margin-bottom: -10px;margin-left:10%;">
        <ul>
            <li>Name of the Deceased <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%; border-bottom:1px solid black; width:48%;"><b>{{ $dec?->name_of_deceased ?? '—' }}</b></span></li>
            <li>Who has died on (Date of Death) <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%; border-bottom:1px solid black; width:48%;"><b>{{ $dod }}</b></span></li>
            <li>Who was buried at (Burial Site) <span style="position:absolute; left:45%">:</span>
                <span style="text-transform:uppercase; position:absolute; left:48%; border-bottom:1px solid black; width:48%;">
                    <b>{{ $aptName }}</b>
                    @if(!empty($location))
                        <span style="text-transform:none;"><b> ({{ $location }})</b></span>
                    @endif
                </span>
            </li>
        </ul>
    </div>
    <div class="row" style="margin-bottom: -10px;">
        <p style="text-indent: 30px;">With the following recommendations</p>
    </div>
    <div class="row" style="margin-bottom: -10px;margin-left:10%;">
        <ul>
          <li>Renewal Period <span style="position:absolute; left:45%">:</span>
  <span style="text-transform:uppercase; position:absolute; left:48%; border-bottom:1px solid black; width:48%;">
    <b>{{ $periodLabel }}</b>
  </span>
</li>
            <li>Fee as per Ordinance No. 47 <br>Series of 2003 <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%; border-bottom:1px solid black; width:48%;"> <b>{{ $feeNumeric ?? '—' }}</b>
</span></li>
        </ul>
    </div>
    <div class="row" style="margin-bottom: -10px;">
        <p style="text-indent: 30px;">If in case the remains will be transferred to another location, necessary report will be submitted to your office for your information.</p>
    </div>

    <div class="row" style="margin-bottom: 40px;">
        <h5>LOCATION VERIFIED BY:</h5>
    </div>
    <div class="row">
       <table style="width:100%;">
            <tr style="text-align:center;">
                <th>{{ $verifierName ?? '—' }}</th>
                <th>ENGR. GILBERT O. LABRADORES</th>
            </tr>
            <tr style="text-align:center;">
                <td>SAN JUAN CEMETERY STAFF</td>

                <td>SUPERVISING ADMINISTRATIVE OFFICER</td>
            </tr>
            <tr style="text-align:center;">
                <td></td>
                <td>SAN JUAN CITY CEMETERY</td>
            </tr>
        </table>
    </div>
</body>
</html>
