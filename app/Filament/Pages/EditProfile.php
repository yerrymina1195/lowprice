<?php

namespace App\Filament\Pages;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\Concerns;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Auth\Authenticatable;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $view = 'filament.pages.edit-profile';
    protected static bool $shouldRegisterNavigation = false;
    public ?array $profileData = [];
    public ?array $passwordData = [];
    public function mount(): void
    {
        $this->fillForms();
    }
    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editPasswordForm',
        ];
    }
    public function editProfileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')->aside()
                    ->description('Update your account\'s profile information and email address.')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('profileData');
    }
    public function editPasswordForm(Form $form): Form
    {
        return  $form->schema([
            Forms\Components\Section::make('Update Password')->aside()
                ->description('Ensure your account is using long, random password to stay secure.')
                ->schema([
                    Forms\Components\TextInput::make('Current password')
                        ->password()
                        ->required()
                        ->currentPassword(),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required()
                        ->rule(Password::default())
                        ->autocomplete('new-password')
                        ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                        ->live(debounce: 500)
                        ->same('passwordConfirmation'),
                    Forms\Components\TextInput::make('passwordConfirmation')
                        ->password()
                        ->required()
                        ->dehydrated(false),
                ]),
        ])
            ->model($this->getUser())
            ->statePath('passwordData');
    }
    protected function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();
        if (!$user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }
        return $user;
    }
    protected function fillForms(): void
    {
        $data = $this->getUser()->attributesToArray();
        $this->editProfileForm->fill($data);
        $this->editPasswordForm->fill();
    }


       //...
       protected function getUpdateProfileFormActions(): array
       {
           return [
               Action::make('updateProfileAction')
                   ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                   ->submit('editProfileForm'),
           ];
       }
       protected function getUpdatePasswordFormActions(): array
       {
           return [
               Action::make('updatePasswordAction')
                   ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                   ->submit('editPasswordForm'),
           ];
       }
       //...


       public function updateProfile(): void
       {
           $data = $this->editProfileForm->getState();
           $this->handleRecordUpdate($this->getUser(), $data);
           $this->sendSuccessNotification(); 
       }
       public function updatePassword(): void
       {
           $data = $this->editPasswordForm->getState();
           $this->handleRecordUpdate($this->getUser(), $data);
           if (request()->hasSession() && array_key_exists('password', $data)) {
               request()->session()->put(['password_hash_' . Filament::getAuthGuard() => $data['password']]);
           }
           $this->editPasswordForm->fill();
           $this->sendSuccessNotification(); 
       }
       private function handleRecordUpdate(Model $record, array $data): Model
       {
           $record->update($data);
           return $record;
       }

       
}



// use Filament\Pages\Page;

// class EditProfile extends Page implements HasForms
// {
//     protected static ?string $navigationIcon = 'heroicon-o-document-text';

//     use InteractsWithForms;
//     protected static string $view = 'filament.pages.edit-profile';
//     protected static bool $shouldRegisterNavigation = false;
// }