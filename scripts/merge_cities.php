<?php
$cities = './cities';

$merged = [];
foreach (scandir($cities) as $country) {
    if ($country == '.' || $country == '..') {
        continue;
    }
    $country_id = explode('.', $country)[0];
    echo $country."\t".$country_id."\n";
    $merged[$country_id] = json_decode(file_get_contents($cities.'/'.$country), true);
}

file_put_contents('cities.json', json_encode($merged));
