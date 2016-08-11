@extends('layouts.app')
@section('content')
<div class='container'>
    <div class='col-lg-6 col-lg-push-3'>
        {!! Form::open(['action'=>'CsvImportController@upload','method'=>'POST', 'files'=>true,'class' => 'form-horizontal']) !!}
        <div class='form-group'>
        {!! Form::label('csv_upload', 'Select File', ['class' => 'control-label']) !!}
        {!! Form::file('csv', ['class' => 'file', 'data-show-preview' => false, 'id' => 'csv_upload']) !!}
        </div>
        <div class='form-group'>
        {!! Form::submit('Submit', array('class'=>'btn btn-primary pull-right')) !!}
        </div>
        {!! Form::close() !!}    
    </div>
</div>
<script>
    jQuery(function () {
        jQuery('#csv_upload').fileinput();
    });

</script>
@endsection