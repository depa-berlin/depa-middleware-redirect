<?php

namespace DepaRedirectMiddleware\Repository;

use Doctrine\ORM\EntityRepository;
use DepaRedirectMiddleware\Entity\MwRedirectResponse;

/**
 * @method MwRedirectResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method MwRedirectResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method MwRedirectResponse[]    findAll()
 * @method MwRedirectResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MwRedirectResponseRepository extends EntityRepository
{

}
