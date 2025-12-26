<?php

namespace App\Http\Controllers\admin\system;

use App\Http\Controllers\common\AdminController;
use App\Http\Services\annotation\MiddlewareAnnotation;
use App\Http\Services\tool\CommonTool;
use App\Models\SystemLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: 'Operation log Management')]
class LogController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new SystemLog();
    }

    #[NodeAnnotation(title: 'list', auth: true)]
    public function index(): View|JsonResponse
    {
        if (!request()->ajax()) return $this->fetch();
        [$page, $limit, $where, $excludeFields] = $this->buildTableParams(['month']);
        $month = !empty($excludeFields['month']) ? date('Ym', strtotime($excludeFields['month'])) : date('Ym');
        if (empty($month)) $month = date('Ym');
        try {
            $count = $this->model->setMonth($month)->where($where)->count();
            $list  = $this->model->setMonth($month)->where($where)->orderBy($this->order, $this->orderDirection)->with(['admin'])->paginate($limit)->items();
        }catch (\PDOException|\Exception $exception) {
            $count = 0;
            $list  = [];
        }
        $data = [
            'code'  => 0,
            'msg'   => '',
            'count' => $count,
            'data'  => $list,
        ];
        return json($data);
    }

    #[NodeAnnotation(title: 'export', auth: true)]
    public function export(): View|bool
    {
        if (config('easyadmin.IS_DEMO', false)) {
            return $this->error(ea_trans('Modification is not allowed in the demonstration environment', false));
        }
        [$page, $limit, $where, $excludeFields] = $this->buildTableParams(['month']);
        $tableName = $this->model->getTable();
        $tableName = CommonTool::humpToLine(lcfirst($tableName));
        $prefix    = config('database.connections.mysql.prefix');
        $dbList    = DB::select("show full columns from {$prefix}{$tableName}");
        $header    = [];
        foreach ($dbList as $vo) {
            $comment = !empty($vo->Comment) ? $vo->Comment : $vo->Field;
            if (!in_array($vo->Field, $this->noExportFields)) {
                $header[] = [$comment, $vo->Field];
            }
        }
        $month = !empty($excludeFields['month']) ? date('Ym', strtotime($excludeFields['month'])) : date('Ym');
        if (empty($month)) $month = date('Ym');
        try {
            $list = $this->model->setMonth($month)->where($where)->orderBy($this->order, $this->orderDirection)->limit(100000)->get();
        }catch (\PDOException|\Exception $exception) {
            return $this->error($exception->getMessage());
        }
        if (empty($list)) return $this->error(ea_trans('No data available', false));
        $list     = $list->toArray();
        try {
            exportExcel($header, $list, 'log');
        }catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
        return $this->success('success');
    }

    #[MiddlewareAnnotation(ignore: MiddlewareAnnotation::IGNORE_LOG)]
    #[NodeAnnotation(title: 'Framework Log', auth: true, ignore: NodeAnnotation::IGNORE_NODE)]
    public function record(): View
    {
        return (new \Wolfcode\PhpLogviewer\laravel\LogViewer())->fetch();
    }

    #[NodeAnnotation(title: 'Delete specified log', auth: true)]
    public function deleteMonthLog(): View|JsonResponse
    {
        if (!request()->ajax()) {
            return $this->fetch();
        }

        if ($this->isDemo) return $this->error(ea_trans('Modification is not allowed in the demonstration environment', false));

        $monthsAgo = (int)request()->post('month', 0);
        if ($monthsAgo < 1) return $this->error('Month error');

        $dbPrefix   = env('DB_PREFIX');
        $dbLike     = "{$dbPrefix}system_log_";
        $tables     = DB::select("SHOW TABLES LIKE '$dbLike%'");
        $threshold  = date('Ym', strtotime("-$monthsAgo month"));
        $tableNames = [];
        try {
            foreach ($tables as $table) {
                $tableName = current($table);
                if (!preg_match("/^$dbLike\d{6}$/", $tableName)) continue;
                $datePart   = substr($tableName, -6);
                $issetTable = DB::select("SHOW TABLES LIKE '$tableName'");
                if (!$issetTable) continue;
                if ($datePart - $threshold <= 0) {
                    DB::statement("DROP TABLE `$tableName`");
                    $tableNames[] = $tableName;
                }
            }
        }catch (\Throwable) {
        }
        if (empty($tableNames)) return $this->error('Nothing to delete');
        return $this->success('success <br/>' . implode('<br>', $tableNames));
    }

}
