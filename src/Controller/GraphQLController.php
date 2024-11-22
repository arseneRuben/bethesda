<?php

namespace App\Controller;

use App\Service\GraphQLClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GraphQLController extends AbstractController
{
    private $graphqlClient;

    public function __construct(GraphQLClient $graphqlClient)
    {
        $this->graphqlClient = $graphqlClient;
    }

    /**
     * @Route("/etudiants", name= "etudiants")
     * **/
    public function fetchEtudiants(): JsonResponse
    {
        $query = <<<GRAPHQL
        query {
            allEtudiants {
                id
                nom
                prenom
            }
        }
        GRAPHQL;

        $response = $this->graphqlClient->query($query);

        return $this->json($response);
    }
}
