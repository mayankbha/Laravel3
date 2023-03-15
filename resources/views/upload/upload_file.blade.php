{{ Form::open(array('url'=>'api/uploadvideo','files'=>true)) }}
  
  {{ Form::label('file','Upload file',array('id'=>'','class'=>'')) }} <br/>
  {{ Form::file('file','',array('id'=>'','class'=>'')) }} <br/>

  <input type="text" name="title" placeholder="title" /> <br/>
  <input type="text" name="datetime" placeholder="datetime" value="{{\Carbon\Carbon::now()->timestamp}}" /> <br/>
   <input type="text" name="game" placeholder="game" /> <br/>
    <input type="text" name="type" placeholder="type" /> <br/>
<input type="text" name="views" placeholder="views" /> <br/>
<input type="text" name="likes" placeholder="likes" /> <br/>
  <input type="text" name="token" placeholder="token" value="{{$token}}" /> <br/>
  <!-- submit buttons -->
  {{ Form::submit('Upload', array('class' => 'small radius button')) }}
  
{{ Form::close() }}