</div><?php /* ! #mainContent */ ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@0.5.7/chartjs-plugin-annotation.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script type="text/javascript">
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
            var dataset = [];
            $.each(self.data('values'), function(k, v){
                dataset.push({
                    label: cpts[k].name,
                    borderColor: cpts[k].color,
                    backgroundColor: cpts[k].color,
                    fill: false,
                    borderWidth: 5,
                    data: v,
                });
            });
            var ctx = document.getElementById(self.attr('id')).getContext('2d');
            var myLineChart = new Chart.Line(ctx, {
                data: {
                    labels: self.data('labels'),
                    datasets: dataset,
                },
                options:{
                    plugins: {
                        datalabels: false
                    }
                }
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
    })
</script>
<?php if(isset($scripts) && is_array($scripts) && count($scripts) > 0): ?>
    <?php foreach($scripts as $script): ?>
        <?php echo $script ?>
    <?php endforeach ?>
<?php endif ?>
</body>
</html>
