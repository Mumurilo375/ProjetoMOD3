<?php
// bootstrap.php - VERSÃO FINAL CORRIGIDA

// 1. Carrega o Autoload do Composer
require_once "vendor/autoload.php";

// 2. Importa as classes necessárias do Doctrine
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

// 3. Define as configurações do Doctrine
$paths = [__DIR__."/src/Model"];
$isDevMode = true;

// 4. Define as configurações de conexão com o banco de dados
$dbParams = [
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '',
    'dbname'   => 'projeto_starrate',
    'host'     => '127.0.0.1'
];

// 5. Cria a configuração e o EntityManager da forma moderna
$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
$connection = DriverManager::getConnection($dbParams, $config);
$entityManager = new EntityManager($connection, $config);