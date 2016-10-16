<?php

namespace SilexProvider;

use Exception;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Provider to configure Doctrine ORM in Silex Application.
 *
 * @author Eder Soares <edersoares@me.com>
 */
class DoctrineOrmServiceProvider implements ServiceProviderInterface {

    private $configs;

    /**a
     * Load settings for Doctrine ORM works.
     *
     * @param array $configs
     */
    public function __construct($configs) {
        $this->configs = $configs;
    }

    /**
     * Register Service Provider.
     *
     * @param Container $app Silex Application
     * @return EntityManager
     */
    public function register(Container $app) {

        $app['orm'] = function ($app) {

            if (false === array_key_exists('mapping', $this->configs))
                throw new Exception('Mapping configuration not defined.');
            else if (false === array_key_exists('type', $this->configs['mapping']))
                throw new Exception('Mapping type configuration not defined.');
            else if (false === array_key_exists('paths', $this->configs['mapping']))
                throw new Exception('Mapping paths configuration not defined.');
            else if (false === array_key_exists('database', $this->configs))
                throw new Exception('Database configuration not defined.');

            $mapping = $this->configs['mapping'];
            $database = $this->configs['database'];
            $development = array_key_exists('development', $this->configs) ? (boolean) $this->configs['development'] : false;

            switch ($mapping['type']) {

                case 'annotations':
                    $setup = Setup::createAnnotationMetadataConfiguration($mapping['paths'], $development);
                break;

                case 'xml':
                    $setup = Setup::createXMLMetadataConfiguration($mapping['paths'], $development);
                break;

                case 'yaml':
                    $setup = Setup::createYAMLMetadataConfiguration($mapping['paths'], $development);
                break;

                default:
                    throw new Exception('Mapping type not founded.');
                break;
            }

            return EntityManager::create($database, $setup);
        };

    }

}
