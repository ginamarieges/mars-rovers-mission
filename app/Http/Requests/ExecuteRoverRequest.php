<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ExecuteRoverRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    protected function prepareForValidation() {
        $initial = $this->input('initial', []);
        $commandsValue = $this->input('commands');

        $this->merge([
            'commands' => is_string($commandsValue) ? strtoupper($commandsValue) : $commandsValue,
            'initial' => [
                'x' => $initial['x'] ?? null,
                'y' => $initial['y'] ?? null,
                'direction' => isset($initial['direction']) && is_string($initial['direction'])
                    ? strtoupper($initial['direction'])
                    : ($initial['direction'] ?? null),
            ],
        ]);
    }

    public function rules(): array {
        return [
            'initial' => ['required', 'array'],
            'initial.x' => ['required', 'integer', 'min:0', 'max:199'],
            'initial.y' => ['required', 'integer', 'min:0', 'max:199'],
            'initial.direction' => ['required', 'string', 'in:N,E,S,W'],
            'commands' => ['required', 'string', 'regex:/^[FLR]+$/'],
            'obstacles' => ['sometimes', 'array'],
            'obstacles.*' => ['required', 'array'],
            'obstacles.*.x' => ['required', 'integer', 'min:0', 'max:199'],
            'obstacles.*.y' => ['required', 'integer', 'min:0', 'max:199'],
        ];
    }

    public function withValidator(Validator $validator) {
        $validator ->after(function (Validator $validator) {
            $initialX = $this->input('initial.x');
            $initialY = $this->input('initial.y');

            foreach ($this->input('obstacles', []) as $obstacle) {
                if (($obstacle['x'] ?? null) === $initialX && ($obstacle['y'] ?? null) === $initialY) {
                    $validator->errors()->add(
                        'obstacles',
                        'An obstacle can not be placed on the rover initial position'
                    );
                    break;
                }
            }

        });
    }
}
