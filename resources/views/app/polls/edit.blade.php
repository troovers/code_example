@extends('layouts.app')

@section('page_title', trans('default.' . $action, ['object' => 'App poll']))

@section('page-style')
    <link rel="stylesheet" href="{{ asset('css/app/polls/polls.css') }}" type="text/css">
@endsection

@section('content')
    <form class="form-horizontal" method="POST" action="{{ url('/app/polls/save') }}">
        <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
            <label for="question" class="col-sm-2 control-label">Vraagstelling</label>
            <div class="col-sm-10 col-md-4">
                {!! \App\Model\Helpers\Html::input('question', old('question', $poll->question), 'Vraagstelling', 'text', '', $poll->id > 0 ? 'readonly' : '') !!}
            </div>
            @if ($errors->has('question'))
                <span class="help-block"><strong>{{ $errors->first('question') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('deadline') ? ' has-error' : '' }}">
            <label for="deadline" class="col-sm-2 control-label">Stemmen voor</label>
            <div class="col-sm-2">
                {!! \App\Model\Helpers\Html::input('deadline', old('deadline', !empty($poll->deadline) ? $poll->deadline->format('d-m-Y') : ''), 'dd-mm-jjjj', 'text', '', '', '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>') !!}
            </div>
            @if ($errors->has('deadline'))
                <span class="help-block"><strong>{{ $errors->first('deadline') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('notification') ? ' has-error' : '' }}">
            <label for="notification" class="col-sm-2 control-label">Herinnering</label>
            <div class="col-sm-3">
                {!! \App\Model\Helpers\Html::toggle_switch('notification', old('notification', $poll->notification), true, 'class="role-toggle" ' . ($poll->id > 0 ? 'disabled' : '')) !!}
            </div>
            @if ($errors->has('notification'))
                <span class="help-block"><strong>{{ $errors->first('notification') }}</strong></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('notification_days') ? ' has-error ' : '' }} {{ $poll->id > 0 && $poll->notification == 0 ? 'hidden' : '' }}">
            <label for="notification_days" class="col-sm-2 control-label">Herinnering</label>
            <div class="col-sm-3">
                {!! \App\Model\Helpers\Html::input('notification_days', old('notification_days', $poll->notification_days), '', 'number', '', ($poll->id > 0 ? 'readonly ' : '') . 'min="1"', '', 'dag(en) voor deadline') !!}
            </div>
            @if ($errors->has('notification_days'))
                <span class="help-block"><strong>{{ $errors->first('notification_days') }}</strong></span>
            @endif
        </div>
        @if(empty($poll->id))
            <div class="form-group{{ $errors->has('voters') ? ' has-error' : '' }}">
                <label for="voters" class="col-sm-2 control-label">Kies de stemmers</label>
                <div class="col-sm-3">
                    <select name="voters" id="voters" class="form-control">
                        <option value="1">Alle leden</option>
                        <option value="2">Enkel jeugdleden</option>
                        <option value="3">Enkel senioren</option>
                    </select>
                </div>
                @if ($errors->has('voters'))
                    <span class="help-block"><strong>{{ $errors->first('voters') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('new_notification') ? ' has-error' : '' }}">
                <label for="new_notification" class="col-sm-2 control-label">Publicatie notificatie</label>
                <div class="col-sm-3">
                    {!! \App\Model\Helpers\Html::toggle_switch('new_notification', old('new_notification', 0), true, 'class="role-toggle"') !!}
                </div>
                @if ($errors->has('new_notification'))
                    <span class="help-block"><strong>{{ $errors->first('new_notification') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('answers') ? ' has-error' : '' }}">
                <label for="answers" class="col-sm-2 control-label">Antwoorden</label>
                <div class="col-sm-10 col-md-4">
                    <div class="answers">
                        <div class="answer">
                            {!! \App\Model\Helpers\Html::input('answers[]', '', 'Antwoord') !!}
                        </div>
                        <div class="answer">
                            {!! \App\Model\Helpers\Html::input('answers[]', '', 'Antwoord') !!}
                        </div>
                    </div>
                    <a href="#" name="add-answer"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Voeg een antwoord toe</a>
                </div>
            </div>
        @endif
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                {{ csrf_field() }}
                <input name="id" hidden value="{{ $poll->id }}"/>
                <button type="submit" class="btn btn-info">Opslaan</button>

                @if(!empty($poll->id))
                    <a href="{{ url('app/polls/delete') }}" data-id="{{ $poll->id }}" name="single_delete" class="btn btn-danger">Verwijderen</a>
                @endif

                <a href="{{ url('app/polls') }}" class="btn btn-default">Annuleren</a>
            </div>
        </div>

        @if(!empty($poll->id))
            <h1>Totale stemmen per antwoord</h1>
            <canvas id="vote_count"></canvas>

            <h1>Percentage stemmen per antwoord</h1>
            <canvas id="vote_percentages"></canvas>

            <h1>Leden en hum stem</h1>
            <table class="table table-hover table-striped table-bordered">
                <thead>
                <tr>
                    <th width="150">Antwoord</th>
                    <th width="75">Aantal</th>
                    <th>Stemmers</th>
                </tr>
                </thead>
                <tbody>
                @php( $class = count($most_voted) > 1 ? 'warning' : 'success' )

                @foreach($poll->answers as $answer)
                    <tr class="{{ in_array($answer->id, $most_voted) ? $class : '' }}">
                        <td>{{ $answer->answer }}</td>
                        <td>{{ count($answer->votes) }}</td>
                        <td>
                            @foreach($answer->votes as $key => $vote)
                                @if($key + 1 < count($answer->votes))
                                    {{ $vote->first_name . ' ' . $vote->last_name . ', ' }}
                                @else
                                    {{ $vote->first_name . ' ' . $vote->last_name }}
                                @endif
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td><b>Niet gestemd</b></td>
                    <td>{{ count($unvoted) }}</td>
                    <td>
                        {{ implode(', ', $unvoted) }}
                    </td>
                </tr>
                </tfoot>
            </table>
        @endif
    </form>
@endsection

@section('page-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.bundle.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            if($('input[name="id"]').val() != '') {
                initializeChart('vote_count', {!! $votes !!}, {{ $total_votes }}, 5);
                initializeChart('vote_percentages', {!! $percentages !!}, 100, 10);
            }

            $('a[name="add-answer"]').click(function() {
                $('.answers').append('<div class="answer">{!! \App\Model\Helpers\Html::input('answers[]', '', 'Antwoord', 'text', '', '', '', '<a href="#" name="remove-answer"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>') !!}</div>');

                return false;
            });

            $(document).on('click', 'a[name="remove-answer"]', function() {
                $(this).closest('.answer').remove();

                return false;
            });

            // Check if all the answers are filled
            $('button[type="submit"]').click(function() {
                if($('input[name="id"]').val() == '') {
                    if($('input[name="notification"]').val() == 1) {
                        var days = $.geekk.getDateDifference($('input[name="deadline"]').val(), 'days');

                        var notification_days = parseInt($('input[name="notification_days"]').val());

                        if(days <= notification_days) {
                            $.geekk.warning('Voer een geldig aantal dagen voor de herinnering in.');
                            return false;
                        }
                    }
                }

                $('.answer input').each(function() {
                    if($(this).val() == '') {
                        $(this).addClass('error');
                    } else {
                        $(this).removeClass('error');
                    }
                });

                if($('.error').length > 0) {
                    return false;
                }
            });
        });

        function initializeChart(chart, y_axis, max, stepSize) {
            var ctx = $('#' + chart);

            var data = {
                labels: {!! $x_axis !!},
                datasets: [
                    {
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1,
                        data: y_axis,
                    }
                ]
            };

            var myBarChart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0,
                                max: max,
                                stepSize: stepSize
                            }
                        }]
                    }
                }
            });
        }
    </script>
@endsection