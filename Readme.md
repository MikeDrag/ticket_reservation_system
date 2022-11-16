# Ticket Reservation System

# Installation
Clone the repository
```bash
git clone git@github.com:MikeDrag/ticket_reservation_system.git
```
### Link to repo https://github.com/MikeDrag/ticket_reservation_system

##### Make sure you have php 7.4 or up to version 8.1 installed 

## Install composer packages
Run ```composer install```

## Create local environment file
```Create .env.local and add your local configuration```

Example:  ```DATABASE_URL="mysql://root:root@127.0.0.1:3306/flight_reservation_system?serverVersion=8&charset=utf8mb4"``` Replace root with your database user/password and flight_reservation_ticket with your database name

## Create the tables in your database by running
```php bin/console doctrine:migrations:migrate```
## Run your local server
```Symfony server:start```

# Naming convention
1. CamelCase for PHP with tab indentation


### Tip: Use postman to make post requests

### Use the following command to view the routes
```php bin/console debug:router```


# Using the API

## Create a ticket
**Route**: `api/ticket/create`
___
Parameters: `from_airport`, `to_airport`, `passenger_passport_id`
___
Request method: `POST`
___
Usage: Send a post request with the parameters above to `api/ticket/create` to create a new ticket.

## Cancel a ticket
**Route**: `api/ticket/cancel`
___
Parameters: `ticket_id`
___
Request method: `POST`
___
Usage: Send a post request with the parameters above to `api/ticket/cancel` to cancel an existing ticket.

## Change seat
**Route**: `api/ticket/change-seat`
___
Parameters: `ticket_id`, `new_seat_no`
___
Request method: `POST`
___
Usage: Send a post request with the parameters above to `api/ticket/change-seat` to change seat in an existing ticket.

