<?php

use JulienIts\EmailsQueueBundle\Services\EmailService;
use JulienIts\EmailsQueueBundle\Services\EmailsQueueService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    // Commands (#[AsCommand]), controller (#[AsController]) and repositories (ServiceEntityRepository)
    // are registered and tagged through autoconfiguration.
    $services->load('JulienIts\\EmailsQueueBundle\\', '../src/')
        ->exclude([
            '../src/Entity/',
            '../src/EmailsQueueBundle.php',
        ]);

    // Kept public for backward compatibility ($container->get(...))
    $services->get(EmailService::class)->public();
    $services->get(EmailsQueueService::class)->public();
};
