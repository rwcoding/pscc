<?php
namespace Rwcoding\Examples\Pscc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Facades\DB;
use Rwcoding\Pscc\Di;
use Illuminate\Database\Connection;

/**
 * @mixin \Illuminate\Database\Query\Builder
 */
class Base extends Model
{
    private static ?Connection $_connection = null;

    public function getConnection(): Connection
    {
        if (self::$_connection) {
            return self::$_connection;
        }
        $db = Di::my()->config->get("db");
        $manager = new Manager();
        $manager->addConnection([
            'driver'    => $db['driver'] ?? 'mysql',
            'host'      => $db['host'],
            'port'      => $db['port'] ?? 3306,
            'database'  => $db['database'],
            'username'  => $db['username'],
            'password'  => $db['password'],
            'charset'   => $db['charset'] ?? 'utf8mb4',
            'prefix'    => $db['prefix'] ?? '',
        ]);
        $manager->setAsGlobal();
        $manager->bootEloquent();
        $connection = $manager->getConnection();
        self::$_connection = $connection;

        if (PHP_SAPI == "cli") {
            $connection->enableQueryLog();
            DB::swap($connection);
        }

        return $connection;
    }
}