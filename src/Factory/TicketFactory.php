<?php

	namespace App\Factory;

	use App\Entity\Airport;
	use App\Entity\Ticket;
	use Doctrine\ORM\EntityManagerInterface;

	class TicketFactory
	{
		/**
		 * @var EntityManagerInterface
		 */
		private $entityManager;

		public function __construct(EntityManagerInterface $entityManager)
		{
			$this->entityManager = $entityManager;
		}

		public function add(Airport $fromAirport, Airport $toAirport, int $passengerSeat, $departureTime, string $passengerPassportID, $flightId): bool
		{
			$ticket = new Ticket();
			$ticket->setFromAirport($fromAirport);
			$ticket->setToAirport($toAirport);
			$ticket->setPassengerSeat($passengerSeat);
			$ticket->setDepartureTime(new \DateTime($departureTime));
			$ticket->setPassengerPassportID(trim($passengerPassportID));
			$ticket->setFlightID($flightId);
			$this->entityManager->persist($ticket);
			$this->entityManager->flush();
			if ($this->entityManager->contains($ticket))
				return TRUE;
			return FALSE;
		}

		public function setCancelled(Ticket $ticket)
		{
			$ticket->setFlightStatus(Ticket::FLIGHT_STATUS_CANCELLED);
			$this->entityManager->persist($ticket);
			$this->entityManager->flush();
		}

		public function setNewPassengerSeat(Ticket $ticket, int $seatNumber)
		{
			$ticket->setPassengerSeat($seatNumber);
			$this->entityManager->persist($ticket);
			$this->entityManager->flush();
		}
	}