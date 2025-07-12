<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
/**
 * Klasa encji reprezentująca lokalizację wydarzenia.
 *
 * Przechowuje informacje o nazwie, mieście, adresie oraz powiązanych wydarzeniach.
 */
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'location')]
    private Collection $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    /**
     * Zwraca identyfikator lokalizacji.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Zwraca nazwę lokalizacji.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Ustawia nazwę lokalizacji.
     *
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Zwraca miasto lokalizacji.
     *
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Ustawia miasto lokalizacji.
     *
     * @param string $city
     * @return static
     */
    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Zwraca adres lokalizacji.
     *
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Ustawia adres lokalizacji.
     *
     * @param string $address
     * @return static
     */
    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Zwraca kolekcję wydarzeń powiązanych z lokalizacją.
     *
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * Dodaje wydarzenie do lokalizacji.
     *
     * @param Event $event
     * @return static
     */
    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setLocation($this);
        }

        return $this;
    }

    /**
     * Usuwa wydarzenie z lokalizacji.
     *
     * @param Event $event
     * @return static
     */
    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getLocation() === $this) {
                $event->setLocation(null);
            }
        }

        return $this;
    }
}
