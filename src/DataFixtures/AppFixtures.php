<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use ToDoPlanner\Task\TaskStatus;

class AppFixtures extends Fixture
{

    private $passwordHasherFactory;

    public function __construct(PasswordHasherFactoryInterface $encoderFactory)
    {
        $this->passwordHasherFactory = $encoderFactory;
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->createUsers($manager);
        $this->createTasks($manager, $users);
    }

    private function createUsers(ObjectManager $manager): array
    {
        $user1 = new User();
        // $admin->setRoles([]);
        $user1->setEmail('user1@mail.com');
        $user1->setPassword($this->passwordHasherFactory->getPasswordHasher(User::class)->hash('12345'));
        $manager->persist($user1);

        $user2 = new User();
        // $admin->setRoles([]);
        $user2->setEmail('user2@mail.com');
        $user2->setPassword($this->passwordHasherFactory->getPasswordHasher(User::class)->hash('12345'));
        $manager->persist($user2);

        $manager->flush();

        return [$user1, $user2];
    }

    private function createTasks(ObjectManager $manager, array $users): void
    {
        $taskStatuses = [
            TaskStatus::PENDING,
            TaskStatus::DONE,
            TaskStatus::FAILED,
        ];

        $dateAndDurationMap = [
            'today' => 1,
            'yesterday' => 1,
            'tomorrow' => 1,
        ];

        // add THIS_MONTH to the map
        $thisMonthStartDate = date('Y-m-01');
        $thisMonthDurationInDays = (int) date('t');
        $dateAndDurationMap[$thisMonthStartDate] = $thisMonthDurationInDays;

        $weekDurationInDays = 7;

        // add THIS_WEEK to the map
        $thisWeekStartDate = date('Y-m-d', strtotime("last Monday"));
        $dateAndDurationMap[$thisWeekStartDate] = $weekDurationInDays;

        // add next week to the map
        $thisWeekStartDate = date('Y-m-d', strtotime("next Monday"));
        $dateAndDurationMap[$thisWeekStartDate] = $weekDurationInDays;

        foreach ($users as $user) {
            foreach ($dateAndDurationMap as $startDateAlias => $durationInDays)
            {
                foreach ($taskStatuses as $taskStatus)
                {
                    $titleParts = [
                        $startDateAlias,
                        $taskStatus,
                        'task',
                    ];
                    $title = implode(' ', $titleParts);

                    $todayTask = new Task();
                    $todayTask->setTitle($title);
                    $todayTask->setDescription($title . ' description');
                    $todayTask->setStartDate(new \DateTime($startDateAlias));
                    $todayTask->setDurationInDays($durationInDays);
                    $todayTask->setUser($user);
                    $todayTask->setStatus($taskStatus);
                    $manager->persist($todayTask);
                }
            }
        }

        $manager->flush();
    }

}
