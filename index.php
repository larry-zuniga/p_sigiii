<?php include("db_config.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mapa con PostGIS + Leaflet</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
    <h2>Visualización básica con PostGIS</h2>
    <div id="map"></div>

    <?php
    // Ejemplo: capa de puntos (ej. tabla "mov_centros_serviciodetransito_9377" con columna geom en SRID 9377)
 $sql = "SELECT gid, nombre, direccion, ST_AsGeoJSON(ST_Transform(geom, 4326)) as geojson 
            FROM mov_centros_serviciodetransito_9377 
            LIMIT 50;";
    $result = pg_query($conn, $sql);

    $features = [];
    while ($row = pg_fetch_assoc($result)) {
        $geometry = json_decode($row['geojson']);
        $feature = [
            "type" => "Feature",
            "geometry" => $geometry,
            "properties" => [
                "gid" => $row['gid'],
                "nombre" => $row['nombre'],
                "direccion" => $row['direccion']
            ]
        ];
        $features[] = $feature;
    }

    $geojson = json_encode([
        "type" => "FeatureCollection",
        "features" => $features
    ]);
    ?>

    <script>
    // Inicializar mapa
    var map = L.map('map').setView([0, -78], 6);

    // Capa base
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    // Datos desde PHP
    var geojsonData = <?php echo $geojson; ?>;

    // Añadir capa GeoJSON
    L.geoJSON(geojsonData, {
        onEachFeature: function (feature, layer) {
            if (feature.properties && feature.properties.nombre) {
                layer.bindPopup("Nombre: " + feature.properties.nombre + "<br>Dirección: " + feature.properties.direccion);
            }
        }
    }).addTo(map);
    </script>
</body>
</html>
