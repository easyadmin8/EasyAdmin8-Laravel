<?php

namespace App\Console\Commands;

use App\Http\Services\curd\BuildCurd;
use App\Http\Services\console\CliEcho;
use Illuminate\Console\Command;

class Curd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:curd {table  : for table name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '后台一键生成 CURD';

    /**
     * Execute the console command.
     */
    public function handle(): bool
    {
        $table = $this->argument('table');
        $build = (new BuildCurd())->setTable($table);
        $build->render();
        $fileList = $build->getFileList();

        $this->info(">>>>>>>>>>>>>>>");
        $build->create();
        foreach ($fileList as $key => $val) {
            $this->info($key);
        }
        $this->info(">>>>>>>>>>>>>>>");
        CliEcho::success('自动生成CURD成功');
        return true;
    }
}
