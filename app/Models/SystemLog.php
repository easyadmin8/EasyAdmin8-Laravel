<?php

namespace App\Models;

use App\Http\Services\SystemLogService;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SystemLog extends BaseModel
{
    public static function bootSoftDeletes() {}

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = 'system_log_' . date('Ym');
    }

    public function admin(): HasOne
    {
        return $this->hasOne(SystemAdmin::class, 'id', 'admin_id')->select('id', 'username');
    }

    public function setMonth($month): static
    {
        SystemLogService::instance()->detectTable();
        $this->table = 'system_log_' . $month;
        return $this;
    }
}
