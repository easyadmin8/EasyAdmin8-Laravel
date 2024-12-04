<?php

namespace App\Http;

use App\Http\Services\tool\CommonTool;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use jianyan\excel\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\annotation\ControllerAnnotation;

/**
 * 后台CURD复用
 * Trait Curd
 * @package app\admin\traits
 */
trait Curd
{

    /**
     * @NodeAnnotation(title="list")
     */
    public function index(): View|JsonResponse
    {
        if (!request()->ajax()) return $this->fetch();
        if (request()->input('selectFields')) {
            return $this->selectList();
        }
        list($page, $limit, $where) = $this->buildTableParams();
        $count = $this->model->where($where)->count();
        $list  = $this->model->where($where)->orderByDesc($this->order)->paginate($limit)->items();
        $data  = [
            'code'  => 0,
            'msg'   => '',
            'count' => $count,
            'data'  => $list,
        ];
        return json($data);
    }

    /**
     * @NodeAnnotation(title="add")
     */
    public function add(): View|JsonResponse
    {
        if (request()->ajax()) {
            try {
                $save = insertFields($this->model);
            }catch (\Exception $e) {
                return $this->error(ea_trans('operation failed', false) . ':' . $e->getMessage());
            }
            return $save ? $this->success(ea_trans('operation successful', false)) : $this->error(ea_trans('operation failed', false));
        }
        return $this->fetch();
    }

    /**
     * @NodeAnnotation(title="edit")
     */
    public function edit(): View|JsonResponse
    {
        $id  = (int)request()->input('id');
        $row = $this->model->find($id);
        if (empty($row)) return $this->error(ea_trans('data does not exist', false));
        if (request()->ajax()) {
            try {
                $save = updateFields($this->model, $row);
            }catch (\PDOException|\Exception $e) {
                return $this->error(ea_trans('operation failed', false) . ':' . $e->getMessage());
            }
            return $save ? $this->success(ea_trans('operation successful', false)) : $this->error(ea_trans('operation failed', false));
        }
        $this->assign(compact('row'));
        return $this->fetch();
    }

    /**
     * @NodeAnnotation(title="delete")
     */
    public function delete(): JsonResponse
    {
        if (!request()->ajax()) return $this->error();
        $id = request()->input('id');
        if (!is_array($id)) $id = (array)$id;
        $row = $this->model->whereIn('id', $id)->get()->toArray();
        if (empty($row)) return $this->error(ea_trans('data does not exist', false));
        try {
            $save = $this->model->whereIn('id', $id)->delete();
        }catch (\PDOException|\Exception $e) {
            return $this->error(ea_trans('operation failed', false) . ':' . $e->getMessage());
        }
        return $save ? $this->success(ea_trans('operation successful', false)) : $this->error(ea_trans('operation failed', false));
    }

    /**
     * @NodeAnnotation(title="export")
     */
    public function export(): View|bool
    {
        if (config('easyadmin.IS_DEMO', false)) {
            return $this->error(ea_trans('Modification is not allowed in the demonstration environment', false));
        }
        list($page, $limit, $where) = $this->buildTableParams();
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
        $list = $this->model->where($where)->limit(100000)->orderByDesc('id')->get();
        if (empty($list)) return $this->error(ea_trans('No data available', false));
        $list     = $list->toArray();
        $fileName = time();
        try {
            return Excel::exportData($list, $header, $fileName, 'xlsx');
        }catch (Exception|\PhpOffice\PhpSpreadsheet\Exception$e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @NodeAnnotation(title="modify")
     */
    public function modify(): JsonResponse
    {
        if (!request()->ajax()) return $this->error();
        $post      = request()->post();
        $rules     = [
            'id'    => 'required',
            'field' => 'required',
            'value' => 'required',
        ];
        $validator = Validator::make($post, $rules, [
            'id'    => 'ID' . ea_trans('Cannot be empty', false),
            'field' => 'field' . ea_trans('Cannot be empty', false),
            'value' => 'value' . ea_trans('Cannot be empty', false),
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $row = $this->model->find($post['id']);
        if (empty($row)) {
            return $this->error(ea_trans('data does not exist', false));
        }
        try {
            foreach ($post as $key => $item) if ($key == 'field') $row->$item = $post['value'];
            $row->save();
        }catch (\PDOException|\Exception $e) {
            return $this->error(ea_trans('operation failed', false) . ":" . $e->getMessage());
        }
        return $this->success(ea_trans('operation successful', false));
    }

}
