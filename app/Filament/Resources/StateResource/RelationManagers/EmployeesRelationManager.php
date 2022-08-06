<?php

namespace App\Filament\Resources\StateResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\State;
use App\Models\Country;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('first_name')
                //     ->required()
                //     ->maxLength(255),
                Card::make()->schema([
                    Select::make('country_id')
                        ->label('Country')
                        ->options(Country::all()
                            ->pluck('name', 'id')
                            ->toArray())
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(fn (callable $set) => $set('state_id', null)),

                    Select::make('state_id')
                        ->label('State')
                        ->options(function (callable $get) {
                            $country = Country::find($get('country_id'));
                            if (!$country) {
                                return State::all()
                                    ->pluck('name', 'id');
                            }
                            return $country
                                ->states
                                ->pluck('name', 'id');
                        })
                        ->reactive()
                        ->required(),

                    Select::make('city_id')
                        ->label('City')->options(function (callable $get) {
                            $state = State::find($get('state_id'));
                            if (!$state) {
                                return State::all()
                                    ->pluck('name', 'id');
                            }
                            return $state
                                ->cities
                                ->pluck('name', 'id');
                        })
                        ->reactive()
                        ->required(),

                    Select::make('department_id')
                        ->relationship('department', 'name')
                        ->required(),

                    TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('address')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('zip_code')
                        ->required()
                        ->maxLength(6),
                    DatePicker::make('birth_date')
                        ->required(),
                    DatePicker::make('date_hired')
                        ->required(),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('first_name'),
                TextColumn::make('id'),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('country.name')->sortable()->searchable(),
                // TextColumn::make('state.name')->sortable()->searchable(),
                // TextColumn::make('city.name')->sortable()->searchable(),
                TextColumn::make('department.name')->sortable()->searchable(),
                TextColumn::make('zip_code')->sortable()->searchable(),
                TextColumn::make('birth_date')->sortable()->searchable(),
                TextColumn::make('date_hired')->sortable()->searchable(),
                TextColumn::make('created_at')->dateTime(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}