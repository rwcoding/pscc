<?php

namespace Rwcoding\Examples\Pscc\Apis\User;

use Rwcoding\Examples\Pscc\ApiBase;
use Rwcoding\Examples\Pscc\Models\UserModel;

/**
 * @property int $id
 */
class Restore extends ApiBase
{
    public array $rules = [
        "id" => "required|numeric|min:1",
    ];

    public function handle()
    {
        $user = UserModel::onlyTrashed()->find($this->id);
        if (!$user) {
            return $this->failure("无效的用户");
        }
        if ($user->restore()) {
            return $this->success([], "恢复成功");
        }
        return $this->failure("恢复失败");
    }
}