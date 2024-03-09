DG.then(function () {
    var map;

    map = DG.map("map", {
        center: [55.74846, 37.533355],
        zoom: 14,
        minZoom: 3,
        maxZoom: 18,
    });

    map.locate({ setView: true, watch: true })
        .on("locationfound", function (e) {
            DG.marker([e.latitude, e.longitude]).addTo(map);
        })
        .on("locationerror", function (e) {
            DG.popup()
                .setLatLng(map.getCenter())
                .setContent("Доступ к определению местоположения отключён")
                .openOn(map);
        });

    DG.control.location({ position: "bottomright" }).addTo(map);
    DG.control.scale().addTo(map);
    DG.control.ruler({ position: "bottomleft" }).addTo(map);
    DG.control.traffic().addTo(map);
});
