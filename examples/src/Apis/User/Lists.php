<?php

namespace Rwcoding\Examples\Pscc\Apis\User;

use Rwcoding\Examples\Pscc\ApiBase;
use Rwcoding\Examples\Pscc\Models\UserModel;

class Lists extends ApiBase
{
    public array $rules = [
        "page" => "numeric|min:1",
        "size" => "numeric|min:1|max:100"
    ];

    public function handle()
    {
        $data = $this->getData();

        $page = $data['page'] ?? 1;
        $size = $data['size'] ?? 10;

        $users = UserModel::orderBy("id")->skip(($page-1)*$size)->take($size)->get();
        $total = UserModel::count();

        return $this->success(["users"=>$users, "total"=>$total]);
    }
}