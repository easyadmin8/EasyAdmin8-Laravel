<?php

namespace App\Models;

class WebsiteChannel extends BaseModel
{

    public function getPidChannelList(): array
    {
        $list = $this->where('status', 1)->select('id', 'pid', 'title')->orderByDesc('sort')->get()->toArray();
        return array_merge([['id' => 0, 'pid' => 0, 'title' => '顶级栏目']], $this->buildPidChannel(0, $list));
    }

    protected function buildPidChannel(int $pid, array $list, int $level = 0): array
    {
        $newList = [];
        foreach ($list as $vo) {
            if ((int)$vo['pid'] === $pid) {
                $vo['level'] = $level + 1;
                if ($vo['level'] > 1) {
                    $vo['title'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;', $vo['level'] - 1) . $vo['title'];
                }
                $newList[] = $vo;
                $newList = array_merge($newList, $this->buildPidChannel((int)$vo['id'], $list, $vo['level']));
            }
        }
        return $newList;
    }

}
