<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\SchoolYear;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findAllOfCurrentYear(SchoolYear $year)
    {

        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.attributions', 'a')
            ->leftJoin('a.schoolYear', 'sc')
            ->where('sc.id=:year')
            ->orderBy('u.fullName')
            ->setParameter('year', $year->getId());
        
        return $qb->getQuery()->getResult();
    }

    public function findTeacherSize(SchoolYear $year)
    {
        $userIds =  $this->createQueryBuilder('u')
            ->select('id')
            ->leftJoin('u.attributions', 'a')
            ->where('a.year_id=:year')
            ->groupBy('u.id')
            ->having($this->createQueryBuilder('a')->expr()->gte('count(a.id)', ':minimumCount'))
            ->setParameter('year', $year->getId())
            ->setParameter('minimumCount', "0")
            ->getQuery()->getResult();

        $query = $this->createQueryBuilder('u')
            ->select('COUNT(id) ')
            ->where($this->createQueryBuilder('u')->expr()->in('u.userIds', ':userIds'))
            ->setParameter('userIds', $userIds);


        return $query->getQuery()->getResult();
    }

    public function findNotYetHeadTeacher(SchoolYear $year)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
                SELECT * FROM User u 
                WHERE u.id NOT IN
                (
                    SELECT teacher_id FROM main_teacher 
                    WHERE school_year_id != :year
                )
            ';
        $resultSet = $conn->executeQuery($sql, ['year' => $year]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
