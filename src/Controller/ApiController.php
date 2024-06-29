<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class ApiController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/users', methods: ['GET'])]
    public function getUsers(): JsonResponse
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $this->json($users);
    }

    #[Route('/users/{id}', methods: ['GET'])]
    public function fetchUser($id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return $this->json($user);
    }

    #[Route('/users', methods: ['POST'])]
    public function addUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user);
    }

    #[Route('/users/{id}', methods: ['PUT'])]
    public function editUser($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $user->setName($data['name']);
        $user->setEmail($data['email']);

        $this->entityManager->flush();

        return $this->json($user);
    }

    #[Route('/users/{id}', methods: ['DELETE'])]
    public function deleteUser($id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User deleted']);
    }

    #[Route('/groups', methods: ['GET'])]
    public function getGroups(): JsonResponse
    {
        $groups = $this->entityManager->getRepository(Group::class)->findAll();
        return $this->json($groups);
    }

    #[Route('/groups/{id}', methods: ['GET'])]
    public function getGroup($id): JsonResponse
    {
        $group = $this->entityManager->getRepository(Group::class)->find($id);

        if (!$group) {
            return new JsonResponse(['error' => 'Group not found'], 404);
        }

        return $this->json($group);
    }

    #[Route('/groups', methods: ['POST'])]
    public function addGroup(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $group = new Group();
        $group->setName($data['name']);

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $this->json($group);
    }

    #[Route('/groups/{id}', methods: ['PUT'])]
    public function editGroup($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $group = $this->entityManager->getRepository(Group::class)->find($id);

        if (!$group) {
            return new JsonResponse(['error' => 'Group not found'], 404);
        }

        $group->setName($data['name']);

        $this->entityManager->flush();

        return $this->json($group);
    }

    #[Route('/groups/{id}', methods: ['DELETE'])]
    public function deleteGroup($id): JsonResponse
    {
        $group = $this->entityManager->getRepository(Group::class)->find($id);

        if (!$group) {
            return new JsonResponse(['error' => 'Group not found'], 404);
        }

        $this->entityManager->remove($group);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Group deleted']);
    }
}
