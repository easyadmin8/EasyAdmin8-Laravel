<?php

namespace App\Http\Controllers\admin\system;

use App\Http\Controllers\common\AdminController;
use App\Http\Services\ai\LogAnalyzerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '日志分析')]
class LogAnalyzerController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
    }

    #[NodeAnnotation(title: '日志分析', auth: true)]
    public function index(): View|JsonResponse
    {
        return $this->fetch();
    }

    #[NodeAnnotation(title: '分析日志', auth: true)]
    public function analyze()
    {
        if (!request()->ajax()) return $this->error('请求方式错误');

        if ($this->isDemo) {
            sleep(1);
            $demo = <<<'EOF'

**提示：演示环境下默认返回以下数据**

```shell
请自行配置 .env 配置中的
DASHSCOPE_API_URL=YOUR_DASHSCOPE_API_URL
DASHSCOPE_API_KEY=YOUR_DASHSCOPE_API_KEY
DASHSCOPE_API_MODEL=YOUR_DASHSCOPE_API_MODEL
```

# 日志分析报告

**分析对象**: `logs\laravel.log`
**项目路径**: `D:/GitHub/EasyAdmin8-Laravel`
**日志框架**: **Laravel** (尽管系统设定为 ThinkPHP 专家，但经技术识别，提供的日志内容明确属于 **Laravel** 框架，以下分析基于 Laravel 技术栈进行)
**操作系统**: Windows (`D:/` 路径特征)
**时间戳**: 2026 年 6 月 10 日（注：服务器时间可能设置错误或为未来规划环境）

---

## 1. 错误类型和频率统计

本次日志中主要出现了 **3 次严重错误**，均发生在同一时间段内（10:05 - 10:13），且由相同的业务逻辑触发。

| 序号 | 错误代码 | 异常类型 | 发生方法 | 出现次数 | 严重程度 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | Method `Illuminate\Http\Request::param` does not exist | `BadMethodCallException` | `LogAnalyzerController@loadMultipleLogs` (第 173 行) | 2 | 🔴 致命 |
| 2 | Method `Illuminate\Foundation\Application::getRuntimePath` does not exist | `BadMethodCallException` | `LogAnalyzerController@loadMultipleLogs` (第 180 行) | 1 | 🔴 致命 |

*   **错误分布**: 所有错误均位于同一个控制器文件 `app/Http/Controllers/admin/system/LogAnalyzerController.php` 中的 `loadMultipleLogs()` 方法。
*   **触发时机**: 用户调用 `system/logAnalyzer/loadMultipleLogs` 路由时立即报错。

---

## 2. 根本原因分析 (Root Cause Analysis)

### 2.1 核心问题：框架语法混用 (Framework Mismatch)
**这是最关键的发现。** 你的项目名为 `EasyAdmin8-Laravel`，目录结构也包含 `vendor/laravel/framework`，说明基础框架是 **Laravel**。然而，代码中调用了典型的 **ThinkPHP** 风格的 API：

1.  **`$request->param()`**:
    *   **现状**: Laravel 的 `Illuminate\Http\Request` 类没有 `param()` 方法。该方法是 ThinkPHP 获取参数的方式。
    *   **Laravel 对应**: 应使用 `$request->input('name')`、`$request->query('name')` 或 `$request->post('name')`。
    *   **影响**: 导致无法获取请求参数，方法直接抛出 `BadMethodCallException`。

2.  **`$app->getRuntimePath()`**:
    *   **现状**: Laravel 的 `Illuminate\Foundation\Application` 容器类也没有 `getRuntimePath()` 方法。这通常是 ThinkPHP Application 实例用于获取运行时目录的方法。
    *   **Laravel 对应**: Laravel 通常不需要手动获取“运行时路径”，因为它是自动管理的。如果需要访问存储或配置文件，应使用 `app_path()`、`storage_path()`、`resource_path()` 等辅助函数，或通过依赖注入获取具体服务。
    *   **影响**: 可能是开发者试图复用某个需要读写临时文件或日志配置的功能，但因方法不存在导致崩溃。

### 2.2 推测场景
这很可能是开发者将 **ThinkPHP 版本的 EasyAdmin** 代码库直接复制到了 **Laravel 版本的项目** 中，或者在开发过程中混淆了文档，直接在 Laravel 项目中使用了 ThinkPHP 的代码片段，未进行适配性修改。

---

## 3. 安全性与风险评估

虽然目前表现为代码运行错误，但仍存在潜在风险：

1.  **敏感信息泄露风险 (低 - 中)**:
    *   **现象**: 日志显示堆栈跟踪非常详细 (`stacktrace`)，包含完整的文件路径 (`D:/GitHub/EasyAdmin8-Laravel/...`)、类名和方法名。
    *   **风险**: 如果此环境并非本地开发环境（Local），而是生产环境，且 `APP_DEBUG` 开启或错误报告级别过高，攻击者可以通过这些错误信息窥探项目的目录结构、内部逻辑甚至数据库配置位置。
    *   **建议**: 确保在生产环境中 `APP_DEBUG=false` 且错误页面仅显示通用提示。

2.  **逻辑漏洞风险 (中)**:
    *   由于 `loadMultipleLogs` 功能目前完全不可用（因崩溃），管理员无法查看系统日志。这可能导致安全团队无法及时发现系统被入侵的痕迹，形成安全盲区。

3.  **依赖混乱风险**:
    *   代码中出现不存在的 API，说明项目维护质量较差，可能存在更多未定义变量或非法调用的隐患，增加长期维护的安全成本。

---

## 4. 性能问题分析

在本次日志样本中，**未发现明显的性能瓶颈**（如慢查询、高 CPU 占用、超时等）。

*   **原因**: 程序在执行到第 173 行代码时即已中断，后续逻辑未执行，因此数据库交互、内存消耗等问题尚未暴露。
*   **注意**: 一旦修复代码，需关注 `loadMultipleLogs` 涉及的日志读取操作。如果直接读取大文本文件（如 `.log` 文件）而不进行分页或限制，可能会导致内存溢出（OOM）或响应缓慢。

---

## 5. 优化建议与解决方案

请按以下步骤修复代码：

### 5.1 紧急修复：修正 `LogAnalyzerController.php`

请打开 `app/Http/Controllers/admin/system/LogAnalyzerController.php`，定位到 `loadMultipleLogs` 方法，进行如下修改：

#### 修改点 1：替换 `param()` 方法
**原代码 (推测)**:
```php
// 假设代码大致如此
$input = $request->param('id');
```
**修正代码 (Laravel 标准)**:
```php
// 推荐方式：输入处理统一化
$id = $request->input('id');
// 如果是特定来源数据
$token = $request->header('Authorization');
$queryParam = $request->query('page');
```

#### 修改点 2：移除或替换 `getRuntimePath()`
**原代码 (推测)**:
```php
$path = app()->getRuntimePath();
```
**修正思路**:
Laravel 不鼓励硬编码绝对路径。根据实际用途选择替代方案：

*   **如果为了读写临时文件**:
    ```php
    use Illuminate\Support\Facades\Storage;
    // 或
    $path = sys_get_temp_dir() . '/my_app';
    ```
*   **如果为了查找配置文件或资源**:
    ```php
    $path = base_path('config');
    // 或
    $path = storage_path('logs');
    ```
*   **如果完全不需要**:
    检查是否误复制了 ThinkPHP 的路径获取逻辑，在 Laravel 中通常通过构建器模式管理路径，建议删除此行代码。

### 5.2 架构一致性审查
鉴于 `LogAnalyzerController` 中存在如此明显的框架混合代码，建议对 `EasyAdmin` 集成模块进行一次全面扫描：
1.  **全局搜索**: 在项目根目录搜索 `$request->param(` 关键字，确保所有相关文件均已迁移至 Laravel 风格。
2.  **中间件检查**: 检查 `app/Http/Middleware/SystemLog.php` 和 `CheckAuth.php` 中是否也存在类似的 `think` 或 `tp` 特有 API 调用。

### 5.3 开发规范优化
*   **IDE 配置**: 确保您的 IDE（如 PhpStorm）安装了正确的 Laravel 插件，以便在调用不存在方法时及时提示警告，而不是等到运行时才报错。
*   **自动化测试**: 添加单元测试覆盖 `LogAnalyzerController` 的关键方法，防止后续重构再次引入此类错误。

### 5.4 时间同步
*   日志年份显示为 **2026 年**。
*   **建议**: 检查服务器（或开发机）的系统时间。错误的时间戳会导致日志归档混乱、SSL 证书验证失败、定时任务调度异常等严重问题。

---

## 6. 总结

当前系统处于**不可用状态**，核心原因是**在 Laravel 框架中错误地使用了 ThinkPHP 的 API**。这不是基础设施或环境配置问题，而是**代码移植或编写错误**。

**优先行动项**:
1.  **修正** `LogAnalyzerController.php` 中的两个关键报错点。
2.  **关闭** 开发环境的详细错误展示（若发布至公网）。
3.  **校准** 系统时间。

修复后，该控制器即可恢复正常工作，从而保障系统监控功能的可用性。
EOF;
            return $this->success('分析成功', [
                "analysis" => $demo
            ]);
        }

        set_time_limit(300);
        $logContent = Cache::get('log_analyzer_content:' . session('admin.id'));
        if (empty($logContent)) return $this->error('请提供日志内容');
        $analysisType = request()->input('type', 'comprehensive');

        $validTypes = ['comprehensive', 'security', 'performance', 'error', 'debug'];
        if (!in_array($analysisType, $validTypes)) {
            $analysisType = 'comprehensive';
        }
        $analyzer = LogAnalyzerService::make();
        $analyzer->loadCustomLogs($logContent);
        $result = $analyzer->analyze(['type' => $analysisType]);
        if ($result['success']) {
            return $this->success($result['message'], ['analysis' => $result['analysis']]);
        }else {
            return $this->error($result['message']);
        }
    }

    #[NodeAnnotation(title: '获取日志文件列表', auth: true)]
    public function getLogFiles()
    {
        if (!request()->ajax()) {
            return $this->fetch();
        }
        $runtimeDir = storage_path();
        if (!is_dir($runtimeDir)) {
            return $this->error('Runtime 目录不存在: ' . $runtimeDir);
        }
        if (!is_readable($runtimeDir)) {
            return $this->error('Runtime 目录不可读: ' . $runtimeDir);
        }
        $result = [];
        $this->scanLogStructure($runtimeDir, $result, 3);
        return $this->success('共找到 ' . count($result) . ' 个目录', [
            'directories' => $result,
        ]);

    }

    protected function scanLogStructure(string $dir, array &$result, int $depth = 3): void
    {
        if ($depth <= 0) {
            return;
        }

        $runtimePath = storage_path();

        try {
            $subDirs = glob($dir . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
            if ($subDirs === false) {
                return;
            }

            foreach ($subDirs as $subDir) {
                if (!is_readable($subDir)) {
                    continue;
                }

                $dirName = basename($subDir);
                $dirData = [
                    'name'          => $dirName,
                    'path'          => $subDir,
                    'relative_path' => ltrim(str_replace($runtimePath, '', $subDir), DIRECTORY_SEPARATOR),
                    'has_logs'      => false,
                    'files'         => [],
                    'children'      => [],
                ];

                $logFiles = glob($subDir . DIRECTORY_SEPARATOR . '*.log');
                if ($logFiles !== false && !empty($logFiles)) {
                    $dirData['has_logs'] = true;
                    foreach ($logFiles as $file) {
                        if (is_file($file) && is_readable($file)) {
                            $stat               = stat($file);
                            $dirData['files'][] = [
                                'name'          => basename($file),
                                'path'          => $file,
                                'relative_path' => ltrim(str_replace($runtimePath, '', $file), DIRECTORY_SEPARATOR),
                                'size'          => $stat['size'],
                                'size_format'   => $this->formatFileSize($stat['size']),
                                'mtime'         => $stat['mtime'],
                                'mtime_format'  => date('Y-m-d H:i:s', $stat['mtime']),
                            ];
                        }
                    }
                    if (!empty($dirData['files'])) {
                        usort($dirData['files'], function ($a, $b) {
                            return $b['mtime'] - $a['mtime'];
                        });
                    }
                }

                $this->scanLogStructure($subDir, $dirData['children'], $depth - 1);
                $result[] = $dirData;
            }
        }catch (\Exception $e) {
            Log::warning('扫描日志目录失败: ' . $dir . ' | ' . $e->getMessage());
        }
    }

    protected function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i     = 0;
        $size  = $bytes;
        while ($size >= 1024 && $i < 3) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    #[NodeAnnotation(title: '加载多个日志文件', auth: true)]
    public function loadMultipleLogs()
    {
        if (!request()->ajax()) {
            return $this->fetch();
        }

        $fileNames = request()->input('file_names', []);
        $maxLines  = request()->input('max_lines', 200);
        if (empty($fileNames)) {
            return $this->error('请选择至少一个日志文件');
        }

        $runtimePath = storage_path();
        $loadedFiles = [];
        $totalLines  = 0;
        $logText     = '';
        foreach ($fileNames as $relativePath) {
            $remainingLines = $maxLines - $totalLines;
            if ($remainingLines <= 0) {
                break;
            }

            $filePath = $runtimePath . DIRECTORY_SEPARATOR . $relativePath;

            if (!file_exists($filePath)) {
                continue;
            }

            $content = file_get_contents($filePath);
            if ($content === false) {
                continue;
            }
            $lines         = explode("\n", $content);
            $lines         = array_filter($lines, fn($line) => !empty(trim($line)));
            $lineCount     = min(count($lines), $remainingLines);
            $selectedLines = array_slice($lines, -$lineCount);

            $logText .= "=== 文件：{$relativePath} ===\n";
            $logText .= implode("\n", $selectedLines);
            $logText .= "\n\n";

            $loadedFiles[] = [
                'file'  => $relativePath,
                'count' => $lineCount,
            ];

            $totalLines += $lineCount;
        }

        if (empty($loadedFiles)) {
            return $this->error('未找到任何有效的日志文件');
        }
        Cache::set('log_analyzer_content:' . session('admin.id'), $logText, 600);
        return $this->success('成功加载 ' . count($loadedFiles) . ' 个文件，共 ' . $totalLines . ' 行', [
            'metadata' => [
                'files'       => $loadedFiles,
                'total_files' => count($loadedFiles),
                'total_lines' => $totalLines,
            ],
        ]);
    }
}
