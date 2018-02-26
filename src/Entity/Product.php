<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="product", indexes={@Index(name="idx_article_id", columns={"article_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 *
 */
class Product
{
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
     * @Assert\NotBlank()
     * @ORM\Column(name="article_id", type="string", length=255)
     */
    private $articleId;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="article_name", type="string", length=255, unique=true)
     */
    private $articleName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Image", cascade={"persist"})
     * @ORM\JoinTable(name="product_images",
     *     joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)}
     * )
     *
     */
    private $images;


    /**
     * Product constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->images = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
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
     */
    public function setCreatedAt(\DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;
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
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getArticleId(): ?string
    {
        return $this->articleId;
    }

    /**
     * @param string $articleId
     */
    public function setArticleId(string $articleId)
    {
        $this->articleId = $articleId;
    }

    /**
     * @return string
     */
    public function getArticleName(): ?string
    {
        return $this->articleName;
    }

    /**
     * @param string $articleName
     * @return Product
     */
    public function setArticleName(string $articleName): self
    {
        $this->articleName = $articleName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Product
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get images
     *
     *
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    public function setImages()
    {
        //return $this->images;
    }




}
