<?php

use App\Http\Controllers\CalculatorController;
use App\Models\Fruit;
use App\Models\Quiz;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

// 값을 받아오는
//Route::get('/api/history', [CalculatorController::class, 'calculateHistory']);
Route::get('/redis/get', [CalculatorController::class, 'testSimpleGet']);
Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->post('/redis/set', [CalculatorController::class, 'testSimpleSet']);

// 삭제하는 route
Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->delete('api/delete/{id}', [CalculatorController::class, 'deleteCalculation']);

// 우선 지난번처럼 미들웨어를 사용해서 일단은 동작, api.php를 통해 하는 방법에 관하여 문의
Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->post('/api/calc', [CalculatorController::class, 'calculate']);

// 업데이트 주소 넣기
Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->put('/api/update/{id}', [CalculatorController::class, 'updatedCalculation']);

Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->post('/fruit', function (Request $request) {
        $fruit = Fruit::create([
            'fruit' => $request->input('fruit'),
            'size' => $request->input('size'),
            'color' => $request->input('color'),
        ]);
        return response()->json([
            'message' => 'DB저장 완료!',
            'data' => $fruit
        ]);
    });

Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->post('/quiz/sport', function (Request $request) {
        $q1 = $request->input('quiz.sport.q1');

        $quiz = Quiz::create([
            'subject' => 'sport',
            'question' => $q1['question'],
            'options' => $q1['options'],
            'answer' => $q1['answer'],
        ]);

        return response()->json([
            'subject' => 'sport',
            'data' => $quiz
        ]);
    });

Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->post('/quiz/maths', function (Request $request) {
        $quizData = $request->input('quiz.maths.q1.answer');

        return response()->json([
            'subject' => 'maths',
            'data' => $quizData
        ]);
    });
