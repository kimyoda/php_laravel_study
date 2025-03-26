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
        $result = null;
        $isValid = true;
        $message = '';


        // try, catch로 에러 발생 점검
        try {
            // 수식계산시도
            $result = eval('return ' . trim($expression) . ';');
            $message = "계산 결과는 {$result}입니다!";
        } catch (\Throwable $e) {
            // 에러 발생 시
            $isValid = false;
            $message = "잘못된 수식입니다!";
        }

        // db에 저장
        Calculation::create([
            'expressions' => $expression,
            'result' => $result ?? 0,
            'is_valid' => $isValid,
        ]);

        // 응답 반환
        return response()->json([
            "message" => $message,
            "data" => [
                'expressions' => $expression,
                'result' => $result ?? null,
                'is_valid' => $isValid
            ]
        ]);
    }
}
