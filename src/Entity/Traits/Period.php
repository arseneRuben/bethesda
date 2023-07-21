<?php

namespace App\Entity\Traits;
trait Period  {


    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $wording;


      /**
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="date")
     */
    private $endDate;


    /**
     * @ORM\Column(type="boolean")
     */
    private $activated;
    

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getActivated(): ?bool
    {
        return $this->activated;
    }

   

    public function setActivated(bool $activated): self
    {
        $this->activated = $activated;

        return $this;
    }

    public function __toString() {
        $name = ( is_null($this->getWording())) ? "" : $this->getWording();
        return (string) ($name );
    }
} 