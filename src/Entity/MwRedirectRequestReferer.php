<?php

namespace DepaRedirectMiddleware\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DepaRedirectMiddleware\Repository\MwRedirectRequestRefererRepository;

/**
 * @ORM\Table(name="mw_redirect_request_referer")
 * @ORM\Entity(MwRedirectRequestRefererRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class MwRedirectRequestReferer
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="referer_url" ,type="string", length=255, nullable=false)
     */
    protected string $refererUrl;

    /**
     * @ORM\ManyToMany(targetEntity="Redirect\Entity\MwRedirectRequest", mappedBy="requestOrigins")
     * @ORM\JoinTable(name="mw_redirect_request_has_mw_redirect_request_referer",
     *      joinColumns={@ORM\JoinColumn(name="mw_redirect_request_referer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="mw_redirect_request_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $redirectRequests;

    //new arraycollection
    public function __construct()
    {
        $this->redirectRequests = new ArrayCollection();
    }

    //getter and setter
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRefererUrl(): string
    {
        return $this->refererUrl;
    }

    /**
     * @param string $refererUrl
     */
    public function setRefererUrl(string $refererUrl): void
    {
        $this->refererUrl = $refererUrl;
    }
}