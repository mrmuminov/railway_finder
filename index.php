<?php

require 'RailwayService.php';

// Environment variables
$interval = (int)getenv("APP_INTERVAL"); // 30, in second
$date = getenv("APP_DATE"); // YYYY-MM-DD
$depStationCode = getenv("APP_FROM"); // station id
$arvStationCode = getenv("APP_TO"); // station id
$maxCost = (int)getenv("APP_MAX_COST"); // 250000 in sum
$botToken = getenv("APP_BOT_TOKEN"); // telegram bot token
$chatId = getenv("APP_CHAT_ID"); // telegram chat id

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
