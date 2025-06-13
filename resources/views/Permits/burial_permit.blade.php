<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BURIAL PERMIT APPLICATION</title>
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
            <p style="position: absolute; left: 15%; text-transform:uppercase;"><b><u>{{$date}}</u></b></p><br>
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
            <li>Applicant's Name <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><u><b>virgiia f. villarmino</b></u></span></li>
            <li>Address <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><u><b>virgiia f. villarmino</b></u></span></li>
            <li>Contact Number <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><u><b>virgiia f. villarmino</b></u></span></li>
            <li>Relationship to the Deceased <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><u><b>virgiia f. villarmino</b></u></span></li>
        </ul>
    </div>
    <div class="row" style="margin-bottom: -10px;">
        <p style="text-indent: 30px;">We hereby confirm that the late</p>
    </div>
    <div class="row" style="margin-bottom: -10px;margin-left:10%;">
        <ul>
            <li>Name of the Deceased <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><u><b>virgiia f. villarmino</b></u></span></li>
            <li>Who has died on (Date of Death) <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><u><b>virgiia f. villarmino</b></u></span></li>
            <li>Who was buried at (Burial Site) <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><u><b>virgiia f. villarmino</b></u></span></li>
        </ul>
    </div>
    <div class="row" style="margin-bottom: -10px;">
        <p style="text-indent: 30px;">With the following recommendations</p>
    </div>
    <div class="row" style="margin-bottom: -10px;margin-left:10%;">
        <ul>
            <li>Renewal Period <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><u><b>virgiia f. villarmino</b></u></span></li>
            <li>Fee as per Ordinance No. 47 <br>Series of 2003 <span style="position:absolute; left:45%">:</span> <span style="text-transform:uppercase; position:absolute; left:48%"><u><b>virgiia f. villarmino</b></u></span></li>
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
                <th>LUIGI T. PASCUAL</th>
                <th>ENGR. GILBERT O. LABRADORES</th>
            </tr>
            <tr style="text-align:center;">
                <td>SAN JUAN CEMETERY STAFF</td>
                <td>ICO - SAN JUAN CITY CEMETERY AFFAIRS OFFICE</td>
            </tr>
        </table>
    </div>
</body>
</html>