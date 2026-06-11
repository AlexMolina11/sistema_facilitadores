<?php

namespace App\Modules\Seg\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function authenticateThrottleKey(): string
    {
        return Str::lower((string) $this->input('email')).'|'.$this->ip();
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->authenticateThrottleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->authenticateThrottleKey());

        throw ValidationException::withMessages([
            'email' => "Demasiados intentos fallidos. Intente nuevamente en {$seconds} segundos.",
        ]);
    }

    public function hitRateLimiter(): void
    {
        RateLimiter::hit($this->authenticateThrottleKey(), 60);
    }

    public function clearRateLimiter(): void
    {
        RateLimiter::clear($this->authenticateThrottleKey());
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingrese un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }
}
