<?php

namespace JamesGifford\LaravelArchitecture\Support\Queries\Transfers;

use Illuminate\Http\Request;
use JamesGifford\LaravelArchitecture\Support\Queries\Constants\SearchQuery;
use JamesGifford\LaravelArchitecture\Support\Transfers\Attributes\BuildsFrom;
use JamesGifford\LaravelArchitecture\Support\Transfers\TransferAbstract;

class SearchFilters extends TransferAbstract
{
    private const DEFAULTS = [
        'search' => '',
        'sortBy' => 'last_visit_at',
        'sortDirection' => 'desc',
        'since' => '',
        'perPage' => 15,
        'page' => 1,
    ];

    public function __construct(
        public string $search,
        public string $sortBy,
        public string $sortDirection,
        public string $since,
        public int $perPage,
        public int $page = 1,
    ) {}

    #[BuildsFrom(Request::class)]
    public static function buildFromRequest(Request $request): static
    {
        $query = $request->query();

        return new self(
            search: (string) ($query[SearchQuery::SEARCH] ?? self::DEFAULTS['search']),
            sortBy: (string) ($query[SearchQuery::SORT_BY] ?? self::DEFAULTS['sortBy']),
            sortDirection: (string) ($query[SearchQuery::SORT_DIRECTION] ?? self::DEFAULTS['sortDirection']),
            since: (string) ($query[SearchQuery::SINCE] ?? self::DEFAULTS['since']),
            perPage: (int) ($query[SearchQuery::PER_PAGE] ?? self::DEFAULTS['perPage']),
            page: (int) ($query[SearchQuery::PAGE] ?? self::DEFAULTS['page']),
        );
    }

    #[BuildsFrom('array')]
    public static function buildFromArray(array $array): static
    {
        return new self(
            search: (string) ($array['search'] ?? self::DEFAULTS['search']),
            sortBy: (string) ($array['sortBy'] ?? self::DEFAULTS['sortBy']),
            sortDirection: (string) ($array['sortDirection'] ?? self::DEFAULTS['sortDirection']),
            since: (string) ($array['since'] ?? self::DEFAULTS['since']),
            perPage: (int) ($array['perPage'] ?? self::DEFAULTS['perPage']),
            page: (int) ($array['page'] ?? self::DEFAULTS['page']),
        );
    }

    public static function buildDefault(): static
    {
        return new self(
            search: self::DEFAULTS['search'],
            sortBy: self::DEFAULTS['sortBy'],
            sortDirection: self::DEFAULTS['sortDirection'],
            since: self::DEFAULTS['since'],
            perPage: self::DEFAULTS['perPage'],
            page: self::DEFAULTS['page']
        );
    }
}
