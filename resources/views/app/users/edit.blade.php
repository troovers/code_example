@extends('layouts.app')

@section('page_title', trans('default.' . $action, ['object' => 'App gebruiker']))

@section('content')
    <form class="form-horizontal" method="POST" action="{{ url('/app/users/save') }}">
        <div class="form-group{{ $errors->has('member_id') ? ' has-error' : '' }}">
            <label for="member_id" class="col-sm-2 control-label">Selecteer een lid</label>
            <div class="col-sm-10 col-md-5">
                {!! \App\Model\Helpers\Html::select('member_id', $members, 'id', 'display_name', old('member_id', $user->member_id), 'Selecteer een lid') !!}
            </div>
            @if ($errors->has('member_id'))
                <span class="help-block"><strong>{{ $errors->first('member_id') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label for="password" class="col-sm-2 control-label">Wachtwoord</label>
            <div class="col-sm-10 col-md-5">
                {!! \App\Model\Helpers\Html::input('password', '', 'Wachtwoord', 'password') !!}
            </div>
            @if ($errors->has('password'))
                <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('password-confirm') ? ' has-error' : '' }}">
            <label for="password-confirm" class="col-sm-2 control-label">Wachtwoord</label>
            <div class="col-sm-10 col-md-5">
                {!! \App\Model\Helpers\Html::input('password-confirm', '', 'Wachtwoord bevestiging', 'password') !!}
            </div>
            @if ($errors->has('password-confirm'))
                <span class="help-block"><strong>{{ $errors->first('password-confirm') }}</strong></span>
            @endif
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                {{ csrf_field() }}
                <input name="id" hidden value="{{ $user->id }}"/>
                <button type="submit" class="btn btn-info">Opslaan</button>

                @if(!empty($user->id))
                    <a href="{{ url('app/users/delete') }}" data-id="{{ $user->id }}" name="single_delete" class="btn btn-danger">Verwijderen</a>
                @endif

                <a href="{{ url('app/users') }}" class="btn btn-default">Annuleren</a>
            </div>
        </div>
    </form>
@endsection