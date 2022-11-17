<?php

	namespace App\Repository;

	use App\Entity\Ticket;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\ORM\AbstractQuery;
	use Doctrine\Persistence\ManagerRegistry;

	/**
	 * @extends ServiceEntityRepository<Ticket>
	 *
	 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
	 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
	 * @method Ticket[]    findAll()
	 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
	 */
	class TicketRepository extends ServiceEntityRepository
	{
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, Ticket::class);
		}

		public function add(Ticket $entity, bool $flush = false): void
		{
			$this->getEntityManager()->persist($entity);

			if ($flush) {
				$this->getEntityManager()->flush();
			}
		}

		public function remove(Ticket $entity, bool $flush = false): void
		{
			$this->getEntityManager()->remove($entity);

			if ($flush) {
				$this->getEntityManager()->flush();
			}
		}

		public function getTicket($id)
		{
			return $this->createQueryBuilder('t')
				->where('t.id = :ticket_id')
				->setParameter('ticket_id', $id)
				->getQuery()
				->getArrayResult();
		}

		public function getAllTickets(): array
		{
			return $this->createQueryBuilder('t')
				->select('t.id', 't.passengerSeat', 't.passengerPassportID', 't.departureTime', 'CASE t.flight_status WHEN 1 THEN \'Active\' ELSE \'Cancelled\' END AS status','t.flightID')
				->getQuery()
				->getResult(AbstractQuery::HYDRATE_ARRAY);
		}

//    public function findOneBySomeField($value): ?Ticket
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
	}
