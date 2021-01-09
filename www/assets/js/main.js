$(document).ready(function(){
    var maps = [];
    $('.map').each(function(){
        var self = $(this);
        var id = self.data('id');
        var latlng = [self.data('lat'), self.data('lng')];
        self.height(self.closest('.compteur-data').find('.col-8').height());
        maps[id] = L.map('map-'+id).setView(latlng, 17);
        var CartoDB_Positron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(maps[id]);
        L.marker(latlng).addTo(maps[id]);
    });

    $('.js-chart-data').each(function(){
        var self = $(this);
        if(self.data('dates').length > 0){
            var ctx = document.getElementById(self.find('canvas').attr('id')).getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
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
                }
            });
        }
    });
});
