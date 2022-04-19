<?php

namespace DepaRedirectMiddleware\Repository;

use Doctrine\ORM\EntityRepository;
use DepaRedirectMiddleware\Entity\MwRedirectRequestReferer;
/**
 * @method MwRedirectRequestReferer|null find($id, $lockMode = null, $lockVersion = null)
 * @method MwRedirectRequestReferer|null findOneBy(array $criteria, array $orderBy = null)
 * @method MwRedirectRequestReferer[]    findAll()
 * @method MwRedirectRequestReferer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MwRedirectRequestRefererRepository extends EntityRepository
{
}
