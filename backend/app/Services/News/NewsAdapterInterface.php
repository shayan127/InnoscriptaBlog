<?php

namespace App\Services\News;

interface NewsAdapterInterface
{
    public function transform(array $data, string $origin): array;
}
