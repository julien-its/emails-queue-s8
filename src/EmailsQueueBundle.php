<?php

namespace JulienIts\EmailsQueueBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class EmailsQueueBundle extends AbstractBundle
{
    protected string $extensionAlias = 'emails_queue';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('mode')
                    ->info('If mode is not "prod", emails will not be sent to the defined to/cc/bcc but to debug_to / debug_cc')
                    ->defaultValue('dev')
                ->end()
                ->scalarNode('debug_to')->defaultNull()->end()
                ->scalarNode('debug_cc')->defaultNull()->end()
            ->end()
        ;
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$builder->hasExtension('doctrine')) {
            return;
        }

        // Explicit attribute mapping so the entities are found whatever the app's auto_mapping setting
        $builder->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'EmailsQueueBundle' => [
                        'type' => 'attribute',
                        'is_bundle' => false,
                        'dir' => __DIR__.'/Entity',
                        'prefix' => 'JulienIts\EmailsQueueBundle\Entity',
                        'alias' => 'EmailsQueue',
                    ],
                ],
            ],
        ]);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()
            ->set('emails_queue.mode', $config['mode'])
            ->set('emails_queue.debug_to', $config['debug_to'])
            ->set('emails_queue.debug_cc', $config['debug_cc'])
        ;

        $container->import('../config/services.php');
    }
}
