<?php

declare(strict_types=1);

namespace VsWest\Framework\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use VsWest\Framework\Constants\ErrorCode;
use VsWest\Framework\Exception\CommonException;
use Throwable;

/**
 * 常规错误信息返回.
 */
class CommonExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function handle(Throwable $throwable, \Psr\Http\Message\ResponseInterface $response)
    {
        ## 记录日志
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());

        ## 格式化输出
        $data     = responseDataFormat($throwable->getCode(), $throwable->getMessage());
        $httpCode = ErrorCode::getHttpCode($data['code']);
        if (! $httpCode && class_exists(\App\Constants\AppErrCode::class)) {
            $httpCode = \App\Constants\AppErrCode::getHttpCode($data['code']);
        }
        $dataStream = new SwooleStream(json_encode($data, JSON_UNESCAPED_UNICODE));

        ## 阻止异常冒泡
        $this->stopPropagation();
        return $response->withHeader('Server', 'marryandjoy')
            ->withAddedHeader('Content-Type', 'application/json;charset=utf-8')
            ->withStatus($httpCode)
            ->withBody($dataStream);
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof CommonException;
    }
}
