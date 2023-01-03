<?php

namespace Functional\Controller;

use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ApiControllerTest extends WebTestCase
{

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@mail.com');
        $this->client->loginUser($testUser);
    }

    public function testCreateTask(): void
    {
        $newTaskFields = [
            'title' => 'Test',
            'description' => 'Description',
            'start_date' => '2025-01-01',
            'duration_in_days' => '1',
        ];
        $this->client->request(Request::METHOD_POST, '/api/tasks', [], [], [], json_encode($newTaskFields));
        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $jsonResult = json_decode($content, true);
        $this->assertEquals('ok', $jsonResult['status']);
        $this->assertIsArray($jsonResult['task']);
    }

    public function testGetTasks(): void
    {
        $this->client->request(Request::METHOD_GET, '/api/tasks');
        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $jsonResult = json_decode($content, true);

        $this->assertEquals('ok', $jsonResult['status']);
        $this->assertIsArray($jsonResult['group_collection']);
        $this->assertCount(3, $jsonResult['group_collection']);
    }

}
