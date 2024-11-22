<?php

namespace App\Controller;

use App\Service\GraphQLClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtudiantController extends AbstractController
{
    private GraphQLClient $graphqlClient;

    public function __construct(GraphQLClient $graphqlClient)
    {
        $this->graphqlClient = $graphqlClient;
    }

    #[Route('/etudiants', name: 'etudiants')]
    public function index(): Response
    {
        $query = <<<GRAPHQL
        query {
            allEtudiants {
                id
                nom
                prenom
                classe
                dateNaissance
            }
        }
        GRAPHQL;

        // l'Appele l'API GraphQL
        $response = $this->graphqlClient->query($query);

        // Passer les donnees a la vue Twig
        return $this->render('etudiants/index.html.twig', [
            'etudiants' => $response['data']['allEtudiants'],
        ]);
    }
}
