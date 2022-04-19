<?php

namespace DepaRedirectMiddleware\Crawler;

use Dot\AnnotatedServices\Annotation\Inject;
use DepaRedirectMiddleware\Observer\RedirectCrawlerObserver;
use DepaRedirectMiddleware\Service\RedirectService;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RedirectCrawlerCommand
 * @package Redirect\Crawler
 * @author Maurice {Schwazr@designpark.de}
 *
 * Crawls all routes for a given domain and saves them as possible redirects / response objects
 *
 * @example php vendor/bin/laminas redirect:crawler 'http://flemming-klingbeil.devel'
 */
class RedirectCrawlerCommand extends Command
{
    /** @var RedirectService $redirectService */
    protected RedirectService $redirectService;

    /**
     * RedirectMiddleware constructor.
     * @param RedirectService $redirectService
     * @Inject({RedirectService::class })
     */
    public function __construct(RedirectService $redirectService)
    {
        parent::__construct();
        $this->redirectService = $redirectService;
    }

    protected function configure()
    {
        $this->setName('redirect:crawler')
            ->setDescription('Crawl redirects')
            ->addArgument('domain', InputArgument::REQUIRED, 'Domain to crawl');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Crawling redirects...</info>');
        $url = $input->getArgument('domain');
        $profile = new CrawlInternalUrls($url);

        Crawler::create()
            ->setCrawlProfile($profile)
            ->setCrawlObserver(new RedirectCrawlerObserver($this->redirectService, $output))
            ->startCrawling($url);

        return 0;
    }
}
