<?php

	namespace App\Service;

	class ResponseMessageService
	{
		const ERROR_CODE_MESSAGES = [
			10 => 'Ticket does not exist.',
			11 => 'Failed to save ticket into database.',
			12 => 'Something went wrong, please try later again...',
			13 => 'Failed to cancel ticket.',
			14 => 'Ticket id or new seat number is empty.',
			15 => 'This seat is already occupied. Please try another.',
			16 => 'You can only pick a seat between 1 and 32. Please try again.',
			17 => 'The seat number should be a number between 1 and 32.',
			18 => 'Ticket with given ID does not exist or is not valid.',
			19 => 'Source airport missing. Please provide source airport.',
			20 => 'Destination airport missing. Please provide destination airport.',
		#	21 => 'Destination airport missing. Please provide destination airport.',
			22 => 'Passenger\'s passport ID missing. Please provide passengers passport ID.',
			23 => 'The passport id should be an integer.',
			24 => 'One of your AITA codes is wrong. Please make sure you provide the correct AITA code for your source and destination flight.',
			25 => 'Please enter a departure time...',
			26 => 'Please enter a valid date...',
			27 => 'Ticket id is empty. Please enter a valid ticket id.',
			28 => 'Invalid data. Only Swedish airports allowed.',
			29 => 'There are no seats left',
		];

		const SUCCESS_CODE_MESSAGES = [
			50 => 'Ticket created successfully.',
			51 => 'Ticket seat changed successfully.',
			52 => 'Ticket is already cancelled.',
			53 => 'Ticket cancelled successfully.',
		];

		public function getErrorMessage(int $code)
		{
			return ['message' => self::ERROR_CODE_MESSAGES[$code], 'code' => $code];
		}

		public function getSuccessMessage(int $code)
		{
			return ['message' => self::SUCCESS_CODE_MESSAGES[$code]];
		}
	}