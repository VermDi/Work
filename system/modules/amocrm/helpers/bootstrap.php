<?php

use AmoCRM\Client\AmoCRMApiClient;
use Symfony\Component\Dotenv\Dotenv;
use \modules\amocrm\models\AmoCrmSettings;

include_once __DIR__ . '/../vendor/autoload.php';

//$dotenv = new Dotenv();
//$dotenv->load(__DIR__ . '/.env.dist', __DIR__ . '/.env');

$settings = AmoCrmSettings::instance()->get();

$clientId = $settings->client_id;
$clientSecret = $settings->client_secret;
$redirectUri = $settings->redirect_url;

$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

include_once __DIR__ . '/token_actions.php';
include_once __DIR__ . '/error_printer.php';
