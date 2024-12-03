<!DOCTYPE html>
<html>

<head>
    <title>Geolocation</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

    <style>
        body {
            margin: 0;
            padding: 0;
        }
    </style>

</head>

<body>
    <div id="map" style="width:100%; height: 100vh"></div>
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <script>
        var map = L.map('map').setView([9.756266004848117, 125.51085579069725], 11);
        mapLink = "<a href='http://openstreetmap.org'>OpenStreetMap</a>";
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', { attribution: 'Leaflet &copy; ' + mapLink + ', contribution', maxZoom: 18 }).addTo(map);

        var taxiIcon = L.icon({
            iconUrl: 'taxi.png',
            iconSize: [70, 70]
        });


        // Set the waypoints for routing
        var waypoints = [
            L.latLng(9.784362970701153, 125.48995482772433), // User1
            L.latLng(9.590998754116097, 125.696990683107)  // User2
        ];

        L.Routing.control({
            waypoints: waypoints
        }).on('routesfound', function (e) {
            var routes = e.routes;
            console.log(routes);

            // Extract and display the distance and duration
            var routeSummary = e.routes[0].summary;
            console.log('Distance:', routeSummary.totalDistance, 'Duration:', routeSummary.totalTime);
        }).addTo(map);
    </script>
</body>

</html>
