<?php
namespace Rwcoding\Examples\Pscc\Models;

use Rwcoding\Pscc\Core\Db\SoftDeletesZero;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 */
class BlogModel extends BaseModel
{
    use SoftDeletesZero;
    public $timestamps = false;

    protected $table = "blog";

    public function user()
    {
        return $this->hasOne(UserModel::class, "id", "user_id");
    }
}