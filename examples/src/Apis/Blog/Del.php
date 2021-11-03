<?php

namespace Rwcoding\Examples\Pscc\Apis\Blog;

use Rwcoding\Examples\Pscc\ApiBase;
use Rwcoding\Examples\Pscc\Models\BlogModel;

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
        $blog = BlogModel::find($this->id);
        if (!$blog) {
            return $this->failure("无效的文章");
        }
        if ($blog->delete()) {
            return $this->success([], "删除成功");
        }
        return $this->failure("删除失败");
    }

}