<?php

namespace App\Http\Controllers\admin\mall;

use App\Http\Controllers\common\AdminController;
use App\Http\Services\annotation\MiddlewareAnnotation;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\annotation\ControllerAnnotation;
use App\Models\MallCate;
use App\Models\MallGoods;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Wolfcode\Ai\Enum\AiType;
use Wolfcode\Ai\Service\AiChatService;

#[ControllerAnnotation(title: 'Mall Product Management')]
class GoodsController extends AdminController
{
    #[NodeAnnotation(ignore: ['export'])] // 过滤不需要生成的权限节点 默认 CURD 中会自动生成部分节点 可以在此处过滤
    protected array $ignoreNode;

    public function initialize()
    {
        parent::initialize();
        $this->model = new MallGoods();
        $cate        = (new MallCate())->pluck('title', 'id')->toArray();
        $this->assign(compact('cate'));
    }

    #[NodeAnnotation(title: 'list', auth: true)]
    public function index(): View|JsonResponse
    {
        if (!request()->ajax()) return $this->fetch();
        list($page, $limit, $where) = $this->buildTableParams();
        $count = $this->model->where($where)->count();
        $list  = $this->model->where($where)->with(['cate'])->orderBy($this->order, $this->orderDirection)->paginate($limit)->items();
        $data  = [
            'code'  => 0,
            'msg'   => '',
            'count' => $count,
            'data'  => $list,
        ];
        return json($data);
    }

    #[NodeAnnotation(title: 'stock', auth: true)]
    public function stock(): View|JsonResponse
    {
        $id  = request()->input('id');
        $row = $this->model->find($id);
        if (empty($row)) return $this->error(ea_trans('data does not exist', false));
        if (request()->ajax()) {
            $post = request()->post();
            try {
                $params['total_stock'] = $row->total_stock + $post['stock'];
                $params['stock']       = $row->stock + $post['stock'];
                $save                  = updateFields($this->model, $row, $params);
            }catch (\Exception $e) {
                return $this->error(ea_trans('operation failed', false));
            }
            return $save ? $this->success(ea_trans('operation successful', false)) : $this->error(ea_trans('operation failed', false));
        }
        $this->assign(compact('row'));
        return $this->fetch();
    }

    #[MiddlewareAnnotation(ignore: MiddlewareAnnotation::IGNORE_LOGIN)]
    public function no_check_login(): string
    {
        return '这里演示方法不需要经过登录验证';
    }


    #[NodeAnnotation(title: 'AI', auth: true)]
    public function aiOptimization(): View|JsonResponse
    {
        $message = request()->post('message');
        if (empty($message)) return $this->error('message cannot be empty');

        // 演示环境下 默认返回的内容
        if ($this->isDemo) {
            $content = <<<EOF
演示环境中 默认返回的内容

我来帮你优化这个标题，让它更有吸引力且更符合电商平台的搜索逻辑:

"商务男士高端定制马克杯 | 办公室精英必备 | 优质陶瓷防烫手柄"

这个优化后的标题:
1. 突出了目标用户群体(商务男士)
2. 强调了产品定位(高端定制)
3. 点明了使用场景(办公室)
4. 添加了材质和功能特点(优质陶瓷、防烫手柄)
5. 使用了吸引人的关键词(精英必备)

这样的标题不仅更具体，也更容易被搜索引擎识别，同时能精准触达目标客户群。您觉得这个版本如何?
EOF;
            $choices = [['message' => [
                'role'    => 'assistant',
                'content' => $content,
            ]]];
            return $this->success('success', compact('choices'));
        }

        try {
            $result  = AiChatService::instance()
                // 当使用推理模型时，可能存在超时的情况，所以需要设置超时时间为 0
                // ->setTimeLimit(0)
                // 请替换为您需要的模型类型
                ->setAiType(AiType::QWEN)
                // 如果需要指定模型的 API 地址，可自行设置
                // ->setAiUrl('https://xxx.com')
                // 请替换为您的模型
                ->setAiModel('qwen-plus')
                // 请替换为您的 API KEY
                ->setAiKey('sk-1234567890')
                // 此内容会作为系统提示，会影响到回答的内容 当前仅作为测试使用
                ->setSystemContent('你现在是一位资深的海外电商产品经理')
                ->chat($message);
            $choices = $result['choices'];
        }catch (\Throwable $exception) {
            $choices = [['message' => [
                'role'    => 'assistant',
                'content' => $exception->getMessage(),
            ]]];
        }
        return $this->success('success', compact('choices'));
    }

}
