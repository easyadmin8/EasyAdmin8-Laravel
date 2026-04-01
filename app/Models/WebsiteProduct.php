<?php

namespace App\Models;

class WebsiteProduct extends BaseModel
{

    public function category(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WebsiteProductCategory::class, 'id', 'category_id')->select('id', 'title');
    }

}
