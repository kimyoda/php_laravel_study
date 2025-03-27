<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function calculate(Request $request)
    {
        // JSON 요청에서 값 받기
        $expression = $request->input('expressions');
        try {
            $result = eval('return ' . trim($expression) . ';');

            // DB 저장
            Calculation::create([
                'expressions' => $expression,
                'result' => $result,
                'is_valid' => true,
            ]);

            return response()->json([
                'message' => "계산 결과는 {$result}입니다!",
                'data' => [
                    'expressions' => $expression,
                    'result' => $result,
                    'is_valid' => true
                ]
            ]);
        } catch (\Throwable $e) {
            // 그냥 에러 나면 여기서 끝!
            return response()->json([
                'message' => '잘못된 수식입니다!',
                'error' => $e->getMessage() // 필요하면 표시
            ], 403);
        }
    }
}
