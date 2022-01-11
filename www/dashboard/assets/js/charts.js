function shuffle(a) {
    var j, x, i;
    for (i = a.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = a[i];
        a[i] = a[j];
        a[j] = x;
    }
    return a;
}
$('document').ready(function(){
    //var colors = ['#3d0a91', '#ab296a', '#146c43', '#0dcaf0', '#ffc107'];
    var colors = ['#75cbb7', '#cae26e'];
    $('.pie').each(function(){
        var self = $(this);
        var ctx = document.getElementById(self.attr('id')).getContext('2d');
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: self.data('labels'),
                datasets: [{
                    data: self.data('values'),
                    backgroundColor: colors //shuffle(colors)
                }],
            },
            options: {
                responsive: true,
                tooltips: {
                    enabled: false,
                },
                plugins: {
                    datalabels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        formatter: (value, ctx) => {
                            return value+' par jour';
                        },
                        color: '#fff',
                    }
                }
            }
        });
    });
    $('.line').each(function(){
        var self = $(this);
        var ctx = document.getElementById(self.attr('id')).getContext('2d');
        var myLineChart = new Chart.Line(ctx, {
            data: {
                labels: self.data('labels'),
                datasets: [{
                    data: self.data('values'),
                    backgroundColor: '#fff3cd',
                    borderColor: '#ffda6a',
                }],
            },
            options:{
                legend: {
                    display: false,
                },
                scales: {
                    yAxes:[{
                        ticks:{
                            max: self.data('max')+200,
                            beginAtZero:true,
                            stepSize: 200,
                        }
                    }],
                },
                plugins: {
                    datalabels: false
                }
            }
        });
    });
    $('.linechart').each(function(){
        var self = $(this);
        var cpts = self.data('cpts');
        var labels = self.data('labels');
        var dataset = [];
        $.each(self.data('values'), function(k, v){
            dataset.push({
                id: cpts[k].id,
                label: cpts[k].name,
                borderColor: cpts[k].color,
                backgroundColor: cpts[k].color,
                fill: false,
                borderWidth: 2,
                data: v,
            });
        });
        var labelsPrev = self.data('labels-previous');
        var datasetPrev = [];
        $.each(self.data('values-previous'), function(k, v){
            datasetPrev.push({
                id: cpts[k].id,
                label: cpts[k].name,
                borderColor: cpts[k].color,
                backgroundColor: cpts[k].color,
                fill: false,
                borderWidth: 2,
                data: v,
            });
        });
        var ctx = document.getElementById(self.attr('id')).getContext('2d');
        var myLineChart = new Chart.Line(ctx, {
            data: {
                labels: labels,
                datasets: dataset,
            },
            options:{
                plugins: {
                    datalabels: false
                },
                legend: {
                    display: false
                },
                elements: {
                    point:{
                        radius: 0
                    }
                }
            }
        });
        myLineChart.data.datasets.forEach(function(ds){
            ds.hidden = !$('#cpt_'+ds.id).prop('checked');
        });
        myLineChart.update();
        $('#legend-linechart input').on('click', function(){
            myLineChart.data.datasets.forEach(function(ds){
                ds.hidden = !$('#cpt_'+ds.id).prop('checked');
            });
            myLineChart.update();
        });
        $('#cpt_show').on('click', function(){
            $('#legend-linechart input').prop('checked', true);
                myLineChart.data.datasets.forEach(function(ds){
                    ds.hidden = false;
                });
                myLineChart.update();
        });
        $('#cpt_hide').on('click', function(){
            $('#legend-linechart input').prop('checked', false);
            myLineChart.data.datasets.forEach(function(ds){
                ds.hidden = true;
            });
            myLineChart.update();
        });
        $('#y_previous').on('click', function(){
            myLineChart.data.datasets = datasetPrev;
            myLineChart.data.labels = labelsPrev;
            myLineChart.data.datasets.forEach(function(ds){
                ds.hidden = !$('#cpt_'+ds.id).prop('checked');
            });
            myLineChart.update();
            $(this).removeClass('bg-orange-400').addClass('bg-orange-600');
            $('#y_current').removeClass('bg-orange-600').addClass('bg-orange-400');
        });
        $('#y_current').on('click', function(){
            myLineChart.data.datasets = dataset;
            myLineChart.data.labels = labels;
            myLineChart.data.datasets.forEach(function(ds){
                ds.hidden = !$('#cpt_'+ds.id).prop('checked');
            });
            myLineChart.update();
            $(this).removeClass('bg-orange-400').addClass('bg-orange-600');
            $('#y_previous').removeClass('bg-orange-600').addClass('bg-orange-400');
        });

    });
    $('.bar-tomtom').each(function(){
        var self = $(this);
        var ctx = document.getElementById(self.attr('id')).getContext('2d');
        var bg1 = "#842029";
        var bg2 = "#58151c";
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: self.data('labels'),
                datasets: [{
                    label: self.data('label'),
                    backgroundColor: bg1,
                    hoverBackgroundColor: bg2,
                    borderWidth:1,
                    borderSkipped: 'right',
                    data: self.data('values'),
                }]
            },
            options: {
                scales: {
                    yAxes:[{
                        ticks:{
                            max: 100,
                            beginAtZero:true
                        }
                    }]
                },
                legend: {
                    display: false,
                },
                plugins: {
                    datalabels: false
                },
            }
        });
    });
    $('.bar-stack').each(function(){
        var self = $(this);
        var ctx = document.getElementById(self.attr('id')).getContext('2d');
        var bg1 = "#cae26e";
        var bg2 = "#75cbb7";
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: self.data('labels'),
                datasets: [{
                    label: self.data('label'),
                    backgroundColor: bg1,
                    hoverBackgroundColor: bg2,
                    borderWidth:1,
                    borderSkipped: 'right',
                    data: self.data('values'),
                }]
            },
            options: {
                legend: {
                    display: false,
                },
                plugins: {
                    datalabels: false
                },
                annotation: {
                    annotations: [{
                        type: 'line',
                        mode: 'horizontal',
                        scaleID: 'y-axis-0',
                        value: 0,
                        endValue: self.data('max'),
                        borderColor: 'gray',
                        borderWidth: 3,
                        borderDash: [2, 2],
                    }],
                    drawTime: "afterDraw" // (default)
                }
            }
        });
    });
    $('.bar').each(function(){
        var self = $(this);
        var ctx = document.getElementById(self.attr('id')).getContext('2d');
        var colors = ["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)",
    "rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)",
"rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)",
"rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)"];
        var bgColors = [];
        for (var i = 0; i < self.data('values').length; i++) {
          bgColors.push(colors[i % colors.length]);
        }
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: self.data('labels'),
                datasets: [{
                    label: self.data('label'),
                    barPercentage: 0.5,
                    barThickness: 6,
                    maxBarThickness: 8,
                    minBarLength: 2,
                    backgroundColor:bgColors,
                    borderColor:["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)",
                "rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)",
            "rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)",
        "rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)"],
                    borderWidth:1,
                    data: self.data('values'),
                }]
            },
            options: {
                scales: {
                    yAxes:[{
                        ticks:{
                            max: self.data('max'),
                            beginAtZero:true
                        }
                    }]
                },
                plugins: {
                    datalabels: false
                },
                annotation: {
                    annotations: [{
                        type: 'line',
                        mode: 'horizontal',
                        scaleID: 'y-axis-0',
                        value: self.data('global-avg'),
                        borderColor: 'gray',
                        borderWidth: 3,
                        label: {
                            enabled: true,
                            content: 'moy. : '+self.data('global-avg'),
                            backgroundColor: 'rgba(0,0,0,0.3)',
                            position: "end",
                            font: {
                                size: 7,
                            }
                        }
                    }],
                    drawTime: "afterDraw" // (default)
                }
            }
        });
    });
});
