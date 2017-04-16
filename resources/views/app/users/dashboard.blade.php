@extends('layouts.app')

@section('page_title', 'App gebruikers')

@section('content')
    {!! \App\Model\Helpers\Html::toolbar('app/users', $buttons, true) !!}

    @if(count($users) == 0)
        <p class="bg-warning">Er zijn geen gebruikers gevonden, klik op <b>Toevoegen</b> om er een aan te maken</p>
    @else
        <form class="form-horizontal data-table" method="POST" action="{{ url('/app/users/delete') }}">
            {{ csrf_field() }}
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    @if(\App\Model\Users\User::is_allowed_to('delete', 'app/users'))
                        <th width="50"><input type="checkbox" id="check_all"></th>
                    @endif
                    <th width="50">#</th>
                    <th>{{ \App\Model\Helpers\Ordering::create_label('Naam', 'display_name', $filters) }}</th>
                    <th width="300">{{ \App\Model\Helpers\Ordering::create_label('E-mailadres', 'email', $filters) }}</th>
                    <th width="200">{{ \App\Model\Helpers\Ordering::create_label('Ingelogd', 'last_app_login', $filters) }}</th>
                    <th width="50">{{ \App\Model\Helpers\Ordering::create_label('ID', 'id', $filters) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $key => $user)
                    <tr>
                        @if(User::is_allowed_to('delete', 'app/users'))
                            <td>
                                @if(!is_null($user->member_id))
                                    <input type="checkbox" name="users[]" value="{{ $user->id }}">
                                @endif
                            </td>
                        @endif
                        <td>{{ \App\Model\Helpers\Html::row_number($users, $key) }}</td>
                        <td>
                            @if(\App\Model\Users\User::is_allowed_to('edit', 'app/users') && !is_null($user->member_id))
                                <a href="{{ url('/app/users/edit/' . $user->id) }}">{{ $user->display_name }}</a>
                            @else
                                {{ $user->display_name }}
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ !is_null($user->last_app_login) ? date('d-m-Y', strtotime($user->last_app_login)) . ' om ' . date('H:i', strtotime($user->last_app_login)) : '' }}</td>
                        <td>{{ $user->id }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </form>
    @endif

    <div class="text-center">
        {{ \App\Model\Helpers\Html::pagination($users) }}
    </div>
@endsection