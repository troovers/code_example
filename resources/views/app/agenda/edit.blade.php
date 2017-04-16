@extends('layouts.app')

@section('page_title', trans('default.' . $action, ['object' => 'App agenda item']))

@section('content')
    <form class="form-horizontal" method="POST" action="{{ url('/app/agenda/save') }}">
        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
            <label for="title" class="col-sm-2 control-label">Titel</label>
            <div class="col-sm-10 col-md-4">
                {!! \App\Model\Helpers\Html::input('title', old('title', $agenda_item->title), 'Titel') !!}
            </div>
            @if ($errors->has('title'))
                <span class="help-block"><strong>{{ $errors->first('title') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
            <label for="description" class="col-sm-2 control-label">Omschrijving</label>
            <div class="col-sm-6">
                <textarea name="description" id="description" rows="10" cols="80">{{ old('description', $agenda_item->description) }}</textarea>
            </div>
            @if ($errors->has('description'))
                <span class="help-block"><strong>{{ $errors->first('description') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('start') ? ' has-error' : '' }}">
            <label for="start_date" class="col-sm-2 control-label">Aanvang</label>
            <div class="col-sm-2">
                {!! \App\Model\Helpers\Html::input('start_date', old('start_date', $agenda_item->start->format('d-m-Y')), 'dd-mm-jjjj', 'text', '', '', '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>') !!}
            </div>
            <div class="col-sm-2">
                {!! \App\Model\Helpers\Html::input('start_time', old('start_time', $agenda_item->start->format('H:i')), 'hh:mm', 'text', '', '', '<span class="glyphicon glyphicon-time" aria-hidden="true"></span>') !!}
            </div>
            @if ($errors->has('start'))
                <span class="help-block"><strong>{{ $errors->first('start') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('end') ? ' has-error' : '' }}">
            <label for="end_date" class="col-sm-2 control-label">Einde</label>
            <div class="col-sm-2">
                {!! \App\Model\Helpers\Html::input('end_date', old('end_date', $agenda_item->end->format('d-m-Y')), 'dd-mm-jjjj', 'text', '', '', '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>') !!}
            </div>
            <div class="col-sm-2">
                {!! \App\Model\Helpers\Html::input('end_time', old('end_time', $agenda_item->end->format('H:i')), 'hh:mm', 'text', '', '', '<span class="glyphicon glyphicon-time" aria-hidden="true"></span>') !!}
            </div>
            @if ($errors->has('end'))
                <span class="help-block"><strong>{{ $errors->first('end') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
            <label for="address" class="col-sm-2 control-label">Adres</label>
            <div class="col-sm-10 col-md-4">
                {!! \App\Model\Helpers\Html::input('address', old('address', $agenda_item->address), 'Adres') !!}
            </div>
            @if ($errors->has('address'))
                <span class="help-block"><strong>{{ $errors->first('address') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('zip_code') ? ' has-error' : '' }}">
            <label for="zip_code" class="col-sm-2 control-label">Postcode</label>
            <div class="col-sm-4 col-md-2">
                {!! \App\Model\Helpers\Html::input('zip_code', old('zip_code', $agenda_item->zip_code), '1234AB') !!}
            </div>
            @if ($errors->has('zip_code'))
                <span class="help-block"><strong>{{ $errors->first('zip_code') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
            <label for="city" class="col-sm-2 control-label">Plaats</label>
            <div class="col-sm-10 col-md-4">
                {!! \App\Model\Helpers\Html::input('city', old('city', $agenda_item->city), 'Plaats') !!}
            </div>
            @if ($errors->has('city'))
                <span class="help-block"><strong>{{ $errors->first('city') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('notification') ? ' has-error' : '' }}">
            <label for="notification" class="col-sm-2 control-label">Herinnering</label>
            <div class="col-sm-3">
                {!! \App\Model\Helpers\Html::toggle_switch('notification', old('notification', $agenda_item->notification), true, 'class="role-toggle"') !!}
            </div>
            @if ($errors->has('notification'))
                <span class="help-block"><strong>{{ $errors->first('notification') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('notification_days') ? ' has-error' : '' }}">
            <label for="notification_days" class="col-sm-2 control-label">Herinnering</label>
            <div class="col-sm-3">
                {!! \App\Model\Helpers\Html::input('notification_days', old('notification_days', $agenda_item->notification_days), '', 'number', '', 'min="1"', '', 'dagen voor aanvang') !!}
            </div>
            @if ($errors->has('notification_days'))
                <span class="help-block"><strong>{{ $errors->first('notification_days') }}</strong></span>
            @endif
        </div>
        @if(empty($agenda_item->id))
            <div class="form-group{{ $errors->has('new_notification') ? ' has-error' : '' }}">
                <label for="new_notification" class="col-sm-2 control-label">Publicatie notificatie</label>
                <div class="col-sm-3">
                    {!! \App\Model\Helpers\Html::toggle_switch('new_notification', old('new_notification', 0), true, 'class="role-toggle"') !!}
                </div>
                @if ($errors->has('new_notification'))
                    <span class="help-block"><strong>{{ $errors->first('new_notification') }}</strong></span>
                @endif
            </div>
        @endif
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                {{ csrf_field() }}
                <input name="id" hidden value="{{ $agenda_item->id }}"/>
                <input name="start" hidden value="{{ old('start', $agenda_item->start) }}">
                <input name="end" hidden value="{{ old('end', $agenda_item->end) }}">
                <button type="submit" class="btn btn-info">Opslaan</button>

                @if(!empty($agenda_item->id))
                    <a href="{{ url('app/agenda/delete') }}" data-id="{{ $agenda_item->id }}" name="single_delete" class="btn btn-danger">Verwijderen</a>
                @endif

                @php( $url = isset($return_url) ? $return_url : 'app/agenda' )

                <a href="{{ url($url) }}" class="btn btn-default">Annuleren</a>
            </div>
        </div>
    </form>
@endsection

@section('page-script')
    <script type="text/javascript" src="{{ asset('libraries/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/app/agenda.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            CKEDITOR.replace('description');

            $('#start_date, #start_time').change(function() {
                $.agenda.updateStartDate();
            });

            $('#end_date, #end_time').change(function() {
                $.agenda.updateEndDate();
            });
        });
    </script>
@endsection