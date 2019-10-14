<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Order
{
	/**
	 * @var \Ramsey\Uuid\UuidInterface $id The UUID identifier of this object
	 * @example e2984465-190a-4562-829e-a8cca81aa35d
	 *
	 * @ApiProperty(
	 * 	   identifier=true,
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "The UUID identifier of this object",
	 *             "type"="string",
	 *             "format"="uuid",
	 *             "example"="e2984465-190a-4562-829e-a8cca81aa35d"
	 *         }
	 *     }
	 * )
	 *
	 * @Assert\Uuid
	 * @ORM\Id
	 * @ORM\Column(type="uuid", unique=true)
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	private $id;
	
	/**
	 * @param string $reference The human readable reference for this request, build as {gemeentecode}-{year}-{referenceId}. Where gemeentecode is a four digit number for gemeenten and a four letter abriviation for other organizations
	 *
	 * @ApiProperty(
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "The human readable reference for this request",
	 *             "type"="string",
	 *             "example"="6666-2019-0000000012",
	 *             "maxLength"="255"
	 *         }
	 *     }
	 * )
	 *
	 * @Groups({"read"})
	 * @ORM\Column(type="string", length=255, nullable=true) //, unique=true
	 * @ApiFilter(SearchFilter::class, strategy="exact")
	 */
	private $reference;
	
	/**
	 * @param string $referenceId The autoincrementing id part of the reference, unique on a organization-year-id basis
	 *
	 * @ORM\Column(type="integer", length=11, nullable=true)
	 */
	private $referenceId;
    
    /**
     * @var string $targetOrganization The RSIN of the organization that ownes this proces
     * @example 002851234
     *
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *         	   "description" = "The RSIN of the organization that ownes this proces",
     *             "type"="string",
     *             "example"="002851234",
     *              "maxLength"="255",
     *             "required"=true
     *         }
     *     }
     * )
     *
     * @Assert\NotNull
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255)
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    private $targetOrganization;

    /**
     * @Groups({"read","write"})
     * @ORM\Column(type="money")
     */
    private $price;
    
    /**
     * @var Datetime $createdAt The moment this request was created by the submitter
     *
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
    }
    
    public function getId()
    {
    	return $this->id;
    }
    
    public function setId(string $id): self
    {
    	$this->id = $id;
    	
    	return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }
    
    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        
        return $this;
    }
    
    public function getReferenceId(): ?int
    {
        return $this->reference;
    }
    
    public function setReferenceId(int $referenceId): self
    {
        $this->referenceId = $referenceId;
        
        return $this;
    }
    
    public function getRsin(): ?string
    {
        return $this->rsin;
    }
    
    public function setRsin(string $rsin): self
    {
        $this->rsin = $rsin;
        
        return $this;
    }
    
    public function getSubmitter(): ?string
    {
        return $this->submitter;
    }
    
    public function setSubmitter(string $submitter): self
    {
        $this->submitter = $submitter;
        
        return $this;
    }
    
    public function getSubmitterPerson(): ?bool
    {
        return $this->submitterPerson;
    }
    
    public function setSubmitterPerson(bool $submitterPerson): self
    {
        $this->submitterPerson = $submitterPerson;
        
        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $orderLine): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }
    
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }
}
