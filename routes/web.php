<?php

use App\Models\Fruit;
use App\Models\Quiz;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->post('/fruit', function (Request $request) {
        $fruit = Fruit::create([
            'fruit'=> $request->input('fruit'),
            'size'=> $request->input('size'),
            'color'=> $request->input('color'),
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
