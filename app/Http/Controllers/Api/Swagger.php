<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Forum API",
 *         version="0.2",
 *         description="Forum API"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="",
 *     description="Bearer {token}"
 * )
 */
 class Swagger {}


class SwaggerApiController {
    /**
    * @OA\Post(
    *   path="/api/generate_token",
    *   summary="Generate API token",
    *   tags={"API/Main"},
    *   @OA\RequestBody(
    *       required=true,
    *       @OA\JsonContent(
    *           required={"email", "password"},
    *           @OA\Property(property="email", type="string", example="test@tempmail.com"),
    *           @OA\Property(property="password", type="string", example="pass"),
    *       )
    *   ),
    *   @OA\Response(response=200, description="Token generated")
    * )
    */
    public function generateToken() {}
}

class SwaggerApiNotationController {
    /**
    * @OA\Get(
    *   path="/api/v1/notation/list",
    *   summary="Get user notations",
    *   tags={"API/Notations"},
    *   security={{"bearerAuth":{}}},
    *   @OA\Header(
    *     header="Content-Type",
    *     description="",
    *     required=true,
    *     @OA\Schema(type="string", example="application/json")
    *   ),
    *   @OA\Response(response=200, description="User list notations")
    * )
    */
    public function list() {}

    /**
    * @OA\Get(
    *   path="/api/v1/notation/get_notation",
    *   summary="Get user notation by Id",
    *   tags={"API/Notations"},
    *   security={{"bearerAuth":{}}},
    *   @OA\Header(
    *     header="Content-Type",
    *     description="",
    *     required=true,
    *     @OA\Schema(type="string", example="application/json")
    *   ),
    *   @OA\Parameter(
    *     name="notation_id",
    *     in="query",
    *     required=true,
    *     description="Notation id",
    *     @OA\Schema(
    *         type="integer",
    *         example=1
    *     )
    *   ),
    *   @OA\Response(response=200, description="User notation info")
    * )
    */
    public function getNotationById() {}

    /**
    * @OA\Put(
    *   path="/api/v1/notation/update_notation",
    *   summary="Update notation",
    *   tags={"API/Notations"},
    *   security={{"bearerAuth":{}}},
    *   @OA\RequestBody(
    *       required=true,
    *       @OA\JsonContent(
    *           required={"notation_id", "text"},
    *           @OA\Property(property="notation_id", type="integer", example=1),
    *           @OA\Property(property="text", type="string", example="Example text notation"),
    *       )
    *   ),
    *   @OA\Response(response=200, description="Notation update successfuly")
    * )
    */
    public function updateNotation() {}
}
