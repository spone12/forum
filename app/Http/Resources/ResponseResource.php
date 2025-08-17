<?php

namespace App\Http\Resources;

use App\Enums\ResponseCodeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseResource extends JsonResource
{
    private bool $success;
    private int $statusCode;

    /**
     * @param $resource
     * @param bool $success
     * @param int $statusCode
     */
    public function __construct($resource, bool $success = true, int $statusCode = ResponseCodeEnum::OK)
    {
        parent::__construct($resource);
        $this->success = $success;
        $this->statusCode = $statusCode;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'success' => $this->success,
            'data' => $this->success ? $this->resource : null,
            'message' => !$this->success ? $this->resource : null,
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->statusCode);
    }
}
