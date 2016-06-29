<?php

// Check for stupid
if(count($argv) == 1) { echo "Run a command, dip shit.\n"; die(); }

require "vendor/autoload.php";

use GuzzleHttp\Client;
use Steam\Configuration;
use Steam\Runner\GuzzleRunner;
use Steam\Runner\DecodeJsonStringRunner;
use Steam\Steam;
use Steam\Utility\GuzzleUrlBuilder;

// Load env
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// CS:GO app id
define("APP_ID", getenv('APP_ID'));

// Player steam id
define("STEAM_ID", getenv('STEAM_ID'));

// Setup steam web api and auth
$steam = new Steam(new Configuration([
    Configuration::STEAM_KEY => getenv('API_KEY')
]));

$steam->addRunner(new GuzzleRunner(new Client(), new GuzzleUrlBuilder()));
$steam->addRunner(new DecodeJsonStringRunner());

/**
 * Commands
 */

switch($argv[1]) {

    // Get player game stats
    case 'game stats':
        $result = $steam->run(new \Steam\Command\UserStats\GetUserStatsForGame(STEAM_ID, APP_ID));
        break;

    case 'achievements':
        $result = $steam->run(new \Steam\Command\UserStats\GetPlayerAchievements(STEAM_ID, APP_ID));
        break;

    case 'summaries':
        $result = $steam->run(new \Steam\Command\User\GetPlayerSummaries([STEAM_ID]));
        break;

    case 'schema':
        $result = $steam->run(new \Steam\Command\UserStats\GetSchemaForGame(APP_ID));
        break;

    default:
        echo "Command not registered.";
        die();
}

// Dump result
echo var_dump(json_encode($result));