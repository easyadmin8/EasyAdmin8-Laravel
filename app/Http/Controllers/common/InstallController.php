<?php

namespace App\Http\Controllers\common;

use App\Http\JumpTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InstallController extends Controller
{
    use JumpTrait;

    public function index(Request $request): View|JsonResponse
    {
        $isInstall   = false;
        $installPath = config_path() . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR;
        $errorInfo   = null;
        if (is_file($installPath . DIRECTORY_SEPARATOR . 'lock' . DIRECTORY_SEPARATOR . 'install.lock')) {
            $isInstall = true;
            $errorInfo = '已安装系统，如需重新安装请删除文件：/config/install/lock/install.lock，或者删除 /install 路由';
        }elseif (version_compare(phpversion(), '8.1.0', '<')) {
            $errorInfo = 'PHP版本不能小于8.1.0';
        }elseif (!extension_loaded("PDO")) {
            $errorInfo = '当前未开启PDO，无法进行安装';
        }
        if (!$request->ajax()) {
            $envInfo     = [
                'DB_HOST'   => $isInstall ? '' : env('DB_HOST', '127.0.0.1'),
                'DB_NAME'   => $isInstall ? '' : env('DB_DATABASE', 'easyadmin8_laravel'),
                'DB_USER'   => $isInstall ? '' : env('DB_USERNAME', 'root'),
                'DB_PASS'   => $isInstall ? '' : env('DB_PASSWORD', 'root'),
                'DB_PORT'   => $isInstall ? '' : env('DB_PORT', 3306),
                'DB_PREFIX' => $isInstall ? '' : env('DB_PREFIX', 'ea8_'),
            ];
            $currentHost = ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/';
            $result      = compact('errorInfo', 'currentHost', 'isInstall', 'envInfo');
            return view('install', $result);
        }
        if ($errorInfo) return $this->error($errorInfo);
        $envFile = base_path() . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($envFile)) return $this->error('.env 配置文件不存在');
        $charset = 'utf8mb4';

        $post       = $request->post();
        $cover      = $post['cover'] == 1;
        $database   = $post['database'];
        $hostname   = $post['hostname'];
        $hostport   = $post['hostport'];
        $dbUsername = $post['db_username'];
        $dbPassword = $post['db_password'];
        $prefix     = $post['prefix'];
        $adminUrl   = $post['admin_url'];
        $username   = $post['username'];
        $password   = $post['password'];
        // 参数验证
        $validateError = null;
        // 判断是否有特殊字符
        $check = preg_match('/[0-9a-zA-Z]+$/', $adminUrl, $matches);
        if (!$check) {
            $validateError = '后台地址不能含有特殊字符, 只能包含字母或数字。';
            return $this->error($validateError);
        }

        if (strlen($adminUrl) < 2) {
            $validateError = '后台的地址不能小于2位数';
        }elseif (strlen($password) < 5) {
            $validateError = '管理员密码不能小于5位数';
        }elseif (strlen($username) < 4) {
            $validateError = '管理员账号不能小于4位数';
        }
        if (!empty($validateError)) return $this->error($validateError);
        $config = [
            "driver"   => 'mysql',
            "host"     => $hostname,
            "database" => $database,
            "port"     => $hostport,
            "username" => $dbUsername,
            "password" => $dbPassword,
            "prefix"   => $prefix,
            "charset"  => $charset,
        ];
        try {
            Config::set("database.connections.mysql", $config);
        }catch (\Throwable|Exception $exception) {
            return $this->error($exception->getMessage());
        }
        // 检测数据库连接
        $this->checkConnect($config);
        // 检测数据库是否存在
        if (!$cover && $this->checkDatabase($database)) return $this->error('数据库已存在，请选择覆盖安装或者修改数据库名');
        // 创建数据库
        $this->createDatabase($database, $config);

        // 导入sql语句等等
        $this->install($username, $password, array_merge($config, ['database' => $database]), $adminUrl);
        return $this->success('系统安装成功，正在跳转登录页面');
    }

    protected function install($username, $password, $config): bool|string
    {
        $installPath = config_path() . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR;
        $sqlPath     = file_get_contents($installPath . 'sql' . DIRECTORY_SEPARATOR . 'install.sql');
        $sqlArray    = $this->parseSql($sqlPath, $config['prefix'], 'ea_');
        try {
            foreach ($sqlArray as $vo) {
                DB::statement($vo);
            }
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $tableName      = 'system_admin';
            $update         = [
                'username' => $username, 'head_img' => '/static/admin/images/head.jpg', 'password' => $hashedPassword, 'create_time' => time(), 'update_time' => time()
            ];
            DB::table($tableName)->where('id', 1)->update($update);
            //  处理安装文件
            !is_dir($installPath) && @mkdir($installPath);
            !is_dir($installPath . 'lock' . DIRECTORY_SEPARATOR) && @mkdir($installPath . 'lock' . DIRECTORY_SEPARATOR);
            @file_put_contents($installPath . 'lock' . DIRECTORY_SEPARATOR . 'install.lock', date('Y-m-d H:i:s'));
        }catch (\Exception $e) {
            $data = [
                'code' => 0,
                'msg'  => "系统安装失败：" . $e->getMessage(),
            ];
            die(json_encode($data));
        }
        return true;
    }

    protected function parseSql($sql = '', $to = '', $from = ''): array
    {
        list($pure_sql, $comment) = [[], false];
        $sql = explode("\n", trim(str_replace(["\r\n", "\r"], "\n", $sql)));
        foreach ($sql as $key => $line) {
            if ($line == '') {
                continue;
            }
            if (preg_match("/^(#|--)/", $line)) {
                continue;
            }
            if (preg_match("/^\/\*(.*?)\*\//", $line)) {
                continue;
            }
            if (str_starts_with($line, '/*')) {
                $comment = true;
                continue;
            }
            if (str_ends_with($line, '*/')) {
                $comment = false;
                continue;
            }
            if ($comment) {
                continue;
            }
            if ($from != '') {
                $line = str_replace('`' . $from, '`' . $to, $line);
            }
            if ($line == 'BEGIN;' || $line == 'COMMIT;') {
                continue;
            }
            array_push($pure_sql, $line);
        }
        //$pure_sql = implode($pure_sql, "\n");
        $pure_sql = implode("\n", $pure_sql);
        return explode(";\n", $pure_sql);
    }

    protected function createDatabase($database, $config): bool
    {
        $dsn = $this->pdoDsn($config);
        try {
            $pdo = new \PDO($dsn, $config['username'] ?? 'root', $config['password'] ?? '');
            $pdo->query("CREATE DATABASE IF NOT EXISTS `{$database}` DEFAULT CHARACTER SET {$config['charset']} COLLATE=utf8mb4_general_ci");
        }catch (\PDOException $e) {
            return false;
        }
        return true;
    }

    protected function checkDatabase($database): bool
    {
        try {
            $check = DB::select("SELECT * FROM information_schema.schemata WHERE schema_name='{$database}'");
        }catch (\Throwable $exception) {
            $check = false;
        }
        if (empty($check)) {
            return false;
        }else {
            return true;
        }
    }

    protected function checkConnect(array $config): bool
    {
        $dsn = $this->pdoDsn($config);
        try {
            $pdo      = new \PDO($dsn, $config['username'] ?? 'root', $config['password'] ?? '');
            $res      = $pdo->query('select VERSION()');
            $_version = $res->fetch()[0] ?? 0;
            if (version_compare($_version, '5.7.0', '<')) {
                $data = [
                    'code' => 0,
                    'msg'  => 'mysql版本最低要求 5.7.x',
                ];
                die(json_encode($data));
            }
        }catch (\PDOException $e) {
            $data = [
                'code' => 0,
                'msg'  => $e->getMessage()
            ];
            die(json_encode($data));
        }
        return true;
    }

    /**
     * @param array $config
     * @param bool $needDatabase
     * @return string
     */
    protected function pdoDsn(array $config, bool $needDatabase = false): string
    {
        $host     = $config['host'] ?? '127.0.0.1';
        $database = $config['database'] ?? '';
        $port     = $config['port'] ?? '3306';
        $charset  = $config['charset'] ?? 'utf8mb4';
        if ($needDatabase) return "mysql:host=$host;port=$port;dbname=$database;charset=$charset";
        return "mysql:host=$host;port=$port;charset=$charset";
    }
}
