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
        li{
            margin-bottom:5px;
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
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>VALUE</b></span>
            </li>
            <li>APPLICANT'S NAME 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $burial?->requesting_party ?? '—' }}</b></span>
            </li>
            <li>ADDRESS 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>VALUE</b></span>
            </li>
            <li>CONTACT NUMBER 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>VALUE</b></span>
            </li>
            <li>RELATIONSHIP TO THE PERSON TO BE BURIED 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>VALUE</b></span>
            </li>
            <li>NAME OF THE DEAD PERSON 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $deceasedName ?? '—' }}</b></span>
            </li>
            <li>ADDRESS OF THE DEAD PERSON <br>BEFORE DEATH 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>VALUE</b></span>
            </li>
            <li>DATE OF BIRTH 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b></b></span>
            </li>
            <li>DATE OF DEATH 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $deathDate ?? '—' }}</b></span>
            </li>
            <li>BURIAL SITE 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $burialLocation ?? '—' }}</b></span>
            </li>
            <li>AMOUNT TO BE PAID as per <br>Ordinance No. 69 Series of 2022 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>{{ $feeNumeric ?? '—' }}</b></span>
            </li>
            <li>FUNERAL SERVICE 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>VALUE</b></span>
            </li>
            <li>ASSIGNED GRAVE DIGGER 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>VALUE</b></span>
            </li>
            <li>INTERNMENT SCHEDULE 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>VALUE</b></span>
            </li>
            <li>Other pertinent information, if Any 
                <span style="position: absolute; left: 51%;">:</span>
                <span style="text-transform:uppercase; position:absolute; left:52%; border-bottom:1px solid black; width:45%;"><b>VALUE</b></span>
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
                <th>NAME OF VERIFIER</th>
                <th>NAME OF RECOMMENDER</th>
            </tr>
            <tr style="text-align:center;">
                <td>POSITION OF VERIFIER</td>
                <td>POSITION OF RECOMMENDER</td>
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