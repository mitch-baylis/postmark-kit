<?php

namespace MitchBaylis\PostmarkKit\Suppression;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * One recipient Postmark told us not to write to again.
 *
 * @property string $id
 * @property string $email
 * @property string $reason
 * @property string|null $provider_id
 * @property string|null $detail
 * @property \Illuminate\Support\Carbon $suppressed_at
 */
class SuppressedAddress extends Model
{
    use HasUuids;

    protected $table = 'postmark_suppressions';

    protected $fillable = [
        'email',
        'reason',
        'provider_id',
        'detail',
        'suppressed_at',
    ];

    protected $casts = [
        'suppressed_at' => 'datetime',
    ];
}
