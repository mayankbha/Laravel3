{{ Form::open(array('url'=>'api/uploadlog','files'=>true)) }}

{{ Form::label('file','Upload Log',array('id'=>'','class'=>'')) }} <br/>
{{ Form::file('file','',array('id'=>'','class'=>'')) }} <br/>

{{ Form::label('description','Upload decscription',array('id'=>'','class'=>'')) }} <br/>
<input type="text" name="token" placeholder="token" /> <br/>
{{ Form::textarea('description','',array('id'=>'','class'=>'')) }} <br/>

<!-- submit buttons -->
{{ Form::submit('Upload', array('class' => 'small radius button')) }}

{{ Form::close() }}