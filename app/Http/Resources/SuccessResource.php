<?php

namespace App\Http\Resources;

use App\Enums\ResponseCodeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuccessResource extends JsonResource
{
    /** @var int $statusCode */
    private int $statusCode;

    /** @var string $message */
    private string $message;

    /**
     * @param mixed  $resource
     * @param string $message
     * @param int    $statusCode
     */
    public function __construct(
        $resource,
        string $message = '',
        int $statusCode = ResponseCodeEnum::OK
    ) {
        parent::__construct($resource);
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'success' => true,
            'data' => $this->resource ?: null,
            'message' => $this->message ?: trans('success.success'),
        ];
    }

    /**
     * @param $request
     * @param $response
     * @return void
     */
    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->statusCode);
    }
}
