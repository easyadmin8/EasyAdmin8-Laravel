<?php

namespace App\Http\Services\curd;

use App\Http\Services\curd\exceptions\FileException;
use App\Http\Services\curd\exceptions\TableException;
use App\Http\Services\tool\CommonTool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 快速构建系统CURD
 * Class BuildCurd
 * @package EasyAdmin\curd
 */
class BuildCurd
{

    /**
     * 当前目录
     * @var string
     */
    protected string $dir;

    /**
     * 应用目录
     * @var string
     */
    protected string $rootDir;

    /**
     * 分隔符
     * @var string
     */
    protected string $DS = DIRECTORY_SEPARATOR;

    /**
     * 数据库名
     * @var string
     */
    protected string $dbName;

    /**
     *  表前缀
     * @var string
     */
    protected mixed $tablePrefix = 'ea';

    /**
     * 主表
     * @var string
     */
    protected string $table;

    /**
     * 表注释名
     * @var string
     */
    protected string $tableComment;

    /**
     * 主表列信息
     * @var array
     */
    protected array $tableColumns;

    /**
     * 数据列表可见字段
     * @var string
     */
    protected string $fields;

    /**
     * 是否软删除模式
     * @var bool
     */
    protected bool $delete = false;

    /**
     * 是否强制覆盖
     * @var bool
     */
    protected bool $force = false;

    /**
     * 关联模型
     * @var array
     */
    protected array $relationArray = [];

    /**
     * 控制器对应的URL
     * @var string
     */
    protected string $controllerUrl;

    /**
     * 生成的控制器名
     * @var string
     */
    protected string $controllerFilename;


    /**
     * 控制器命名
     * @var string
     */
    protected string $controllerName;

    /**
     * 控制器命名空间
     * @var string
     */
    protected string $controllerNamespace;

    /**
     * 视图名
     * @var string
     */
    protected string $viewFilename;

    /**
     * js文件名
     * @var string
     */
    protected string $jsFilename;

    /**
     * 生成的模型文件名
     * @var string
     */
    protected string $modelFilename;

    /**
     * 主表模型命名
     * @var string
     */
    protected string $modelName;

    /**
     * 复选框字段后缀
     * @var array
     */
    protected array $checkboxFieldSuffix = ['checkbox'];

    /**
     * 单选框字段后缀
     * @var array
     */
    protected array $radioFieldSuffix = ['radio'];

    /**
     * 下拉选择字段后缀
     * @var array
     */
    protected array $selectFieldSuffix = ['select'];

    /**
     * 单图片字段后缀
     * @var array
     */
    protected array $imageFieldSuffix = ['image', 'logo', 'photo', 'icon'];

    /**
     * 多图片字段后缀
     * @var array
     */
    protected array $imagesFieldSuffix = ['images', 'photos', 'icons'];

    /**
     * 单文件字段后缀
     * @var array
     */
    protected array $fileFieldSuffix = ['file'];

    /**
     * 多文件字段后缀
     * @var array
     */
    protected array $filesFieldSuffix = ['files'];

    /**
     * 时间字段后缀
     * @var array
     */
    protected array $dateFieldSuffix = ['date', 'time'];

    /**
     * 日期时间字段后缀
     * @var array
     */
    protected array $datetimeFieldSuffix = ['datetime'];

    /**
     * 开关组件字段
     * @var array
     */
    protected array $switchFields = [];

    /**
     * 下拉选择字段
     * @var array
     */
    protected array $selectFields = ['select'];
    /**
     * 单选选择字段
     * @var array
     */
    protected array $radioFields = ['radio'];

    /**
     * 复选字段
     * @var array
     */
    protected array $checkboxFields = ['checkbox'];

    /**
     * 富文本字段
     * @var array
     */
    protected array $editorFields = [];

    /**
     * 排序字段
     * @var array
     */
    protected array $sortFields = [];

    /**
     * 忽略字段
     * @var array
     */
    protected array $ignoreFields = ['update_time', 'delete_time'];

    /**
     * 外键字段
     * @var array
     */
    protected array $foreignKeyFields = [];

    /**
     * 相关生成文件
     * @var array
     */
    protected array $fileList = [];

    /**
     * 表单类型
     * @var array
     */
    protected array $formTypeArray = ['text', 'image', 'images', 'file', 'files', 'select', 'switch', 'date', 'editor', 'textarea', 'checkbox', 'radio'];

    /**
     * 初始化
     * BuildCurd constructor.
     */
    public function __construct()
    {
        $this->tablePrefix = config('database.connections.mysql.prefix');
        $this->dbName      = config('database.connections.mysql.database');
        $this->dir         = __DIR__;
        $this->rootDir     = base_path();
        return $this;
    }

    public function setTablePrefix($prefix): static
    {
        $this->tablePrefix = $prefix;
        return $this;
    }

    /**
     * 设置主表
     * @param $table
     * @return $this
     * @throws TableException
     */
    public function setTable($table): static
    {
        $this->table = $table;
        try {

            // 获取表列注释
            $columns = DB::select("SHOW FULL COLUMNS FROM {$this->tablePrefix}{$this->table}");
            foreach ($columns as $vo) {
                $column = [
                    'type'     => $vo->Type,
                    'comment'  => !empty($vo->Comment) ? $vo->Comment : $vo->Field,
                    'required' => $vo->Null == "NO",
                    'default'  => $vo->Default,
                ];

                // 格式化列数据
                $this->buildColumn($column);

                $this->tableColumns[$vo->Field] = $column;

                if ($vo->Field == 'delete_time') {
                    $this->delete = true;
                }

            }
            $this->tableComment = $this->table;
        }catch (\Exception $e) {
            throw new TableException($e->getMessage());
        }

        // 初始化默认控制器名
        $nodeArray = explode('_', $this->table);
        if (count($nodeArray) == 1) {
            $this->controllerFilename = ucfirst($nodeArray[0]);
        }else {
            foreach ($nodeArray as $k => $v) {
                if ($k == 0) {
                    $this->controllerFilename = "{$v}{$this->DS}";
                }else {
                    $this->controllerFilename .= ucfirst($v);
                }
            }
        }

        // 初始化默认模型名
        $this->modelFilename = ucfirst(CommonTool::lineToHump($this->table));

        $this->buildViewJsUrl();

        // 构建数据
        $this->buildStructure();

        return $this;
    }

    /**
     * 设置关联表
     * @param $relationTable
     * @param $foreignKey
     * @param null $primaryKey
     * @param null $modelFilename
     * @param array $onlyShowFields
     * @param null $bindSelectField
     * @return $this
     * @throws TableException
     */
    public function setRelation($relationTable, $foreignKey, $primaryKey = null, $modelFilename = null, $onlyShowFields = [], $bindSelectField = null)
    {
        if (!isset($this->tableColumns[$foreignKey])) {
            throw new TableException("主表不存在外键字段：{$foreignKey}");
        }
        if (!empty($modelFilename)) {
            $modelFilename = str_replace('/', $this->DS, $modelFilename);
        }
        try {
            $columns       = Db::select("SHOW FULL COLUMNS FROM {$this->tablePrefix}{$relationTable}");
            $formatColumns = [];
            $delete        = false;
            if (!empty($bindSelectField) && !in_array($bindSelectField, array_column($columns, 'Field'))) {
                throw new TableException("关联表{$relationTable}不存在该字段: {$bindSelectField}");
            }
            foreach ($columns as $vo) {
                if (empty($primaryKey) && $vo['Key'] == 'PRI') {
                    $primaryKey = $vo['Field'];
                }
                if (!empty($onlyShowFields) && !in_array($vo['Field'], $onlyShowFields)) {
                    continue;
                }
                $colum = [
                    'type'    => $vo['Type'],
                    'comment' => $vo['Comment'],
                    'default' => $vo['Default'],
                ];

                $this->buildColumn($colum);

                $formatColumns[$vo['Field']] = $colum;
                if ($vo['Field'] == 'delete_time') {
                    $delete = true;
                }
            }

            $modelFilename = empty($modelFilename) ? ucfirst(CommonTool::lineToHump($relationTable)) : $modelFilename;
            $modelArray    = explode($this->DS, $modelFilename);
            $modelName     = array_pop($modelArray);

            $relation = [
                'modelFilename'   => $modelFilename,
                'modelName'       => $modelName,
                'foreignKey'      => $foreignKey,
                'primaryKey'      => $primaryKey,
                'bindSelectField' => $bindSelectField,
                'delete'          => $delete,
                'tableColumns'    => $formatColumns,
            ];
            if (!empty($bindSelectField)) {
                $relationArray                                      = explode('\\', $modelFilename);
                $this->tableColumns[$foreignKey]['bindSelectField'] = $bindSelectField;
                $this->tableColumns[$foreignKey]['bindRelation']    = end($relationArray);
            }
            $this->relationArray[$relationTable] = $relation;
            $this->selectFields[]                = $foreignKey;
        }catch (\Exception $e) {
            throw new TableException($e->getMessage());
        }
        return $this;
    }

    /**
     * 设置控制器名
     * @param $controllerFilename
     * @return $this
     */
    public function setControllerFilename($controllerFilename): static
    {
        $this->controllerFilename = str_replace('/', $this->DS, $controllerFilename);
        $this->buildViewJsUrl();
        return $this;
    }

    /**
     * 设置模型名
     * @param $modelFilename
     * @return $this
     */
    public function setModelFilename($modelFilename): static
    {
        $this->modelFilename = str_replace('/', $this->DS, $modelFilename);
        $this->buildViewJsUrl();
        return $this;
    }

    /**
     * 设置显示字段
     * @param $fields
     * @return $this
     */
    public function setFields($fields): static
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * 设置删除模式
     * @param $delete
     * @return $this
     */
    public function setDelete($delete): static
    {
        $this->delete = $delete;
        return $this;
    }

    /**
     * 设置是否强制替换
     * @param $force
     * @return $this
     */
    public function setForce($force): static
    {
        $this->force = $force;
        return $this;
    }

    /**
     * 设置复选框字段后缀
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setCheckboxFieldSuffix($array, bool $replace = false): static
    {
        $this->checkboxFieldSuffix = $replace ? $array : array_merge($this->checkboxFieldSuffix, $array);
        return $this;
    }

    /**
     * 设置单选框字段后缀
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setRadioFieldSuffix($array, bool $replace = false): static
    {
        $this->radioFieldSuffix = $replace ? $array : array_merge($this->radioFieldSuffix, $array);
        return $this;
    }

    /**
     * 设置单图片字段后缀
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setImageFieldSuffix($array, bool $replace = false): static
    {
        $this->imageFieldSuffix = $replace ? $array : array_merge($this->imageFieldSuffix, $array);
        return $this;
    }

    /**
     * 设置多图片字段后缀
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setImagesFieldSuffix($array, bool $replace = false): static
    {
        $this->imagesFieldSuffix = $replace ? $array : array_merge($this->imagesFieldSuffix, $array);
        return $this;
    }

    /**
     * 设置单文件字段后缀
     * @param $array
     * @return $this
     */
    public function setFileFieldSuffix($array): static
    {
        $this->fileFieldSuffix = array_merge($this->fileFieldSuffix, $array);
        return $this;
    }

    /**
     * 设置多文件字段后缀
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setFilesFieldSuffix($array, bool $replace = false): static
    {
        $this->filesFieldSuffix = $replace ? $array : array_merge($this->filesFieldSuffix, $array);
        return $this;
    }

    /**
     * 设置时间字段后缀
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setDateFieldSuffix($array, bool $replace = false): static
    {
        $this->dateFieldSuffix = $replace ? $array : array_merge($this->dateFieldSuffix, $array);
        return $this;
    }

    /**
     * 设置日期时间字段后缀
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setDatetimeFieldSuffix($array, bool $replace = false): static
    {
        $this->datetimeFieldSuffix = $replace ? $array : array_merge($this->datetimeFieldSuffix, $array);
        return $this;
    }

    /**
     * 设置开关字段
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setSwitchFields($array, bool $replace = false): static
    {
        $this->switchFields = $replace ? $array : array_merge($this->switchFields, $array);
        return $this;
    }

    /**
     * 设置下拉选择字段
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setSelectFields($array, bool $replace = false): static
    {
        $this->selectFields = $replace ? $array : array_merge($this->selectFields, $array);
        return $this;
    }

    /**
     * 设置排序字段
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setSortFields($array, bool $replace = false): static
    {
        $this->sortFields = $replace ? $array : array_merge($this->sortFields, $array);
        return $this;
    }

    /**
     * 设置忽略字段
     * @param $array
     * @param bool $replace
     * @return $this
     */
    public function setIgnoreFields($array, bool $replace = false): static
    {
        $this->ignoreFields = $replace ? $array : array_merge($this->ignoreFields, $array);
        return $this;
    }

    public function setEditorFields($array, $replace = false): static
    {
        $this->editorFields = $replace ? $array : array_merge($this->editorFields, $array);
        return $this;
    }

    /**
     * 获取相关的文件
     * @return array
     */
    public function getFileList(): array
    {
        return $this->fileList;
    }

    /**
     * 构建基础视图、JS、URL
     * @return $this
     */
    protected function buildViewJsUrl(): static
    {
        $nodeArray   = explode($this->DS, $this->controllerFilename);
        $formatArray = [];
        foreach ($nodeArray as $vo) {
            $formatArray[] = Str::snake(lcfirst($vo));
        }
        $this->controllerUrl = implode('.', $formatArray);
        $this->viewFilename  = implode($this->DS, $formatArray);
        $this->jsFilename    = $this->viewFilename;

        // 控制器命名空间
        $namespaceArray            = $nodeArray;
        $this->controllerName      = array_pop($namespaceArray);
        $namespaceSuffix           = implode('\\', $namespaceArray);
        $this->controllerNamespace = empty($namespaceSuffix) ? "App\Http\Controllers\admin" : "App\Http\Controllers\admin\\{$namespaceSuffix}";

        // 主表模型命名
        $modelArray = explode($this->DS, $this->modelFilename);

        $this->modelName = array_pop($modelArray);

        return $this;
    }

    /**
     * 构建字段
     * @return $this
     */
    protected function buildStructure(): static
    {
        foreach ($this->tableColumns as $key => $val) {

            // 排序
            if (in_array($key, ['sort'])) {
                $this->sortFields[] = $key;
            }

            // 富文本
            if (in_array($key, ['describe', 'content', 'details'])) {
                $this->editorFields[] = $key;
            }

        }
        return $this;
    }

    /**
     * 构建必填
     * @param $require
     * @return string
     */
    protected function buildRequiredHtml($require): string
    {
        return $require ? 'lay-verify="required"' : "";
    }

    /**
     * 构建初始化字段信息
     * @param $column
     * @return mixed
     */
    protected function buildColumn(&$column): mixed
    {

        $string = $column['comment'];

        $column['define'] = json_encode([1 => '系统自动生成A', 2 => '请自行修改B'], JSON_UNESCAPED_UNICODE);

        // 处理定义类型
        preg_match('/{[\s\S]*?}/i', $string, $formTypeMatch);
        if (!empty($formTypeMatch) && isset($formTypeMatch[0])) {
            $column['comment'] = str_replace($formTypeMatch[0], '', $column['comment']);
            $formType          = trim(str_replace('}', '', str_replace('{', '', $formTypeMatch[0])));
            $_formType         = $this->checkCommentFormType($formType);
            if ($_formType) {
                $column['formType'] = $_formType;
            }
        }

        // 处理默认定义
        preg_match('/\([\s\S]*?\)/i', $string, $defineMatch);
        if (!empty($formTypeMatch) && isset($defineMatch[0])) {
            $column['comment'] = str_replace($defineMatch[0], '', $column['comment']);
            if (isset($column['formType']) && in_array($column['formType'], ['images', 'files', 'select', 'switch', 'radio', 'checkbox', 'date'])) {
                $define = str_replace(')', '', str_replace('(', '', $defineMatch[0]));
                if (in_array($column['formType'], ['select', 'switch', 'radio', 'checkbox'])) {
                    $formatDefine = [];
                    $explodeArray = explode(',', $define);
                    foreach ($explodeArray as $vo) {
                        $voExplodeArray = explode(':', $vo);
                        if (count($voExplodeArray) == 2) {
                            $formatDefine[trim($voExplodeArray[0])] = trim($voExplodeArray[1]);
                        }
                    }
                    !empty($formatDefine) && $column['define'] = $formatDefine;
                }else {
                    $column['define'] = $define;
                }
            }
        }

        $column['comment'] = trim($column['comment']);

        return $column;
    }

    /**
     * 构建下拉控制器
     * @param $field
     * @return mixed
     */
    protected function buildSelectController($field): mixed
    {
        $field      = CommonTool::lineToHump(ucfirst($field));
        $name       = "get{$field}List";
        $selectCode = CommonTool::replaceTemplate(
            $this->getTemplate("controller{$this->DS}select"),
            [
                'name' => $name,
            ]);
        return $selectCode;
    }

    /**
     * 构架下拉模型
     * @param $field
     * @param $array
     * @return mixed
     */
    protected function buildSelectModel($field, $array): mixed
    {
        $field  = CommonTool::lineToHump(ucfirst($field));
        $name   = "get{$field}List";
        $values = '[';
        foreach ($array as $k => $v) {
            $values .= "'{$k}'=>'{$v}',";
        }
        $values     .= ']';
        $selectCode = CommonTool::replaceTemplate(
            $this->getTemplate("model{$this->DS}select"),
            [
                'name'   => $name,
                'values' => $values,
            ]);
        return $selectCode;
    }

    /**
     * 构架关联下拉模型
     * @param $relation
     * @param $filed
     * @return mixed
     */
    protected function buildRelationSelectModel($relation, $filed): mixed
    {
        $relationArray = explode('\\', $relation);
        $name          = end($relationArray);
        $name          = "get{$name}List";
        $selectCode    = CommonTool::replaceTemplate(
            $this->getTemplate("model{$this->DS}relationSelect"),
            [
                'name'     => $name,
                'relation' => $relation,
                'values'   => $filed,
            ]);
        return $selectCode;
    }

    /**
     * 构建下拉框视图
     * @param $field
     * @param string $select
     * @return mixed
     */
    protected function buildOptionView($field, string $select = ''): mixed
    {
        //        $field      = CommonTool::lineToHump(ucfirst($field));
        //        $name       = "get{$field}List";
        $optionCode = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}module{$this->DS}option"),
            [
                'name'   => "notes['$field']",
                'select' => $select,
            ]);
        return $optionCode;
    }

    /**
     * 构建单选框视图
     * @param $field
     * @param string $select
     * @return mixed
     */
    protected function buildRadioView($field, string $select = ''): mixed
    {
        //        $formatField = CommonTool::lineToHump(ucfirst($field));
        //        $name        = "get{$formatField}List";
        $optionCode = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}module{$this->DS}radioInput"),
            [
                'field'  => $field,
                'name'   => "notes['$field']",
                'select' => $select,
            ]);
        return $optionCode;
    }

    /**
     * 构建多选框视图
     * @param $field
     * @param string $select
     * @return mixed
     */
    protected function buildCheckboxView($field, string $select = ''): mixed
    {
        //        $formatField = CommonTool::lineToHump(ucfirst($field));
        //        $name        = "get{$formatField}List";
        $optionCode = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}module{$this->DS}checkboxInput"),
            [
                'field'  => $field,
                'name'   => "notes['$field']",
                'select' => $select,
            ]);
        return $optionCode;
    }

    /**
     * 初始化
     * @return $this
     */
    public function render(): static
    {

        // 初始化数据
        $this->renderData();

        // 控制器
        $this->renderController();

        // 模型
        $this->renderModel();

        // 视图
        $this->renderView();

        // JS
        $this->renderJs();

        return $this;
    }

    /**
     * 初始化数据
     * @return $this
     */
    protected function renderData(): static
    {

        // 主表
        foreach ($this->tableColumns as $field => $val) {

            // 过滤字段
            if (in_array($field, $this->ignoreFields)) {
                unset($this->tableColumns[$field]);
                continue;
            }

            $this->tableColumns[$field]['formType'] = $this->tableColumns[$field]['formType'] ?? 'text';

            // 判断图片
            if ($this->checkContain($field, $this->imageFieldSuffix)) {
                $this->tableColumns[$field]['formType'] = 'image';
                continue;
            }
            if ($this->checkContain($field, $this->imagesFieldSuffix)) {
                $this->tableColumns[$field]['formType'] = 'images';
                continue;
            }

            // 判断文件
            if ($this->checkContain($field, $this->fileFieldSuffix)) {
                $this->tableColumns[$field]['formType'] = 'file';
                continue;
            }
            if ($this->checkContain($field, $this->filesFieldSuffix)) {
                $this->tableColumns[$field]['formType'] = 'files';
                continue;
            }

            // 判断日期
            if ($this->checkContain($field, $this->dateFieldSuffix)) {
                $this->tableColumns[$field]['formType'] = 'date';
                continue;
            }

            // 判断日期时间
            if ($this->checkContain($field, $this->datetimeFieldSuffix)) {
                $this->tableColumns[$field]['formType'] = 'datetime';
                continue;
            }

            if (in_array($field, $this->radioFields) || $this->checkContain($field, $this->radioFieldSuffix)) {
                $this->tableColumns[$field]['formType'] = 'radio';
                continue;
            }

            if (in_array($field, $this->checkboxFields) || $this->checkContain($field, $this->checkboxFieldSuffix)) {
                $this->tableColumns[$field]['formType'] = 'checkbox';
                continue;
            }

            // 判断开关
            if (in_array($field, $this->switchFields)) {
                $this->tableColumns[$field]['formType'] = 'switch';
                continue;
            }


            // 判断富文本
            if (in_array($field, $this->editorFields) || in_array($val['type'], ['text', 'tinytext', 'mediumtext', 'longtext'])) {
                $this->tableColumns[$field]['formType'] = 'editor';
                continue;
            }

            // 判断排序
            if (in_array($field, $this->sortFields)) {
                $this->tableColumns[$field]['formType'] = 'sort';
                continue;
            }

            // 判断下拉选择
            if (in_array($field, $this->selectFields)) {
                $this->tableColumns[$field]['formType'] = 'select';
                continue;
            }

        }

        // 关联表
        foreach ($this->relationArray as $table => $tableVal) {
            foreach ($tableVal['tableColumns'] as $field => $val) {

                // 过滤字段
                if (in_array($field, $this->ignoreFields)) {
                    unset($this->relationArray[$table]['tableColumns'][$field]);
                    continue;
                }

                // 判断是否已初始化
                if (isset($this->relationArray[$table]['tableColumns'][$field]['formType'])) {
                    continue;
                }

                // 判断图片
                if ($this->checkContain($field, $this->imageFieldSuffix)) {
                    $this->relationArray[$table]['tableColumns'][$field]['formType'] = 'image';
                    continue;
                }
                if ($this->checkContain($field, $this->imagesFieldSuffix)) {
                    $this->relationArray[$table]['tableColumns'][$field]['formType'] = 'images';
                    continue;
                }

                // 判断文件
                if ($this->checkContain($field, $this->fileFieldSuffix)) {
                    $this->relationArray[$table]['tableColumns'][$field]['formType'] = 'file';
                    continue;
                }
                if ($this->checkContain($field, $this->filesFieldSuffix)) {
                    $this->relationArray[$table]['tableColumns'][$field]['formType'] = 'files';
                    continue;
                }

                // 判断时间
                if ($this->checkContain($field, $this->dateFieldSuffix)) {
                    $this->relationArray[$table]['tableColumns'][$field]['formType'] = 'date';
                    continue;
                }

                // 判断开关
                if (in_array($field, $this->switchFields)) {
                    $this->relationArray[$table]['tableColumns'][$field]['formType'] = 'switch';
                    continue;
                }

                // 判断富文本
                if (in_array($field, $this->editorFields) || in_array($val['type'], ['text', 'tinytext', 'mediumtext', 'longtext'])) {
                    $this->relationArray[$table]['tableColumns'][$field]['formType'] = 'editor';
                    continue;
                }

                // 判断排序
                if (in_array($field, $this->sortFields)) {
                    $this->relationArray[$table]['tableColumns'][$field]['formType'] = 'sort';
                    continue;
                }

                // 判断下拉选择
                if (in_array($field, $this->selectFields)) {
                    $this->relationArray[$table]['tableColumns'][$field]['formType'] = 'select';
                    continue;
                }

                $this->relationArray[$table]['tableColumns'][$field]['formType'] = 'text';
            }
        }

        return $this;

    }

    /**
     * 初始化控制器
     * @return $this
     */
    protected function renderController(): static
    {
        $controllerFile = "{$this->rootDir}{$this->DS}app{$this->DS}Http{$this->DS}Controllers{$this->DS}admin{$this->DS}{$this->controllerFilename}Controller.php";
        if (empty($this->relationArray)) {
            $controllerIndexMethod = '';
        }else {
            $relationCode = '';
            foreach ($this->relationArray as $key => $val) {
                $relation     = CommonTool::lineToHump($key);
                $relationCode = "->withJoin('{$relation}', 'LEFT')\r";
            }
            $controllerIndexMethod = CommonTool::replaceTemplate(
                $this->getTemplate("controller{$this->DS}indexMethod"),
                [
                    'relationIndexMethod' => $relationCode,
                ]);
        }
        $selectList = '';
        //        foreach ($this->relationArray as $relation) {
        //            if (!empty($relation['bindSelectField'])) {
        //                $relationArray = explode('\\', $relation['modelFilename']);
        //                $selectList    .= $this->buildSelectController(end($relationArray));
        //            }
        //        }

        //        foreach ($this->tableColumns as $field => $val) {
        //            if (isset($val['formType']) && in_array($val['formType'], ['select', 'switch', 'radio', 'checkbox']) && isset($val['define'])) {
        //                $selectList .= $this->buildSelectController($field);
        //            }
        //        }

        $modelFilenameExtend = str_replace($this->DS, '\\', $this->modelFilename);

        $controllerValue                 = CommonTool::replaceTemplate(
            $this->getTemplate("controller{$this->DS}controller"),
            [
                'controllerName'       => $this->controllerName,
                'controllerNamespace'  => $this->controllerNamespace,
                'controllerAnnotation' => $this->tableComment,
                'modelFilename'        => "\App\Models\\{$modelFilenameExtend}",
                'indexMethod'          => $controllerIndexMethod,
                'selectList'           => $selectList,
            ]);
        $this->fileList[$controllerFile] = $controllerValue;
        return $this;
    }

    /**
     * 初始化模型
     * @return $this
     */
    protected function renderModel(): static
    {
        // 主表模型
        $modelFile = "{$this->rootDir}{$this->DS}app{$this->DS}Models{$this->DS}{$this->modelFilename}.php";
        if (empty($this->relationArray)) {
            $relationList = '';
        }else {
            $relationList = '';
            foreach ($this->relationArray as $key => $val) {
                $relation     = CommonTool::lineToHump($key);
                $relationCode = CommonTool::replaceTemplate(
                    $this->getTemplate("model{$this->DS}relation"),
                    [
                        'relationMethod' => $relation,
                        'relationModel'  => "\App\Models\\{$val['modelFilename']}",
                        'foreignKey'     => $val['foreignKey'],
                        'primaryKey'     => $val['primaryKey'],
                    ]);
                $relationList .= $relationCode;
            }
        }

        $selectList = '';
        foreach ($this->relationArray as $relation) {
            if (!empty($relation['bindSelectField'])) {
                $selectList .= $this->buildRelationSelectModel($relation['modelFilename'], $relation['bindSelectField']);
            }
        }
        $selectArrays = [];
        foreach ($this->tableColumns as $field => $val) {
            if (isset($val['formType']) && in_array($val['formType'], ['select', 'switch', 'radio', 'checkbox']) && isset($val['define'])) {
                $selectArrays += [$field => is_array($val['define']) ? $val['define'] : json_decode($val['define'], true)];
            }
        }
        $extendNamespaceArray = explode($this->DS, $this->modelFilename);
        $extendNamespace      = null;
        if (count($extendNamespaceArray) > 1) {
            array_pop($extendNamespaceArray);
            $extendNamespace = '\\' . implode('\\', $extendNamespaceArray);
        }

        $samePrefix                 = $this->tablePrefix == config('database.connections.mysql.prefix');
        $modelTemplate              = $samePrefix ? 'model' : 'model_table';
        $modelValue                 = CommonTool::replaceTemplate(
            $this->getTemplate("model{$this->DS}{$modelTemplate}"),
            [
                'modelName'      => $this->modelName,
                'modelNamespace' => "App\Models{$extendNamespace}",
                'table'          => $this->table,
                'prefix_table'   => $samePrefix ? "" : $this->tablePrefix,
                'deleteTime'     => $this->delete ? '"delete_time"' : 'false',
                'softDelete'     => $this->delete ? '' : 'public static function bootSoftDeletes() {}',
                'relationList'   => $relationList,
                //                'selectList'     => $selectList,
                'selectArrays'   => CommonTool::replaceArrayString(var_export($selectArrays, true)),
            ]);
        $this->fileList[$modelFile] = $modelValue;

        // 关联模型
        foreach ($this->relationArray as $key => $val) {
            $relationModelFile = "{$this->rootDir}{$this->DS}app{$this->DS}admin{$this->DS}model{$this->DS}{$val['modelFilename']}.php";

            // todo 判断关联模型文件是否存在, 存在就不重新生成文件, 防止关联模型文件被覆盖
            $relationModelClass = "\\app\\admin\\model\\{$val['modelFilename']}";
            if (class_exists($relationModelClass) && method_exists(new $relationModelClass, 'getName')) {
                $tableName = (new $relationModelClass)->getName();
                if (CommonTool::humpToLine(lcfirst($tableName)) == CommonTool::humpToLine(lcfirst($key))) {
                    continue;
                }
            }

            $extendNamespaceArray = explode($this->DS, $val['modelFilename']);
            $extendNamespace      = null;
            if (count($extendNamespaceArray) > 1) {
                array_pop($extendNamespaceArray);
                $extendNamespace = '\\' . implode('\\', $extendNamespaceArray);
            }

            $relationModelValue                 = CommonTool::replaceTemplate(
                $this->getTemplate("model{$this->DS}model"),
                [
                    'modelName'      => $val['modelName'],
                    'modelNamespace' => "App\Models{$extendNamespace}",
                    'table'          => $key,
                    'deleteTime'     => $val['delete'] ? '"delete_time"' : 'false',
                    'relationList'   => '',
                    'selectList'     => '',
                ]);
            $this->fileList[$relationModelFile] = $relationModelValue;
        }
        return $this;
    }

    /**
     * 初始化视图
     * @return $this
     */
    protected function renderView(): static
    {
        // 列表页面
        $viewIndexFile                  = "{$this->rootDir}{$this->DS}resources{$this->DS}views{$this->DS}admin{$this->DS}{$this->viewFilename}{$this->DS}index.blade.php";
        $viewIndexValue                 = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}index"),
            [
                'controllerUrl' => $this->controllerUrl,
                'notesScript'   => $this->formatNotesScript(),
            ]);
        $this->fileList[$viewIndexFile] = $viewIndexValue;

        // 添加页面
        $viewAddFile = "{$this->rootDir}{$this->DS}resources{$this->DS}views{$this->DS}admin{$this->DS}{$this->viewFilename}{$this->DS}add.blade.php";
        $addFormList = '';

        foreach ($this->tableColumns as $field => $val) {

            if (in_array($field, ['id', 'create_time'])) {
                continue;
            }

            $templateFile = "view{$this->DS}module{$this->DS}input";
            $define       = '';

            // 根据formType去获取具体模板
            if ($val['formType'] == 'image') {
                $templateFile = "view{$this->DS}module{$this->DS}image";
            }elseif ($val['formType'] == 'images') {
                $templateFile = "view{$this->DS}module{$this->DS}images";
                $define       = $val['define'] ?? '|';
            }elseif ($val['formType'] == 'file') {
                $templateFile = "view{$this->DS}module{$this->DS}file";
            }elseif ($val['formType'] == 'files') {
                $templateFile = "view{$this->DS}module{$this->DS}files";
                $define       = $val['define'] ?? '|';
            }elseif ($val['formType'] == 'editor') {
                $templateFile   = "view{$this->DS}module{$this->DS}editor";
                $val['default'] = '""';
            }elseif ($val['formType'] == 'date') {
                $templateFile = "view{$this->DS}module{$this->DS}date";
                $define       = 'date';
            }elseif ($val['formType'] == 'datetime') {
                $templateFile = "view{$this->DS}module{$this->DS}date";
                $define       = 'datetime';
            }elseif ($val['formType'] == 'radio') {
                $templateFile = "view{$this->DS}module{$this->DS}radio";
                if (!empty($val['define'])) {
                    $define = $this->buildRadioView($field);
                }
            }elseif ($val['formType'] == 'checkbox') {
                $templateFile = "view{$this->DS}module{$this->DS}checkbox";
                if (!empty($val['define'])) {
                    $define = $this->buildCheckboxView($field);
                }
            }elseif ($val['formType'] == 'select') {
                $templateFile = "view{$this->DS}module{$this->DS}select";
                if (isset($val['bindRelation'])) {
                    $define = $this->buildOptionView($val['bindRelation']);
                }elseif (!empty($val['define'])) {
                    $define = $this->buildOptionView($field);
                }
            }elseif (in_array($field, ['remark']) || $val['formType'] == 'textarea') {
                $templateFile = "view{$this->DS}module{$this->DS}textarea";
            }

            $addFormList .= CommonTool::replaceTemplate(
                $this->getTemplate($templateFile),
                [
                    'comment'  => $val['comment'],
                    'field'    => $field,
                    'required' => $this->buildRequiredHtml($val['required']),
                    'value'    => $val['default'],
                    'define'   => $define,
                ]);
        }
        $viewAddValue                 = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}form"),
            [
                'formList' => $addFormList,
            ]);
        $this->fileList[$viewAddFile] = $viewAddValue;


        // 编辑页面
        $viewEditFile = "{$this->rootDir}{$this->DS}resources{$this->DS}views{$this->DS}admin{$this->DS}{$this->viewFilename}{$this->DS}edit.blade.php";
        $editFormList = '';
        foreach ($this->tableColumns as $field => $val) {

            if (in_array($field, ['id', 'create_time'])) {
                continue;
            }

            $templateFile = "view{$this->DS}module{$this->DS}input";
            $define       = '';
            $value        = '{{$row[\'' . $field . '\']}}';

            // 根据formType去获取具体模板
            if ($val['formType'] == 'image') {
                $templateFile = "view{$this->DS}module{$this->DS}image";
            }elseif ($val['formType'] == 'images') {
                $templateFile = "view{$this->DS}module{$this->DS}images";
            }elseif ($val['formType'] == 'file') {
                $templateFile = "view{$this->DS}module{$this->DS}file";
            }elseif ($val['formType'] == 'files') {
                $templateFile = "view{$this->DS}module{$this->DS}files";
            }elseif ($val['formType'] == 'editor') {
                $templateFile = "view{$this->DS}module{$this->DS}editor";
                $value        = '$row[\'' . $field . '\']';
            }elseif ($val['formType'] == 'date') {
                $templateFile = "view{$this->DS}module{$this->DS}date";
                $define       = 'date';
            }elseif ($val['formType'] == 'datetime') {
                $templateFile = "view{$this->DS}module{$this->DS}date";
                $define       = 'datetime';
            }elseif ($val['formType'] == 'radio') {
                $templateFile = "view{$this->DS}module{$this->DS}radio";
                if (!empty($val['define'])) {
                    $define = $this->buildRadioView($field, '@if($row["' . $field . '"]==$k)checked=""@endif');
                }
            }elseif ($val['formType'] == 'checkbox') {
                $templateFile = "view{$this->DS}module{$this->DS}checkbox";
                if (!empty($val['define'])) {
                    $define = $this->buildCheckboxView($field, '@if($row["' . $field . '"]==$k)checked=""@endif');
                }
            }elseif ($val['formType'] == 'select') {
                $templateFile = "view{$this->DS}module{$this->DS}select";
                if (isset($val['bindRelation'])) {
                    $define = $this->buildOptionView($val['bindRelation'], '@if($row["' . $field . '"]==$k)selected=""@endif');
                }elseif (!empty($val['define'])) {
                    $define = $this->buildOptionView($field, '@if($row["' . $field . '"]==$k)selected=""@endif');
                }
            }elseif (in_array($field, ['remark']) || $val['formType'] == 'textarea') {
                $templateFile = "view{$this->DS}module{$this->DS}textarea";
            }

            $editFormList .= CommonTool::replaceTemplate(
                $this->getTemplate($templateFile),
                [
                    'comment'  => $val['comment'],
                    'field'    => $field,
                    'required' => $this->buildRequiredHtml($val['required']),
                    'value'    => $value,
                    'define'   => $define,
                ]);
        }
        $viewEditValue                 = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}form"),
            [
                'formList' => $editFormList,
            ]);
        $this->fileList[$viewEditFile] = $viewEditValue;

        $viewRecycleFile                  = "{$this->rootDir}{$this->DS}resources{$this->DS}views{$this->DS}admin{$this->DS}{$this->viewFilename}{$this->DS}recycle.blade.php";
        $viewRecycleValue                 = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}recycle"),
            [
                'controllerUrl' => $this->controllerUrl,
                'notesScript'   => $this->formatNotesScript(),
            ]
        );
        $this->fileList[$viewRecycleFile] = $viewRecycleValue;
        return $this;
    }

    /**
     * 初始化JS
     * @return $this
     */
    protected function renderJs(): static
    {
        $jsFile = "{$this->rootDir}{$this->DS}public{$this->DS}static{$this->DS}admin{$this->DS}js{$this->DS}{$this->jsFilename}.js";

        $indexCols = "    {type: 'checkbox'},\r";

        // 主表字段
        foreach ($this->tableColumns as $field => $val) {

            if ($val['formType'] == 'image') {
                $templateValue = "{field: '{$field}', title: '{$val['comment']}', templet: ea.table.image}";
            }elseif ($val['formType'] == 'images') {
                continue;
            }elseif ($val['formType'] == 'file') {
                $templateValue = "{field: '{$field}', title: '{$val['comment']}', templet: ea.table.url}";
            }elseif ($val['formType'] == 'files') {
                continue;
            }elseif ($val['formType'] == 'editor') {
                continue;
            }elseif (in_array($field, $this->switchFields)) {
                if (!empty($val['define'])) {
                    $templateValue = "{field: '{$field}', search: 'select', selectList: notes?.{$field} || {}, title: '{$val['comment']}', templet: ea.table.switch}";
                }else {
                    $templateValue = "{field: '{$field}', title: '{$val['comment']}', templet: ea.table.switch}";
                }
            }elseif (in_array($val['formType'], ['select', 'checkbox', 'radio', 'switch'])) {
                if (!empty($val['define'])) {
                    $templateValue = "{field: '{$field}', search: 'select', selectList: notes?.{$field} || {}, title: '{$val['comment']}'}";
                }else {
                    $templateValue = "{field: '{$field}', title: '{$val['comment']}'}";
                }
            }elseif (in_array($field, ['remark'])) {
                $templateValue = "{field: '{$field}', title: '{$val['comment']}', templet: ea.table.text}";
            }elseif (in_array($field, $this->sortFields)) {
                $templateValue = "{field: '{$field}', title: '{$val['comment']}', edit: 'text'}";
            }else {
                $templateValue = "{field: '{$field}', title: '{$val['comment']}'}";
            }

            $indexCols .= $this->formatColsRow("{$templateValue},\r");
        }

        // 关联表
        foreach ($this->relationArray as $table => $tableVal) {
            $table = CommonTool::lineToHump($table);
            foreach ($tableVal['tableColumns'] as $field => $val) {
                if ($val['formType'] == 'image') {
                    $templateValue = "{field: '{$table}.{$field}', title: '{$val['comment']}', templet: ea.table.image}";
                }elseif ($val['formType'] == 'images') {
                    continue;
                }elseif ($val['formType'] == 'file') {
                    $templateValue = "{field: '{$table}.{$field}', title: '{$val['comment']}', templet: ea.table.url}";
                }elseif ($val['formType'] == 'files') {
                    continue;
                }elseif ($val['formType'] == 'editor') {
                    continue;
                }elseif ($val['formType'] == 'select') {
                    $templateValue = "{field: '{$table}.{$field}', title: '{$val['comment']}'}";
                }elseif (in_array($field, ['remark'])) {
                    $templateValue = "{field: '{$table}.{$field}', title: '{$val['comment']}', templet: ea.table.text}";
                }elseif (in_array($field, $this->switchFields)) {
                    $templateValue = "{field: '{$table}.{$field}', title: '{$val['comment']}', templet: ea.table.switch}";
                }elseif (in_array($field, $this->sortFields)) {
                    $templateValue = "{field: '{$table}.{$field}', title: '{$val['comment']}', edit: 'text'}";
                }else {
                    $templateValue = "{field: '{$table}.{$field}', title: '{$val['comment']}'}";
                }

                $indexCols .= $this->formatColsRow("{$templateValue},\r");
            }
        }

        $indexCols .= $this->formatColsRow("{width: 250, title: '操作', templet: ea.table.tool},\r");

        $jsValue                 = CommonTool::replaceTemplate(
            $this->getTemplate("static{$this->DS}js"),
            [
                'controllerUrl' => $this->controllerUrl,
                'indexCols'     => $indexCols,
            ]);
        $this->fileList[$jsFile] = $jsValue;

        $recycleCols = $indexCols;
        $indexCols   .= $this->formatColsRow("{width: 250, title: '操作', templet: ea.table.tool},\r");
        $jsValue     = CommonTool::replaceTemplate(
            $this->getTemplate("static{$this->DS}js"),
            [
                'controllerUrl' => $this->controllerUrl,
                'indexCols'     => $indexCols,
                'recycleCols'   => $recycleCols,
            ]
        );
        $this->fileList[$jsFile] = $jsValue;
        return $this;
    }

    /**
     * 检测文件
     * @return $this
     * @throws FileException
     */
    protected function check(): static
    {
        // 是否强制性
        if ($this->force) {
            return $this;
        }
        foreach ($this->fileList as $key => $val) {
            if (is_file($key)) {
                throw new FileException("文件已存在：{$key}");
            }
        }
        return $this;
    }

    /**
     * 开始生成
     * @return array
     * @throws FileException
     */
    public function create(): array
    {
        $this->check();
        foreach ($this->fileList as $key => $val) {

            // 判断文件夹是否存在,不存在就创建
            $fileArray = explode($this->DS, $key);
            array_pop($fileArray);
            $fileDir = implode($this->DS, $fileArray);
            if (!is_dir($fileDir)) {
                mkdir($fileDir, 0775, true);
            }

            // 写入
            file_put_contents($key, $val);
        }
        return array_keys($this->fileList);
    }

    /**
     * 开始删除
     * @return array
     */
    public function delete(): array
    {
        $deleteFile = [];
        foreach ($this->fileList as $key => $val) {
            if (is_file($key)) {
                unlink($key);
                $deleteFile[] = $key;
            }
        }
        return $deleteFile;
    }

    /**
     * 检测字段后缀
     * @param $string
     * @param $array
     * @return bool
     */
    protected function checkContain($string, $array): bool
    {
        foreach ($array as $vo) {
            if (str_starts_with($vo, $string)) {
                return true;
            }
            if (str_ends_with($vo, $string)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 格式化表单行
     * @param $value
     * @return string
     */
    protected function formatColsRow($value): string
    {
        return "                    {$value}";
    }

    /**
     * 获取对应的模板信息
     * @param $name
     * @return false|string
     */
    protected function getTemplate($name): bool|string
    {
        return file_get_contents("{$this->dir}{$this->DS}templates{$this->DS}{$name}.code");
    }

    /**
     * 检测字段注释归类的类型
     * @param string $formType
     * @return string|null
     */
    protected function checkCommentFormType(string $formType = ''): ?string
    {
        $classProperties = get_class_vars(get_class($this));
        foreach ($classProperties as $property => $classProperty) {
            if (empty($property)) continue;
            if (str_ends_with($property, 'FieldSuffix')) {
                if (in_array($formType, $this->$property)) {
                    return $this->$property[0] ?? '';
                }
            }
        }
        return '';
    }

    protected function formatNotesScript(): string
    {
        return '    let notes = JSON.parse(\'{!! json_encode($notes,256) !!}\');';
    }
}
