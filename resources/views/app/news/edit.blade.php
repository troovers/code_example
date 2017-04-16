@extends('layouts.app')

@section('page_title', trans('default.' . $action, ['object' => 'App artikel']))

@section('content')
    <form class="form-horizontal" method="POST" action="{{ url('/app/news/save') }}">
        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
            <label for="title" class="col-sm-2 control-label">Titel</label>
            <div class="col-sm-10">
                {!! \App\Model\Helpers\Html::input('title', old('title', $article->title), 'Titel') !!}
            </div>
            @if ($errors->has('title'))
                <span class="help-block"><strong>{{ $errors->first('title') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
            <label for="content-textarea" class="col-sm-2 control-label">Inhoud</label>
            <div class="col-sm-10">
                <textarea name="content" id="content-textarea" rows="10" cols="80">{{ old('content', $article->content) }}</textarea>
            </div>
            @if ($errors->has('content'))
                <span class="help-block"><strong>{{ $errors->first('content') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('posted_at') ? ' has-error' : '' }}">
            <label for="posted_at" class="col-sm-2 control-label">Geplaatst op</label>
            <div class="col-sm-2">
                {!! \App\Model\Helpers\Html::input('posted_at', old('posted_at', $article->posted_at->format('d-m-Y')), 'dd-mm-jjjj', 'text', '', '', '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>') !!}
            </div>
            @if ($errors->has('posted_at'))
                <span class="help-block"><strong>{{ $errors->first('posted_at') }}</strong></span>
            @endif
        </div>
        @if(empty($article->id))
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
                <input name="id" hidden value="{{ $article->id }}"/>
                <button type="submit" class="btn btn-info">Opslaan</button>

                @if(!empty($article->id))
                    <a href="{{ url('app/news/delete') }}" data-id="{{ $article->id }}" name="single_delete" class="btn btn-danger">Verwijderen</a>
                @endif

                <a href="{{ url('app/news') }}" class="btn btn-default">Annuleren</a>
            </div>
        </div>
    </form>
@endsection

@section('page-script')
    <script type="text/javascript" src="{{ asset('libraries/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        CKEDITOR.replace('content-textarea');
    </script>
@endsection