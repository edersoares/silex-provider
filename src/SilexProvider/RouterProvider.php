<?php

namespace SilexProvider;

use Exception;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provider to configure Routes in Silex Application.
 *
 * @author Eder Soares <edersoares@me.com>
 */
class RouterProvider implements ServiceProviderInterface {

    /**
     * Load routes for Silex Application.
     *
     * @param array $routes
     */
    public function __construct($routes) {
        $this->routes = (array) $routes;
    }

    /**
     * Register Service Provider.
     *
     * @param Container $app Silex Application
     * @return RouteCollection
     */
    public function register(Container $app) {

        $app->extend('routes', function (RouteCollection $routes) use ($app) {

            foreach ($this->routes as $controller => $actions) {

                $actions = (array) $actions;

                foreach ($actions as $action => $config) {

                    $class = sprintf('%s::%s', $controller, $action);

                    if (array_key_exists('pattern', $config))
                        $pattern = str_replace('//', '/', $config['pattern']);
                    else
                        throw new Exception('Pattern not defined in route: ' . $class);

                    if (array_key_exists('method', $config))
                        $method = strtoupper($config['method']);
                    else
                        throw new Exception('Method not defined in route: ' . $class);

                    $route = $app->match($pattern, $class)->method($method);

                    if (array_key_exists('value', $config))
                        foreach ($config['value'] as $key => $value)
                            $route->value($key, $value);

                    if (array_key_exists('assert', $config))
                        foreach ($config['assert'] as $key => $value)
                            $route->assert($key, $value);

                    if (array_key_exists('requireHttp', $config) && $config['requireHttp'])
                        $route->requireHttp();

                    if (array_key_exists('requireHttps', $config) && $config['requireHttps'])
                        $route->requireHttps();

                    if (array_key_exists('bind', $config))
                        $route->bind($config['bind']);

                }
            }

            return $routes;
        });

    }

}
