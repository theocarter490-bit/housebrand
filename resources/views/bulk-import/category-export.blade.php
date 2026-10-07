<table>
    <thead>
        <tr>
            <td colspan="2" align="center" style="font-size:18px; margin-bottom:10px;">Category List</td>
        </tr>
        <tr width="600px">
            <th bgcolor="#dddddd" align="center" style="font-size:14px" width="250px">{{_trans('keyword.Category').' '._trans('keyword.Name')}}</th>
            <th bgcolor="#dddddd" align="center" style="font-size:14px" width="250px">{{_trans('keyword.ID')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $category)
            <tr>
                <td align="center" style="border-bottom:1px solid #dddddd;font-size:14px">{{ $category->name }}</td>
                <td align="center" style="border-bottom:1px solid #dddddd;font-size:14px">{{ $category->id }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
