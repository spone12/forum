<?php

namespace App\Http\Resources;

use App\Enums\ResponseCodeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    /** @var int $statusCode */
    private int $statusCode;

    /**
     * @param $resource
     * @param int $statusCode
     */
    public function __construct(
        $resource = null,
        int $statusCode = ResponseCodeEnum::SERVER_ERROR
    ) {
        parent::__construct($resource);
        $this->statusCode = $statusCode;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'success' => false,
            'data' => null,
            'message' => $this->resource ?: trans('errors.error'),
            'errors' => $this->when(is_array($this->resource), $this->resource)
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
