<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank
     */
    private string $userName;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank
     */
    private string $login;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private ?string $apiToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $apiTokenExpiryDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);
        if (!is_string($encryptedPassword)) {
            throw new \RuntimeException('Password encryption failure');
        }

        $this->password = $encryptedPassword;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function getApiTokenExpiryDate(): ?DateTimeInterface
    {
        return $this->apiTokenExpiryDate;
    }

    public function setApiTokenExpiryDate(?DateTimeInterface $apiTokenExpiryDate): self
    {
        $this->apiTokenExpiryDate = $apiTokenExpiryDate;

        return $this;
    }

    public function isApiTokenExpired(): bool
    {
        if (is_null($this->getApiToken())) {
            return true;
        }

        if ($this->getApiTokenExpiryDate() <= new \DateTime()) {
            return true;
        }

        return false;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        // Nothing.
    }
}
