<?php

namespace DepaRedirectMiddleware\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use DepaRedirectMiddleware\Entity\MwRedirectRequest;
use DepaRedirectMiddleware\Entity\MwRedirectRequestReferer;
use DepaRedirectMiddleware\Entity\MwRedirectResponse;
use DepaRedirectMiddleware\Entity\MwRedirectResponseStatuscode;
use DepaRedirectMiddleware\Repository\MwRedirectRequestRefererRepository;
use DepaRedirectMiddleware\Repository\MwRedirectRequestRepository;
use DepaRedirectMiddleware\Repository\MwRedirectResponseRepository;
use DepaRedirectMiddleware\Repository\MwRedirectResponseStatuscodeRepository;

/**
 * Class RedirectService
 * @package Redirect\Service
 *
 * @author Maurice (schwarz@designpark.de)
 *
 * @Service
 */
class RedirectService
{
    /** @var EntityManager $em */
    protected EntityManager $em;

    /** @var MwRedirectRequestRepository $redirectRequestRepository */
    protected $redirectRequestRepository;

    /** @var MwRedirectResponseRepository $redirectResponseRepository */
    protected $redirectResponseRepository;

    /** @var MwRedirectRequestRefererRepository $redirectRequestOriginRepository */
    protected $redirectRequestOriginRepository;

    /** @var MwRedirectResponseStatuscodeRepository $redirectResponseStatuscodeRepository */
    protected MwRedirectResponseStatuscodeRepository $redirectResponseStatuscodeRepository;

    //redirect Statuscodes
    const REDIRECT_STATUSCODE_301 = 301;
    const REDIRECT_STATUSCODE_302 = 302;
    const REDIRECT_STATUSCODE_303 = 303;
    const REDIRECT_STATUSCODE_307 = 307;
    const REDIRECT_STATUSCODE_308 = 308;

    /**
     * RedirectService constructor.
     * @param EntityManager $em
     * @Inject({EntityManager::class})
     */
    public function __construct(
        EntityManager $em
    ) {
        $this->em = $em;
        $this->redirectRequestRepository = $em->getRepository(MwRedirectRequest::class);
        $this->redirectResponseRepository = $em->getRepository(MwRedirectResponse::class);
        $this->redirectResponseStatuscodeRepository = $em->getRepository(MwRedirectResponseStatuscode::class);
        $this->redirectRequestOriginRepository = $em->getRepository(MwRedirectRequestReferer::class);
    }

    /**
     * @param array $criteria
     * @param null $orderBy
     * @return MwRedirectRequest|null
     */
    public function findOneRequestBy(array $criteria, $orderBy = null): ?MwRedirectRequest
    {
        return $this->redirectRequestRepository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param $orderBy
     * @return MwRedirectResponse|null
     */
    public function findOneResponseBy(array $criteria, $orderBy = null): ?MwRedirectResponse
    {
        return $this->redirectResponseRepository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param $orderBy
     * @return MwRedirectRequestReferer|null
     */
    public function findOneRefererBy(array $criteria, $orderBy = null): ?MwRedirectRequestReferer
    {
        return $this->redirectRequestOriginRepository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param string $requestUrl
     * @return MwRedirectRequest|null
     * check with requestUrl if the request is already in the database
     */
    public function checkRequest(string $requestUrl): ?MwRedirectRequest
    {
        $request = $this->findOneRequestBy(['requestUrl' => $requestUrl]);
        if ($request) {
            return $request;
        }
        return null;
    }

    /**
     * @param MwRedirectRequest $request
     * @return void
     * increase the counter of the request
     */
    public function increaseRequestCounter(MwRedirectRequest $request): void
    {
            $request->setRequestCount($request->getRequestCount() + 1);
            $this->redirectRequestRepository->saveRequest($request);
    }

    /**
     * @param string $requestUrl
     * @return MwRedirectRequest
     * create and save new request
     */
    public function createRequest(string $requestUrl): MwRedirectRequest
    {
        /** @var MwRedirectRequest $request */
        $request = new MwRedirectRequest();

        $request->setRequestUrl($requestUrl);
        $request->setRequestCount(1);

        $this->redirectRequestRepository->saveRequest($request);

        return $request;
    }

    /**
     * @param string $responseUrl
     * @return MwRedirectResponse|null
     */
    public function checkResponse(string $responseUrl): ?MwRedirectResponse
    {
        $response = $this->findOneResponseBy(['responseUrl' => $responseUrl]);
        if ($response) {
            return $response;
        }
        return null;
    }

    //create new mvRedirectResponse
    public function createResponse(string $responseUrl, MwRedirectResponseStatuscode $responseStatuscode): MwRedirectResponse
    {
        $response = new MwRedirectResponse();
        $response->setResponseUrl($responseUrl);
        $response->setMwRedirectResponseStatuscode($responseStatuscode);
        return $response;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveResponse(MwRedirectResponse $response)
    {
        $this->em->persist($response);
        $this->em->flush();
    }

    /**
     * @param array $criteria
     * @param $orderBy
     * @return MwRedirectResponseStatuscode|null
     */
    public function findStatuscodeBy(array $criteria, $orderBy = null): ?MwRedirectResponseStatuscode
    {
        return $this->redirectResponseStatuscodeRepository->findOneBy($criteria, $orderBy);
    }

    //get Permanent redirect statuscode
    public function getPermanentRedirectStatuscode(): MwRedirectResponseStatuscode
    {
        return $this->findStatuscodeBy(['type' => self::REDIRECT_STATUSCODE_301]);
    }

    //process create new response

    /**
     * @param string $responseUri
     * @return MwRedirectResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function processCreateResponse(string $responseUri): MwRedirectResponse
    {
        $responseStatuscode = $this->getPermanentRedirectStatuscode();

        $response = $this->createResponse($responseUri, $responseStatuscode);
        $this->saveResponse($response);
        return $response;
    }

    /**
     * @param string $refererUrl
     * @return MwRedirectRequestReferer
     * create new origin
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createReferer(string $refererUrl): MwRedirectRequestReferer
    {
        $referer = new MwRedirectRequestReferer();
        $referer->setRefererUrl($refererUrl);

        $this->saveReferer($referer);

        return $referer;
    }

    /**
     * @param MwRedirectRequestReferer $referer
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveReferer(MwRedirectRequestReferer $referer)
    {
        $this->em->persist($referer);
        $this->em->flush();
    }

    /**
     * @param MwRedirectRequest $request
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveRequest(MwRedirectRequest $request)
    {
        $this->em->persist($request);
        $this->em->flush();
    }

}