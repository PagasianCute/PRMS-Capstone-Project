document.addEventListener("DOMContentLoaded", function () {
  const map = L.map("map").setView([7.1097, 125.6087], 7);

  L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "© OpenStreetMap",
  }).addTo(map);

  let marker1, marker2, route;

  function computeTravelTime() {
    const address1 = encodeURIComponent(
      document.getElementById("address1").value.trim()
    );
    const address2 = encodeURIComponent(
      document.getElementById("address2").value.trim()
    );

    if (!address1 || !address2) {
      console.error("Error: Both addresses must be provided");
      return;
    }

    const apiKey = "YOUR_OPENROUTESERVICE_API_KEY";
    const directionsUrl = `https://api.openrouteservice.org/v2/directions/driving-car?api_key=${apiKey}&start=${address1}&end=${address2}`;

    fetch(directionsUrl)
      .then((response) => response.json())
      .then((data) => handleDirectionsResponse(data))
      .catch((error) => console.error("Error fetching directions:", error));
  }

  function handleDirectionsResponse(data) {
    if (data && data.features && data.features.length > 0) {
      const travelTime = data.features[0].properties.segments[0].duration / 60; // in minutes
      document.getElementById(
        "travelTime"
      ).innerText = `Travel Time: ${travelTime.toFixed(2)} minutes`;

      removeMapLayers();

      const startCoords = data.features[0].geometry.coordinates[0];
      const endCoords =
        data.features[0].geometry.coordinates[
          data.features[0].geometry.coordinates.length - 1
        ];

      marker1 = L.marker([startCoords[1], startCoords[0]]).addTo(map);
      marker2 = L.marker([endCoords[1], endCoords[0]]).addTo(map);

      route = L.polyline(data.features[0].geometry.coordinates).addTo(map);

      map.fitBounds(route.getBounds());
    } else {
      console.error("Error: Invalid response structure");
    }
  }

  function removeMapLayers() {
    if (marker1) map.removeLayer(marker1);
    if (marker2) map.removeLayer(marker2);
    if (route) map.removeLayer(route);
  }
});
