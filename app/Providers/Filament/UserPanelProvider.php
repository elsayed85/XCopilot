<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->default()
            ->login()
            ->registration()
            ->profile()
            ->authGuard('web')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->navigationItems([
                NavigationItem::make('')
                    ->label('Chat')
                    ->url(fn (): string => route('chat.index'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text'),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Chat')
                    ->url(fn (): string => route('chat.index'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text'),
            ])
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->plugins([
                BreezyCore::make()
                    ->myProfile(shouldRegisterNavigation: true, hasAvatars: true)
                    ->passwordUpdateRules(rules: [Password::default()->mixedCase()->uncompromised(3)])
                    ->enableTwoFactorAuthentication()
                    ->enableSanctumTokens(permissions : ['*'])
                    ->avatarUploadComponent(fn ($fileUpload) => $fileUpload->disableLabel()),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
