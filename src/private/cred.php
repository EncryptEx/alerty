<?php 
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(
    [
        // Database Credentials
        'DB_HOST',
        'DB_NAME',
        'DB_USER',
        'DB_PASS',

        // Mail Credentials

        'MAIL_PORT',
        'MAIL_SENDER',
        'MAIL_PWD',
        'MAIL_HOST',

        // Hash generator secret
        'HASH_SALT'
        ]
);