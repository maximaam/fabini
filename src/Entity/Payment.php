<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Payment
{
    const STATUS_INIT = 1;
    const STATUS_CONFIRM = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $paymentId;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $paypalPaymentDetails;

    /**
     * @var array
     *
     * @ORM\Column(type="simple_array")
     */
    private $productsIds;

    /**
     * Product constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Pre update callback
     *
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * @param string $paymentId
     * @return $this
     */
    public function setPaymentId(string $paymentId): self
    {
        $this->paymentId = $paymentId;
        return $this;
    }

    /**
     * @return array
     */
    public function getPaypalPaymentDetails(): array
    {
        return $this->paypalPaymentDetails;
    }
    /**
     * @param array $paypalPaymentDetails
     * @return $this
     */
    public function setPaypalPaymentDetails(array $paypalPaymentDetails): self
    {
        $this->paypalPaymentDetails = $paypalPaymentDetails;
        return $this;
    }

    /**
     * @return array
     */
    public function getProductsIds(): array
    {
        return $this->paypalPaymentDetails;
    }
    /**
     * @param array $ids
     * @return $this
     */
    public function setProductsIds(array $ids): self
    {
        $this->productsIds = $ids;
        return $this;
    }


}
