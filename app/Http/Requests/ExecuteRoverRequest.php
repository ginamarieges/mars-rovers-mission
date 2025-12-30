<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Form request used to validate the rover execution endpoint.
 *
 * It prepares and validate the incoming request before it reaches the controller.
 */

class ExecuteRoverRequest extends FormRequest {
    /**
     * In this case no authorization logic is needed for this endpoint.
     * Any request is allowed to execute the rover simulation.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Normalize input values before validation.
     *
     * Commands and direction are converted to uppercase.
     */
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

     /**
     * Define the basic validation rules for the request.
     *
     * These rules ensure the rover starts inside the world bounds,
     * uses a valid direction, receives a valid command sequence,
     * and that all obstacles are well-formed and within the world.
     */
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

    /**
     * Run additional validation after the basic rules.
     *
     * This check ensures that no obstacle is placed on the rover's
     * initial position.
     */
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
