$(document).ready(function () {
    // Map init
    var map = L.map("map"),
        popupLoadingTemplate = _.template($("#popup_loading_template").text()),
        popupTemplate = _.template($("#popup_template").text());

    // Tiles
    L.tileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
    }).addTo(map);

    // City icon
    var CityIcon = L.Icon.extend({
        options: {
            iconUrl     : "/assets/img/marker.png",
            iconSize    : [40, 40],
            iconAnchor  : [20, 45],
            shadowUrl   : "/assets/img/marker-shadow.png",
            shadowSize  : [48, 30],
            shadowAnchor: [5, 35],
            popupAnchor : [0, -30]
        }
    });

    // Get cities
    $.get("/api/cities").done(function (cities) {
        var points = [];

        // Drawing city markers
        _.each(cities, function (city) {
            points.push(city.coords);

            var marker = L.marker(city.coords, {
                icon: new CityIcon
            });

            marker
                .on("popupopen", function (e) {
                    var popup = e.popup;

                    // Render loading message
                    popup.setContent(popupLoadingTemplate());

                    // Loading city data
                    $.get("/api/cities/" + city._id).done(function (data) {
                        // Calculating the elapsed time since the last update
                        var updated = moment(moment.unix(_.first(data).fetched.sec)).fromNow();

                        // Render popup message
                        popup.setContent(popupTemplate({city: city, data: data, updated: updated}));

                        // Filter data, get only HH:00 timestamps
                        data = _.first(_.filter(data, function (item) {
                            return moment.unix(item.fetched.sec).minute() === 0;
                        }), 24).reverse();

                        // Chart
                        var chartData = {
                            labels  : _.map(data, function (item) {
                                var hour = moment.unix(item.fetched.sec).hour();
                                return hour % 2 === 0 ? hour : "";
                            }),
                            datasets: [
                                {
                                    label               : city.name,
                                    fillColor           : "transparent",
                                    strokeColor         : "#45956E",
                                    pointColor          : "#45956E",
                                    pointStrokeColor    : "#45956E",
                                    pointHighlightFill  : "#fff",
                                    pointHighlightStroke: "#45956E",
                                    data                : _.pluck(data, "temp")
                                }
                            ]
                        };

                        var ctx = _.first($("#chart_" + city._id)).getContext("2d");
                        new Chart(ctx).Line(chartData, {
                            tooltipFillColor       : "#808080",
                            tooltipTemplate        : "<%= value %> C",
                            pointDotRadius         : 3,
                            pointHitDetectionRadius: 4
                        });
                    });
                })
                .bindPopup("")
                .addTo(map);
        });

        if (points.length > 1) {
            // Calculating boundaries
            var westNorth = new L.LatLng(points[0][0], points[0][1]),
                eastSouth = new L.LatLng(points[1][0], points[1][1]);

            _.each(points, function (point) {
                if (point[0] < westNorth.lat) {
                    westNorth.lat = point[0];
                }

                if (point[1] < westNorth.lng) {
                    westNorth.lng = point[1];
                }

                if (point[0] > eastSouth.lat) {
                    eastSouth.lat = point[0];
                }

                if (point[1] > eastSouth.lng) {
                    eastSouth.lng = point[1];
                }
            });

            // Fit bounds into map
            map.fitBounds(new L.LatLngBounds(westNorth, eastSouth), {padding: [50, 50]});

            // Set max zoom
            map.options.minZoom = map.getZoom() - 1;
            map.options.maxZoom = map.getZoom();
        } else if (points.length === 1) {
            // One city available, just zoom it
            map.setView(new L.LatLng(points[0][0], points[0][1]), 6);
        }
    });
});