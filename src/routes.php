<?php
$routes = [
    'getDeliveryQuote',
    'getDeliveryZones',
    'createDelivery',
    'getAllDeliveries',
    'getDelivery',
    'cancelDelivery',
    'returnDelivery',
    'addCourierTip',
    'metadata'
];
foreach($routes as $file) {
    require __DIR__ . '/../src/routes/'.$file.'.php';
}

