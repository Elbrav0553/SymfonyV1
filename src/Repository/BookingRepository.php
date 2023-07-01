<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Service\ServiceException;
use App\Service\ServiceExceptionData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private SerializerInterface $serializer)
    {
        parent::__construct($registry, Booking::class);
    }

    public function save(Booking $entity, bool $flush = false): void
    {

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Booking[] Returns an array of Booking objects
     */
    public function findAllWithoutDeletedAt(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.deletedAt is null ')
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    /**
     * @return Booking Returns Booking object
     */
    public function findOneWithoutDeletedAt($id): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.id = :id ')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOrFail(int $id): JsonResponse
    {
        try {
            $statusCode = 200;
            $bookingData = $this->findOneWithoutDeletedAt($id);
            $message = "User find";
        } catch (\Throwable $th) {
            //throw $th;
            $statusCode = 500;
            return new JsonResponse(null, $statusCode, []);
        }

        if (!$bookingData) {

            $exceptionData = new ServiceExceptionData(404, 'Booking Not Found');

            //throw new ServiceException($exceptionData);
            $statusCode = 404;
            $message = 'Booking Not Found';
        }
        $serializedData = $this->serializer->serialize($bookingData, 'json');

        
        return new JsonResponse($serializedData, $statusCode, [], true);


        //return $booking;
    }

    //    /**
    //     * @return Booking[] Returns an array of Booking objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Booking
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
