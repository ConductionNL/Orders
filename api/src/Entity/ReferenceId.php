<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ReferenceIdRepository")
 */
class ReferenceId
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read"})
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;

    /**
     *
     * @Groups({"read","write"})
     * @MaxDepth(1)
     * @ORM\OneToOne(targetEntity="App\Entity\Order", inversedBy="referenceId", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $referencedOrder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReferencedOrder(): ?Order
    {
        return $this->referencedOrder;
    }

    public function setReferencedOrder(Order $referencedOrder): self
    {
        $this->referencedOrder = $referencedOrder;
        return $this;
    }
}
