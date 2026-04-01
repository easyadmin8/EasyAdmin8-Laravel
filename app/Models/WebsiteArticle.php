<?php

namespace App\Models;

class WebsiteArticle extends BaseModel
{

    public function category(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WebsiteArticleCategory::class, 'id', 'category_id')->select('id', 'title', 'slug');
    }

}
