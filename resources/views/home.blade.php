@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                  {!! Form::open(['action' => 'HomeController@updateLibraries', 'method' => 'post']) !!}
                    <div class="form-group">
                      <label for="libraries">Jelenleg figyelt könyvtárak:</label>
                      <select class="selectpicker" multiple name="libraries[]" id="libraries" data-width="100%" data-live-search="true" data-actions-box="true">
                        @foreach($libraries as $library)
                          @if(Auth::user()->libraries->contains($library->id))
                            <option value="{{$library->id}}" selected>{{$library->name}}</option>
                          @else
                            <option value="{{$library->id}}">{{$library->name}}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                    {{Form::submit('Könyvtárak frissítése', ['class'=>'btn btn-primary'])}}
                  {!! Form::close() !!}
                  <h3>Feliratkozásaid</h3>
                  <p><a href="/subscriptions/create" class="btn btn-default">Új feliratkozás</a></p>
                  @if(count($subscriptions) > 0)
                    <table class="table table-striped">
                      <tr>
                        <th>Cím</th>
                        <th>recnum</th>
                        <th>Állapot</th>
                        <th></th>
                        <th></th>
                      </tr>
                      @foreach($subscriptions as $subscription)
                        <tr>
                          <td>{{$subscription->title}}</td>
                          <td>{{$subscription->recnum}}</td>
                          <td>{{$subscription->state->description}}</td>
                          @if($subscription->state_id == 1)
                            <td><a href="/subscriptions/{{$subscription->id}}/edit" class="btn btn-default">Felfüggesztés</a></td>
                          @else
                            <td><a href="/subscriptions/{{$subscription->id}}/edit" class="btn btn-default">Újraindítás</a></td>
                          @endif
                          <td>
                            {!!Form::open(['action' => ['SubscriptionsController@destroy', $subscription->id], 'method' => 'POST'])!!}
                              {{Form::hidden('_method','DELETE')}}
                              {{Form::submit('Törlés', ['class'=>'btn btn-danger'])}}
                            {!!Form::close()!!}
                          </td>
                        </tr>
                      @endforeach
                    </table>
                    {{$subscriptions->links()}} <!-- pagination buttons -->
                  @else
                    Még nincsen feliratkozásod.
                  @endif
                <p>

                </p>
            </div>
        </div>
    </div>
</div>
@endsection
