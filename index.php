<?php
require_once 'inc/config.php';
require_once 'inc/api.php';

$city = 'Marilia'; // Cidade padrão
if (isset($_GET['city'])) {
    $city = $_GET['city'];
}
$days = 7; // Dias da semana


$results = Api::get($city, $days);
if ($results['status'] == 'error') {
    echo 'Error: ' . $results['message'];
    exit;
}

$data = json_decode($results['data'], true);


// location data
$location = [];
$location['name'] = $data['location']['name'];
$location['region'] = $data['location']['region'];
$location['country'] = $data['location']['country'];
$location['latitude'] = $data['location']['lat'];
$location['longitude'] = $data['location']['lon'];
$location['current_time'] = $data['location']['localtime'];


// current weather data
$current = [];
$current['info'] = 'Neste momento:';
$current['temperature'] = $data['current']['temp_c'];
$current['condition'] = $data['current']['condition']['text'];
$current['condition_icon'] = $data['current']['condition']['icon'];
$current['wind_speed'] = $data['current']['wind_kph'];

// forecast weather data
$forecast = [];
foreach ($data['forecast']['forecastday'] as $day) {
    $forecast_day = [];
    $forecast_day['info'] = 'Previsão do dia:';

    // Extraindo e formatando a data
    $originalDate = $day['date'];
    $dateTime = new DateTime($originalDate);
    $formattedDate = $dateTime->format('d/m/Y');
    $forecast_day['date'] = $formattedDate; // Atualizando com a data formatada
    $forecast_day['condition'] = $day['day']['condition']['text'];
    $forecast_day['condition_icon'] = $day['day']['condition']['icon'];
    $forecast_day['max_temp'] = $day['day']['maxtemp_c'];
    $forecast_day['min_temp'] = $day['day']['mintemp_c'];
    $forecast[] = $forecast_day;
}

function city_selected($city, $selected_city)
{
    if ($city == $selected_city) {
        return 'selected';
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsões Climáticas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="bg-dark text-white">
    <div class="container-fluid mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-10 p-5 bg-light text-black">

                <div class="row">
                    <div class="col-9">
                        <h3>Previsão do Tempo <strong><? $location['name'] ?></strong></h3>
                        <p class="my-2">Região: <?= $location['region'] ?> | <?= $location['country'] ?> | Previsão para <strong><?= $days ?> dias</strong></p>
                    </div>

                </div>
                <div class="col-3 text-end">
                    <select class="form-select">
                        <option value="Marilia" <?= city_selected('Marilia', $city) ?>>Marilia</option>
                        <option value="Assis" <?= city_selected('Assis', $city) ?>>Assis</option>
                        <option value="Lins" <?= city_selected('Lins', $city) ?>>Lins</option>
                        <option value="Bauru" <?= city_selected('Bauru', $city) ?>>Bauru</option>
                        <option value="Campinas" <?= city_selected('Campinas', $city) ?>>Campinas</option>

                    </select>
                </div>

                <h3>Previsão da Semana : <strong><?= $city ?></strong></h3>
                <hr>

                <!-- current -->
                <?php
                $weather_info = $current;
                include 'inc/weather_info.php';

                ?>

                <!-- forecast -->
                <?php foreach ($forecast as $day) : ?>
                    <?php
                    $weather_info = $day;
                    include 'inc/weather_info.php';
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
            const select = document.querySelector('select');
            select.addEventListener('change', (e) => {
                const city = e.target.value;
                console.log(city);
                window.location.href = `index.php?city=${city}`;
            });
        </script>
</body>

</html>