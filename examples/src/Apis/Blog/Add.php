<?php

namespace Rwcoding\Examples\Pscc\Apis\Blog;

use Rwcoding\Examples\Pscc\ApiBase;
use Rwcoding\Examples\Pscc\Models\BlogModel;
use Rwcoding\Examples\Pscc\Models\UserModel;

class Add extends ApiBase
{
    public array $rules = [
        "uid"      => "required|numeric|min:1",
        "title"    => "required|min:3|max:100",
        "content"  => "required",
    ];

    public function handle()
    {
        $data = $this->getData();

        if (!UserModel::find($data["uid"])) {
            return $this->failure("无效的用户ID");
        }

        $blog = new BlogModel();
        $blog->user_id = $data["uid"];
        $blog->title = $data["title"];
        $blog->content = $data["content"];
        if ($blog->save()) {
            return $this->success(["id"=>$blog->id], "创建成功");
        }
        return $this->failure("创建失败");
    }
}