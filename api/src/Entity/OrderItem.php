<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use DateTime;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * An entity representing an item of an order
 *
 * This entity represents an item that is placed on the order
 *
 * @author Robert Zondervan <robert@conduction.nl>
 * @category entity
 * @license EUPL <https://github.com/ConductionNL/orderregistratiecomponent/blob/master/LICENSE.md>
 * @package orderregistratiecomponent
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\OrderLineItem")
 */
class OrderItem
{
    /**
     * @var UuidInterface
     *
     * @ApiProperty(
     * 	   identifier=true,
     *     attributes={
     *         "openapi_context"={
     *         	   "description" = "The UUID identifier of this object",
     *             "type"="string",
     *             "format"="uuid",
     *             "example"="e2984465-190a-4562-829e-a8cca81aa35d"
     *         }
     *     }
     * )
     *
     * @Groups({"read"})
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;
    /**
     * @var Order $order The order that contains this item
     *
     * @Groups({"read","write"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $order;
    /**
     * @var string $offer The offer this item represents
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "example"="http://example.org/offers/1",
     *             "default"="http://example.org/offers/1"
     *         }
     *     }
     * )
     * @ORM\Column(type="string", length=255)
     * @Groups({"read","write"})
     * @Assert\Url
     * @Assert\NotNull
     * @MaxDepth(1)
     */
    private $offer;

    /**
     * @var string $product The product this item represents. DEPRECATED: REPLACED BY OFFER
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     * @MaxDepth(1)
     * @Assert\Length(
     *     max = 255
     * )
     * @deprecated
     */
    private $product;

    /**
     * @var int $quantity The quantity of the items that are ordered
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "example"=1,
     *             "default"=1
     *         }
     *     }
     * )
     * @Groups({"read","write"})
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $quantity;

    /**
     *  @var string $price The price of this product
     *  @example 50.00
     *
     *  @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "iri"="https://schema.org/price",
     *         	   "description" = "The price of this product",
     *             "type"="string",
     *             "example"="50.00",
     *             "maxLength"="9",
     *             "required" = true
     *         },
     *         "openapi_context"={
     *             "example"="50.00",
     *             "default"="50.00"
     *         }
     *     }
     * )
     * @Groups({"read","write"})
     * @Assert\NotNull
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    private $price;

    /**
     *  @var string $priceCurrency The currency of this product in an [ISO 4217](https://en.wikipedia.org/wiki/ISO_4217) format
     *  @example EUR
     *
     *  @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "iri"="https://schema.org/priceCurrency",
     *         	   "description" = "The currency of this product in an [ISO 4217](https://en.wikipedia.org/wiki/ISO_4217) format",
     *             "type"="string",
     *             "example"="EUR",
     *             "default"="EUR",
     *             "maxLength"="3",
     *             "minLength"="3"
     *         },
     *         "openapi_context"={
     *             "example"="EUR",
     *             "default"="EUR"
     *         }
     *     }
     * )
     *
     * @Assert\Currency
     * @Groups({"read","write"})
     * @ORM\Column(type="string")
     */
    private $priceCurrency;

    /**
     *  @var integer $taxPercentage The tax percentage for this offer as an integer e.g. 9% makes 9
     *  @example 9
     *
     *  @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *         	   "description" = "The tax percentage for this offer as an integer e.g. 9% makes 9",
     *             "type"="integer",
     *             "example"="9",
     *             "maxLength"="3",
     *             "minLength"="1",
     *             "required" = true
     *         },
     *         "openapi_context"={
     *             "example"=9,
     *             "default"=9
     *         }
     *     }
     * )
     *
     * @Assert\NotBlank
     * @Assert\PositiveOrZero
     * @Groups({"read", "write"})
     * @ORM\Column(type="integer")
     */
    private $taxPercentage;

    /**
     * @var DateTime $createdAt The moment this request was created by the submitter
     *

     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @deprecated
     */
    public function getProduct(): ?string
    {
        if($this->product)
            return $this->product;
        else
            return $this->getOffer();
    }

    /**
     * @deprecated
     */
    public function setProduct(string $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getOffer(): ?string
    {
        return $this->offer;
    }

    public function setOffer(string $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getPriceCurrency(): ?string
    {
        return $this->priceCurrency;
    }

    public function setPriceCurrency(string $priceCurrency): self
    {
        $this->priceCurrency = $priceCurrency;

        return $this;
    }

    public function getTaxPercentage(): ?int
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage(int $taxPercentage): self
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }
}
