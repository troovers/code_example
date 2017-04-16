@extends('layouts.app')

@section('page_title', 'App polls')

@section('content')
    {!! \App\Model\Helpers\Html::toolbar('app/polls', $buttons, true) !!}

    @if(count($polls) == 0)
        <p class="bg-warning">Er zijn geen polls gevonden, klik op <b>Toevoegen</b> om er een aan te maken</p>
    @else
        <form class="form-horizontal data-table" method="POST" action="{{ url('/app/polls/delete') }}">
            {{ csrf_field() }}
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    @if(\App\Model\Users\User::is_allowed_to('delete', 'app/polls'))
                        <th width="50"><input type="checkbox" id="check_all"></th>
                    @endif
                    <th width="50">#</th>
                    <th>{{ \App\Model\Helpers\Ordering::create_label('Vraagstelling', 'question', $filters) }}</th>
                    <th width="250">{{ \App\Model\Helpers\Ordering::create_label('Aangemaakt door', 'display_name', $filters) }}</th>
                    <th width="100">{{ \App\Model\Helpers\Ordering::create_label('Gestemd', 'percentage_voted', $filters) }}</th>
                    <th width="50" class="{{ !Auth::user()->is_super_admin() ? 'hidden' : '' }}">{{ \App\Model\Helpers\Ordering::create_label('ID', 'id', $filters) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($polls as $key => $poll)
                    <tr>
                        @if(\App\Model\Users\User::is_allowed_to('delete', 'app/polls'))
                            <td>
                                <input type="checkbox" name="polls[]" value="{{ $poll->id }}">
                            </td>
                        @endif
                        <td>{{ \App\Model\Helpers\Html::row_number($polls, $key) }}</td>
                        <td>
                            @if(\App\Model\Users\User::is_allowed_to('edit', 'app/polls'))
                                <a href="{{ url('/app/polls/edit/' . $poll->id) }}">{{ $poll->question }}</a>
                            @else
                                {{ $poll->title }}
                            @endif
                        </td>
                        <td>{{ $poll->display_name }}</td>
                        <td>{{ number_format($poll->percentage_voted, 1) . ' %' }}</td>
                        <td class="{{ !Auth::user()->is_super_admin() ? 'hidden' : '' }}">{{ $poll->id }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </form>
    @endif

    <div class="text-center">
        {{ \App\Model\Helpers\Html::pagination($polls) }}
    </div>
@endsection