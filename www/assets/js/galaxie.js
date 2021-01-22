(function ($) {
'use strict';
$.fn.storymap = function(options) {
    var defaults = {
        selector: '[data-place]',
        breakpointPos: '33.333%',
        createMap: function () {
            var map = L.map('map').setView([43.60815211731254,3.8779338961662457], 12);
            var CartoDB_Positron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 19,
            }).addTo(map);
            return map;
        }
    };
    var settings = $.extend(defaults, options);
    if (typeof(L) === 'undefined') {
        throw new Error('Storymap requires Laeaflet');
    }
    if (typeof(_) === 'undefined') {
        throw new Error('Storymap requires underscore.js');
    }

    function getDistanceToTop(elem, top) {
        var docViewTop = $(window).scrollTop();
        var elemTop = $(elem).offset().top;
        var dist = elemTop - docViewTop;
        var d1 = top - dist;
        if (d1 < 0) {
            return $(window).height();
        }
        return d1;
    }

    function highlightTopPara(paragraphs, top) {
        var distances = _.map(paragraphs, function (element) {
            var dist = getDistanceToTop(element, top);
            return {el: $(element), distance: dist};
        });
        var closest = _.min(distances, function (dist) {
            return dist.distance;
        });
        _.each(paragraphs, function (element) {
            var paragraph = $(element);
            if (paragraph[0] !== closest.el[0]) {
                paragraph.trigger('notviewing');
            }
        });
        if (!closest.el.hasClass('viewing')) {
            closest.el.trigger('viewing');
        }
    }

    function watchHighlight(element, searchfor, top) {
        var paragraphs = element.find(searchfor);
        highlightTopPara(paragraphs, top);
        $(window).scroll(function () {
            if($(window).scrollTop() > 0.3 * $(window).height()){
                $('#mouseScroller').fadeOut();
            }
            highlightTopPara(paragraphs, top);
        });
    }
    var makeStoryMap = function (element, markers) {
        var topElem = $('<div class="breakpoint-current"></div>')
            .css('top', settings.breakpointPos);
        $('body').append(topElem);
        var top = topElem.offset().top - $(window).scrollTop();
        var searchfor = settings.selector;
        var paragraphs = element.find(searchfor);
        paragraphs.on('viewing', function () {
            $(this).addClass('viewing');
        });
        paragraphs.on('notviewing', function () {
            $(this).removeClass('viewing');
        });
        watchHighlight(element, searchfor, top);
        var map = settings.createMap();
        var initPoint = map.getCenter();
        var initZoom = map.getZoom();
        var fg = L.featureGroup().addTo(map);

        function showMapView(key) {
            fg.clearLayers();
            if (key === 'overview') {
                map.setView(initPoint, initZoom, true);
                for (const [key, marker] of Object.entries(markers)) {
                    fg.addLayer(L.marker([marker.lat, marker.lon], {icon: marker.icon}));
                }
            } else if (markers[key]) {
                var marker = markers[key];
                fg.addLayer(L.marker([marker.lat, marker.lon], {icon: marker.icon}));
                map.setView([marker.lat, marker.lon], marker.zoom, 1);
            }
        }
        paragraphs.on('viewing', function () {
            showMapView($(this).data('place'));
        });
        showMapView('overview');
    };

    makeStoryMap(this, settings.markers);
    return this;
}
$('document').ready(function(){
    // ---- gestion story maps
    $(this).scrollTop(0);
    var markers = [];
    $('.compteur').each(function(){
        var self = $(this);
        var icon = L.icon({
            iconUrl: '/assets/img/markers/'+self.data('color').replace('#', '')+'.png',
            iconSize: [22, 28],
            iconAnchor: [18, 35],
        });
        markers[self.data('place')] = {
            'lat': self.data('lat'),
            'lon': self.data('lng'),
            'icon': icon,
            'zoom': self.data('zoom'),
        };
    });
    $('.main').storymap({markers: markers});
    $('section.overview').css('min-height', $(window).height() * 0.9);
    $('section.overview').addClass('viewing');

    // ---- gestion modales
    var myModal = new bootstrap.Modal(document.getElementById('theModal'));
    $('.modal-yt, .modal-img').click(function(){
        var self = $(this);
        if(self.hasClass('modal-yt')){
            $('#theModal').find('.modal-body').html('<iframe src="https://www.youtube-nocookie.com/embed/'+self.data('video')+'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        }else if(self.hasClass('modal-img')){
            $('#theModal').find('.modal-body').html('<img src="'+self.data('img')+'">');
        }
        myModal.show();
    });

    // ---- gestion chartJS
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
                borderWidth: 2,
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
                legend: {
                    display: false,
                },
                plugins: {
                    datalabels: false
                }
            }
        });
    });

    $('.bar').each(function(){
        var self = $(this);
        var ctx = document.getElementById(self.attr('id')).getContext('2d');
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: self.data('labels'),
                datasets: [{
                    label: self.data('label'),
                    //barPercentage: 0.5,
                    barThickness: 6,
                    maxBarThickness: 8,
                    minBarLength: 2,
                    backgroundColor:["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)"],
                    borderColor:["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)"],
                    borderWidth:1,
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
                    }]
                },
            }
        });
    });
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
});
}(jQuery));
