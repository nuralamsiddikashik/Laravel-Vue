<?php
namespace App\Providers;

use App\Contracts\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {
    public $bindings = [
        CategoryRepositoryInterface::class => CategoryRepository::class,
    ];

    public function register() {

//        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        //
    }
}