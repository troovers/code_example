@extends('layouts.app')

@section('page_title', 'Appgebruik')

@section('content')
    <h1>Logins gesorteerd op dag</h1>
    <canvas id="chart_logins"></canvas>

    <h1>Aantal gebruikers</h1>

    @if($stats_exist)
        {!! \App\Model\Helpers\Html::toolbar('app/stats', [], false, $form_filters) !!}
    @else
        <p class="alert alert-info">Er staan nog geen statistieken in het systeem. Deze worden automatisch toegevoegd.</p>
    @endif

    <canvas id="chart_users"></canvas>
@endsection

@section('page-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.bundle.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {

            initializeLoggins();

            initializeCount();

            function initializeCount() {
                var ctx = $("#chart_users");

                var days = {!! $days_in_month !!};

                var senior_members = {!! $senior_members !!},
                    youth_members = {!! $youth_members !!};

                var data = {
                    labels: days,
                    datasets: [
                        {
                            label: "Senioren",
                            backgroundColor: "rgba(75,192,192,0.2)",
                            borderColor: "rgba(75,192,192,1)",
                            pointBackgroundColor: "rgba(75,192,192,1)",
                            pointBordercolor: "#fff",
                            pointHoverBackgroundColor: "#fff",
                            pointHoverBorderColor: "rgba(75,192,192,1)",
                            data: senior_members
                        }, {
                            label: "Jeugd",
                            backgroundColor: "rgba(255,206,86,0.2)",
                            borderColor: "rgba(255,206,86,1)",
                            pointBackgroundColor: "rgba(255,206,86,1)",
                            pointBorderColor: "#fff",
                            pointHoverBackgroundColor: "#fff",
                            pointHoverBorderColor: "rgba(255,206,86,1)",
                            data: youth_members
                        }
                    ]
                };

                // This will get the first returned node in the jQuery collection.
                var chart = new Chart(ctx, {
                    data: data,
                    type: 'line',
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    min: 0,
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            }

            function initializeLoggins() {
                var ctx = $('#chart_logins');

                var data = {
                    datasets: [{
                        data: {!! $weekly !!},
                        backgroundColor: [
                            "#FF6384",
                            "#4BC0C0",
                            "#FFCE56",
                            "#E7E9ED",
                            "#36A2EB",
                            "#EB7F36",
                            "#7F36EB"
                        ],
                        label: "Totaal aantal logins gesorteerd op dag" // for legend
                    }],
                    labels: ["Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrijdag", "Zaterdag", "Zondag"]
                };

                var chart = new Chart(ctx, {
                    data: data,
                    type: 'polarArea',
                    options: {
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    var allData = data.datasets[tooltipItem.datasetIndex].data;
                                    var tooltipLabel = data.labels[tooltipItem.index];
                                    var tooltipData = allData[tooltipItem.index];

                                    return tooltipLabel + ': ' + tooltipData + ' %';
                                }
                            }
                        },
                        scale: {
                            ticks: {
                                min: 0
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection