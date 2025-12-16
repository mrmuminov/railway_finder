<?php

require 'RailwayService.php';

// Environment variables
$interval = (int)getenv("APP_INTERVAL");
$date = getenv("APP_DATE");
$depStationCode = getenv("APP_FROM");
$arvStationCode = getenv("APP_TO");
$maxCost = (float)getenv("APP_MAX_COST");
$botToken = getenv("APP_BOT_TOKEN");
$chatId = getenv("APP_CHAT_ID");

// Instantiate and run the service
$railwayService = new RailwayService(
    $date,
    $depStationCode,
    $arvStationCode,
    $maxCost,
    $botToken,
    $chatId,
    $interval
);

$railwayService->run();
