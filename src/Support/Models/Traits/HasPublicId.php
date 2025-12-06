<?php

namespace JamesGifford\LaravelArchitecture\Support\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RuntimeException;

trait HasPublicId
{
    /**
     * Define a per-model prefix in one of two ways:
     *  - const PUBLIC_ID_PREFIX = 'ABC';
     *  - public static function publicIdPrefix(): string
     *
     * Prefix may be empty, but if provided must be <= 6 chars including the underscore.
     */

    /**
     * Hook the Eloquent creating event for this trait.
     */
    protected static function bootHasPublicId(): void
    {
        static::creating(function (Model $model) {
            // If the model already has a public_id, validate it
            if (isset($model->public_id) && $model->public_id !== '') {
                static::assertPublicIdIsValid((string) $model->public_id);
                return;
            }

            $model->public_id = static::generatePublicId();
        });
    }

    /**
     * Generate a public_id for this model.
     */
    public static function generatePublicId(): string
    {
        $prefix = static::resolvePublicIdPrefix();

        if (mb_strlen($prefix) > 6) {
            throw new RuntimeException(sprintf(
                'Public id prefix "%s" exceeds 6 characters on model %s.',
                $prefix,
                static::class
            ));
        }

        return static::generateUniquePublicId($prefix);
    }

    /**
     * Resolve the per-model prefix from const or method.
     */
    protected static function resolvePublicIdPrefix(): string
    {
        if (defined(static::class.'::PUBLIC_ID_PREFIX')) {
            return static::ensureStringEndsWithUnderscore(
                (string) constant(static::class.'::PUBLIC_ID_PREFIX')
            );
        }

        if (method_exists(static::class, 'publicIdPrefix')) {
            return static::ensureStringEndsWithUnderscore(
                (string) static::publicIdPrefix()
            );
        }

        return '';
    }

    /**
     * Ensure a string ends with an underscore unless it's empty.
     */
    protected static function ensureStringEndsWithUnderscore(string $value): string
    {
        if ($value === '') {
            return $value;
        }

        return str_ends_with($value, '_') ? $value : $value . '_';
    }

    /**
     * Generate a unique public_id for this model's table.
     */
    protected static function generateUniquePublicId(string $prefix = ''): string
    {
        $attempts = 0;

        do {
            $candidate = $prefix . static::generatePublicIdSuffix();
            static::assertPublicIdIsValid($candidate);

            $exists = static::query()
                ->withoutGlobalScopes()
                ->where('public_id', $candidate)
                ->exists();

            if (! $exists) {
                return $candidate;
            }

            $attempts++;
        } while ($attempts < 10);

        throw new RuntimeException(sprintf(
            'Failed generating a unique public_id for %s after %d attempts.',
            static::class,
            $attempts
        ));
    }

    /**
     * Exactly-20-char alphanumeric suffix.
     * Lowercased to keep it visually consistent.
     */
    protected static function generatePublicIdSuffix(): string
    {
        return Str::lower(Str::random(20));
    }

    /**
     * Quick format guard for any manually set or generated public_id.
     */
    protected static function assertPublicIdIsValid(string $value): void
    {
        $length = mb_strlen($value);

        if ($length < 20 || $length > 26) {
            throw new RuntimeException('Public id must be 20–26 characters (prefix ≤6 + 20-char suffix).');
        }

        $suffix = mb_substr($value, -20);

        if (! preg_match('/^[a-z0-9]{20}$/', $suffix)) {
            throw new RuntimeException('Public id suffix must be exactly 20 lowercase alphanumeric characters.');
        }
    }

    /**
     * Prepare an array of rows so each has a public_id (for bulk ops).
     */
    public static function ensurePublicIds(array $rows): array
    {
        foreach ($rows as &$row) {
            if (!array_key_exists('public_id', $row) || $row['public_id'] === null || $row['public_id'] === '') {
                $row['public_id'] = static::generatePublicId();
            }
        }

        unset($row);

        return $rows;
    }

    /**
     * Safe wrapper around upsert() that ensures public_id is set on inserts and never overwritten on conflicts.
     */
    public static function upsertWithPublicId(array $rows, array|string $uniqueBy, array $update): int
    {
        $rows = static::ensurePublicIds($rows);
        $update = array_values(array_diff($update, ['public_id']));

        return static::query()->upsert($rows, $uniqueBy, $update);
    }

    /**
     * Helper for insert()
     */
    public static function insertWithPublicId(array $rows): bool
    {
        return static::query()->insert(static::ensurePublicIds($rows));
    }

    /**
     * Helper for insertOrIgnore()
     */
    public static function insertOrIgnoreWithPublicId(array $rows): int
    {
        return static::query()->insertOrIgnore(static::ensurePublicIds($rows));
    }

    /**
     * Convenience scope.
     */
    public function scopeWherePublicId(Builder $query, string $publicId): Builder
    {
        return $query->where($this->getTable().'.public_id', $publicId);
    }

    /**
     * Static finder convenience.
     */
    public static function findByPublicId(string $publicId): ?self
    {
        return static::query()->where('public_id', $publicId)->first();
    }

    /**
     * Specify the field to use for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    /**
     * Enable route model binding on "public_id" by setting $usePublicIdForRouting = true;
     */
    public function getRoutePublicIdName(): string
    {
        return property_exists($this, 'usePublicIdForRouting') && $this->usePublicIdForRouting === true
            ? 'public_id'
            : parent::getRoutePublicIdName();
    }
}
