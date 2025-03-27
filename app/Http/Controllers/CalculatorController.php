<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    // 계산식의 값을 db에 저장하는 함수
    public function calculate(Request $request)
    {
        // JSON 요청에서 값 받기
        $expression = $request->input('expressions');
        try {
            $result = eval('return ' . trim($expression) . ';');

            // DB 저장
            // 컨트롤러에서 타입자체선언을 잘 안쓴다. 접근이 자동이 안된다.
            // 주로 아래와 같이 코딩을 해서 숙지 바란다. php 특성
            /** @var Calculation $calc */
            $calc = Calculation::create([
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

    // 계산 목록을 가져오는 함수(Get)
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
        $calc = $query->take($count)->get();
//        $calculations = Calculation::where('is_valid', true)
//            ->orderBy('created_at', 'desc')
//            ->take($count)
//            ->pluck('result');

        // 번호 붙이기(기존 유효한 값만 보냈음)
//        $numberedResult = $calculations->map(function ($el, $idx) {
//            return ($idx + 1) . '번째: ' . $el;
//        });

        // 번호 붙이는 함수 적용(유효하지 않은 값이 적용될 때도 적용)
        $numberedResult = $calc->map(function ($el, $idx) {
            $prefix = ($idx + 1) . '번째: ';
            $message = $el->is_valid ? $el->result : "[오류 결과] {$el->result}";
            return $prefix . $message;
        });

        // 결과값을 json을 통해 응답해주기
        return response()->json([
            'message' => "최신부터 {$calc->count()}개의 계산값입니다.",
            'data' => $numberedResult
        ]);
    }

    // delete 함수 만들어보기
    public function deleteCalculation($id)
    {
        // ID로 기록을 찾는다.
        $calc = Calculation::find($id);

        // 만약 해당 id가 없을 시 해당 아이디가 없다는 호출해주기
        if (!$calc) {
            return response()->json([
                'message' => "해당 {$id}의 계산 기록을 찾을 수 없습니다."
            ], 404);
        }
        // 삭제 처리
        $calc->delete();

        return response()->json([
            'message' => "ID {$id}번째의 계산 기록이 성공적으로 삭제됐습니다!"
        ]);
    }

    // 업데이트 함수 만들어보기
    public function updatedCalculation(Request $request, $id)
    {
        $expression = $request->input('expressions');

        // ID로 찾기
        $calc = Calculation::find($id);

        if (!$calc) {
            return response()->json([
                'message' => "ID {$id}번 계산 기록을 찾을 수 없습니다."
            ], 404);
        }
        // 정규식으로 수식을 검증한다(이건 주임님께 문의, 정규표현식은 구글검색 후 사용)
        // 숫자, 연산자, 괄호, 공백만 포함
        if (!preg_match("#^[0-9+\-*/().\s]+$#", $expression)) {
            // 수식이 잘못된 경우
            $calc->update([
                'expressions' => $expression,
                'result' => 0,
                'is_valid' => false,
            ]);

            return response()->json([
                'message' => "잘못된 수식입니다!",
                'data' => [
                    'expressions' => $expression,
                    'result' => 0,
                    'is_valid' => false
                ]
            ], 400);
        }

        // 정규식은 통과 후 실제 계산 수식 자체가 잘못된 경우도 있으므로 try-catch
        try {
            $result = eval('return ' . trim($expression) . ';');

            $calc->update([
                'expressions' => $expression,
                'result' => $result,
                'is_valid' => true,
            ]);

            return response()->json([
                'message' => "ID {$id}번 계산식이 수정되었습니다!",
                'data' => [
                    'expressions' => $expression,
                    'result' => $result,
                    'is_valid' => true,
                ]
            ]);
        } catch (\Throwable $e) {
            // eval 오류 발생 시
            $calc->update([
                'expressions' => $expression,
                'result' => 0,
                'is_valid' => false,
            ]);

            return response()->json([
                'message' => "계산 중 오류가 발생했습니다!",
                'data' => [
                    'expressions' => $expression,
                    'result' => 0,
                    'is_valid' => false
                ]
            ], 400);
        }
    }
}
