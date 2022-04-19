<?php

namespace DepaRedirectMiddleware\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MwRedirectResponseStatuscode
 * @package Redirect\Entity
 * @ORM\Entity (repositoryClass="Redirect\Repository\MwRedirectResponseStatuscodeRepository")
 * @ORM\Table(name="mw_redirect_response_statuscode")
 * @ORM\HasLifecycleCallbacks
 */
class MwRedirectResponseStatuscode
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    protected int $type;

    //setter and getter
    public function getId(): int
    {
        return $this->id;
    }

    public function setType(int $type): MwRedirectResponseStatuscode
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }


}