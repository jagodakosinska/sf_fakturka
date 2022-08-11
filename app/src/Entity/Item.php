<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
class Item
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     *  @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */

    private $vendor;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getVendor(): ?User
    {
        return $this->vendor;
    }

    public function setVendor(?User $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }
}
