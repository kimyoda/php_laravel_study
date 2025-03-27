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
            // 그냥 에러 나면 여기서 끝입니다!
            return response()->json([
                'message' => '잘못된 수식입니다!',
                'error' => $e->getMessage() // 필요하면 표시
            ], 403);
        }
    }

    // 계산 목록을 가져오는 함수
    public function calculateHistory(Request $request)
    {
        // 클라이언트에서 값 받아오기, 기본 1개
        $count = $request->input('count', 1);
        // 값이 유효한 것만 요청해달라고 할때 값 주는 것 추가
        $validOnly = filter_var($request->input('valid_only', true), FILTER_VALIDATE_BOOLEAN);

        // 전체 혹은 유효한 값만 넣는 조건 넣기Í
        $query = Calculation::orderBy('created_at', 'desc');

        if ($validOnly) {
            $query->where('is_valid', true);
        }

        // orderBy로 최근 값을 정렬시켜 조회
        $calculations = $query->take($count)->get();
//        $calculations = Calculation::where('is_valid', true)
//            ->orderBy('created_at', 'desc')
//            ->take($count)
//            ->pluck('result');

        // 번호 붙이기(기존 유효한 값만 보냈음)
//        $numberedResult = $calculations->map(function ($el, $idx) {
//            return ($idx + 1) . '번째: ' . $el;
//        });

        // 번호 붙이는 함수 적용(유효하지 않은 값이 적용될 때도 적용)
        $numberedResult = $calculations->map(function ($el, $idx) {
            $prefix = ($idx + 1) . '번째: ';
            $message = $el->is_valid ? $el->result : "[오류 결과] {$el->result}";
            return $prefix . $message;
        });

        // 결과값을 json을 통해 응답해주기
        return response()->json([
            'message' => "최신부터 {$calculations->count()}개의 계산값입니다.",
            'data' => $numberedResult
        ]);
    }
}
