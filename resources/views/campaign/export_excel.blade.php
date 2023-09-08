<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Sender Email</th>
            <th>Sender Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $value)
        <tr>
            <td>{{ $value->name }}</td>
            <td>{{ $value->sender_name }}</td>
            <td>{{ $value->sender_email }}</td>
        </tr>
        @endforeach
    </tbody>
</table>