<?php

namespace Rwcoding\Examples\Pscc\Apis\User;

use Rwcoding\Examples\Pscc\ApiBase;
use Rwcoding\Examples\Pscc\Models\UserModel;

/**
 * @property int $id
 */
class Del extends ApiBase
{
    public array $rules = [
        "id"   => "required|numeric|min:1",
    ];

    public function handle()
    {
        $data = $this->getData();

        $user = UserModel::find($this->id);
        if (!$user) {
            return $this->failure("无效的用户");
        }
        if ($user->delete()) {
            return $this->success([], "删除成功");
        }
        return $this->failure("删除失败");
    }
}