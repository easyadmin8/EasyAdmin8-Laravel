<?php

namespace App\Models;

use App\Http\Services\SystemLogService;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SystemLog extends BaseModel
{

    /**
     * 阻止软删除的全局作用域应用 有些模型可能不需要软删除
     * @return void
     */
    public static function bootSoftDeletes() {}

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = 'system_log_' . date('Ym');
    }

    public function admin(): HasOne
    {
        return $this->hasOne(SystemAdmin::class, 'id', 'admin_id')->select('id','username');
    }

    public function setMonth($month): static
    {
        SystemLogService::instance()->detectTable();
        $this->table = 'system_log_' . $month;
        return $this;
    }
}
