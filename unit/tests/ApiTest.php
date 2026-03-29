<?php

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private string $serverUrl;

    /** @var resource|false|null */
    private $serverProcess = null;

    private string $dbPath;

    protected function setUp(): void
    {
        $baseDir = dirname(__DIR__);
        $this->dbPath = $baseDir . '/database/users.db';
        if (! is_dir(dirname($this->dbPath))) {
            mkdir(dirname($this->dbPath), 0777, true);
        }

        $pdo = new PDO('sqlite:' . $this->dbPath);
        $pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, name TEXT)');
        $pdo->exec('DELETE FROM users');
        $pdo->exec("INSERT INTO users (id, name) VALUES (1, 'John Doe'), (2, 'Jane Smith')");

        $port = 18765;
        $this->serverUrl = 'http://127.0.0.1:' . $port;
        $publicDir = realpath($baseDir . '/public');
        $this->assertNotFalse($publicDir);

        $nullDevice = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';
        $this->serverProcess = proc_open(
            [PHP_BINARY, '-S', '127.0.0.1:' . $port, '-t', $publicDir],
            [
                0 => ['pipe', 'r'],
                1 => ['file', $nullDevice, 'w'],
                2 => ['file', $nullDevice, 'w'],
            ],
            $pipes,
            $baseDir
        );

        $this->assertIsResource($this->serverProcess);
        fclose($pipes[0]);

        $deadline = microtime(true) + 10.0;
        $ready = false;
        $client = new Client(['timeout' => 2, 'http_errors' => false]);
        while (microtime(true) < $deadline) {
            try {
                $r = $client->get($this->serverUrl . '/users');
                if ($r->getStatusCode() === 200) {
                    $ready = true;
                    break;
                }
            } catch (\Throwable) {
                // сервер ещё не слушает порт
            }
            usleep(50000);
        }

        $this->assertTrue($ready, 'Встроенный PHP-сервер не поднялся за отведённое время.');
    }

    protected function tearDown(): void
    {
        if (is_resource($this->serverProcess)) {
            proc_terminate($this->serverProcess);
            proc_close($this->serverProcess);
            $this->serverProcess = null;
        }
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
