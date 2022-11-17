<?php

	namespace App\Entity;

	use App\Repository\AirportRepository;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity
	 * @ORM\Entity(repositoryClass=AirportRepository::class)
	 */
	class Airport
	{
		/**
		 * @ORM\Id
		 * @ORM\GeneratedValue
		 * @ORM\Column(type="integer")
		 */
		private $id;

		/**
		 * @ORM\Column(type="string", length=3)
		 */
		private $iata;

		/**
		 * @ORM\Column(type="string", length=4)
		 */
		private $icao;

		/**
		 * @ORM\Column(type="string", length=50)
		 */
		private $name;

		/**
		 * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="fromAirport")
		 */
		private $sourceFlights;

		/**
		 * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="toAirport")
		 */
		private $destinationFlights;

		public function __construct()
		{
			$this->sourceFlights = new ArrayCollection();
			$this->destinationFlights = new ArrayCollection();
		}

		public function getId(): ?int
		{
			return $this->id;
		}

		public function getIata(): ?string
		{
			return $this->iata;
		}

		public function setIata(string $iata): self
		{
			$this->iata = $iata;

			return $this;
		}

		public function getIcao(): ?string
		{
			return $this->icao;
		}

		public function setIcao(string $icao): self
		{
			$this->icao = $icao;

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

		/**
		 * @return Collection<int, Ticket>
		 */
		public function getSourceFlights(): Collection
		{
			return $this->sourceFlights;
		}

		public function addSourceFlight(Ticket $sourceFlight): self
		{
			if (!$this->sourceFlights->contains($sourceFlight)) {
				$this->sourceFlights[] = $sourceFlight;
				$sourceFlight->setFromAirport($this);
			}

			return $this;
		}

		public function removeSourceFlight(Ticket $sourceFlight): self
		{
			if ($this->sourceFlights->removeElement($sourceFlight)) {
				// set the owning side to null (unless already changed)
				if ($sourceFlight->getFromAirport() === $this) {
					$sourceFlight->setFromAirport(null);
				}
			}

			return $this;
		}

		/**
		 * @return Collection<int, Ticket>
		 */
		public function getDestinationFlights(): Collection
		{
			return $this->destinationFlights;
		}

		public function addDestinationFlight(Ticket $destinationFlight): self
		{
			if (!$this->destinationFlights->contains($destinationFlight)) {
				$this->destinationFlights[] = $destinationFlight;
				$destinationFlight->setToAirport($this);
			}

			return $this;
		}

		public function removeDestinationFlight(Ticket $destinationFlight): self
		{
			if ($this->destinationFlights->removeElement($destinationFlight)) {
				// set the owning side to null (unless already changed)
				if ($destinationFlight->getToAirport() === $this) {
					$destinationFlight->setToAirport(null);
				}
			}

			return $this;
		}

	}
