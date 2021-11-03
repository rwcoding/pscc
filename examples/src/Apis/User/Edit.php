<?php

namespace Rwcoding\Examples\Pscc\Apis\User;

use Rwcoding\Examples\Pscc\ApiBase;
use Rwcoding\Examples\Pscc\Models\UserModel;

class Edit extends ApiBase
{
    public array $rules = [
        "id"   => "required|numeric|min:1",
        "name" => "required|min:1|max:100",
    ];

    public function handle()
    {
        $data = $this->getData();

        $user = UserModel::find($data["id"]);
        if (!$user) {
            return $this->failure("无效的用户ID");
        }
        $user->name = $data["name"];
        if ($user->save()) {
            return $this->success();
        }
        return $this->failure("编辑失败");
    }
}