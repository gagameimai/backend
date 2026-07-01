<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DateTimeInterface;

class ResourceCategoryModel extends Model
{
    use HasFactory;

    /**
     * 資料表。
     *
     * @var string
     */
    protected $table = 'resource_category';

    /**
     * 可以被批量賦值的屬性。
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * 不可以被批量賦值的屬性。
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 指定是否模型應該被戳記時間。
     *
     * @var bool
     */
    public $timestamps = true;

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

    public function resources()
    {
        return $this->hasMany(ResourceModel::class, 'resource_category_id', 'id')
            ->selectRaw('resource_category_id, name, url')
            ->where('status', 1)
            ->orderBy('sort', 'ASC');
    }
}
