<?php

namespace App\Models;

class SystemAdmin extends BaseModel
{
    public array $notes = [
        'login_type' => [
            1 => '密码登录',
            2 => '密码 + 谷歌验证码登录'
        ],
    ];

    public function getAuthIdsAttribute($value): array
    {
        if (!$value) return [];
        return explode(',', $value);
    }

    public function getAuthList(): array
    {
        $list = SystemAuth::where('status', 1)->select(['id', 'title'])->get()->toArray();
        return collect($list)->pluck('title', 'id')->toArray();
    }
}
