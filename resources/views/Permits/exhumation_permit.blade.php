<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EXHUMATION REQUEST PERMIT</title>
    <link rel="icon" href="{{asset('/assets/img/sjc.png')}}" type="image/png">
    @inject('carbon', 'Carbon\Carbon')
    <style>

        body{
            font-family:Arial, Helvetica, sans-serif;
        }
        p,span, li, th, td{
            font-family: Arial, Helvetica, sans-serif;
            font-size:0.83em;
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
            <span><b>CEMETERY AFFAIRS OFFICE</b></span>
        </div>
        <div class="container" style="margin-right:20px; margin-left:20px;">
            <div class="row" style="text-align: center; margin-bottom:10px;">
                <p><b>REQUEST FOR EXHUMATION PERMIT</b></p>
            </div>
            <div class="row" style="text-align: right; margin-bottom:10px;">
                <span><b>{{ $dateIssued ?? now()->format('F d, Y') }}</b></span>
            </div>
            <div class="row">
                <p style="text-indent: 30px; text-align: justify;">
                    The underasigned would like to request for <b>EXHUMATION/TRANSFER PERMIT</b> of the late <b><u style="text-transform:uppercase;">{{ $deceasedName ?? '—' }}</u></b> who died on <b><u style="text-transform:uppercase;">{{ $deathDate ?? '—' }}</u></b>
                    and was buried at San Juan City Cemetery located at <b><u style="text-transform:uppercase;">{{ $burialLocation ?? '—' }}</u></b>.
                </p>
            </div>
            <div class="row">
                <ul>
                    <li>Requesting Party <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><b>{{ $exhumation->requesting_party ?? '—' }}</b></span></li>
                    <li>Address <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><b>{{ $exhumation->address ?? '—' }}</b></span></li>
                    <li>Contact Number <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><b>{{ $exhumation->contact ?? '—' }}</b></span></li>
                    <li>Relationship to the Deceased <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><b>{{ $exhumation->relationship_to_deceased ?? '—' }}</b></span></li>
                    <li>Fee as per Ordinance No. 63  <br>Series of 2022 <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"> <b>{{ $feeNumeric ?? '—' }}</b></span></li>
                </ul>
            </div>
            <div class="row">
                <p><b style="text-transform:uppercase;">(FOR TRANSFER: {{ $transferDestination ?? '—' }}.)</b></p>
            </div>
        </div>
        <div class="row" style="text-align:right;">
            <p><b>REQUESTED BY:</b></p><br>
              <p><b>{{ $exhumation->requesting_party ?? '' }}</b></p>
            <p>Signature over Printed Name</p>

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
    </div>
</body>
</html>
