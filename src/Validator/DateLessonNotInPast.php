<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DateLessonNotInPast extends Constraint {
    public string $message = 'This value must not be in the past.';

    /** @var array Specifies roles to which this constraint is not applied */
    public array $exceptions = [ ];
}