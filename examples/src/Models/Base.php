<?php
namespace Rwcoding\Examples\Pscc\Models;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Rwcoding\Pscc\Di;
use Illuminate\Database\Connection;

/**
 * @mixin Builder
 */
class Base extends Model
{
    private static ?Connection $_connection = null;

    public function getConnection(): Connection
    {
        return static::conn();
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_at = time();
            $model->updated_at = time();
        });

        static::updating(function ($model) {
            $model->updated_at = time();
        });

    }

    protected static function conn(): ?Connection
    {
        if (self::$_connection) {
            return self::$_connection;
        }
        static::setEventDispatcher(new Dispatcher(new Container()));
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
        // $manager->setEventDispatcher(new Dispatcher(new Container()));
        $connection = $manager->getConnection();
        self::$_connection = $connection;

        if (PHP_SAPI == "cli") {
            $connection->enableQueryLog();
            DB::swap($connection);
        }

        return $connection;
    }
}