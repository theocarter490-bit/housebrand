<table>
    <thead>
        <tr>
            <td colspan="2" align="center" style="font-size:18px; margin-bottom:10px;">Brand List</td>
        </tr>
        <tr width="600px">
            <th bgcolor="#dddddd" align="center" style="font-size:14px" width="250px">{{_trans('keyword.Brand').' '._trans('keyword.Name')}}</th>
            <th bgcolor="#dddddd" align="center" style="font-size:14px" width="250px">{{_trans('keyword.ID')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($brands as $brand)
            <tr>
                <td align="center" style="border-bottom:1px solid #dddddd;font-size:14px">{{ $brand->name }}</td>
                <td align="center" style="border-bottom:1px solid #dddddd;font-size:14px">{{ $brand->id }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
