<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ExchangeRateMetadataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity(repositoryClass: ExchangeRateMetadataRepository::class)]
#[HasLifecycleCallbacks]
class ExchangeRateMetadata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $time_last_update_unix = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $time_next_update_unix = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $time_last_update_utc = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $time_next_update_utc = null;

    #[ORM\Column(length: 3, columnDefinition: "CHAR(3) NOT NULL")]
    private ?string $base_currency_code = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'metadata', targetEntity: ExchangeRate::class, orphanRemoval: true)]
    private Collection $exchangeRates;

    public function __construct()
    {
        $this->exchangeRates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeLastUpdateUnix(): ?string
    {
        return $this->time_last_update_unix;
    }

    public function setTimeLastUpdateUnix(string $time_last_update_unix): static
    {
        $this->time_last_update_unix = $time_last_update_unix;

        return $this;
    }

    public function getTimeNextUpdateUnix(): ?string
    {
        return $this->time_next_update_unix;
    }

    public function setTimeNextUpdateUnix(string $time_next_update_unix): static
    {
        $this->time_next_update_unix = $time_next_update_unix;

        return $this;
    }

    public function getTimeLastUpdateUtc(): ?\DateTimeImmutable
    {
        return $this->time_last_update_utc;
    }

    public function setTimeLastUpdateUtc(\DateTimeImmutable $time_last_update_utc): static
    {
        $this->time_last_update_utc = $time_last_update_utc;

        return $this;
    }

    public function getTimeNextUpdateUtc(): ?\DateTimeImmutable
    {
        return $this->time_next_update_utc;
    }

    public function setTimeNextUpdateUtc(\DateTimeImmutable $time_next_update_utc): static
    {
        $this->time_next_update_utc = $time_next_update_utc;

        return $this;
    }

    public function setBaseCurrencyCode(string $base_currency_code): static
    {
        $this->base_currency_code = $base_currency_code;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, ExchangeRate>
     */
    public function getExchangeRates(): Collection
    {
        return $this->exchangeRates;
    }

    public function addExchangeRate(ExchangeRate $exchangeRate): static
    {
        if (!$this->exchangeRates->contains($exchangeRate)) {
            $this->exchangeRates->add($exchangeRate);
            $exchangeRate->setMetadata($this);
        }

        return $this;
    }

    public function removeExchangeRate(ExchangeRate $exchangeRate): static
    {
        if ($this->exchangeRates->removeElement($exchangeRate)) {
            // set the owning side to null (unless already changed)
            if ($exchangeRate->getMetadata() === $this) {
                $exchangeRate->setMetadata(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
        $this->setUpdatedAtValue();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getBaseCurrencyCode(): ?string
    {
        return $this->base_currency_code;
    }
}
