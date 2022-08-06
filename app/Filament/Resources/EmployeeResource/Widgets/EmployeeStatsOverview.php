<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Country;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class EmployeeStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $ng = Country::where('country_code', 'NGN')->withCount('employees')->first();
        return [
            Card::make('All Employees', Employee::all()->count())
                ->color('success'),
            Card::make($ng->name . 'Employees', $ng->employees_count),
            Card::make('Average time on page', '3:12'),
        ];
    }
}