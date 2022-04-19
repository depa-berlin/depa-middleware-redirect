<?php

namespace DepaRedirectMiddleware\Observer;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Dot\AnnotatedServices\Annotation\Inject;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use DepaRedirectMiddleware\Service\RedirectService;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Symfony\Component\Console\Output\OutputInterface;

class RedirectCrawlerObserver extends CrawlObserver
{
    /** @var RedirectService $redirectService */
    protected RedirectService $redirectService;

    /** @var OutputInterface $output */
    protected OutputInterface $output;

    /**
     * @param RedirectService $redirectService
     * @param OutputInterface $output
     * @Inject({RedirectService::class})
     */
    public function __construct(RedirectService $redirectService, OutputInterface $output)
    {
        $this->redirectService = $redirectService;
        $this->output = $output;
    }
    /**
     * @inheritDoc
     */
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null)
    {
        $uri = $url->getPath();

        $responseObj = $this->redirectService->checkResponse($uri);

        if(!$responseObj){
            //save new Response Objekt to db
            try {
                $this->redirectService->processCreateResponse($uri);
                $this->output->writeln("<info>Response Objekt erstellt: $uri</info>");
            } catch (OptimisticLockException|ORMException $e) {
                //do nothing
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null)
    {
        $this->output->writeln("<error>Crawl failed: $url</error>" . $requestException->getMessage());
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling()
    {
        $this->output->writeln("<info>Crawl finished</info>");
    }


}