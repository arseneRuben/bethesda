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
use Knp\Snappy\Pdf;


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
     * Displays a pdf of students grouping by gender.
     *
     * @Route("printgr/{id}", name="admin_stat_print_gender_room", defaults={"id"=0}  )
     * @Method("GET")
     * @Template()
     */
    public function genderRoomPdf( Pdf $pdf, int $id=0): Response
    {
        $year = $this->schoolYearService->sessionYearById();
        $rooms = $this->repo->findAll();
        if($id > 0){
            $rooms = $this->repo->findBy(array("id" => $id));
            $this->viewGender($id);
        } else {
            $this->viewGender();
        }
        $connection = $this->em->getConnection();
        $gender_datas = $connection->executeQuery("SELECT *  FROM V_GENDER_ROOM ")->fetchAll();

        $html = $this->render('statistics/pdf/gender_room.html.twig', [
            "rooms"=>$rooms, 
            'year' => $year,
            "gender_datas"=>$gender_datas, 
        ]);
        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="stat_gender_room' .( count($rooms)==1 ?  $rooms[0]->getName():"") . '.pdf"'
            )
        );

       
    }

    /**
     * Displays a pdf of students grouping by gender.
     *
     * @Route("printagr/{id}", name="admin_stat_print_age_room", defaults={"id"=0}  )
     * @Method("GET")
     * @Template()
     */
    public function ageGroupRoomPdf( Pdf $pdf, int $id=0): Response
    {
        $year = $this->schoolYearService->sessionYearById();
        $rooms = $this->repo->findAll();
        if($id > 0){
            $rooms = $this->repo->findBy(array("id" => $id));
            $this->viewAgeGroup($id);
        } else {
            $this->viewAgeGroup();
        }
        $connection = $this->em->getConnection();
        $age_group_datas = $connection->executeQuery("SELECT *  FROM V_AGE_GROUP_ROOM ")->fetchAll();
        foreach($age_group_datas as $key=>$data){
            if($data["tranche_age"]>50){
                unset($age_group_datas[$key]); // Remove data noise
            }
        }
        $html = $this->render('statistics/pdf/age_group_room.html.twig', [
            "rooms"=>$rooms, 
            'year' => $year,
            "age_group_datas"=>$age_group_datas, 
        ]);
        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="stat_gender_room' .( count($rooms)==1 ?  $rooms[0]->getName():"") . '.pdf"'
            )
        );
    }

    /**
     * Displays a pdf of students grouping by gender.
     *
     * @Route("printagrgen/{id}", name="admin_stat_print_age_room_gender", defaults={"id"=0}  )
     * @Method("GET")
     * @Template()
     */
    public function ageGroupGenderRoomPdf( Pdf $pdf, int $id=0): Response
    {
        $year = $this->schoolYearService->sessionYearById();
        $rooms = $this->repo->findAll();
        if($id > 0){
            $rooms = $this->repo->findBy(array("id" => $id));
            $this->viewGenderAgeGroup($id);
        } else {
            $this->viewGenderAgeGroup();
        }
        $connection = $this->em->getConnection();
        $age_group_gender_datas = $connection->executeQuery("SELECT *  FROM V_AGE_GROUP_GENDER_ROOM ")->fetchAll();
        foreach($age_group_gender_datas as $key=>$data){
            if($data["age"]>50){
                unset($age_group_gender_datas[$key]); // Remove data noise
            }
        }
        //dd( $age_group_gender_datas);
        $html = $this->render('statistics/pdf/age_group_gender_room.html.twig', [
            "rooms"=>$rooms, 
            'year' => $year,
            "age_group_gender_datas"=>$age_group_gender_datas, 
        ]);
        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="stat_gender_agegrp_room' .( count($rooms)==1 ?  $rooms[0]->getName():"") . '.pdf"'
            )
        );

       
    }
    /**
     * Lists all Sequenceme entities.
     *
     * @Route("/{id}", name="admin_statistics", defaults={"id"=0})
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
            $this->viewGenderAgeGroup();
        } else {
            $this->viewGender($id);
            $this->viewAgeGroup($id);
            $this->viewGenderAgeGroup($id);
        }  
        $gender_datas = $connection->executeQuery("SELECT *  FROM V_GENDER_ROOM ")->fetchAll();
        $age_group_datas = $connection->executeQuery("SELECT *  FROM V_AGE_GROUP_ROOM ")->fetchAll();
        $age_group_gender_datas = $connection->executeQuery("SELECT *  FROM V_AGE_GROUP_GENDER_ROOM ")->fetchAll();
         // Traitements de donnees pour les graphes de repartition de sexe par classe
        foreach ($rooms as $room) {
            $roomNames[] = $room->getName();
        }
        $masculin = [];
        $feminin = [];
       
        foreach ($roomNames as $name) {
            foreach($gender_datas as $data){
                if(strcmp($data["room"], $name)==0  && strcmp($data["gender"], "0")==0){
                    array_push($masculin , $data["workforce"]);
                }
                if(strcmp($data["room"], $name)==0  && strcmp($data["gender"], "1")==0){
                    array_push($feminin , $data["workforce"]);
                }
                continue;
            }
        }
        // Traitement des donnees du graphes des groupes d'ages
        $age_groups_weight= [];
        $age_groups_label= [];
        $previousKey = null;
        foreach ($age_group_datas as $key=>$group) {
            array_push($age_groups_weight , $group["effectif"]);
            if ($previousKey == null) {
                array_push($age_groups_label , "0_".$group["tranche_age"]);
            } else {
                array_push($age_groups_label , $age_group_datas[$previousKey]["tranche_age"]."_".$group["tranche_age"]);
            }
            $previousKey = $key;
        }
        // Encodage Json
        $roomNames = json_encode($roomNames);
        if($id > 0){
            $roomNames = json_encode($this->repo->findOneById($id)->getName());
        }
        $ageGroupsWeight = json_encode($age_groups_weight);
        $ageGroupsLabel = json_encode($age_groups_label);

        foreach($age_group_gender_datas as $key=>$data){
            if($data["age"]>50){
                unset($age_group_gender_datas[$key]); // Remove data noise
            }
        }
        foreach($age_group_datas as $key=>$data){
            if($data["tranche_age"]>50){
                unset($age_group_datas[$key]); // Remove data noise
            }
        }
        $scatterData = $age_group_gender_datas;
       
        
        return $this->render('statistics/dashboard.html.twig', [
            "rooms"=>$rooms, 
            "feminin"=>json_encode($feminin),
            "masculin"=> json_encode($masculin), 
            "roomNames"=>$roomNames,
            "ageGroupsLabel"=>$ageGroupsLabel,
            "ageGroupsWeight"=>$ageGroupsWeight,
            'scatterData' => json_encode($scatterData), 
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

        // Cette fonction genere les vues d'effectif par sexe par classe
        public function viewGenderAgeGroup(int $room=0){
            $year = $this->schoolYearService->sessionYearById();
            $connection = $this->em->getConnection();
            if($room>0){
                $statement = $connection->prepare(
                    " CREATE OR REPLACE VIEW V_AGE_GROUP_GENDER_ROOM  AS
                        SELECT
                            FLOOR(DATEDIFF(NOW(), birthday) / 365 / 5) * 5 AS age, std.gender as sexe,
                            COUNT(std.id) AS poids
                        FROM  student    std  
                        JOIN  subscription sub    ON  sub.student_id      =   std.id     
                        JOIN  class_room room    ON  sub.class_room_id     =   room.id
                        WHERE sub.school_year_id =? AND  room.id = ?
                        GROUP BY
                                std.gender, FLOOR(DATEDIFF(NOW(), birthday) / 365 / 5)
                        ORDER BY
                            age;
                    "
                );
                $statement->bindValue(2, $room);
            } else {
                $statement = $connection->prepare(
                    " CREATE OR REPLACE VIEW V_AGE_GROUP_GENDER_ROOM  AS
                    SELECT
                        FLOOR(DATEDIFF(NOW(), birthday) / 365 / 5) * 5 AS age, std.gender as sexe,
                        COUNT(std.id) AS poids
                    FROM  student    std  
                    JOIN  subscription sub    ON  sub.student_id      =   std.id     
                    JOIN  class_room room    ON  sub.class_room_id     =   room.id
                    WHERE sub.school_year_id =? 
                    GROUP BY
                         std.gender, FLOOR(DATEDIFF(NOW(), birthday) / 365 / 5)
                    ORDER BY
                        age;
                    "
                );
            }
            $statement->bindValue(1, $year->getId());
            $statement->execute();
        }

}