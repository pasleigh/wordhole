var myline_chart = {
    chart: {
        type: 'line',
    },
    title: {
        text: 'Wordhole'
    },

    subtitle: {
        text: '-'
    },

    yAxis: {
        title: {
            text: 'Score relative to par'
        },
        tickInterval: 1
    },

    xAxis: {
        accessibility: {
            rangeDescription: 'Range: 2010 to 2020'
        }
    },

    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
            pointStart: 2010
        }
    },

    series: [{
        name: 'Installation & Developers',
        data: [43934, 48656, 65165, 81827, 112143, 142383,
            171533, 165174, 155157, 161454, 154610]
    }, {
        name: 'Manufacturing',
        data: [24916, 37941, 29742, 29851, 32490, 30282,
            38121, 36885, 33726, 34243, 31050]
    }],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    },

    tooltip: {
        formatter: function () {
            //alert(this);
            return "<B>Wordle " + this.x + "</B><BR>" + '<span style="color:' + this.series.color
                + '">● </span><B>' + this.series.name + '</B> <BR>Round total: ' + this.y
                + '<BR>This hole [' + this.point.h + ']: ' + this.point.v + ' ' + this.point.c;
        }
    },
    exporting: {
        filename: 'wordhole_chart',
        scale: 2,
        sourceWidth: 1000,
        //sourceHeight: 600
    }

};

var mycolumn_chart = {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Wordhole mean scores for round ...',
        align: 'center'
    },
    subtitle: {
        text: 'Score for each Wordle',
        align: 'center'
    },
    xAxis: {
        categories: ['HELLO', 'SHOOT', 'CHINA', 'SPOON', 'WHELK', 'CLAMP','','','','','','','','','','','',''],
        crosshair: true,
        accessibility: {
            description: 'Wordle word'
        }
    },
    yAxis: {
        min: 2,
        max: 6,
        title: {
            text: 'Mean Score'
        }
    },
    legend:{
        enabled: false
    },
    tooltip: {
        formatter: function () {
            //alert(this);
            return "<B>Wordle word: " + this.x + "</B><BR>" +  'Mean score: ' + this.y.toFixed(2);
        }
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        },
        series:{
            dataLabels:{
                useHTML: 'false',
                enabled: true,
                format: '{point.y:,.2f}',
                y: 35,
                rotation: -90,
                style:{
                    useHTML: 'false',
                    fontWeight: 'normal'
                }
            }
        }
    },
    series: [
        {
            name: 'Wordle',
            data: [3.2, 4.2, 2.5, 4.0, 3.6, 3.3,0,0,0,0,0,0,0,0,0,0,0,0]
        }
    ],
    exporting: {
        filename: 'wordhole_mean_scores_chart',
        scale: 2,
        sourceWidth: 1000,
        //sourceHeight: 600
    }
}