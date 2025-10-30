<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        DB::listen(function ($query) {
//            // 쿼리 정보 로깅 (SQL 쿼리, 바인딩된 데이터, 실행 시간)
//            Log::info($query->sql, $query->bindings, $query->time);
//
//            // 혹은 더 간결하게 출력하고 싶을 경우
//            // echo "Query: " . $query->sql . "\n";
//            // print_r($query->bindings);
//            // echo "\n";
//        });
        Schema::defaultStringLength(191);
    }

}
