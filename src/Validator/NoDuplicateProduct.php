<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class NoDuplicateProduct extends Constraint
{
    public string $message = 'Produit dupliqué dans les produits (product id {{ id }}).';
}
