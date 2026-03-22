<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class ApiTest extends TestCase {

    private $serverUrl;
    private $dbPath = __DIR__ . '/../database/users.db';

    protected function setUp(): void
    {
        $pdo = new PDO("sqlite:{$this->dbPath}");
        $pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, name TEXT)');
        $pdo->exec("DELETE FROM users");
        $pdo->exec("INSERT INTO users (id, name) VALUES (1, 'John Doe'), (2, 'Jane Smith')");
        
        $this->serverUrl = 'http://localhost:8000';
        shell_exec('php -S localhost:8000 -t public/ > /dev/null 2>&1 &');
        sleep(1);
    }

    protected function tearDown(): void {
        shell_exec('pkill -f "php -S localhost:8000" || true');
    }

    public function testUserApiReturnsUsers(): void
    {
        $client = new Client();
        $response = $client->get($this->serverUrl . '/users');

        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertIsArray($data['users']);
        $this->assertCount(2, $data['users']);
        $this->assertSame('John Doe', $data['users'][0]['name']);
    }
}
