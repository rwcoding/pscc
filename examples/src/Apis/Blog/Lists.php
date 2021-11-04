<?php
namespace Rwcoding\Examples\Pscc\Apis\Blog;

use Illuminate\Support\Arr;
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

        print_r(Arr::pluck([['id'=>1,'title'=>'tt1'],['id'=>5,'title'=>'ttx'],], 'title', 'id'));

        return $this->success(["blogs"=>$blogs, "total"=>$total]);
    }
}