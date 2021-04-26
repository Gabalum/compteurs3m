$(document).ready(function(){
    var maps = [];
    $('.map-fh').each(function(){
        var self = $(this);
        self.height($(window).height());
    });

    $('.map').each(function(){
        var self = $(this);
        var id = self.data('id');
        var latlng = [self.data('lat'), self.data('lng')];
        if(self.hasClass('detail')){
            self.height(self.width());
        }else{
            self.height(self.closest('.compteur-data').find('.data-col-2').height());
        }
        maps[id] = L.map('map-'+id, {scrollWheelZoom: false}).setView(latlng, 17);
        var CartoDB_Positron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19,
        }).addTo(maps[id]);
        L.marker(latlng).addTo(maps[id]);
    });

    $('.map-total').each(function(){
        var map;
        var self = $(this);
        var id = self.attr('id');
        var center = self.data('center');
        var data = self.data('pins');
        if(!self.hasClass('map-fh')){
            self.height(self.width() / 2.5);
        }
        map = L.map(id, {scrollWheelZoom: false}).setView(center, 12);
        var CartoDB_Positron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19,
        }).addTo(map);

        var icon = L.icon({
            iconUrl: '/assets/img/markers/8540f5.png',
            iconSize: [22, 28],
            iconAnchor: [18, 35],
        });

        var iconClassic = L.icon({
            iconUrl: '/assets/img/markers/3d8bfd.png',
            iconSize: [22, 28],
            iconAnchor: [18, 35],
        });
        $.each(data, function(k, v){
            if(typeof v[2] != 'undefined') {
                L.marker(v, {icon: icon}).addTo(map);
            }else{
                L.marker(v, {icon: iconClassic}).addTo(map);
            }
        });
    });

    $('.js-chart-data').each(function(){
        var self = $(this);
        if(self.data('dates').length > 0){
            var ctx = document.getElementById(self.find('canvas').attr('id')).getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                },
                data: {
                    labels: self.data('dates'),
                    datasets: [
                        {
                            label: self.data('label'),
                            data: self.data('data'),
                            fill: false,
                            borderColor: "white",
                        },
                    ]
                },
            });
        }
    });
    $('.bar-detail').each(function(){
        var self = $(this);
        var ctx = document.getElementById(self.attr('id')).getContext('2d');
        var bg1 = "#cae26e";
        var bg2 = "#75cbb7";
        if(self.hasClass('records')){
            bg1 = ["rgba(255, 99, 132, 0.5)","rgba(255, 159, 64, 0.5)","rgba(255, 205, 86, 0.5)","rgba(75, 192, 192, 0.5)","rgba(54, 162, 235, 0.5)","rgba(153, 102, 255, 0.5)","rgba(201, 203, 207, 0.5)","rgba(255, 99, 132, 0.5)","rgba(255, 159, 64, 0.5)","rgba(255, 205, 86, 0.5)","rgba(75, 192, 192, 0.5)","rgba(54, 162, 235, 0.5)","rgba(153, 102, 255, 0.5)","rgba(201, 203, 207, 0.5)"];
            bg2 = ["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)"];
        };
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
                plugins: {
                    datalabels: false
                },
                legend: {
                    display: false,
                },
                scales: {
                    yAxes:[{
                        ticks:{
                            max: self.data('max'),
                            beginAtZero:true
                        }
                    }],
                    xAxes: [{
                        categoryPercentage: 1.0,
                        barPercentage: 1.0
                    }]
                },
            }
        });
    });
    $('#menuToggler').on('change', function(){
        var self = $(this);
        if(self.prop('checked')) {
            $('.map').css('visibility', 'hidden');
        }else{
            $('.map').css('visibility', 'visible');
        }
    });
    $('#menu a').on('click', function(){
        $('#menuToggler').prop('checked', false);
        $('.map').css('visibility', 'visible');
    });

    $('#menuToggler').prop('checked', false);
    $('.map').css('visibility', 'visible');
});
