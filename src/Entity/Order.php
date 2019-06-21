<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      collectionOperations={
 *          "get"={"access_control"="is_granted('ROLE_USER')"},
 *          "post"={"access_control"="is_granted('ROLE_USER')"}
 *      },
 *      itemOperations={
 *          "get"={"access_control"="is_granted('ROLE_USER') and object.getUser() == user"},
 *          "put"={"access_control"="is_granted('ROLE_USER') and object.getUser() == user"},
 *          "delete"={"access_control"="is_granted('ROLE_USER') and object.getUser() == user"}
 *      },
 *      normalizationContext={
 *          "groups"={"read"}
 *      },
 *      denormalizationContext={
 *          "groups"={"write"}
 *      }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @Groups({"read"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
