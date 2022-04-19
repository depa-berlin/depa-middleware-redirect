<?php

namespace DepaRedirectMiddleware\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DepaRedirectMiddleware\Repository\MwRedirectRequestRepository;

/**
 * @ORM\Table(name="mw_redirect_request" )
 * @ORM\Entity(MwRedirectRequestRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class MwRedirectRequest
{
    //id
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="request_url" ,type="string", length=255, nullable=false)
     */
    protected string $requestUrl;

    /**
     * @ORM\Column(name="request_count" ,type="integer", nullable=false, options={"default" : 1})
     */
    protected int $requestCount = 1;

    /**
     * Response
     *
     * @ORM\ManyToOne(targetEntity="DepaRedirectMiddleware\Entity\MwRedirectResponse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mw_redirect_response_id", referencedColumnName="id")
     * })
     */
    private $mwRedirectResponse;

    /**
     * @ORM\ManyToMany(targetEntity=MwRedirectRequestReferer::class, inversedBy="redirectRequests")
     *   * @ORM\JoinTable(name="mw_redirect_request_has_mw_redirect_request_referer",
     *      joinColumns={@ORM\JoinColumn(name="mw_redirect_request_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="mw_redirect_request_referer_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $requestOrigins;

    public function __construct()
    {
        $this->requestOrigins = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    public function setRequestUrl(string $requestUrl): MwRedirectRequest
    {
        $this->requestUrl = $requestUrl;
        return $this;
    }

    public function getRequestCount(): int
    {
        return $this->requestCount;
    }

    public function setRequestCount(int $requestCount): MwRedirectRequest
    {
        $this->requestCount = $requestCount;
        return $this;
    }

    public function getMwRedirectResponse(): ?MwRedirectResponse
    {
        return $this->mwRedirectResponse;
    }

    public function setMwRedirectResponse(MwRedirectResponse $mwRedirectResponse): MwRedirectRequest
    {
        $this->mwRedirectResponse = $mwRedirectResponse;
        return $this;
    }

    /**
     * @return Collection|MwRedirectRequestReferer[]
     */
    public function getRequestOrigins(): Collection
    {
        return $this->requestOrigins;
    }

    public function addRequestOrigin(MwRedirectRequestReferer $origin): self
    {
        if (!$this->requestOrigins->contains($origin)) {
            $this->requestOrigins[] = $origin;
        }

        return $this;
    }

    public function removeRequestOrigin(MwRedirectRequestReferer $origin): self
    {
        $this->requestOrigins->removeElement($origin);

        return $this;
    }

}