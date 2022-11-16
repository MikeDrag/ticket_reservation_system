<?php

	namespace App\Controller;

	use App\Service\TicketService;
	use Exception;
	use Psr\Log\LoggerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;

	class TicketController extends AbstractController
	{
		/**
		 * @var LoggerInterface
		 */
		private $logger;

		public function __construct(LoggerInterface $logger)
		{
			$this->logger = $logger;
		}

		/**
		 * Create a flight ticket
		 *
		 * @Route("api/ticket/create", name="create_ticket", methods={"POST"})
		 *
		 * @throws Exception
		 */
		public function createTicket(Request $request, TicketService $ticketService): JsonResponse
		{
			try
			{
				if ($ticketService->create())
				{
					return $this->returnSuccessResponse('Ticket created successfully');
				}
				else
				{
					$this->logger->error('Failed to save ticket into database', ['ticket_creation_failed']);
					throw new Exception('Something went wrong, please try later again...');
				}
			}
			catch (Exception $exception)
			{
				return $this->returnErrorResponse($exception->getMessage());
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
				$ticketService->cancel();
				return $this->returnSuccessResponse('Ticket cancelled successfully');
			}
			catch (Exception $e)
			{
				return $this->returnErrorResponse($e->getMessage());
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
				$ticketService->changeSeat();
				return $this->returnSuccessResponse('Ticket seat changed successfully');
			}
			catch (Exception $e)
			{
				return $this->returnErrorResponse($e->getMessage());
			}
		}

		public function returnSuccessResponse(string $message): JsonResponse
		{
			return $this->json(['success' => $message]);
		}

		public function returnErrorResponse(string $message): JsonResponse
		{
			return $this->json(['error' => $message]);
		}
	}
