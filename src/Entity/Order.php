<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="orders")
 */
class Order
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $track_id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $track_tk;


    /**
     * @ORM\Column(type="string")
     */
    private $created_at;


    /**
     * @ORM\Column(type="integer")
     */
    private $delivery_cost;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="order")
     */
    private $OrderItem;

    public function __construct()
    {
        $this->OrderItem = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrackId(): ?string
    {
        return $this->track_id;
    }

    public function setTrackId(string $track_id): self
    {
        $this->track_id = $track_id;

        return $this;
    }

    public function getTrackTk(): ?string
    {
        return $this->track_tk;
    }

    public function setTrackTk(string $track_tk): self
    {
        $this->track_tk = $track_tk;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }


    public function getDeliveryCost(): ?int
    {
        return $this->delivery_cost;
    }

    public function setDeliveryCost(int $delivery_cost): self
    {
        $this->delivery_cost = $delivery_cost;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItem(): Collection
    {
        return $this->OrderItem;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->OrderItem->contains($orderItem)) {
            $this->OrderItem[] = $orderItem;
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->OrderItem->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
}
