<?php

namespace App\Controller;

use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/card', name: 'api_card_')]
#[OA\Tag(name: 'Card', description: 'Routes for all about cards')]
class ApiCardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/all', name: 'List all cards', methods: ['GET'])]
    #[OA\Put(description: 'Return all cards in the database')]
    #[OA\Response(response: 200, description: 'List all cards')]
    public function cardAll(): Response
    {
        $this->logger->info('List all cards');
        $cards = $this->entityManager->getRepository(Card::class)->findAll();
        return $this->json($cards);
    }

    #[Route('/all/{page}/{limit}', name: 'List all cards with pagination', methods: ['GET'])]
    #[OA\Parameter(name: 'page', description: 'Page number', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'limit', description: 'Number of records per page', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Put(description: 'Return all cards in the database')]
    #[OA\Response(response: 200, description: 'List all cards')]
    public function cardAllPaginated(int $page = 1, int $limit = 100): Response
    {
        $this->logger->info('List all cards with pagination');
        $cards = $this->entityManager->getRepository(Card::class)->findAllPaginated($page, $limit);
        $total = $this->entityManager->getRepository(Card::class)->getTotalCount();
        return $this->json([
            'cards' => $cards,
            'total' => $total,
        ]);
    }

    #[Route('/search/{name}', name: 'Search card', methods: ['GET'])]
    #[OA\Parameter(name: 'name', description: 'Name of the card', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Put(description: 'Search a card by name')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardSearch(string $name): Response
    {
        $this->logger->info('Search card ' . $name);
        $cards = $this->entityManager->getRepository(Card::class)->searchByName($name);
        if (!$cards) {
            return $this->json(['error' => 'Card not found'], 404);
        }
        return $this->json($cards);
    }

    #[Route('/set-codes/', name: 'List all setCode', methods: ['GET'])]
    #[OA\Put(description: 'Return all setCode in the database')]
    #[OA\Response(response: 200, description: 'List all setCode')]
    #[OA\Response(response: 404, description: 'setCode not found')]
    public function cardSetCode(): Response
    {
        $this->logger->info('List all setCode');
        $setCodes = $this->entityManager->getRepository(Card::class)->getAllSetCode();
        return $this->json($setCodes);
    }

    #[Route('/set-code/{setCode}/{page}/{limit}', name: 'List cards by setCode', methods: ['GET'])]
    #[OA\Parameter(name: 'setCode', description: 'setCode of the cards', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'page', description: 'Page number', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'limit', description: 'Number of records per page', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Put(description: 'Return all cards with the given setCode')]
    #[OA\Response(response: 200, description: 'List all cards with the given setCode')]
    public function cardBySetCode(string $setCode, int $page = 1, int $limit = 100): Response
    {
        $this->logger->info('List cards by setCode ' . $setCode);
        $cards = $this->entityManager->getRepository(Card::class)->findBySetCode($setCode, $page, $limit);
        $total = $this->entityManager->getRepository(Card::class)->getCountBySetCode($setCode);
        return $this->json([
            'cards' => $cards,
            'total' => $total,
        ]);
    }

    #[Route('/{uuid}', name: 'Show card', methods: ['GET'])]
    #[OA\Parameter(name: 'uuid', description: 'UUID of the card', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Put(description: 'Get a card by UUID')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardShow(string $uuid): Response
    {
        $this->logger->info('Show card ' . $uuid);
        $card = $this->entityManager->getRepository(Card::class)->findOneBy(['uuid' => $uuid]);
        if (!$card) {
            return $this->json(['error' => 'Card not found'], 404);
        }
        return $this->json($card);
    }
}