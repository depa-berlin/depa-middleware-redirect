<?php

declare(strict_types=1);

namespace DepaRedirectMiddleware;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Dot\AnnotatedServices\Factory\AnnotatedServiceAbstractFactory;
use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use DepaRedirectMiddleware\Crawler\RedirectCrawlerCommand;
use DepaRedirectMiddleware\Middleware\RedirectMiddleware;
use DepaRedirectMiddleware\Observer\RedirectCrawlerObserver;

/**
 * The configuration provider for the DepaRedirectRequestMiddleware module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine' => $this->getDoctrineConfig(),
            'laminas-cli' => $this->getCliConfig(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'abstract_factories' => [
                AnnotatedServiceAbstractFactory::class,
            ],
            'invokables' => [
            ],
            'factories'  => [
                RedirectMiddleware::class => AnnotatedServiceFactory::class,
                RedirectCrawlerObserver::class => AnnotatedServiceFactory::class,
                RedirectCrawlerCommand::class => AnnotatedServiceFactory::class,
            ],
        ];
    }

    public function getDoctrineConfig(): array
    {
        return [
            'driver' => [
                'orm_default' => [
                    'drivers' => [
                        'DepaRedirectMiddleware\Entity' => 'RedirectEntities',
                    ]
                ],
                'RedirectEntities' => [
                    'class' => AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/Entity'],
                ]
            ]
        ];
    }
    public function getCliConfig(): array
    {
        return [
            'commands' => [
                // ...
                'redirect:crawler' => RedirectCrawlerCommand::class,
                // ...
            ],
        ];
    }
}
