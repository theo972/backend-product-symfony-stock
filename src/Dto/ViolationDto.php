<?php

namespace App\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema]
final class ViolationDto
{
    #[OA\Property(example: "name")]
    public string $propertyPath;

    #[OA\Property(example: "This value should not be blank.")]
    public string $message;

    #[OA\Property(example: "401", nullable: true)]
    public ?string $code = null;
}
