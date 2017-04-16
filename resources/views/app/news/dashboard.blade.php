@extends('layouts.app')

@section('page_title', 'App nieuws')

@section('content')
    {!! \App\Model\Helpers\Html::toolbar('app/news', $buttons, true) !!}

    @if(count($articles) == 0)
        <p class="bg-warning">Er zijn geen artikelen gevonden, klik op <b>Toevoegen</b> om er een aan te maken</p>
    @else
        <form class="form-horizontal data-table" method="POST" action="{{ url('/app/news/delete') }}">
            {{ csrf_field() }}
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    @if(User::is_allowed_to('delete', 'app/news'))
                        <th width="50"><input type="checkbox" id="check_all"></th>
                    @endif
                    <th width="50">#</th>
                    <th>{{ \App\Model\Helpers\Ordering::create_label('Titel', 'title', $filters) }}</th>
                    <th width="250">{{ \App\Model\Helpers\Ordering::create_label('Auteur', 'author', $filters) }}</th>
                    <th width="150">{{ \App\Model\Helpers\Ordering::create_label('Geplaatst op', 'posted_at', $filters) }}</th>
                    <th width="50" class="{{ !Auth::user()->is_super_admin() ? 'hidden' : '' }}">{{ \App\Model\Helpers\Ordering::create_label('ID', 'id', $filters) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($articles as $key => $article)
                    <tr>
                        @if(User::is_allowed_to('delete', 'app/users'))
                            <td>
                                <input type="checkbox" name="articles[]" value="{{ $article->id }}">
                            </td>
                        @endif
                        <td>{{ \App\Model\Helpers\Html::row_number($articles, $key) }}</td>
                        <td>
                            @if(\App\Model\Users\User::is_allowed_to('edit', 'app/news'))
                                <a href="{{ url('/app/news/edit/' . $article->id) }}">{{ $article->title }}</a>
                            @else
                                {{ $article->title }}
                            @endif
                        </td>
                        <td>{{ $article->author }}</td>
                        <td>{{ !is_null($article->posted_at) ? $article->posted_at->format('d-m-Y') . ' om ' . $article->posted_at->format('H:i') : '' }}</td>
                        <td class="{{ !Auth::user()->is_super_admin() ? 'hidden' : '' }}">{{ $article->id }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </form>
    @endif

    <div class="text-center">
        {{ \App\Model\Helpers\Html::pagination($articles) }}
    </div>
@endsection