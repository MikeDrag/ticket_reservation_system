<?php

	namespace App\Service;

	use App\Entity\Airport;
	use App\Entity\Ticket;
	use App\Factory\TicketFactory;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\RequestStack;

	class TicketService
	{
		const AITA_LENGTH = 3;
		const MINIMUM_SEAT_NUMBER = 1;
		const MAXIMUM_SEAT_NUMBER = 32;

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
		/**
		 * @var ResponseMessageService
		 */
		private $codeMessage;

		public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, TicketFactory $ticketFactory, ResponseMessageService $responseMessageService)
		{
			$this->requestStack = $requestStack;
			$this->entityManager = $entityManager;
			$this->ticketFactory = $ticketFactory;
			$this->codeMessage = $responseMessageService;
		}

/*		private function ticketEntity()
		{
			return $this->entityManager->getRepository(Ticket::class);
		}
*/

		public function getTickets()
		{
			return $this->entityManager->getRepository(Ticket::class)->getAllTickets();
		}

		public function getTicketById($id)
		{
			return $this->entityManager->getRepository(Ticket::class)->getTicket($id);
		}

		public function create()
		{
			$request = $this->requestStack->getCurrentRequest();
			$passengerSeat = rand(self::MINIMUM_SEAT_NUMBER, self::MAXIMUM_SEAT_NUMBER);
			if ($errorFound = $this->validatePostData($request)) {
				return self::returnErrorResponse($errorFound);
			}
			$departureTime = date('Y-m-d H:i:s', intval($request->request->get("departure_time")));
			if (!$this->validateDateTime($departureTime, 'Y-m-d H:i:s'))
				return self::returnErrorResponse($this->codeMessage->getErrorMessage(25));
			$toAirport = $this->entityManager->getRepository(Airport::class)->findOneBy(['iata' => $request->request->get("to_airport")]);
			$fromAirport = $this->entityManager->getRepository(Airport::class)->findOneBy(['iata' => $request->request->get("from_airport")]);
			$passengerPassportID = $request->request->get("passenger_passport_id");
			$departureTime = date('Y-m-d H:i:s', intval($request->request->get("departure_time")));
			if (isset($fromAirport) && isset($toAirport) && isset($passengerPassportID))
			{
				$getTicketsWithSameFlight = $this->entityManager->getRepository(Ticket::class)->findBy(['flightID' => md5($fromAirport->getIata() . $toAirport->getIata() . strtotime($departureTime))]);
				if ($getTicketsWithSameFlight) {
					$flightSeats = [];
					foreach ($getTicketsWithSameFlight as $ticket) {
						$flightSeats[] = $ticket->getPassengerSeat();
					}
					$i = 0;
					while (in_array($passengerSeat, $flightSeats)) {
						$i++;
						$passengerSeat = rand(self::MINIMUM_SEAT_NUMBER, self::MAXIMUM_SEAT_NUMBER);
						if ($i > self::MAXIMUM_SEAT_NUMBER) {
							return self::returnErrorResponse($this->codeMessage->getErrorMessage(29));
						}
					}
				}
				$flightId = md5($fromAirport->getIata() . $toAirport->getIata() . strtotime($departureTime));

				$this->ticketFactory->add($fromAirport, $toAirport, $passengerSeat, $departureTime, $passengerPassportID, $flightId);

				return self::returnSuccessResponse($this->codeMessage->getSuccessMessage(50));
			}
			else
				return self::returnErrorResponse($this->codeMessage->getErrorMessage(28));
		}

		public function cancel()
		{
			$request = $this->requestStack->getCurrentRequest();
			if (!$request->request->get("ticket_id"))
				return self::returnErrorResponse($this->codeMessage->getErrorMessage(27));
			else {
				$ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(['id' => $request->get('ticket_id')]);
				if ($ticket) {
					if ($ticket->isFlightStatus() == 0)
						return $this->codeMessage->getSuccessMessage(52);
					else {
						$this->ticketFactory->setCancelled($ticket);
						return $this->codeMessage->getSuccessMessage(53);
					}
				} else
					return self::returnErrorResponse($this->codeMessage->getErrorMessage(18));
			}
		}

		public function changeSeat(): JsonResponse
		{
			$request = $this->requestStack->getCurrentRequest();
			if (!$request->request->get("ticket_id") || !$request->request->get("new_seat_no"))
				return self::returnErrorResponse($this->codeMessage->getErrorMessage(14));
			else
			{
				$ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(['id' => $request->get('ticket_id')]);
				if ($ticket) {
					if ($this->entityManager->getRepository(Ticket::class)->findOneBy(['passengerSeat' => $request->request->get("new_seat_no")]))
						return self::returnErrorResponse($this->codeMessage->getErrorMessage(15));
					if ($request->request->get("new_seat_no") < self::MINIMUM_SEAT_NUMBER || $request->request->get("new_seat_no") > self::MAXIMUM_SEAT_NUMBER)
						return self::returnErrorResponse($this->codeMessage->getErrorMessage(16));
					if (!is_numeric($request->request->get("new_seat_no")))
						return self::returnErrorResponse($this->codeMessage->getErrorMessage(17));
					$this->ticketFactory->setNewPassengerSeat($ticket, $request->request->get("new_seat_no"));
					return self::returnSuccessResponse($this->codeMessage->getSuccessMessage(51));
				}
				else
					return self::returnErrorResponse($this->codeMessage->getErrorMessage(18));
			}
		}

		public function validateDateTime($dateStr, $format)
		{
			date_default_timezone_set('UTC');
			$date = \DateTime::createFromFormat($format, $dateStr);
			return $date && ($date->format($format) === $dateStr);
		}

		private function validatePostData($request)
		{
			if (!$request->request->get("from_airport"))
				return $this->codeMessage->getErrorMessage(19);
			if (!$request->request->get("to_airport"))
				return $this->codeMessage->getErrorMessage(20);
			if (!$request->request->get("passenger_passport_id"))
				return $this->codeMessage->getErrorMessage(22);
			if (!is_numeric($request->request->get("passenger_passport_id")))
				return $this->codeMessage->getErrorMessage(23);
			if (strlen($request->request->get("from_airport")) != self::AITA_LENGTH || strlen($request->request->get("to_airport")) != self::AITA_LENGTH)
				return $this->codeMessage->getErrorMessage(24);
			if (!$request->request->get("departure_time"))
				return $this->codeMessage->getErrorMessage(25);
		}

		public function returnSuccessResponse($message): JsonResponse
		{
			return new JsonResponse(['data' => $message]);
		}

		public function returnErrorResponse($message): JsonResponse
		{
			return new JsonResponse(['error' => $message]);
		}
	}
