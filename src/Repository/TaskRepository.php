<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ToDoPlanner\Task\Group\Type;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function add(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws \Exception
     */
    public function findCurrentByGorupType(int $user_id, string $groupType): array
    {
        if ($groupType == Type::TODAY) {
            return $this->findTodayTasks($user_id);
        }
        elseif ($groupType == Type::THIS_WEEK) {
            return $this->findThisWeekTasks($user_id);
        }
        elseif ($groupType == Type::THIS_MONTH) {
            return $this->findThisMonthTasks($user_id);
        }
        else {
            throw new \Exception('Undefined group type: ' . $groupType);
        }
    }

    public function findTodayTasks(int $user_id): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user_id')
            ->andWhere('t.start_date = :today_date')
            ->andWhere('t.duration_in_days = :duration')
            ->setParameter('user_id', $user_id)
            ->setParameter('today_date', date('Y-m-d'))
            ->setParameter('duration', 1)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findThisWeekTasks(int $user_id): array
    {
        $firstDayOfThisWeek = date('Y-m-d', strtotime('last monday'));
        $firstDayOfNextWeek = date('Y-m-d', strtotime('next monday'));
        $weekDurationInDays = 7;
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user_id')
            ->andWhere('t.start_date >= :start_date')
            ->andWhere('t.start_date < :end_date')
            ->andWhere('t.duration_in_days = :duration')
            ->setParameter('user_id', $user_id)
            ->setParameter('start_date', $firstDayOfThisWeek)
            ->setParameter('end_date', $firstDayOfNextWeek)
            ->setParameter('duration', $weekDurationInDays)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findThisMonthTasks(int $user_id): array
    {
        $firstDayOfThisMonth = date('Y-m-01');
        $mouthDurationInDays = date('t');
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user_id')
            ->andWhere('t.start_date = :start_date')
            ->andWhere('t.duration_in_days = :duration')
            ->setParameter('user_id', $user_id)
            ->setParameter('start_date', $firstDayOfThisMonth)
            ->setParameter('duration', $mouthDurationInDays)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
