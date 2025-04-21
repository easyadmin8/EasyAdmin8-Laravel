<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    /**
     * 启用软删除
     * 如果不启用 可以删除此配置
     */
    use SoftDeletes;

    /**
     * 自定义软删除字段名
     * Laravel 默认使用 deleted_at 字段进行软删除
     * 如果你的项目中使用的是其他字段名，可以在这里进行修改
     * @var string
     */
    const DELETED_AT = 'delete_time';

    /**
     * 自定义保存的时间戳格式，U 表示 Unix 时间戳 (秒)
     * 如果需要日期格式 可删除此配置
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * 与模型关联的数据表。
     *
     * @var string
     */
    protected $table = "";

    /**
     * 指示模型是否主动维护时间戳。
     *
     * @var bool
     */
    public $timestamps = false;

    protected $casts = [
        'create_time' => 'App\Casts\CarbonDate:Y-m-d H:i:s',
        'update_time' => 'App\Casts\CarbonDate:Y-m-d H:i:s',
        'delete_time' => 'App\Casts\CarbonDate:Y-m-d H:i:s',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $calledClass = get_called_class();
        $className   = substr(strrchr($calledClass, '\\'), 1);
        $this->table = $this->getTableName($className);
    }

    /**
     * @param string $className
     * @return string
     */
    public function getTableName(string $className): string
    {
        return parse_name($className);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function addAll(array $data = []): bool
    {
        return DB::table($this->getTable())->insert($data);
    }

}
