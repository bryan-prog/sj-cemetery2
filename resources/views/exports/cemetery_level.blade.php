<table>

    <tr><td colspan="12"></td></tr>


    <tr>
        <td><strong>PROPERTY TYPE</strong></td><td>{{ $meta['propertyType'] }}</td>
        <td></td>
        <td><strong>BUILDING TYPE</strong></td><td>{{ $meta['buildingType'] }}</td>
        <td></td>
        <td><strong>LOCATION</strong></td><td>{{ $meta['location'] }}</td>
        <td></td>
        <td><strong>BURIAL TYPE</strong></td><td>{{ $meta['burialType'] }}</td>
    </tr>
    <tr>
        <td><strong>LEVEL</strong></td><td>{{ $meta['levelShort'] }}</td>
        <td></td>
        <td><strong>ROW NUMBER</strong></td><td>{{ $meta['rowShort'] }}</td>
    </tr>


    <tr><td colspan="12"></td></tr>


    <tr>
       {{-- <td><strong>Level</strong></td>--}}
        <td><strong>Row No.</strong></td>
        <td><strong>Column No.</strong></td>

        <td colspan="3" style="text-align:center;"><strong> Name of Deceased</strong></td>

        <td><strong>Sex</strong><br/><em>(Optional)</em></td>

        <td><strong>Date of Birth</strong><br/><em>mm/dd/yyyy</em></td>
        <td><strong>Date of Death</strong><br/><em>mm/dd/yyyy</em></td>

        <td><strong>Year of Renewal</strong></td>
        <td><strong>Contact Person</strong><br/><em>(Optional)</em></td>
        <td><strong>Contact Person</strong><br/><em>(Optional)</em></td>
    </tr>
 


    @foreach ($rows as $r)
        <tr>
            {{--<td>{{ $r['level'] }}</td>--}}
            <td>{{ $r['row_no'] }}</td>
            <td>{{ $r['col_no'] }}</td>

            <td>{{ $r['first'] }} {{ $r['mi'] }} {{ $r['surname'] }}</td>
            <td></td>
            <td></td>
            <td>{{ $r['sex'] }}</td>
            <td>{{ $r['dob'] }}</td>
            <td>{{ $r['dod'] }}</td>

            <td>{{ $r['renew'] }}</td>
            <td></td>
            <td>{{ $r['contact2'] }}</td>
        </tr>
    @endforeach
</table>
