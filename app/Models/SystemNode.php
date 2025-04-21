<?php

namespace App\Models;

class SystemNode extends BaseModel
{
    /**
     * 阻止软删除的全局作用域应用 有些模型可能不需要软删除
     * @return void
     */
    public static function bootSoftDeletes() {}

    public function getNodeTreeList(): array
    {
        $list = $this->get()->toArray();
        return $this->buildNodeTree($list);
    }

    protected function buildNodeTree($list): array
    {
        $newList      = [];
        $repeatString = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        foreach ($list as $vo) {
            if ($vo['type'] == 1) {
                $newList[] = $vo;
                foreach ($list as $v) {
                    if ($v['type'] == 2 && str_contains($v['node'], $vo['node'] . '/')) {
                        $v['node'] = "{$repeatString}├{$repeatString}" . $v['node'];
                        $newList[] = $v;
                    }
                }
            }
        }
        return $newList;
    }
}
