<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ExecuteRoverRequest;
use Illuminate\Http\JsonResponse;

final class RoverExecuteController extends Controller {
    public function __invoke(ExecuteRoverRequest $request): JsonResponse {
        return response()->json([ 'validated' => $request->validated()]
        );
    }
}
