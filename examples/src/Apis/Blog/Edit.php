<?php

namespace Rwcoding\Examples\Pscc\Apis\Blog;

use Rwcoding\Examples\Pscc\ApiBase;
use Rwcoding\Examples\Pscc\Models\BlogModel;

class Edit extends ApiBase
{
    public array $rules = [
        "id"      => "required|numeric|min:1",
        "title"   => "required|min:3|max:100",
        "content" => "required",
    ];

    public function handle()
    {
        $data = $this->getData();
        $blog = BlogModel::find($data["id"]);
        if (!$blog) {
            return $this->failure("无效的ID");
        }
        $blog->title = $data["title"];
        $blog->content = $data["content"];
        if ($blog->save()) {
            return $this->success([],"编辑成功");
        }
        return $this->failure("编辑失败");
    }
}