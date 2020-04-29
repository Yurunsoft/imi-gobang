<?php
namespace ImiApp\MainServer\ErrorHandler;

use Imi\App;
use Imi\RequestContext;
use ImiApp\Enum\MessageCode;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\RequestHeader;
use ImiApp\Exception\BusinessException;
use Imi\Server\Http\Error\IErrorHandler;

class HttpErrorHandler implements IErrorHandler
{
    public function handle(\Throwable $throwable): bool
    {
        $cancelThrow = false;
        if($throwable instanceof BusinessException)
        {
            $code = $throwable->getCode();
        }
        else
        {
            $code = MessageCode::ERROR;
        }
        $data = [
            'code'      =>  $code ?? $throwable->getCode(),
            'message'   =>  $throwable->getMessage(),
        ];
        if(App::isDebug())
        {
            $data['exception'] = [
                'message'   =>    $throwable->getMessage(),
                'code'      =>    $throwable->getCode(),
                'file'      =>    $throwable->getFile(),
                'line'      =>    $throwable->getLine(),
                'trace'     =>    explode(PHP_EOL, $throwable->getTraceAsString()),
            ];
        }
        RequestContext::get('response')
        ->withAddedHeader(RequestHeader::CONTENT_TYPE, MediaType::APPLICATION_JSON)
        ->write(json_encode($data))
        ->send();
        return $cancelThrow;
    }
}