<?php

declare(strict_types=1);

namespace Tests;

use App\App;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Spiral\Boot\Environment;
use Spiral\Database\Database;

abstract class BaseTest extends TestCase
{
    protected $app;
    protected $container;
    protected $orm;
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = $this->makeApp(
            [
                'DOTENV_PATH' => __DIR__ . '/../.env.testing',
            ]
        );
        $this->container = $this->app->get(ContainerInterface::class);
        $this->orm = $this->app->get(ORMInterface::class)->withHeap(new Heap());
        $this->db = $this->app->get(Database::class);
    }

    protected function makeApp(array $env = []): App
    {
        $root = dirname(__DIR__) . '/';

        $runtime = sys_get_temp_dir() . '/mapper'; // . uniqid();
        //        dump($runtime);

        return App::init(
            [
                'root'    => $root,
                'app'     => $root . 'app/',
                'runtime' => $runtime,
                'cache'   => $runtime,
            ],
            new Environment($env),
            false
        );
    }

    public function transaction($fresh = false): Transaction
    {
        $orm = $fresh ? $this->orm->withHeap(new Heap()) : $this->orm;
        return new Transaction($orm);
    }

    public function clean(): void
    {
        $this->orm->getHeap()->clean();
    }
}
