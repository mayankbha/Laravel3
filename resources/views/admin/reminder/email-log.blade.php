<table class="table table-hover">
    <tbody><tr>
        <th>Reminder id</th>
        <th>User id</th>
        <th>Email</th>
        <th>Send At</th>
        <th>Template Id</th>
        <th>Current Status</th>
    </tr>
    @foreach($list_email_log as $item)
        <tr>
            <td>{{$item->user_reminder_id}}</td>
            <td>{{$item->user_id}}</td>
            <td>{{$item->email}}</td>
            <td>{{$item->updated_at}}</td>
            <td>{{$item->templateToString()}}</td>
            <td>{{$item->currentStatusToString()}}</td>
        </tr>
    @endforeach

    </tbody>
</table>