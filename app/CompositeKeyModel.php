<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompositeKeyModel extends Model
{
    /**
     * @var array $primaryKey
     */
    protected $primaryKey = ['id', 'id2'];

    /**
     * @param array $options does nothing
     * @return boolean whether it's saved
     */
    public function save(array $options = [])
    {
        $where = [];

        foreach ($this->primaryKey as $key) {
            $where[] = [$key, $this->getAttributeValue($key)];
        }

        $affected = DB::table($this->getTable())->where($where)
            ->update($this->getAttributes());

        if (!$affected) {
            DB::table($this->getTable())->insert($this->getAttributes());
        }

        return true;
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        foreach ($this->getKeyName() as $key) {
            // UPDATE: Added isset() per devflow's comment.
            if (isset($this->$key))
                $query->where($key, '=', $this->$key);
            else
                throw new Exception(__METHOD__ . 'Missing part of the primary key: ' . $key);
        }

        return $query;
    }

    // UPDATE: From jessedp. See his edit, below.
    /**
     * Execute a query for a single record by ID.
     *
     * @param  array  $ids Array of keys, like [column => value].
     * @param  array  $columns
     * @return mixed|static
     */
    public static function find($ids, $columns = ['*'])
    {
        $me = new self;
        $query = $me->newQuery();
        foreach ($me->getKeyName() as $key) {
            $query->where($key, '=', $ids[$key]);
        }
        return $query->first($columns);
    }
}
