<?php

namespace App\Http\Controllers\admin\system;

use App\Http\Controllers\common\AdminController;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\annotation\ControllerAnnotation;
use App\Http\Services\curd\BuildCurd;
use App\Http\Services\curd\exceptions\FileException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

#[ControllerAnnotation(title: 'CURD可视化管理')]
class CurdGenerateController extends AdminController
{

    #[NodeAnnotation(title: '列表', auth: true)]
    public function index(): View
    {
        return $this->fetch();
    }

    #[NodeAnnotation(title: '保存', auth: true)]
    public function save(): Response|JsonResponse|View
    {
        if (!request()->ajax()) return $this->error();
        $type = request()->input('type', '');
        switch ($type) {
            case "search":
                $tb_prefix = request()->input('tb_prefix', '');
                $tb_name   = request()->input('tb_name', '');
                if (empty($tb_name)) return $this->error('参数错误');
                try {
                    $list = DB::select("show full columns from {$tb_prefix}{$tb_name}");
                    $data = [];
                    foreach ($list as $value) {
                        $data[] = [
                            'name'  => $value->Field,
                            'type'  => $value->Type,
                            'key'   => $value->Key,
                            'extra' => $value->Extra,
                            'null'  => $value->Null,
                            'desc'  => $value->Comment,
                        ];
                    }
                    return $this->success('查询成功', compact('data', 'list'));
                }catch (\PDOException|\Exception $exception) {
                    return $this->error($exception->getMessage());
                }
                break;
            case "add":
                $tb_prefix = request()->input('tb_prefix', '');
                $tb_name   = request()->input('tb_name', '');
                if (empty($tb_name)) return $this->error('参数错误');

                $tb_fields = request()->input('tb_fields');
                $force     = (int)request()->post('force', 0);
                try {
                    $build = (new BuildCurd())->setTablePrefix($tb_prefix)->setTable($tb_name);
                    $build->setForce($force); // 强制覆盖
                    // 新增字段类型
                    if ($tb_fields) {
                        foreach ($tb_fields as $tk => $tf) {
                            if (empty($tf)) continue;
                            $tf = array_values($tf);
                            switch ($tk) {
                                case 'ignore':
                                    $build->setIgnoreFields($tf, true);
                                    break;
                                case 'select':
                                    $build->setSelectFields($tf, true);
                                    break;
                                case 'radio':
                                    $build->setRadioFieldSuffix($tf, true);
                                    break;
                                case 'checkbox':
                                    $build->setCheckboxFieldSuffix($tf, true);
                                    break;
                                case 'image':
                                    $build->setImageFieldSuffix($tf, true);
                                    break;
                                case 'images':
                                    $build->setImagesFieldSuffix($tf, true);
                                    break;
                                case 'date':
                                    $build->setDateFieldSuffix($tf, true);
                                    break;
                                case 'datetime':
                                    $build->setDatetimeFieldSuffix($tf, true);
                                    break;
                                case 'editor':
                                    $build->setEditorFields($tf, true);
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                    $build    = $build->render();
                    $fileList = $build->getFileList();
                    if (empty($fileList)) return $this->error('这里什么都没有');
                    $result = $build->create();
                    $_file  = $result[0] ?? '';
                    $link   = '';
                    if (!empty($_file)) {
                        $_fileExp      = explode(DIRECTORY_SEPARATOR, $_file);
                        $_fileExp_last = array_slice($_fileExp, -2);
                        $link          = '/' . config('easyadmin.ADMIN', 'admin') . '/' . $_fileExp_last[0] . '/' . Str::snake(explode('Controller.php', end($_fileExp_last))[0] ?? '') . '/index';
                    }
                    return $this->success('生成成功', compact('result', 'link'));
                }catch (FileException $exception) {
                    return json(['code' => -1, 'msg' => $exception->getMessage(), '__token__' => csrf_token()]);
                }
                break;
            case "delete":
                $tb_prefix = request()->input('tb_prefix', '');
                $tb_name   = request()->input('tb_name', '');
                if (empty($tb_name)) return $this->error('参数错误');
                try {
                    $build    = (new BuildCurd())->setTablePrefix($tb_prefix)->setTable($tb_name);
                    $build    = $build->render();
                    $fileList = $build->getFileList();
                    if (empty($fileList)) return $this->error('这里什么都没有');
                    $result = $build->delete();
                    return $this->success('删除自动生成CURD文件成功', compact('result'));
                }catch (FileException $exception) {
                    return json(['code' => -1, 'msg' => $exception->getMessage(), '__token__' => csrf_token()]);
                }
                break;
            case 'console':
                $command = request()->input('command', '');
                if (empty($command)) $this->error('请输入命令');
                $commandExp = explode(' ', $command);
                try {
                    $output = Artisan::call('curd', [...$commandExp]);
                }catch (\Throwable $exception) {
                    return $this->error($exception->getMessage() . $exception->getLine());
                }
                if (empty($output)) $this->error('设置错误');
                return $this->success();
                break;
            default:
                return $this->error('参数错误');
                break;
        }
    }

}
