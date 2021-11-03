<?php

namespace Rwcoding\Pscc\Core\Db;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withTrashed(bool $withTrashed = true)
 * @method static static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder onlyTrashed()
 * @method static static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withoutTrashed()
 */
trait SoftDeletesZero
{
    use SoftDeletes;

    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingScopeZero);
    }

    public function initializeSoftDeletes()
    {
        $this->dateFormat = "U";
    }

    public function trashed()
    {
        return $this->{$this->getDeletedAtColumn()} > 0;
    }

    protected function runSoftDelete()
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();

        $columns = [$this->getDeletedAtColumn() => $this->fromDateTime($time)];

        $this->{$this->getDeletedAtColumn()} = $time;

        $this->{$this->getUpdatedAtColumn()} = $time;
        $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));

        $this->fireModelEvent('trashed', false);
    }

    public function restore()
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()} = 0;

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }
}