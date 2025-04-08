<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationLabel(): string
    {
        return 'Orders';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([  Forms\Components\TextInput::make('status')
                ->required()
                ->label("status"),
            Forms\Components\TextInput::make('total_amount')
                ->disabled()
                ->label("total_amount"),
            // Forms\Components\Select::make('user_id')
            //     ->relationship('user', 'first_name')
            //     ->searchable()
            //     ->preload()
            //     ->label("customer_first_name"),
         
            Forms\Components\TextInput::make('customer_first_name')
                ->label("customer_first_name")   
                ->formatStateUsing(fn ($record) => $record->user?->first_name ?? 'N/A')
                ->disabled(),
            Forms\Components\TextInput::make('customer_last_name')
                ->label("customer_last_name")   
                ->formatStateUsing(fn ($record) => $record->user?->last_name ?? 'N/A')
                ->disabled(),
         
            Forms\Components\TextInput::make('product_name')
                ->label('Product Name')
                ->formatStateUsing(fn ($record) => $record->invoice->items->first()?->product->title ?? 'N/A')
                ->disabled(),

            
            Forms\Components\TextInput::make('invoice.items.quantity')
                ->formatStateUsing(fn ($record) => $record->invoice->items->first()?->product->quantity ?? 'N/A')
                ->label('product_quantity')
                ->disabled(),
        
            Forms\Components\TextInput::make('invoice.fulfilment_method')
                ->formatStateUsing(fn ($record) => $record->invoice?->fulfilment_method ?? 'N/A')
                ->label('fulfilment method')
                ->disabled(),

            Forms\Components\TextInput::make('created_at')
                ->label('created_at')
                ->disabled()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('transaction_id')
                ->searchable(),               
            Tables\Columns\TextColumn::make('invoice.items.product.title')
                ->label('product_name'),
            Tables\Columns\TextColumn::make('invoice.items.quantity')
                ->label('product_quantity'),
            Tables\Columns\TextColumn::make('invoice.fulfilment_method')
                ->label('fulfilment method')
                ->searchable(),
            Tables\Columns\TextColumn::make('status')
                    ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('created_at'),
            Tables\Columns\TextColumn::make('total_amount')
                ->label('total amount'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'delivered' => 'Delivered',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                    ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            // 'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
