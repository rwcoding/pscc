<?php
namespace Rwcoding\Examples\Pscc\Models;

use Rwcoding\Pscc\Core\Db\SoftDeletesZero;

/**
 * @property int $id
 * @property int $user_id
 * @property string $username
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 */
class UserModel extends BaseModel
{
    use SoftDeletesZero;
    public $timestamps = false;

    protected $table = "user";
}