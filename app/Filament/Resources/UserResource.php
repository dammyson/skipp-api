<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

use Filament\Resources\Pages\Page;
use App\Filament\Clusters\Settings;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
// use App\Filament\Resources\CustomerResource\Pages;
use Filament\Tables\Columns\ViewColumn;



use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // protected static ?string $cluster = Settings::class;
    
    protected static ?string $navigationGroup = 'Settings';

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
        // return $form
        //     ->schema([
        //         Forms\Components\Select::make('fulfimentMethod_id')
        //             ->relationship('fulfilmentMethods', 'method_name')
        //             ->searchable()
        //             ->multiple()
        //             ->preload(),
        //         Forms\Components\TextInput::make('pin_number')
        //             ->label("pin number"),
        //         Forms\Components\TextInput::make('first_name')
        //             ->label("first_name"),

        //         Forms\Components\TextInput::make('last_name')
        //             ->label("last_name"), 
        //     ]);

        return $form
        ->schema([
            Section::make('Profile')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('fulfimentMethod_id')
                                    ->relationship('fulfilmentMethods', 'method_name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                            ->prefixIcon('heroicon-o-user'),

                        TextInput::make('first_name')
                            ->label('first name')
                            ->prefixIcon('heroicon-o-envelope'),
                        TextInput::make('last_name')
                            ->label('last Name')
                            ->prefixIcon('heroicon-o-envelope'),

                        TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->tel()
                            ->prefixIcon('heroicon-o-phone'),

                        TextInput::make('pin_number')
                            ->label('pin Number'),
                            

                        TextInput::make('address')
                            ->label('Address')
                            ->prefixIcon('heroicon-o-map-pin'),
                    ]),

                    FileUpload::make('image_url')
                    ->label('Change Avatar')                    
                    ->hint('Supported Format: SVG, JPG, PNG (10mb each)')
                    ->hintColor('gray')
                    ->panelLayout('compact')
                    ->disk('cloudinary') // Ensure you have the correct disk configured in `config/filesystems.php`
                    ->directory('uploads') // Optional: define a folder in Cloudinary
                    ->saveUploadedFileUsing(function ($file) {
                        $path = Storage::disk('cloudinary')->putFile('uploads', $file);
                        return Storage::disk('cloudinary')->url($path);
                    })
                    
                    ->getUploadedFileNameForStorageUsing(fn ($file) => $file->hashName()),

                    // FileUpload::make('avatar')
                    //     ->label('Change Avatar')
                    //     ->avatar()
                    //     ->image()
                    //     ->imageEditor()
                    //     ->maxSize(10240)
                    //     ->imagePreviewHeight('100')
                    //     ->hint('Supported Format: SVG, JPG, PNG (10mb each)')
                    //     ->hintColor('gray')
                    //     ->panelLayout('compact'),
                ])
                ->columns(1)
                ->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('first_name')
        //             ->label('first name')
        //             ->searchable(), 
        //         Tables\Columns\TextColumn::make('last_name')
        //             ->label('last name')
        //             ->searchable(), 
        //         Tables\Columns\TextColumn::make('email')
        //             ->label('email')
        //             ->searchable(), 
        //         Tables\Columns\TextColumn::make('phone_number')
        //             ->label('email')
        //             ->searchable(), 
        //     ])
        //     ->filters([
        //         //
        //     ])
        //     ->actions([
        //         Tables\Actions\ViewAction::make(),
        //         Tables\Actions\EditAction::make(),
        //     ])
        //     ->bulkActions([
        //         Tables\Actions\BulkActionGroup::make([
        //             Tables\Actions\DeleteBulkAction::make(),
        //         ]),
        //     ]);

            return $table
            ->columns([
                ViewColumn::make('profile_card')
                    ->view('tables.columns.user-profile-card'),
            ])->paginated(false)
            ->defaultSort('first_name')
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ListUsers::class,
            Pages\EditUser::class,
            Pages\ChangePassword::class
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
            // 'create' => Pages\CreateUser::route('/create'),
            // 'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'changePassword' => Pages\ChangePassword::route('/{record}/change-password'),
        ];
    }
}
