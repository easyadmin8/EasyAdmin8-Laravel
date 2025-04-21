<?php

namespace App\Models;

class SystemUploadfile extends BaseModel
{
    /**
     * 阻止软删除的全局作用域应用 有些模型可能不需要软删除
     * @return void
     */
    public static function bootSoftDeletes() {}
}
