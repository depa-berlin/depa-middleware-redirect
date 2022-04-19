<?php

namespace DepaRedirectMiddleware\Repository;

use Doctrine\ORM\EntityRepository;
use DepaRedirectMiddleware\Entity\MwRedirectRequest;

/**
 * @method MwRedirectRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method MwRedirectRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method MwRedirectRequest[]    findAll()
 * @method MwRedirectRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MwRedirectRequestRepository extends EntityRepository
{
    /**
     * Updates $request with $data.
     * @param MwRedirectRequest $request
     */
    public function saveRequest(MwRedirectRequest $request)
    {
        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->flush();
    }
}
