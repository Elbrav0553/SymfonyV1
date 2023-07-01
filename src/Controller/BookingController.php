<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");



#[Route('/booking')]
class BookingController extends AbstractController
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private EntityManagerInterface $entityManager
    ){}
    #[Route('/', name: 'app_booking_index', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {

        $message = "KO";
        $data = "";
        try {
            //$booking =  $entityManager->getRepository(Booking::class)->findall();
            $booking = $this->bookingRepository->findAllWithoutDeletedAt();
            $data = $booking;//$serializer->serialize($booking, 'array');
            $message = "OK";
        } catch (Exception $exception) {
            //throw $th;
            $message = "KO" . $exception->getFile() . "@" . $exception->getLine() . " " . $exception->getMessage();
        }
        return $this->json([
            'message' => 'Welcome to your new booking controller! ' . $message,
            'data' => $data,
            'path' => 'src/Controller/BookingController.php',
        ]);
    }
    #[Route('/new', name: 'app_booking_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $message = "KO";
        $data = "";
        try {
            
            $booking = new Booking();
            $parameter = json_decode($request->getContent(), true);


            $booking->setStatus($parameter['status']);
            $booking->setDeletedAt(null);
            $booking->setCreatedAt(new \DateTimeImmutable());
            $booking->setDescription($parameter['description']);

            $this->bookingRepository->save($booking,true);
            //$booking->save();
            /*$entityManager->persist($booking);
            $entityManager->flush();*/
            $message = 'OK';
            $data = 'Saved new booking with id ' . $booking->getId();
            //} catch (\Throwable $th) {
        } catch (Exception $exception) {
            //throw $th;
            $message = "KO" . $exception->getFile() . "@" . $exception->getLine() . " " . $exception->getMessage();
        }


        //dd($message);
        //dd($flushResponse);

        //return new Response('Saved new booking with id ' . $booking->getId());
        return $this->json([
            'message' => 'Welcome to your new booking controller! ' . $message,
            'data' => $data,
            'path' => 'src/Controller/BookingController.php',
        ]);
    }

    #[Route('/{id}', name: 'app_booking_show', methods: ['GET'])]
    public function show(SerializerInterface $serializer, int $id): JsonResponse
    {
        return $booking =  $this->bookingRepository->findOrFail($id);
          
    }
    #[Route('/{id}/edit', name: 'app_booking_edit', methods: ['POST'])]
    public function edit(SerializerInterface $serializer, Request $request, int $id): JsonResponse
    {
        $message = "KO";
        $data = "";
        
        try {
            $booking =  $this->entityManager->getRepository(Booking::class)->find($id);
            $message = 'OK';
            $data = $serializer->serialize($booking, 'json');
        } catch (Exception $exception) {
            $message = "KO" . $exception->getFile() . "@" . $exception->getLine() . " " . $exception->getMessage();
        }
        if (!$booking) {
            //throw $this->createNotFoundException(
            //'No booking found for id ' . $id
            //);
            $message = "KO" . 'No booking found for id ' . $id;
            $data = '';

            return $this->json([
                'message' => '/booking/{id} ' . $message,
                'data' => $data,
                'path' => 'src/Controller/BookingController.php',
            ]);
        }

        try {
            $parameter = json_decode($request->getContent(), true);


            $booking->setStatus($parameter['status']);
            $booking->setDeletedAt(null);
            $booking->setCreatedAt(new \DateTimeImmutable());
            $booking->setDescription($parameter['description']);

            $this->entityManager->persist($booking);
            $this->entityManager->flush();
            $message = 'OK';
            $data = 'Saved updated booking with id ' . $booking->getId();
            //} catch (\Throwable $th) {
        } catch (Exception $exception) {
            //throw $th;
            $message = "KO params " . $exception->getFile() . "@" . $exception->getLine() . " " . $exception->getMessage();
        }


        return $this->json([
            'message' => '/booking/{id}/edit ' . $message,
            'data' => $data,
            'path' => 'src/Controller/BookingController.php',
        ]);
    }
    #[Route('/{id}/delete', name: 'app_booking_delete', methods: ['POST','DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $message = "KO";
        $data = "";
        try {
            $booking =  $this->entityManager->getRepository(Booking::class)->find($id);
            $message = 'OK';
        } catch (Exception $exception) {
            $message = "KO" . $exception->getFile() . "@" . $exception->getLine() . " " . $exception->getMessage();
        }
        if (!$booking) {
            
            $message = "KO" . 'No booking found for id ' . $id;
            $data = '';

            return $this->json([
                'message' => '/booking/{id}/delete ' . $message,
                'data' => $data,
                'path' => 'src/Controller/BookingController.php',
            ]);
        }

        try {
            
            $bookingOldId = $booking->getId();
            
            $booking->setDeletedAt(new \DateTimeImmutable());
            $this->entityManager->persist($booking);
            $this->entityManager->flush();
            //$entityManager->remove($booking);
            //$entityManager->flush();//si no no hace nada
            $message = 'OK';
            $data = 'DELETED booking with id ' . $bookingOldId;
            //} catch (\Throwable $th) {
        } catch (Exception $exception) {
            //throw $th;
            $message = "KO" . $exception->getFile() . "@" . $exception->getLine() . " " . $exception->getMessage();
        }


        return $this->json([
            'message' => '/booking/{id}/delete ' . $message,
            'data' => $data,
            'path' => 'src/Controller/BookingController.php',
        ]);
    }
}
