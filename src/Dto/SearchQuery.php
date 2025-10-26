<?php
namespace App\Dto;

final readonly class SearchQuery
{
    public function __construct(
        public ?string $support,
        public ?string $query,
        public int $page = 1,
        public int $perPage = 20,
        public ?string $sort = null,
        public string $order = 'asc',
        public array $filters = []
    ) {}
}
