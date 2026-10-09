<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class TelefonoInternacional implements ValidationRule
{
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        if (!is_string($value)) {
            $fail('El campo :attribute debe contener un teléfono válido.');
            return;
        }

        $numero = trim($value);

        // Exigir el prefijo internacional.
        if (!preg_match('/^\+[1-9]\d{1,14}$/', $numero)) {
            $fail(
                'El campo :attribute debe contener un número internacional válido, incluyendo el código del país.'
            );
            return;
        }

        try {
            $util = PhoneNumberUtil::getInstance();

            $telefono = $util->parse($numero, 'ZZ');

            if (!$util->isValidNumber($telefono)) {
                $fail('El número de teléfono no es válido para el país indicado.');
                return;
            }

            $normalizado = $util->format(
                $telefono,
                PhoneNumberFormat::E164
            );

            if ($normalizado !== $numero) {
                $fail('El número de teléfono debe estar en formato internacional E.164.');
            }

        } catch (NumberParseException $e) {
            $fail('No se pudo interpretar el número de teléfono.');
        }
    }
}