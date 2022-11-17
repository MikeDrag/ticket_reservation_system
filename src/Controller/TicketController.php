<?php

	namespace App\Controller;

	use App\Service\ResponseMessageService;
	use App\Service\TicketService;
	use Exception;
	use Psr\Log\LoggerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Manages flight tickets
	 */
	class TicketController extends AbstractController
	{
		/**
		 * @var LoggerInterface
		 */
		private $logger;

		/**
		 * @var ResponseMessageService
		 */
		private $codeMessage;

		public function __construct(LoggerInterface $logger, ResponseMessageService $responseMessageService)
		{
			$this->logger = $logger;
			$this->codeMessage = $responseMessageService;
		}

		/**
		 * List ticket(s)
		 *
		 * @Route("api/ticket/list", name="list_tickets", methods={"GET"})
		 *
		 */
		public function list(Request $request, TicketService $ticketService): JsonResponse
		{
			if ($request->query->get('ticket_id'))
			{
				$ticket = $ticketService->getTicketById($request->get('ticket_id'));
				if (!$ticket)
					return $ticketService->returnErrorResponse($this->codeMessage->getErrorMessage(10));
				$ticket[0]['flight_status'] = (($ticket[0]['flight_status'] ? 'Active' : 'Cancelled'));

				return $this->json(['data' => $ticket]);
			}
			else
			{
				$tickets = $ticketService->getTickets();
				return $this->json(['data' => $tickets]);
			}
		}

		/**
		 * Create a ticket
		 *
		 * @Route("api/ticket/create", name="create_ticket", methods={"POST"})
		 *
		 */
		public function createTicket(TicketService $ticketService)
		{
			try
			{
				return $ticketService->create();
			}
			catch(Exception $exception)
			{
				$this->logger->error($exception->getMessage());
				return $this->json('Oops something went wrong..', 500);
			}
		}

		/**
		 * Cancel an active ticket
		 *
		 * @Route("api/ticket/cancel", name="cancel_ticket", methods={"POST"})
		 *
		 * @param TicketService $ticketService
		 *
		 * @return JsonResponse
		 */
		public function cancelTicket(TicketService $ticketService): JsonResponse
		{
			try
			{
				return $ticketService->returnSuccessResponse($ticketService->cancel());
			}
			catch (Exception $e)
			{
				$this->logger->error(sprintf($this->codeMessage->getErrorMessage(13) . '. Reason %s', $e->getMessage()), ['ticket_creation_failed']);
				return $ticketService->returnErrorResponse($e->getMessage());
			}
		}

		/**
		 * Change seat in a ticket
		 *
		 * @Route("api/ticket/change-seat", name="change_seat_ticket", methods={"POST"})
		 *
		 * @param TicketService $ticketService
		 *
		 * @return JsonResponse
		 */
		public function changeSeatTicket(TicketService $ticketService): JsonResponse
		{
			try
			{
				return $ticketService->changeSeat();
			}
			catch(Exception $exception)
			{
				$this->logger->error($exception->getMessage());
				return $this->json('Oops something went wrong..', 500);
			}
		}
	}
