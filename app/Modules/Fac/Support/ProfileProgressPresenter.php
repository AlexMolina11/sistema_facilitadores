<?php

namespace App\Modules\Fac\Support;

use Illuminate\Support\Str;

class ProfileProgressPresenter
{
    public static function friendlyMessage(string $criterion): string
    {
        $criterion = trim($criterion);

        $messages = [
            'Datos personales obligatorios' => 'Completar los datos personales obligatorios.',
            'Documento de identidad con archivo' => 'Agregar un documento de identidad con su archivo adjunto.',
            'Residencia completa' => 'Completar país, departamento, municipio y distrito de residencia.',

            'Correo principal' => 'Registrar un correo electrónico principal.',
            'Teléfono registrado' => 'Registrar al menos un número de teléfono.',
            'Contacto de emergencia' => 'Registrar un contacto de emergencia.',

            'Experiencia laboral' => 'Agregar al menos una experiencia laboral.',

            'Educación formal' => 'Agregar al menos un atestado de educación formal.',
            'Educación continua' => 'Agregar al menos un atestado de educación continua.',

            'Área de especialización con evidencia' => 'Asociar al menos un área de especialización con evidencia.',

            'Idioma registrado' => 'Registrar al menos un idioma.',
            'Disponibilidad actual' => 'Registrar disponibilidad actual.',

            'Referencia Referencias Laborales' => 'Agregar al menos una referencia laboral.',
            'Referencia Referencias Personales' => 'Agregar al menos una referencia personal.',
            'Referencia Referencia de formación impartida' => 'Agregar una referencia de formación impartida.',
        ];

        if (isset($messages[$criterion])) {
            return $messages[$criterion];
        }

        if (Str::startsWith($criterion, 'Área vinculada al atestado:')) {
            $name = trim(Str::after($criterion, 'Área vinculada al atestado:'));

            return 'Asociar un área de especialización al atestado "' . $name . '".';
        }

        if (Str::startsWith($criterion, 'Área vinculada a capacitación FEPADE:')) {
            $name = trim(Str::after($criterion, 'Área vinculada a capacitación FEPADE:'));

            return 'Asociar un área de especialización a la capacitación FEPADE "' . $name . '".';
        }

        if (Str::startsWith($criterion, 'Referencia ')) {
            $name = trim(Str::after($criterion, 'Referencia '));

            return 'Agregar referencia: ' . $name . '.';
        }

        return $criterion;
    }

    public static function sectionFor(string $criterion): string
    {
        $criterion = trim($criterion);

        if (in_array($criterion, [
            'Datos personales obligatorios',
            'Documento de identidad con archivo',
            'Residencia completa',
        ], true)) {
            return 'Perfil';
        }

        if (in_array($criterion, [
            'Correo principal',
            'Teléfono registrado',
            'Contacto de emergencia',
        ], true)) {
            return 'Contacto';
        }

        if ($criterion === 'Experiencia laboral') {
            return 'Experiencia';
        }

        if (in_array($criterion, [
            'Educación formal',
            'Educación continua',
        ], true)) {
            return 'Formación';
        }

        if (
            $criterion === 'Área de especialización con evidencia'
            || Str::startsWith($criterion, 'Área vinculada al atestado:')
            || Str::startsWith($criterion, 'Área vinculada a capacitación FEPADE:')
        ) {
            return 'Especialización';
        }

        if ($criterion === 'Idioma registrado') {
            return 'Idiomas';
        }

        if (Str::startsWith($criterion, 'Referencia ')) {
            return 'Referencias';
        }

        if ($criterion === 'Disponibilidad actual') {
            return 'Disponibilidad';
        }

        return 'General';
    }

    public static function iconForSection(string $section): string
    {
        return match ($section) {
            'Perfil' => 'fa-user',
            'Contacto' => 'fa-phone',
            'Experiencia' => 'fa-briefcase',
            'Formación' => 'fa-graduation-cap',
            'Especialización' => 'fa-star',
            'Idiomas' => 'fa-language',
            'Referencias' => 'fa-handshake',
            'Disponibilidad' => 'fa-calendar-check',
            default => 'fa-circle-info',
        };
    }

    public static function presentCollection(iterable $criteria): array
    {
        $result = [];

        foreach ($criteria as $criterion => $completed) {
            $section = self::sectionFor((string) $criterion);

            $result[$section][] = [
                'criterion' => (string) $criterion,
                'message' => self::friendlyMessage((string) $criterion),
                'completed' => (bool) $completed,
                'icon' => self::iconForSection($section),
            ];
        }

        return $result;
    }

    public static function pendingMessages(iterable $criteria): array
    {
        $messages = [];

        foreach ($criteria as $criterion => $completed) {
            if (!$completed) {
                $messages[] = self::friendlyMessage((string) $criterion);
            }
        }

        return $messages;
    }
}