<?php

namespace Rwcoding\Pscc\Core\Db;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SoftDeletingScopeZero extends \Illuminate\Database\Eloquent\SoftDeletingScope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($model->getQualifiedDeletedAtColumn(),0);
    }

    protected function addRestore(Builder $builder)
    {
        $builder->macro('restore', function (Builder $builder) {
            $builder->withTrashed();

            return $builder->update([$builder->getModel()->getDeletedAtColumn() => 0]);
        });
    }

    protected function addWithoutTrashed(Builder $builder)
    {
        $builder->macro('withoutTrashed', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->where(
                $model->getQualifiedDeletedAtColumn(), 0
            );

            return $builder;
        });
    }

    protected function addOnlyTrashed(Builder $builder)
    {
        $builder->macro('onlyTrashed', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->where(
                $model->getQualifiedDeletedAtColumn(), ">", 0
            );

            return $builder;
        });
    }
}