<h3>List {{$info}}</h3>

<table cellpadding="10">
  <tr>
    <th>File</th>
    <th>Size</th>
    <th>Last Modify</th>
  </tr>
  @foreach($data as $key => $value)
  <tr>
    <td><a href="{{$value['link']}}">{{$value['name']}}</a></td>
    <td>{{$value['size']}}</td>
    <td>{{$value['lastModify']}}</td>
  </tr>
  @endforeach
</table>
@if($info=="Logs" && $max <= count($data))
<div style="text-align: center;"><a href="{{route('list_log', ['max' => $max])}}">Loadmore...</a></div>
@endif