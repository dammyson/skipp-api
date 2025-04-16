<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Wallet;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Actions;

use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Grid;
use Illuminate\Database\Eloquent\Builder;
// use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\WalletResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WalletResource\RelationManagers;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return Wallet::query()->where('user_id', Auth::id()); // Show only the logged-in user's wallet
    }

    public static function form(Form $form): Form
    { 
        return $form
            ->schema([
            Section::make()
                ->schema([
                    Forms\Components\Hidden::make('user_id') // Hide the field from the form
                    ->default(auth()->id()) ,// Automatically set the current user's ID,

               

                // Actions::make([
                //     Action::make('amount')
                //         ->label('top up')
                //         ->color('success')
                //         ->form([
                //             Forms\Components\TextInput::make('amount')->required(),
                //     ])->action(function (array $data) {
                //         // ->url(fn (): string => route('posts.edit', ['post' => $this->post]))

                //         $wallet = Wallet::where('user_id', Auth::user()->id)->first();
                //         $wallet->topUp($data['amount']);
                //         return redirect()->route('payment', ['amount' => $data['amount']]);
                //         // redirect()->route('payment', $data['amount']);
                //     //    dd('funds top up');
                //     //     $user_id = Auth::user()->id;
                //     //    $wallet = Wallet::where('user_id', $user_id)->get();
                        
                //     //    try {
                //     //        $new_top_request = new VerificationService($data['ref']);
                //     //        $verified_request = $new_top_request->run();
                //     //         $top_up  =  new TopUpService($verified_request,  $wallet);
                //     //         $top_up_result =  $top_up->run();
                //     //        return response()->json(['status' => true, 'data' =>  $top_up_result, 'message' => 'Wallet top up successfully'], 200);
                //     //    } catch (\Exception $exception) {
                //     //        Log::error($exception->getMessage());
                //     //        return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
                //     //    }
                //     })
                // ]),

                // Actions::make([
                //     Action::make('amount_deduct')
                //         ->label('withdraw')
                //         ->color('danger')
                //         ->form([
                //             Forms\Components\TextInput::make('amount')->required(),
                //     ])->action(function (array $data) {

                //         $wallet = Wallet::where('user_id', Auth::user()->id)->first();
                //         $wallet->topDown($data['amount']);
                    
                //    })
                // ]),
                ])
               
            ]);
    }

    public static function table(Table $table): Table
    {   
         return $table
         ->paginated(false)
            ->columns([

                Tables\Columns\TextColumn::make('wallet_id')
                ->label('Wallet ID')
                ->getStateUsing(fn (Wallet $record) => $record->id),
    
                Tables\Columns\TextColumn::make('balance')
                    ->label('Wallet Balance'),
        
                Tables\Columns\TextColumn::make('ledger_balance')
                    ->label('Ledger Balance'),
            // Grid::make([
            //     'lg' => 1,
            // ])
            // ->schema([
            //     Tables\Columns\TextColumn::make('wallet_id')
            //     ->label("wallet_id")
            //     ->getStateUsing(fn (Wallet $record) =>  $record->id),
            //     TextColumn::make('balance')
            //     ->label("wallet balance"),
            //     TextColumn::make('ledger_balance')
            //     ->label("ledger balance"),

               
            // ])
        ])
        // return $table
        //     ->columns([
        //         // Tables\Columns\TextColumn::make('reference'),
                
        //         Tables\Columns\TextColumn::make('wallet_id')
        //         ->getStateUsing(fn (Wallet $record) =>  $record->id), // Call the function
        //         Tables\Columns\TextColumn::make('balance')
        //             ->label('balance'),
        //         Tables\Columns\TextColumn::make('ledger_balance')

        //             ->label('ledger_balance'),
               
            // ])
            ->filters([
                //
            ])
            ->actions([

                Action::make('amount_deduct')
                        ->label('send')
                        ->color('danger')
                        ->form([
                            Forms\Components\TextInput::make('amount')->required(),
                    ])->action(function (array $data) {

                        $wallet = Wallet::where('user_id', Auth::user()->id)->first();
                        $wallet->topDown($data['amount']);
                    
                   }),
               
                Action::make('amount')
                    ->label('receive')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')->required(),
                ])->action(function (array $data) {
                    // ->url(fn (): string => route('posts.edit', ['post' => $this->post]))

                    $wallet = Wallet::where('user_id', Auth::user()->id)->first();
                    redirect()->route('payment', ['amount' => $data['amount']]);
                    $wallet->topUp($data['amount']);
                    // redirect()->route('payment', $data['amount']);
                //    dd('funds top up');
                //     $user_id = Auth::user()->id;
                //    $wallet = Wallet::where('user_id', $user_id)->get();
                    
                //    try {
                //        $new_top_request = new VerificationService($data['ref']);
                //        $verified_request = $new_top_request->run();
                //         $top_up  =  new TopUpService($verified_request,  $wallet);
                //         $top_up_result =  $top_up->run();
                //        return response()->json(['status' => true, 'data' =>  $top_up_result, 'message' => 'Wallet top up successfully'], 200);
                //    } catch (\Exception $exception) {
                //        Log::error($exception->getMessage());
                //        return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
                //    }
                })

           
                    
                ])
                ->bulkActions([]); 

                // Tables\Actions\EditAction::make(),
           
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
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
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }
}
