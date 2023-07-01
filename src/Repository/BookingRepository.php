<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Service\ServiceException;
use App\Service\ServiceExceptionData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function __construct(ManagerRegistry $registry, private SerializerInterface $serializer, private ValidatorInterface $validator)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @return Booking Returns Booking object
     */
    public function findOneWithoutDeletedAtQuery($id): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.id = :id ')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    /**
     * @return Booking[] Returns an array of Booking objects
     */
    public function findAllWithoutDeletedAtQuery(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.deletedAt is null ')
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult();
        //->getAllOrNullResult();
    }
    public function save(Booking $entity, bool $flush = false): void
    {

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function new(Booking $booking, array $params, bool $flush = false): JsonResponse
    {

        $statusCode = 201;
        $status = isset($params['status']) ? intval($params['status']) : 0;
        $booking->setStatus($status);
        $booking->setDeletedAt(null);
        $booking->setCreatedAt(new \DateTimeImmutable());
        $booking->setDescription($params['description']);
        $errors = $this->validator->validate($booking);
        if (count($errors) > 0) {
            // Hay errores de validación
            // ...
            $statusCode = 400;
            return new JsonResponse(null, $statusCode, []);
        }

        $this->getEntityManager()->persist($booking);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return new JsonResponse(null, $statusCode, []);
    }
    public function edit(array $params, int $id, bool $flush = false): JsonResponse
    {

        $statusCode = 201;
        try {
            $bookingData =  $this->findOneWithoutDeletedAtQuery($id);
        } catch (\Throwable $th) {
            $statusCode = 500;
            return new JsonResponse(null, $statusCode, []);
        }
        if (!$bookingData) {

            $exceptionData = new ServiceExceptionData(404, 'Booking Not Found');

            //throw new ServiceException($exceptionData);
            $statusCode = 404;
            $message = 'Booking Not Found';
            return new JsonResponse(null, $statusCode, []);
        }
        $status = isset($params['status']) ? intval($params['status']) : 0;
        $bookingData->setStatus($status);
        $bookingData->setDescription($params['description']);
        $errors = $this->validator->validate($bookingData);
        if (count($errors) > 0) {
            // Hay errores de validación
            // ...
            $statusCode = 400;
            return new JsonResponse(null, $statusCode, []);
        }

        $this->getEntityManager()->persist($bookingData);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return new JsonResponse(null, $statusCode, []);
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @return Booking Returns Booking object
     */
    public function findAllWithoutDeletedAt(): JsonResponse
    {
        try {
            $statusCode = 200;
            $bookingData = $this->findAllWithoutDeletedAtQuery();
            //dd($bookingData);
            $message = "Users find";
        } catch (\Throwable $th) {
            //throw $th;
            $statusCode = 500;
            return new JsonResponse(null, $statusCode, []);
        }

        if (!$bookingData) {

            $exceptionData = new ServiceExceptionData(404, 'Bookings Not Found');

            //throw new ServiceException($exceptionData);
            $statusCode = 404;
            $message = 'Bookings Not Found';
        }
        $serializedData = $this->serializer->serialize($bookingData, 'json');


        return new JsonResponse($serializedData, $statusCode, [], true);
    }


    public function findOneWithoutDeletedAt(int $id): JsonResponse
    {
        try {
            $statusCode = 200;
            $bookingData = $this->findOneWithoutDeletedAtQuery($id);
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

    public function delete(int $id, bool $flush = false): JsonResponse
    {

        $statusCode = 200;
        try {
            $bookingData =  $this->findOneWithoutDeletedAtQuery($id);
        } catch (\Throwable $th) {
            $statusCode = 500;
            return new JsonResponse(null, $statusCode, []);
        }
        if (!$bookingData) {

            $exceptionData = new ServiceExceptionData(404, 'Booking Not Found');

            //throw new ServiceException($exceptionData);
            $statusCode = 404;
            $message = 'Booking Not Found';
            return new JsonResponse(null, $statusCode, []);
        }

        $bookingData->setDeletedAt(new \DateTimeImmutable());
        $this->getEntityManager()->persist($bookingData);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return new JsonResponse(null, $statusCode, []);
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
