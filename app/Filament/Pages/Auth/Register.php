<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.pages.auth.register';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getFirstNameFormComponent(), 
                        $this->getLastNameFormComponent(), 
                        $this->getBusinessNameFormComponent(), 
                        $this->getBusinessAddressFormComponent(), 
                        $this->getStoreTypeFormComponent(), 
                        $this->getPhoneNumberFormComponent(), 
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getRoleFormComponent(), 
                    ])
                    ->statePath('data'),
            ),
        ];
    }
 
    protected function getFirstNameFormComponent(): Component
    {
        return TextInput::make('first_name')            
            ->required();
    }
    protected function getLastNameFormComponent(): Component
    {
        return TextInput::make('last_name')            
            ->required();
    }
    protected function getBusinessNameFormComponent(): Component
    {
        return TextInput::make('business_name');
    }


    protected function getStoreTypeFormComponent(): Component
    {
        return Select::make('store_type')
            ->options([
                'road side kisok' => 'road-side-kisok',
                'small retail' => 'small-retails',
                'large' => 'wholesale',
            ])
            ->default('small retail')
            ->required();
    }

    protected function getPhoneNumberFormComponent(): Component
    {
        return TextInput::make('phone_number')
            ->required();
    }

    protected function getBusinessAddressFormComponent(): Component
    {
        return TextInput::make('business_address')
            ->required();
    }
    

    protected function getRoleFormComponent(): Component
    {
        return Select::make('user_type')
            ->options([
                'vendor' => 'Vendor',
                'super-admin' => 'super-admin',
            ])
            ->label('role')
            ->default('vendor')
            ->required();
    }
}
