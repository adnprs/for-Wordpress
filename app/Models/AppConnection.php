<?php

namespace App\Models;

use App\Traits\EncryptsAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppConnection extends Model
{
    use HasFactory;
    use EncryptsAttributes;

    public $timestamps = false;

    protected $fillable = [
        'app_id',
        'endpoint_url',
        'username',
        'encrypted_password',
        'firebase_config_path',
        'default_category',
    ];

    protected array $encrypted = [
        'encrypted_password',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }
}
