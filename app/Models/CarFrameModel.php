<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DateTimeInterface;

class CarFrameModel extends Model
{
    use HasFactory;

    /**
     * 資料表。
     *
     * @var string
     */
    protected $table = 'car_frame';

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
     * Prepare a date for arracategoryy / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function brand()
    {
        return $this->belongsTo(CarBrandModel::class, 'car_brand_id', 'id');
    }

    public function car()
    {
        return $this->belongsTo(CarModel::class, 'car_id', 'id');
    }
}
