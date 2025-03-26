<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

use App\Filament\Clusters\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $cluster = Settings::class;
    

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationIcon = 'heroicon-s-face-smile';


    public static function getNavigationLabel(): string
    {   
        return 'Profile';
    }
    
    public static function getEloquentQuery(): Builder
    {
        return User::query()->where('id', Auth::id()); // Show only the logged-in user's data
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('fulfimentMethod_id')
                    ->relationship('fulfilmentMethods', 'method_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                Forms\Components\TextInput::make('pin_number')
                    ->label("pin number"),
                Forms\Components\TextInput::make('first_name')
                    ->label("first_name"),

                Forms\Components\TextInput::make('last_name')
                    ->label("last_name"), 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('first name')
                    ->searchable(), 
                Tables\Columns\TextColumn::make('last_name')
                    ->label('last name')
                    ->searchable(), 
                Tables\Columns\TextColumn::make('email')
                    ->label('email')
                    ->searchable(), 
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('email')
                    ->searchable(), 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FulfilmentMethodsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
