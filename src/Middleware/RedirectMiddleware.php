<?php

namespace DepaRedirectMiddleware\Middleware;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Dot\AnnotatedServices\Annotation\Inject;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use DepaRedirectMiddleware\Entity\MwRedirectRequest;
use DepaRedirectMiddleware\Entity\MwRedirectResponse;
use DepaRedirectMiddleware\Service\RedirectService;

/**
 * Class RedirectMiddleware
 * @package Redirect\Middleware
 *
 * Middleware zum Redirecten von URLs
 *
 * @author Maurice (schwarz@designpark.de)
 */
class RedirectMiddleware implements MiddlewareInterface
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
        $this->redirectService = $redirectService;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestUri = $request->getUri()->getPath();

        $referer = $request->getHeaderLine('referer');

        /**
         * @var MwRedirectRequest $requestObject
         * check if request already exists
         */
        $requestObject = $this->redirectService->checkRequest($requestUri);

        if ($requestObject) {

            //increase redirect count
            $this->redirectService->increaseRequestCounter($requestObject);

            try {
                $this->processReferer($referer, $requestObject);
            } catch (OptimisticLockException|ORMException $e) {
                //TODO: handle exception
            }

            /** @var MwRedirectResponse $responseObj */
            $responseObj = $requestObject->getMwRedirectResponse();

            if ($responseObj){

                //prevents infinite redirects
                if ($responseObj->getResponseUrl() == $requestUri){
                    return $handler->handle($request);
                }

                $responseUrl = $responseObj->getResponseUrl();
                $responseStatuscode = $responseObj->getMwRedirectResponseStatuscode()->getType();

                //redirect to response url
                return new RedirectResponse($responseUrl, $responseStatuscode);
            }
        }
        else {
            //save request if not already saved
            $requestObject = $this->redirectService->createRequest($requestUri);
            try {
                $this->processReferer($referer, $requestObject);
            } catch (OptimisticLockException|ORMException $e) {
                //TODO: handle exception
            }
        }

        //call next middleware
        return $handler->handle($request);
    }

    //handle origin-header for debugging

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    private function processReferer(String $referer, MwRedirectRequest $requestObject)
    {
        if ($referer && $referer != '') {

            $requestOriginObj = $this->redirectService->findOneRefererBy(['refererUrl' => $referer]);

            //create origin if not exists
            if (!$requestOriginObj) {
                $newOrigin = $this->redirectService->createReferer($referer);
                $requestObject->addRequestOrigin($newOrigin);
            } else {
                $requestObject->addRequestOrigin($requestOriginObj);
            }
            $this->redirectService->saveRequest($requestObject);
        }
    }

}