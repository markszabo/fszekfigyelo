@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Új feliratkozás</div>
            <div class="panel-body">
                {!! Form::open(['action' => 'SubscriptionsController@store', 'method' => 'post']) !!}
                <div class="form-group">
                  {{Form::label('title','Cím')}}
                  {{Form::text('title', '', ['class' => 'form-control', 'placeholder' => 'Cím'])}}
                </div>
                <div class="form-group">
                  {{Form::label('recnum','recnum')}}
                  {{Form::text('recnum', '', ['class' => 'form-control', 'placeholder' => 'recnum'])}}
                </div>
                {{Form::submit('Mentés', ['class'=>'btn btn-primary'])}}
              {!! Form::close() !!}
            </div>
      </div>
    </div>
</div>
@endsection
