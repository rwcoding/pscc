<?php

namespace Rwcoding\Examples\Pscc\Apis\User;

use Rwcoding\Examples\Pscc\ApiBase;
use Rwcoding\Examples\Pscc\Models\UserModel;

class Add extends ApiBase
{
    public array $rules = [
        "username" => "required|min:1|max:100",
        "name"     => "required|min:1|max:100",
    ];

    public function handle()
    {
        $data = $this->getData();
        $user = new UserModel();
        $user->username = $data["username"];
        $user->name = $data["name"];
        if ($user->save()) {
            return $this->success(["id"=>$user->id]);
        }
        return $this->failure("创建失败");
    }
}