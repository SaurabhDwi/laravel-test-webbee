<?php

namespace App\Providers;

use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //$this->fileCreate();
    }

    private function fileCreate()
    {
        DB::listen(function ($query) {
            $data = $sqlquery = '';
            File::append(
                storage_path('query/' . date('d-m') . '-query.log'),
                $query->sql . "	 \t" . json_encode($query->bindings) . "  \t" . PHP_EOL . '----------------------------------------------' . PHP_EOL . PHP_EOL);
        });
    }
}
