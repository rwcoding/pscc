<?php

namespace Rwcoding\Pscc\Core\Web\Session;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * session 数据库存储处理
 * 默认数据库表
 * CREATE TABLE `session` (
 *    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 *    `sess` VARCHAR(50) NOT NULL DEFAULT '',
 *    `created_at` INT(10) UNSIGNED NOT NULL DEFAULT 0,
 *    `updated_at` INT(10) UNSIGNED NOT NULL DEFAULT 0,
 *    `expire` INT(10) UNSIGNED NOT NULL DEFAULT 0,
 *    `data` VARCHAR(3000) NOT NULL DEFAULT '',
 *    PRIMARY KEY (`id`),
 *    UNIQUE INDEX `sess` (`sess`)
 * )
 *
 */
class SessionDbHandler implements SessionHandlerInterface
{
    /**
     * 数据库中已经存在的session记录id
     */
    private int $existId = 0;

    /**
     * @var Session Session组件
     */
    private Session $session;

    /**
     * @var string 链接名字
     */
    private string $connection;

    /**
     * @var string 表名
     */
    private string $table;

    public function __construct(Session $session, string $connection, string $table)
    {
        $this->session = $session;
        $this->connection = $connection;
        $this->table = $table;
    }

    public function data(): array
    {
        $data = Capsule::connection($this->connection)->table($this->table)->where("sess", "=", $this->session->sessionId())->first();
        if ($data && $data['expire'] > time()) {
            $this->existId = $data['id'];
            return [
                'create' => $data['created_at'],
                'update' => $data['updated_at'],
                'expire' => $data['expire'],
                'data' => unserialize($data['data']),
            ];
        }
        return [];
    }

    public function store(): bool
    {
        if (!$this->existId) {
            if ($s = Capsule::connection($this->connection)->table($this->table)->select("id")->where("sess", "=", $this->session->sessionId())->first()) {
                $this->existId = $s->id;
            }
        }
        $data = $this->session->getData();
        $updateData = [
            'created_at' => $data['create'],
            'expire' => $data['expire'],
            'updated_at' => $data['update'],
            'data' => serialize($data['data']),
        ];
        if ($this->existId) {
            return (bool) Capsule::connection($this->connection)->table($this->table)->where("id", $this->existId)->update($updateData);
        } else {
            $updateData['sess'] = $this->session->sessionId();
            return (bool) Capsule::connection($this->connection)->table($this->table)->where("id", $this->existId)->insert($updateData);
        }
    }

    public function destroy(): void
    {
        Capsule::connection($this->connection)->table($this->table)->select("id")->where("sess", "=", $this->session->sessionId())->delete();
    }

    public function gc(): void
    {
        Capsule::connection($this->connection)->table($this->table)->select("id")->where("expire", "<", time())->delete();
    }
}
