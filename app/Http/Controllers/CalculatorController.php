<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function calculate(Request $request)
    {
        // JSON 요청에서 값 받기
        $expression = $request->input('expression');

        // 응답 반환
        return response()->json([
            'message' => '응답 성공!',
            'data' => [
                'expressions' => $expression,
                'result' => eval('return ' . trim($expression) . ';'),
                'is_valid' => true
            ]
        ]);
    }
}
