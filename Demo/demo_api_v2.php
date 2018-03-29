<?php
require '../vendor/autoload.php';
use LuxSciApiClient\LuxSciAPIv2Client;
use LuxSciApiClient_Model\BodyType;
use LuxSciApiClient_Model_V2\SendEmailOrTextRequest;

// Initialize client
$client = new LuxSciAPIv2Client(
    'your_token',
    'your_secret',
    'your_user',
    'your_pass'
);

// Fill request params
$sendEmailOrTextRequest = new SendEmailOrTextRequest();
$sendEmailOrTextRequest->from_name = "your_name";
$sendEmailOrTextRequest->from_address = "from_email@test.info";
$sendEmailOrTextRequest->to = ['to_email1@test.info'];
$sendEmailOrTextRequest->subject = 'Testing from API';
$sendEmailOrTextRequest->body_type = BodyType::Html;
$sendEmailOrTextRequest->body = "<h1>Hello!!<h1><p>Testing from API</p>";

// send a SecureLine Secure Email and/or SecureText
$client->sendEmailOrText($sendEmailOrTextRequest);