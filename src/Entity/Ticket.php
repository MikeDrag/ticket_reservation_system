<?php

	namespace App\Entity;

	use App\Repository\TicketRepository;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity(repositoryClass=TicketRepository::class)
	 */
	class Ticket
	{
		# Active ticket displays with 1
		const FLIGHT_STATUS_ACTIVE = 1;
		# Cancelled ticket displays with 0
		const FLIGHT_STATUS_CANCELLED = 0;

		/**
		 * @ORM\Id
		 * @ORM\GeneratedValue
		 * @ORM\Column(type="integer")
		 */
		private $id;

		/**
		 * @ORM\ManyToOne(targetEntity=Airport::class, inversedBy="sourceFlights")
		 */
		private $fromAirport;

		/**
		 * @ORM\ManyToOne(targetEntity=Airport::class, inversedBy="destinationFlights")
		 */
		private $toAirport;

		/**
		 * @ORM\Column(type="integer", nullable=true)
		 */
		private $passengerSeat;

		/**
		 * @ORM\Column(type="string")
		 */
		private $passengerPassportID;

		/**
		 * @ORM\Column(type="datetime", nullable=true)
		 */
		private $departureTime;

		/**
		 * @ORM\Column(type="boolean")
		 */
		private $flight_status;


		public function __construct()
		{
			$this->flight_status = 1;
		}

		public function getId(): ?int
		{
			return $this->id;
		}

		public function getDepartureTime(): ?\DateTimeInterface
		{
			return $this->departureTime;
		}

		public function setDepartureTime(\DateTimeInterface $departureTime): self
		{
			$this->departureTime = $departureTime;

			return $this;
		}

		public function getFromAirport(): ?Airport
		{
			return $this->fromAirport;
		}

		public function setFromAirport(?Airport $fromAirport): self
		{
			$this->fromAirport = $fromAirport;

			return $this;
		}

		public function getToAirport(): ?Airport
		{
			return $this->toAirport;
		}

		public function setToAirport(?Airport $toAirport): self
		{
			$this->toAirport = $toAirport;

			return $this;
		}

		public function getPassengerSeat(): ?int
		{
			return $this->passengerSeat;
		}

		public function setPassengerSeat(?int $passengerSeat): self
		{
			$this->passengerSeat = $passengerSeat;

			return $this;
		}

		public function getPassengerPassportID(): ?string
		{
			return $this->passengerPassportID;
		}

		public function setPassengerPassportID(string $passengerPassportID): self
		{
			$this->passengerPassportID = $passengerPassportID;

			return $this;
		}

		public function isFlightStatus(): ?bool
		{
			return $this->flight_status;
		}

		public function setFlightStatus(bool $flight_status): self
		{
			$this->flight_status = $flight_status;

			return $this;
		}
	}
