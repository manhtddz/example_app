<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public $repoBinding = [
        'App\Services\Interfaces\IEmployeeRepository' => 'App\Services\Repository\EmployeeRepository',
        'App\Services\Interfaces\ITeamRepository' => 'App\Services\Repository\TeamRepository',
        'App\Services\Interfaces\IProjectRepository' => 'App\Services\Repository\ProjectRepository',
        'App\Services\Interfaces\ITaskRepository' => 'App\Services\Repository\TaskRepository',
    ];
    public function register(): void
    {
        //
        foreach ($this->repoBinding as $key => $value) {
            $this->app->bind($key, $value);
        }
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
