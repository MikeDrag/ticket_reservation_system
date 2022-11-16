<?php

	namespace App\Service;

	use App\Entity\Airport;
	use App\Entity\Ticket;
	use App\Factory\TicketFactory;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	class TicketService
	{
		const AITA_NUMBER_LENGTH = 3;

		/**
		 * @var RequestStack
		 */
		private $requestStack;
		/**
		 * @var EntityManagerInterface
		 */
		private $entityManager;
		/**
		 * @var TicketFactory
		 */
		private $ticketFactory;

		public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, TicketFactory $ticketFactory)
		{
			$this->requestStack = $requestStack;
			$this->entityManager = $entityManager;
			$this->ticketFactory = $ticketFactory;
		}

		/**
		 * @throws \Exception
		 */
		public function create()
		{
			$request = $this->requestStack->getCurrentRequest();
			$departureTime = date("Y-m-d H:i:s", strtotime('+' . mt_rand(0, 30) . ' days'));
			$passengerSeat = rand(1, 32);
			if (!$request->request->get("from_airport"))
				throw new NotFoundHttpException('Source airport missing. Please provide source airport.');
			if (!$request->request->get("to_airport"))
				throw new NotFoundHttpException('Destination airport missing. Please provide destination airport.');
			if (!$request->request->get("passenger_passport_id"))
				throw new NotFoundHttpException('Passenger\'s passport ID missing. Please provide passengers passport ID.');

			if (strlen($request->request->get("from_airport")) != self::AITA_NUMBER_LENGTH || strlen($request->request->get("to_airport")) != self::AITA_NUMBER_LENGTH)
				throw new NotFoundHttpException('One of your AITA codes is wrong. Please make sure you provide the correct AITA code for your source and destination flight.');

			// TODO: Add validation for the passenger's passport id

			$toAirport = $this->entityManager->getRepository(Airport::class)->findOneBy(['iata' => $request->request->get("to_airport")]);
			$fromAirport = $this->entityManager->getRepository(Airport::class)->findOneBy(['iata' => $request->request->get("from_airport")]);
			$passengerPassportID = $request->request->get("passenger_passport_id");

			if (isset($fromAirport) && isset($toAirport))
				return $this->ticketFactory->add($fromAirport, $toAirport, $passengerSeat, $departureTime, $passengerPassportID);
			else
				throw new \Exception('Source or destination missing. Please try again with valid data.');
		}

		public function cancel()
		{
			$request = $this->requestStack->getCurrentRequest();
			if (!$request->request->get("ticket_id"))
				throw new NotFoundHttpException('Ticket id is empty. Please enter a valid ticket id');
			else {
				$ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(['id' => $request->get('ticket_id')]);
				if ($ticket) {
					if ($ticket->isFlightStatus() == 0)
						throw new \Exception('Ticket is already cancelled');
					else
						$this->ticketFactory->setCancelled($ticket);
				} else
					throw new NotFoundHttpException('Ticket with given ID does not exist or is not valid.');
			}
		}

		public function changeSeat()
		{
			$request = $this->requestStack->getCurrentRequest();
			if (!$request->request->get("ticket_id") || !$request->request->get("new_seat_no"))
				throw new NotFoundHttpException('Ticket id or new seat number is empty.');
			else {
				$ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(['id' => $request->get('ticket_id')]);
				if ($ticket) {
					if ($this->entityManager->getRepository(Ticket::class)->findOneBy(['passengerSeat' => $request->request->get("new_seat_no")]))
						throw new \Exception('This seat is already occupied. Please try another.');
					if ($request->request->get("new_seat_no") < 1 || $request->request->get("new_seat_no") > 32)
						throw new \Exception('You can only pick a seat between 1 and 32. Please try again.');

					$this->ticketFactory->setNewPassengerSeat($ticket, $request->request->get("new_seat_no"));
				} else
					throw new NotFoundHttpException('Ticket with given ID does not exist or is not valid.');
			}
		}
	}