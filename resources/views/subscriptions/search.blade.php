@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Keresés</div>
            <div class="panel-body">
                {!! Form::open(['action' => 'SubscriptionsController@search', 'method' => 'post']) !!}
                <div class="form-group">
                  {{Form::label('text','Keresőkifejezés')}}
                  {{Form::text('text', '', ['class' => 'form-control', 'placeholder' => 'Keresőkifejezés'])}}
                </div>
                <div class="form-group">
                  <select class="form-control" name="index" id="index">
                    <option value="ZUTY">Kulcssz&oacute;</option>
                    <option value="AUTH">Szerző, k&ouml;zreműk&ouml;dő</option>
                    <option value="TITL">C&iacute;m, c&iacute;m szavai</option>
                    <option value="SUBJ">T&aacute;rgysz&oacute;</option>
                    <option value="ZTSZ">Zenei t&aacute;rgysz&oacute;</option>
                    <option value="IROD">T&aacute;rgysz&oacute; szakbibliogr&aacute;fi&aacute;k</option>
                    <option value="PUBL">Kiad&oacute;</option>
                    <option value="CALL">Rakt&aacute;ri jelzet</option>
                    <option value="CUTT">Aktu&aacute;lis jelzet</option>
                    <option value="HDZO">Adathordoz&oacute;</option>
                    <option value="TCHN">Technika (k&eacute;pekn&eacute;l)</option>
                    <option value="URLI">Internet-c&iacute;m</option>
                    <option value="PERF">Szereplő, előad&oacute;</option>
                    <option value="PERS">Szem&eacute;lyn&eacute;v</option>
                    <option value="CORP">Test&uuml;leti n&eacute;v</option>
                    <option value="ISBN">ISBN</option>
                    <option value="ISSN">ISSN</option>
                    <option value="MUSN">Gy&aacute;ri sz&aacute;m (av)</option>
                    <option value="TEM2">T&eacute;macsoport (csal&aacute;di k&ouml;nyvt&aacute;rak)</option>
                    <option value="SZCS">Szakcsoport</option>
                    <option value="DOKU">Forr&aacute;sdokumentum</option>
                    <option value="KIVN">Kivonat</option>
                    <option value="UDCO">Oszt&aacute;lyoz&aacute;s (ETO,TO,zenei)</option>
                    <option value="UD0O">ETO-jelzet</option>
                    <option value="UD1O">TO-jelzet</option>
                  </select>
                </div>
                {{Form::submit('Keresés', ['class'=>'btn btn-primary'])}}
              {!! Form::close() !!}
              @if(isset($results))
                <hr>
                @if(count($results) > 0)
                {!! Form::open(['action' => 'SubscriptionsController@store', 'method' => 'post']) !!}
                <div class="form-group">
                {{Form::submit('A kiválasztottak figyelése', ['class'=>'btn btn-primary pull-right', 'id' => 'subscribeToMany', 'name' => 'subscribeToMany', 'disabled' => 'disabled'])}}
              </div>
                @php
                  $i=0;
                @endphp
                <table class="table table-striped" id="searchresult">
                  <tr>
                    <th>Szerző</th>
                    <th>Cím</th>
                    <th>Dátum</th>
                    <th>Típus</th>
                    <th></th>
                    <th></th>
                  </tr>
                  @foreach($results as $result)
                    <tr>
                      <td>{{$result['author']}}</td>
                      <td>{{$result['title']}}</td>
                      <td>{{$result['publishdate']}}</td>
                      <td>{{$result['type']}}</td>
                      <td>
                          <div class="form-group">
                            {{Form::hidden('title_'.$i, $result['title'])}}
                          </div>
                          <div class="form-group">
                            {{Form::hidden('recnum_'.$i, $result['recnum'])}}
                          </div>
                          <div class="form-group">
                            {{ Form::checkbox('subscribes['.$i.']', $i) }}
                          </div>
                      </td>
                      <td>
                          {{Form::button('Figyelés', ['class'=>'btn btn-primary subscribeToOne', 'id' => $result['recnum'], 'value' => $i, 'name' => 'subscribeToOne', 'type' => 'submit'])}}
                      </td>
                    </tr>
                    @php
                      $i++;
                    @endphp
                  @endforeach
                  </table>
                  {!! Form::close() !!}
                @else
                Nincs találat, próbáld újra
                @endif
              @endif
            </div>
      </div>
    </div>
</div>
<script>
$(document).ready(function() {
  $('#searchresult tr').click(function(event) {
    if (event.target.type !== 'checkbox') {
      $(':checkbox', this).trigger('click');
    }
  });
  $(':checkbox').change(function(){ //change
    if ($(':checkbox:checked').length == 0) { //no checkbox checked
      $('#subscribeToMany').attr("disabled", "disabled");
      $('.subscribeToOne').removeAttr("disabled");
      console.log('no checkbox');
    } else { //at least one checkbox is checked
      $('.subscribeToOne').attr("disabled", "disabled");
      $('#subscribeToMany').removeAttr("disabled");
      console.log('some checkbox');
    }
  });
});
</script>
@endsection
