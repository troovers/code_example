@extends('layouts.app')

@section('page_title', 'App agenda')

@section('content')
    {!! \App\Model\Helpers\Html::toolbar('app/agenda', $buttons, true) !!}

    @if(count($agenda_items) == 0)
        <p class="bg-warning">Er zijn geen toekomstige agenda items gevonden, klik op <b>Toevoegen</b> om er een aan te maken</p>
    @else
        <form class="form-horizontal data-table" method="POST" action="{{ url('/app/agenda/delete') }}">
            {{ csrf_field() }}
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    @if(\App\Model\Users\User::is_allowed_to('delete', 'app/agenda'))
                        <th width="50"><input type="checkbox" id="check_all"></th>
                    @endif
                    <th width="50">#</th>
                    <th width="150">{{ \App\Model\Helpers\Ordering::create_label('Aanvang', 'start', $filters) }}</th>
                    <th width="150">{{ \App\Model\Helpers\Ordering::create_label('Eind', 'end', $filters) }}</th>
                    <th>{{ \App\Model\Helpers\Ordering::create_label('Titel', 'title', $filters) }}</th>
                    <th width="350">{{ \App\Model\Helpers\Ordering::create_label('Locatie', 'location', $filters) }}</th>
                    <th width="50" class="{{ !Auth::user()->is_super_admin() ? 'hidden' : '' }}">{{ \App\Model\Helpers\Ordering::create_label('ID', 'id', $filters) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($agenda_items as $key => $agenda_item)
                    <tr>
                        @if(\App\Model\Users\User::is_allowed_to('delete', 'app/agenda'))
                            <td>
                                <input type="checkbox" name="agenda_items[]" value="{{ $agenda_item->id }}">
                            </td>
                        @endif
                        <td>{{ \App\Model\Helpers\Html::row_number($agenda_items, $key) }}</td>
                        <td>{{ !is_null($agenda_item->start) ? $agenda_item->start->format('d-m-Y') . ' om ' . $agenda_item->start->format('H:i') : '' }}</td>
                        <td>{{ !is_null($agenda_item->end) ? $agenda_item->end->format('d-m-Y') . ' om ' . $agenda_item->end->format('H:i') : '' }}</td>
                        <td>
                            @if(\App\Model\Users\User::is_allowed_to('edit', 'app/agenda'))
                                <a href="{{ url('/app/agenda/edit/' . $agenda_item->id) }}">{{ $agenda_item->title }}</a>
                            @else
                                {{ $agenda_item->title }}
                            @endif
                        </td>
                        <td>{{ $agenda_item->location }}</td>
                        <td class="{{ !Auth::user()->is_super_admin() ? 'hidden' : '' }}">{{ $agenda_item->id }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </form>
    @endif

    <div class="text-center">
        {{ \App\Model\Helpers\Html::pagination($agenda_items) }}
    </div>
@endsection