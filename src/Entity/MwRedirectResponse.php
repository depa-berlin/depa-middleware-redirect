<?php

namespace DepaRedirectMiddleware\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="mw_redirect_response")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class MwRedirectResponse
{
    //id
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="response_url", type="string", length=255, nullable=false)
     */
    protected string $responseUrl;

    /**
     *
     * @ORM\ManyToOne(targetEntity="MwRedirectResponseStatuscode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mw_redirect_response_statuscode_id", referencedColumnName="id")
     * })
     */
    protected $mwRedirectResponseStatuscode;

    //getter and setter
    public function getId(): int
    {
        return $this->id;
    }

    public function getResponseUrl(): string
    {
        return $this->responseUrl;
    }

    public function setResponseUrl(string $responseUrl): MwRedirectResponse
    {
        $this->responseUrl = $responseUrl;
        return $this;
    }

    public function getMwRedirectResponseStatuscode(): MwRedirectResponseStatuscode
    {
        return $this->mwRedirectResponseStatuscode;
    }

    public function setMwRedirectResponseStatuscode(MwRedirectResponseStatuscode $mwRedirectResponseStatuscode): MwRedirectResponse
    {
        $this->mwRedirectResponseStatuscode = $mwRedirectResponseStatuscode;
        return $this;
    }

}