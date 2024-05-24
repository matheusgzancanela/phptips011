<?php

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\SendGridHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Logger;

require __DIR__ . "/vendor/autoload.php";

$logger = new Logger("web");
$logger->pushHandler(new BrowserConsoleHandler(Logger::DEBUG));
$logger->pushHandler(new StreamHandler(__DIR__ . "/log.txt", Logger::WARNING));
$logger->pushHandler(new SendGridHandler(
  SENDGRID_EX["user"],
  SENDGRID_EX["passwd"],
  "noreply@siteexemplo.com.br",
  "contato@matheusgz.com.br",
  "Erro em SITE: " . date("d/m/Y H:i:s"),
  Logger::CRITICAL
));
$logger->pushProcessor(function ($record) {
  $record["extra"]["HTTP_HOST"] = $_SERVER["HTTP_HOST"];
  $record["extra"]["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
  $record["extra"]["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
  $record["extra"]["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];
  return $record;
});

$tele_key = "";
$tele_channel = "";
$tele_handler = new TelegramBotHandler(
  $tele_key,
  $tele_channel,
  Logger::EMERGENCY
);
$tele_handler->setFormatter(new LineFormatter("%level_name%: %message%"));
$logger->pushHandler($tele_handler);

// DEBUG
$logger->debug("Olá Mundo!", ["logger" => true]);
$logger->info("Olá Mundo!", ["logger" => true]);
$logger->notice("Olá Mundo!", ["logger" => true]);

// FILE
$logger->warning("Olá Mundo!", ["logger" => true]);
$logger->error("Olá Mundo!", ["logger" => true]);

// EMAIL
// $logger->critical("Olá Mundo!", ["logger" => true]);
// $logger->alert("Olá Mundo!", ["logger" => true]);

//$logger->emergency("Essa mensagem foi enviada pelo Monolog");
