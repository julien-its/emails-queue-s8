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
        $mapping = [
            'EmailsQueueBundle' => [
                'type' => 'attribute',
                'is_bundle' => false,
                'dir' => __DIR__.'/Entity',
                'prefix' => 'JulienIts\EmailsQueueBundle\Entity',
                'alias' => 'EmailsQueue',
            ],
        ];

        // If the app declares named entity managers, a global "orm.mappings" key would make
        // Doctrine create an extra "default" entity manager holding only our entities.
        // Attach the mapping to the app's default entity manager instead.
        $defaultEm = null;
        $entityManagers = [];
        foreach ($builder->getExtensionConfig('doctrine') as $config) {
            $defaultEm = $config['orm']['default_entity_manager'] ?? $defaultEm;
            $entityManagers += $config['orm']['entity_managers'] ?? [];
        }

        if ([] === $entityManagers) {
            $builder->prependExtensionConfig('doctrine', ['orm' => ['mappings' => $mapping]]);

            return;
        }

        $defaultEm ??= array_key_first($entityManagers);

        $builder->prependExtensionConfig('doctrine', [
            'orm' => [
                'entity_managers' => [
                    $defaultEm => ['mappings' => $mapping],
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
