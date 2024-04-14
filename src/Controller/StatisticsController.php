<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Service\SchoolYearService;
use App\Repository\ClassRoomRepository;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Sequence controller.
 *
 * @Route("/admin/stats")
 */
class StatisticsController extends AbstractController
{
    private SchoolYearService $schoolYearService;
    private ClassRoomRepository $repo;
    private StudentRepository $stdRepo;
    private $em;

    public function __construct(EntityManagerInterface $em,StudentRepository $stdRepo,ClassRoomRepository $repo,SchoolYearService $schoolYearService)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->schoolYearService = $schoolYearService;
        $this->stdRepo = $stdRepo;

    }

      /**
     * Displays a graph updated.
     *
     * @Route("/update", name="admin_graph_update",  options = { "expose" = true })
     * @Method("GET")
     * @Template()
     */
    public function updateGraphs(Request $request): JsonResponse
    {
          // URL de redirection et paramètres
        $url = $this->generateUrl('admin_statistics', ['id' => intval($request->query->get('id'))]);
        return new JsonResponse(['url' => $url]);
    }

    /**
     * Lists all Sequenceme entities.
     *
     * @Route("/{id}", name="admin_statistics", defaults={"id"=null})
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request,int $id=0 )
    {
        $rooms = $this->repo->findAll();
        $connection = $this->em->getConnection();
        // Extration des donnees de la BD
        if($id == 0){
            $this->viewGender();
            $this->viewAgeGroup();
        } else {
            $this->viewGender($id);
            $this->viewAgeGroup($id);
        }  
        $datas = $connection->executeQuery("SELECT *  FROM V_GENDER_ROOM ")->fetchAll();
         // Traitements de donnees pour les graphes de repartition de sexe par classe
        foreach ($rooms as $room) {
            $roomNames[] = $room->getName();
        }
        $masculin = [];
        $feminin = [];
        foreach ($roomNames as $name) {
            foreach($datas as $data){
                if(strcmp($data["room"], $name)==0  && strcmp($data["gender"], "0")==0){
                    array_push($masculin , $data["workforce"]);
                }
                if(strcmp($data["room"], $name)==0  && strcmp($data["gender"], "1")==0){
                    array_push($feminin , $data["workforce"]);
                }
                continue;
            }
        }
        $roomNames = json_encode($roomNames);
        if($id > 0){
            $roomNames = json_encode($this->repo->findOneById($id)->getName());
        }
        return $this->render('statistics/dashboard.html.twig', [
            "rooms"=>$rooms, "feminin"=>json_encode($feminin),"masculin"=> json_encode($masculin), "roomNames"=>$roomNames
        ]);
    }

    // Cette fonction genere les vue d'effectif par tranche d'age par classe
    public function viewAgeGroup(int $room=0){
        $year = $this->schoolYearService->sessionYearById();
        $connection = $this->em->getConnection();
        if($room>0){
            $statement = $connection->prepare(
                " CREATE OR REPLACE VIEW V_AGE_GROUP_ROOM  AS
                    SELECT
                        FLOOR(DATEDIFF(NOW(), birthday) / 365 / 5) * 5 AS tranche_age,
                        COUNT(*) AS effectif
                    FROM  student    std  
                    JOIN  subscription sub    ON  sub.student_id      =   std.id     
                    JOIN  class_room room    ON  sub.class_room_id     =   room.id
                    WHERE sub.school_year_id =? AND  room.id = ?
                    GROUP BY
                        FLOOR(DATEDIFF(NOW(), birthday) / 365 / 5)
                    ORDER BY
                        tranche_age;
                "
            );
            $statement->bindValue(2, $room);
        } else {
            $statement = $connection->prepare(
                " CREATE OR REPLACE VIEW V_AGE_GROUP_ROOM  AS
                SELECT
                    FLOOR(DATEDIFF(NOW(), birthday) / 365 / 5) * 5 AS tranche_age,
                    COUNT(*) AS effectif
                FROM  student    std  
                JOIN  subscription sub    ON  sub.student_id      =   std.id     
                JOIN  class_room room    ON  sub.class_room_id     =   room.id
                WHERE sub.school_year_id =? 
                GROUP BY
                    FLOOR(DATEDIFF(NOW(), birthday) / 365 / 5)
                ORDER BY
                    tranche_age;
                "
            );
        }
        $statement->bindValue(1, $year->getId());
        $statement->execute();
    
    }

    // Cette fonction genere les vues d'effectif par sexe par classe
    public function viewGender(int $room=0){
        $year = $this->schoolYearService->sessionYearById();
        $connection = $this->em->getConnection();
        if($room>0){
            $statement = $connection->prepare(
                " CREATE OR REPLACE VIEW V_GENDER_ROOM  AS
                SELECT   room.name as room ,  COUNT(std.id) as workforce,  std.gender as gender
                FROM  student    std  
                JOIN  subscription sub    ON  sub.student_id      =   std.id     
                JOIN  class_room room    ON  sub.class_room_id     =   room.id
                WHERE sub.school_year_id =? AND  room.id = ? 
                GROUP BY   gender;    "
            );
            $statement->bindValue(2, $room);
        } else {
            $statement = $connection->prepare(
                " CREATE OR REPLACE VIEW V_GENDER_ROOM  AS
                SELECT   room.name as room , COUNT(std.id) as workforce,  std.gender as gender
                FROM  student    std  
                JOIN  subscription sub    ON  sub.student_id      =   std.id   
                JOIN  class_room room    ON  sub.class_room_id     =   room.id  
                WHERE sub.school_year_id =? 
                GROUP BY  room, gender;    "
            );
        }
        $statement->bindValue(1, $year->getId());
        $statement->execute();
    }

}