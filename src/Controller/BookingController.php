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
    ) {
    }
    #[Route('/', name: 'app_booking_index', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        return $this->bookingRepository->findAllWithoutDeletedAt();
    }
    #[Route('/new', name: 'app_booking_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {

        return $this->bookingRepository->new(new Booking(), json_decode($request->getContent(), true),true);
    }

    #[Route('/{id}', name: 'app_booking_show', methods: ['GET'])]
    public function show(SerializerInterface $serializer, int $id): JsonResponse
    {
        return $this->bookingRepository->findOneWithoutDeletedAt($id);
    }
    #[Route('/{id}/edit', name: 'app_booking_edit', methods: ['POST'])]
    public function edit(SerializerInterface $serializer, Request $request, int $id): JsonResponse
    {

        return $this->bookingRepository->edit(json_decode($request->getContent(), true), $id, true);
        
    }
    #[Route('/{id}/delete', name: 'app_booking_delete', methods: ['POST', 'DELETE'])]
    public function delete(int $id): JsonResponse
    {
        return $this->bookingRepository->delete($id,true);
    }
}
