# Installation et Configuration Laravel Filament

## Installation

### 1. Installer Filament via Composer

```bash
cd backend
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
```

### 2. Créer un utilisateur admin

```bash
php artisan make:filament-user
```

Ou via code:

```php
php artisan tinker

use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'first_name' => 'Admin',
    'last_name' => 'CSS',
    'email' => 'admin@css.tn',
    'password' => Hash::make('password'),
    'role' => 'admin',
    'user_type' => 'socios',
    'email_verified_at' => now(),
]);
```

### 3. Configuration du Panel Admin

Editer `app/Providers/Filament/AdminPanelProvider.php`:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->brandName('CSS Socios Admin')
        ->brandLogo(asset('images/css-logo.png'))
        ->favicon(asset('images/favicon.png'))
        ->colors([
            'primary' => '#000000', // Noir CSS
            'secondary' => '#FFD700', // Jaune CSS
        ])
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->pages([
            Pages\Dashboard::class,
        ])
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->widgets([
            Widgets\AccountWidget::class,
            Widgets\FilamentInfoWidget::class,
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
        ])
        ->authGuard('web');
}
```

### 4. Middleware pour restreindre l'accès admin

Dans `app/Filament/Pages/Auth/Login.php`:

```php
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        if (!Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ], $data['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        $user = Auth::user();

        // Check if user is admin
        if ($user->role !== 'admin') {
            Auth::logout();
            throw ValidationException::withMessages([
                'data.email' => 'Vous n\'avez pas les droits d\'accès à l\'administration.',
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
```

Et mettre à jour `AdminPanelProvider.php`:

```php
->login(App\Filament\Pages\Auth\Login::class)
```

---

## Création des Resources

### 1. AuctionProduct Resource

```bash
php artisan make:filament-resource AuctionProduct --generate
```

Cela créera:
- `app/Filament/Resources/AuctionProductResource.php`
- `app/Filament/Resources/AuctionProductResource/Pages/ListAuctionProducts.php`
- `app/Filament/Resources/AuctionProductResource/Pages/CreateAuctionProduct.php`
- `app/Filament/Resources/AuctionProductResource/Pages/EditAuctionProduct.php`

### 2. DonationGoal Resource

```bash
php artisan make:filament-resource DonationGoal --generate
```

### 3. PaymentMethod Resource

```bash
php artisan make:filament-resource PaymentMethod --generate
```

---

## Widgets Dashboard

### 1. Stats Overview Widget

```bash
php artisan make:filament-widget StatsOverview --resource=DashboardResource
```

Editer `app/Filament/Widgets/StatsOverview.php`:

```php
<?php

namespace App\Filament\Widgets;

use App\Models\AuctionProduct;
use App\Models\DonationGoal;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Enchères Actives', AuctionProduct::active()->count())
                ->description('Total: ' . AuctionProduct::count())
                ->descriptionIcon('heroicon-o-trophy')
                ->color('success'),

            Stat::make('Objectifs de Don Actifs', DonationGoal::active()->count())
                ->description('Montant collecté: ' . number_format(DonationGoal::sum('current_amount'), 2) . ' TND')
                ->descriptionIcon('heroicon-o-heart')
                ->color('warning'),

            Stat::make('Méthodes de Paiement', PaymentMethod::active()->count())
                ->description('Total transactions: ' . PaymentTransaction::count())
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('info'),
        ];
    }
}
```

### 2. Revenue Chart

```bash
php artisan make:filament-widget RevenueChart --chart
```

---

## Navigation

Personnaliser la navigation dans chaque Resource:

```php
protected static ?string $navigationIcon = 'heroicon-o-trophy';
protected static ?string $navigationLabel = 'Enchères';
protected static ?string $navigationGroup = 'Commerce';
protected static ?int $navigationSort = 1;
```

---

## Accès à l'interface admin

Après installation, accédez à:
```
http://localhost:8000/admin
```

Login avec les credentials admin créés.

---

## Permissions avancées (Optionnel)

Si vous voulez des permissions plus granulaires, installer Spatie Permission:

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

Puis créer des permissions:

```php
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Créer les permissions
Permission::create(['name' => 'manage auctions']);
Permission::create(['name' => 'manage donation goals']);
Permission::create(['name' => 'manage payment methods']);

// Créer le rôle admin
$adminRole = Role::create(['name' => 'admin']);
$adminRole->givePermissionTo(Permission::all());

// Assigner le rôle à un utilisateur
$user->assignRole('admin');
```

Et dans les Resources Filament:

```php
public static function can(string $action, ?Model $record = null): bool
{
    return auth()->user()->can('manage auctions');
}
```

---

## Personnalisation supplémentaire

### Thème personnalisé

Créer un thème custom CSS:

```bash
php artisan make:filament-theme
```

### Traduction en français

Publier les traductions:

```bash
php artisan vendor:publish --tag=filament-translations
```

Puis éditer `lang/fr/filament.php` pour personnaliser les traductions.

---

## Resources utiles

- Documentation Filament: https://filamentphp.com/docs
- Filament Plugins: https://filamentphp.com/plugins
- Filament Community: https://filamentphp.com/community
