<?php

namespace App\Services\Contract;

use App\Models\Part;
use Illuminate\Support\Collection;

interface PartServiceInterface
{
    public function create(array $item): Part;

    public function update(array $item, int $newPositionId): Part;

    public function delete(array $item): bool;

    public function reIndex(array $item, int $newPositionId): void;
}
