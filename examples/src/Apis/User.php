<?php
namespace Rwcoding\Examples\Pscc\Apis;

use Illuminate\Support\Facades\DB;
use Rwcoding\Examples\Pscc\Models\User as UserModel;
use Rwcoding\Pscc\Di;

class User extends Base
{
    public function index()
    {
        $data = $this->getData();
        $validator = Di::my()->validator->make($data, [
            "page" => "numeric|min:1",
            "size" => "digits_between:1,100"
        ]);
        if ($validator->fails()) {
            return $this->failure(implode(";", $validator->errors()->all()));
        }

        $page = $data['page'] ?? 1;
        $size = $data['size'] ?? 10;

        $users = UserModel::orderBy("id")->skip(($page-1)*$size)->take($size)->get();
        $total = UserModel::count();

        return $this->success(["users"=>$users, "total"=>$total]);
    }

    public function add()
    {
        $data = $this->getData();
        $validator = Di::my()->validator->make($data, [
            "username" => "required|min:1|max:100",
            "name"     => "required|min:1|max:100",
        ]);
        if ($validator->fails()) {
            return $this->failure(implode(";", $validator->errors()->all()));
        }

        $user = new UserModel();
        $user->username = $data["username"];
        $user->name = $data["name"];
        if ($user->save()) {
            return $this->success(["id"=>$user->id]);
        }
        return $this->failure("创建失败");
    }

}