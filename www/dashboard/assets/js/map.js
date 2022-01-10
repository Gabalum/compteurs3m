if($('#map-wrapper').length > 0){
    var markers = [];
    $('.map').each(function(){
        var self = $(this);
        var id = self.data('id');
        var latlng = [self.data('lat'), self.data('lng')];
        var center = self.data('center');
        map = L.map('map-'+id, {scrollWheelZoom: false}).setView(center, 12);
        var CartoDB_Positron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19,
        }).addTo(map);
        $('#legend .compteur').each(function(){
            let self = $(this);
            let icon = L.icon({
                iconUrl: '/assets/img/markers/'+self.data('color').replace('#', '')+'.png',
                iconSize: [22, 28],
                iconAnchor: [18, 35],
            });
            markers[self.data('place')] = L.marker([self.data('lat'), self.data('lng')], {icon: icon});
            markers[self.data('place')].bindTooltip(self.data('name')+' : '+self.data('latest'), {
                direction: 'right'
            });
            markers[self.data('place')].addTo(map);
            self.find('a').click(function(){
                map.setView(markers[$(this).attr('id')]._latlng, 18);
                for (const [key, value] of Object.entries(markers)) {
                    value.closeTooltip();
                }
                markers[self.data('place')].openTooltip();
            });
        });
    });
}
