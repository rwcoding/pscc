<?php
namespace Rwcoding\Examples\Pscc\Apis\Blog;

use Rwcoding\Examples\Pscc\ApiBase;
use Rwcoding\Examples\Pscc\Models\BlogModel;

/**
 * @property int $page
 * @property int $size
 */
class Lists extends ApiBase
{
    public array $rules = [
        "page" => "numeric|min:1",
        "size" => "numeric|between:1,100"
    ];

    public function handle()
    {
        $data = $this->getData();
        $page = $data['page'] ?? 1;
        $size = $data['size'] ?? 10;

        $blogs = BlogModel::orderBy("id")->skip(($page-1)*$size)->take($size)->get();

        $total = BlogModel::count();

        return $this->success(["blogs"=>$blogs, "total"=>$total]);
    }
}