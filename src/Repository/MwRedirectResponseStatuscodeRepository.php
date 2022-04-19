<?php

namespace DepaRedirectMiddleware\Repository;

use Doctrine\ORM\EntityRepository;
use DepaRedirectMiddleware\Entity\MwRedirectResponseStatuscode;

/**
 * @method MwRedirectResponseStatuscode|null find($id, $lockMode = null, $lockVersion = null)
 * @method MwRedirectResponseStatuscode|null findOneBy(array $criteria, array $orderBy = null)
 * @method MwRedirectResponseStatuscode[]    findAll()
 * @method MwRedirectResponseStatuscode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MwRedirectResponseStatuscodeRepository extends EntityRepository
{

}
