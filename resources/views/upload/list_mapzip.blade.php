<h3>List {{$info}}</h3>

<ul>
	@foreach($data as $key => $value)
	<li>
		<a href="{{$value['link']}}">{{$value['name']}}</a>
	</li>
	@endforeach
</ul>