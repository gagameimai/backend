<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

use DateTimeInterface;

class AdminModel extends Authenticatable
{
    /**
     * 資料表。
     *
     * @var string
     */
    protected $table = 'admin';

    /**
     * 可以被批量賦值的屬性。
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * 指定是否模型應該被戳記時間。
     *
     * @var bool
     */
    protected $hidden = [];

    /**
     * 不可以被批量賦值的屬性。
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
