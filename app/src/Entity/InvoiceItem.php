<?php

namespace App\Entity;

use App\Repository\InvoiceItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvoiceItemRepository::class)
 */
class InvoiceItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $quantityType;

    /**
     * @ORM\Column(type="integer")
     */
    private $vatRate;

    /**
     * @ORM\Column(type="integer")
     */
    private $unitPrice;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalPrice;

    /**
     * @ORM\ManyToOne(targetEntity=Invoice::class, inversedBy="invoiceItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invoice;

    /**
     * @ORM\ManyToOne(targetEntity=Item::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\Column(type="integer")
     */
    private $itemOrder;

    public function getId(): ?int
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

    public function getQuantityType(): ?string
    {
        return $this->quantityType;
    }

    public function setQuantityType(string $quantityType): self
    {
        $this->quantityType = $quantityType;

        return $this;
    }

    public function getVatRate(): ?int
    {
        return $this->vatRate;
    }

    public function setVatRate(int $vatRate): self
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getItemOrder(): ?int
    {
        return $this->itemOrder;
    }

    public function setItemOrder(int $itemOrder): self
    {
        $this->itemOrder = $itemOrder;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }
}
