<?php

namespace App\Models\Concerns;

use Testcenter\Infrastructure\Shared\UuidBinary;

trait HasBinaryUuid
{
    public static function bootHasBinaryUuid(): void
    {
        static::creating(function (self $model) {
            if (empty($model->attributes['uuid'])) {
                $data = random_bytes(16);
                $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
                $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
                $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
                $model->attributes['uuid'] = UuidBinary::toBin($uuid);
            }
        });
    }

    public function initializeHasBinaryUuid(): void
    {
        $this->primaryKey = 'uuid';
        $this->incrementing = false;
        $this->keyType = 'string';
    }

    /**
     * Returns raw binary so Eloquent relationship JOINs use binary in SQL.
     * Must NOT go through getUuidStrAttribute — those return UUID string which
     * would break binary column comparisons in pivot/foreign-key constraints.
     */
    public function getKey(): mixed
    {
        return $this->attributes['uuid'] ?? null;
    }

    /**
     * $model->uuid_str returns UUID string for display, URLs, and responses.
     * Named uuid_str (not uuid) so that $model->uuid returns raw binary,
     * which is required for correct Eloquent eager-load dictionary matching.
     */
    public function getUuidStrAttribute(): ?string
    {
        $raw = $this->attributes['uuid'] ?? null;
        return $raw !== null ? UuidBinary::toStr($raw) : null;
    }

    /**
     * Accepts UUID string (36 chars) or raw binary (16 bytes) when setting uuid.
     */
    public function setUuidAttribute(mixed $value): void
    {
        $this->attributes['uuid'] = (is_string($value) && strlen($value) === 36)
            ? UuidBinary::toBin($value)
            : $value;
    }

    /**
     * Route model binding: URL carries UUID string, convert to binary for lookup.
     */
    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->where('uuid', UuidBinary::toBin($value))->first();
    }

    /**
     * Route key is UUID string so URLs are human-readable.
     */
    public function getRouteKey(): mixed
    {
        return $this->getUuidStrAttribute();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
