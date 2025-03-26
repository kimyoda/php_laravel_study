<?php

use App\Http\Controllers\CalculatorController;
use Illuminate\Support\Facades\Route;

// 아래와 같이 작성하였으나 해당 루트를 인식하지 못함. 따로 설정이 필요한 부분이나 제가 모르는 부분이 있다면 힌트나
// 어떻게 해야 될 지 알려 주시면 작성해보겠습니다!
Route::post('/calc', [CalculatorController::class, 'calculate']);
