<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoDuplicateProductValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }
        $products = [];
        foreach ($value as $item) {
            $productId = $item?->getProduct()?->getId();
            if ($productId === null) {
                continue;
            }
            if (isset($products[$productId])) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ id }}', (string) $productId)
                    ->addViolation();
                return;
            }
            $products[$productId] = true;
        }
    }
}
