<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLogs extends Model
{
    /**
     * @var string
     */
    protected $table = 'api_logs';

    /**
     * The attributes that are mass assignable.
     * array fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'route',
        'method',
        'ip_address',
        'headers',
        'request_body',
        'status_code',
        'response_body'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     * @var string[]
     */
    protected $casts = [
    ];
}
