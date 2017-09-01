<?php
/**
 * User: drago <mhytry@gmail.com>
 * Date: 29.08.17
 * Time: 22:35
 */
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;
$app['config'] = [
    'prooph' => [
        'service_bus' => [
            'command_bus' => [
                'router' => [
                    'routes' => [
                        'command-1' => 'handler-1',
                    ]
                ]
            ]
        ]
    ]
];

$app['handler-1'] = $app->protect(function ($command) {
    var_dump($command . ' handled!!!');
    return;
});

$app->register(new \GromNaN\Pimple\PimpleContainerProvider());

//not work because CommandBusFactory require PSR11 compatible container and pimple
//$app['commandbus'] = $app->factory(new \Prooph\ServiceBus\Container\CommandBusFactory());


$app['commandbus'] = function () use ($app) {
    $factory = new \Prooph\ServiceBus\Container\CommandBusFactory();
    return $factory($app['container']);
};

$app->get('/command-1', function () use ($app) {
    $app['commandbus']->dispatch('command-1');
    return "";
});


$app->run();