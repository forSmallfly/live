<?php

namespace app;

use app\exceptions\EmptyControllerException;
use app\exceptions\MissRouteException;
use app\exceptions\ParamValidateException;
use app\exceptions\UnauthorizedException;
use app\exceptions\UnauthorizedOperationException;
use app\utils\ResponseTool;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    use ResponseTool;

    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用自定义的方式记录异常日志
        if (!$this->isIgnoreReport($exception)) {
            // 收集异常数据
            $data = [
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'message' => $this->getMessage($exception),
                'code'    => $this->getCode($exception),
            ];
            $log  = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]";

            if ($this->app->config->get('log.record_trace')) {
                $log .= PHP_EOL . $exception->getTraceAsString();
            }

            try {
                $this->app->log->record($log, 'error');
            } catch (Throwable) {
            }
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(\think\Request $request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        if (!$this->app->isDebug()) {
            // 添加自定义异常处理机制
            if ($e instanceof EmptyControllerException
                || $e instanceof MissRouteException
                || $e instanceof ParamValidateException
                || $e instanceof UnauthorizedException
                || $e instanceof UnauthorizedOperationException
            ) {
                return $this->fail($e->getCode(), $e->getMessage());
            }

            return $this->fail(500, 'internal system error');
        }

        return $this->fail($e->getCode(), $e->getMessage());
    }
}
