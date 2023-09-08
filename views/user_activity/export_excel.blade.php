<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Username</th>
            <th>Page</th>
            <th>Activity</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($data))
        @foreach($data as $value)
        <tr>
            <td>{{ $value->created_at }}</td>
            <td>{{ $value->username }}</td>
            <td>{{ $value->page }} / {{ $value->action }} {{ $value->page }}</td>
            @if (is_array($value->activity) || is_object($value->activity))
            <td>
                @foreach ($value->activity as $key => $items)
                @if ($key === 'extra')
                {{ $items }}<br>
                @elseif ($key === 'id')
                {{ $value->page }} {{ strtoupper($key) }}: {{ $items }}<br>
                @elseif ($key === 'name')
                {{ $value->page }} {{ ucfirst($key) }}: {{ $items }}<br>
                @else
                {{ $key }} = {{ print_r($items, true) }} <br>
                @endif
                @endforeach
            </td>
            @else
            <td>
                {{ $value->activity }}
            </td>
            @endif

        </tr>
        @endforeach
        @endif
    </tbody>
</table>