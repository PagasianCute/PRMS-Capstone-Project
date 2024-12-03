<!DOCTYPE html>
<html>
<head>
    <title>Leaflet Routing Machine</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <style>
        body { margin: 0; padding: 0; }
        #map { width: 100%; height: 100vh; }
    </style>
</head>
<body>
    <div id="map"></div>
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script>
        var map = L.map('map').setView([9.75897285347868, 125.51119911343939], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var waypoints = [
            L.latLng(9.590976532760077, 125.69703359446011), // Start point
            L.latLng(9.784351844762556, 125.48988179261147)  // End point
        ];

        L.Routing.control({
            waypoints: waypoints,
            routeWhileDragging: true
        }).addTo(map);
    </script>
</body>
</html>
