<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An entity representing an item of an order.
 *
 * This entity represents an item that is placed on the order
 *
 * @author Robert Zondervan <robert@conduction.nl>
 *
 * @category entity
 *
 * @license EUPL <https://github.com/ConductionNL/orderregistratiecomponent/blob/master/LICENSE.md>
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true},
 *     itemOperations={
 *          "get",
 *          "put",
 *          "delete",
 *          "get_change_logs"={
 *              "path"="/order_items/{id}/change_log",
 *              "method"="get",
 *              "swagger_context" = {
 *                  "summary"="Changelogs",
 *                  "description"="Gets al the change logs for this resource"
 *              }
 *          },
 *          "get_audit_trail"={
 *              "path"="/order_items/{id}/audit_trail",
 *              "method"="get",
 *              "swagger_context" = {
 *                  "summary"="Audittrail",
 *                  "description"="Gets the audit trail for this resource"
 *              }
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\OrderItemRepository")
 * @Gedmo\Loggable(logEntryClass="Conduction\CommonGroundBundle\Entity\ChangeLog")
 * @ORM\HasLifecycleCallbacks
 *
 * @ApiFilter(BooleanFilter::class)
 * @ApiFilter(OrderFilter::class)
 * @ApiFilter(DateFilter::class, strategy=DateFilter::EXCLUDE_NULL)
 * @ApiFilter(SearchFilter::class, properties={"order.customer": "exact"})
 */
class OrderItem
{
    /**
     * @var UuidInterface
     *
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     *
     * @Assert\Uuid
     * @Groups({"read"})
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string The name of the object
     *
     * @example my OrderItem
     *
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @Assert\Length(
     *     max=255
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string The description of the order item
     *
     * @example This is the best order item ever
     *
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @Assert\Length(
     *     max=255
     * )
     * @ORM\Column(type="string", length=2550, nullable=true)
     */
    private $description;

    /**
     * @var Order The order that contains this item
     *
     * @Groups({"read","write"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     */
    private $order;

    /**
     * @var string The offer this item represents
     *
     * @example http://example.org/offers/1
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read","write"})
     * @Assert\Url
     * @MaxDepth(1)
     */
    private $offer;

    /**
     * @var string The product this item represents
     *
     * @example http://example.org/offers/1
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read","write"})
     * @Assert\Url
     * @MaxDepth(1)
     */
    private $product;

    /**
     * @var int The quantity of the items that are ordered
     *
     * @example 1
     *
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\PositiveOrZero
     */
    private $quantity;

    /**
     *  @var string The price of this orderItem
     *
     *  @example 50.00
     *
     * @Gedmo\Versioned
     * @Assert\NotNull
     * @Groups({"read","write"})
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    private $price;

    /**
     * @var string The currency of this product in an [ISO 4217](https://en.wikipedia.org/wiki/ISO_4217) format
     *
     * @example EUR
     *
     * @Gedmo\Versioned
     * @Assert\Currency
     * @Groups({"read","write"})
     * @ORM\Column(type="string")
     */
    private $priceCurrency;

    /**
     * @var ArrayCollection The taxes that affect this offer
     *
     *
     * @MaxDepth(1)
     * @Groups({"read", "write"})
     * @ORM\ManyToMany(targetEntity="App\Entity\Tax", mappedBy="orderItems")
     */
    private $taxes;

    /**
     * @var string The of this offer, only used in combination with subscribtion type products, entered according to the [ISO 8601-standard](https://en.wikipedia.org/wiki/ISO_8601#Durations)
     *
     * @example PT10M
     *
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $recurrence;

    /**
     * @var string The the notice period requered to end an subscribtion, entered according to the [ISO 8601-standard](https://en.wikipedia.org/wiki/ISO_8601#Durations)
     *
     * @example PT10M
     *
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notice;

    /**
     * @var DateTime The moment this subscribtion begins (only used on subscribtions)
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateStart;

    /**
     * @var DateTime The moment this subscribtion end
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEnd;

    /**
     * @var DateTime The moment this request was created by the submitter
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @var DateTime The moment this request was updated
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateModified;

    public function __construct()
    {
        $this->taxes = new ArrayCollection();
    }

    /**
     *  @ORM\PostPersist
     *  @ORM\PostUpdate
     */
    public function prePersist()
    {
        $this->order->calculateTotals();
    }

    public function getId()
    {
        return $this->id;
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

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): self
    {
        $this->product = $product;

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

    /**
     * @return Collection|Tax[]
     */
    public function getTaxes(): Collection
    {
        return $this->taxes;
    }

    public function addTax(Tax $tax): self
    {
        if (!$this->taxes->contains($tax)) {
            $this->taxes[] = $tax;
            $tax->addOrderItem($this);
        }

        return $this;
    }

    public function removeTax(Tax $tax): self
    {
        if ($this->taxes->contains($tax)) {
            $this->taxes->removeElement($tax);
            $tax->removeOrderItem($this);
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRecurrence(): ?string
    {
        return $this->recurrence;
    }

    public function setRecurrence(string $recurrence): self
    {
        $this->recurrence = $recurrence;

        return $this;
    }

    public function getNotice(): ?string
    {
        return $this->notice;
    }

    public function setNotice(string $notice): self
    {
        $this->notice = $notice;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(?\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(?\DateTimeInterface $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }
}
