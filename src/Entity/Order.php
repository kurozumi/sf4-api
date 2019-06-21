<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Annotation\UserAware;
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
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @UserAware(userFieldName="user_id")
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
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     */
    private $orderNo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @Groups({"read", "write"})
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

    public function getOrderNo(): ?string
    {
        return $this->orderNo;
    }

    public function setOrderNo(string $orderNo): self
    {
        $this->orderNo = $orderNo;

        return $this;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
