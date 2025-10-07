<?php
namespace App\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

#[OA\Schema]
final class ValidationErrorResponse
{
    #[OA\Property(example: 401)]
    public int $status;

    #[OA\Property(example: "Validation Failed")]
    public string $title;

    /** @var ViolationDto[] */
    #[OA\Property(
        property: "errors",
        type: "array",
        items: new OA\Items(ref: new Model(type: ViolationDto::class))
    )]
    public array $errors = [];
}
