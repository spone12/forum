DG.then(function () 
{
    map = DG.map('map', 
    {
        center: [55.748460, 37.533355],
        zoom: 15,
        minZoom: 3,
        maxZoom: 18
    });

    DG.marker([55.748460, 37.533355]).addTo(map).bindPopup('Moscow city!');
    
    DG.control.location({position: 'bottomright'}).addTo(map);
    DG.control.scale().addTo(map);
    DG.control.ruler({position: 'bottomleft'}).addTo(map);
    DG.control.traffic().addTo(map);
});