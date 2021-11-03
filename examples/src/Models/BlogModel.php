<?php
namespace Rwcoding\Examples\Pscc\Models;

use Rwcoding\Pscc\Core\Db\SoftDeletesZero;

class BlogModel extends Base
{
    use SoftDeletesZero;
    public $timestamps = false;

    protected $table = "blog";

    public function user()
    {
        return $this->hasOne(UserModel::class, "id", "user_id");
    }
}