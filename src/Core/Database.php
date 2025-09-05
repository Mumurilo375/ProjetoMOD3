<?php

namespace App\Core;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Dotenv\Dotenv;

class Database
{
    private function __construct() {}

    private static EntityManager $entityManager;

    public static function getEntityManager(): EntityManager
    {
        if (!isset(self::$entityManager)) {
            // Define timezone padrão para evitar datas/horas em UTC
            \date_default_timezone_set('America/Sao_Paulo');
            self::$entityManager = new EntityManager(
                self::getConnection(), 
                self::getConfig()
            );
        }

        return self::$entityManager;
    }

    private static function getConfig(): Configuration
    {
        $paths = [__DIR__ . '/../Model'];
        // Em desenvolvimento, habilite geração automática de proxies
        $isDevMode = true;
        $proxyDir = __DIR__ . '/../../var/proxies';
        if (!is_dir($proxyDir)) { @mkdir($proxyDir, 0777, true); }

        return ORMSetup::createAttributeMetadataConfiguration(
            $paths,
            $isDevMode,
            $proxyDir
        );
    }

    private static function getConnection(): Connection
    {
        
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $dbParams = [
            'driver'   => $_ENV['DB_DRIVER'],
            'user'     => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'dbname'   => $_ENV['DB_DBNAME'],
            'host'     => $_ENV['DB_HOST']
        ];
        
        $config = self::getConfig();

        return DriverManager::getConnection(
            $dbParams, 
            $config
        );
    }
}
