<?php

namespace App\Filament\Resources\Partners\Schemas;

use App\Models\PartnerCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Générales')
                    ->description('Informations de base du partenaire')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom du partenaire')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Sera généré automatiquement à partir du nom'),

                        Select::make('category_id')
                            ->label('Catégorie')
                            ->relationship('category', 'name_fr')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Grid::make(2)
                            ->schema([
                                FileUpload::make('logo')
                                    ->label('Logo')
                                    ->image()
                                    ->maxSize(2048)
                                    ->directory('partners/logos'),

                                FileUpload::make('cover_image')
                                    ->label('Image de couverture')
                                    ->image()
                                    ->maxSize(5120)
                                    ->directory('partners/covers'),
                            ]),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull()
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Section::make('Réductions CSS Privilèges')
                    ->description('Configuration des réductions par type d\'utilisateur')
                    ->schema([
                        Select::make('reduction_type')
                            ->label('Type de réduction')
                            ->options([
                                'percentage' => 'Pourcentage (%)',
                                'fixed' => 'Montant fixe (TND)',
                            ])
                            ->required()
                            ->default('percentage')
                            ->live(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('reduction_value_premium')
                                    ->label(fn (Get $get) => 'Réduction Premium ' . ($get('reduction_type') === 'percentage' ? '(%)' : '(TND)'))
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(fn (Get $get) => $get('reduction_type') === 'percentage' ? 100 : 10000)
                                    ->suffix(fn (Get $get) => $get('reduction_type') === 'percentage' ? '%' : 'TND'),

                                TextInput::make('reduction_value_socios')
                                    ->label(fn (Get $get) => 'Réduction Socios ' . ($get('reduction_type') === 'percentage' ? '(%)' : '(TND)'))
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(fn (Get $get) => $get('reduction_type') === 'percentage' ? 100 : 10000)
                                    ->suffix(fn (Get $get) => $get('reduction_type') === 'percentage' ? '%' : 'TND'),
                            ]),

                        Textarea::make('conditions')
                            ->label('Conditions d\'utilisation')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Conditions spécifiques pour bénéficier de la réduction'),

                        Textarea::make('exclusions')
                            ->label('Exclusions')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Produits ou services exclus de la réduction'),
                    ])
                    ->columns(2),

                Section::make('Coordonnées et Localisation')
                    ->description('Informations de contact et géolocalisation')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('website')
                            ->label('Site web')
                            ->url()
                            ->maxLength(255)
                            ->prefix('https://'),

                        TextInput::make('address')
                            ->label('Adresse')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('city')
                                    ->label('Ville')
                                    ->maxLength(100),

                                TextInput::make('governorate')
                                    ->label('Gouvernorat')
                                    ->maxLength(100),

                                TextInput::make('postal_code')
                                    ->label('Code postal')
                                    ->maxLength(10),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->helperText('Pour la recherche à proximité'),

                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->helperText('Pour la recherche à proximité'),
                            ]),

                        Textarea::make('opening_hours')
                            ->label('Horaires d\'ouverture')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Format JSON: {"lundi": "9h-18h", "mardi": "9h-18h", ...}'),
                    ])
                    ->columns(2),

                Section::make('Configuration Avancée')
                    ->description('Paramètres de contrat et affichage')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('status')
                                    ->label('Statut')
                                    ->options([
                                        'active' => 'Actif',
                                        'inactive' => 'Inactif',
                                        'pending' => 'En attente',
                                        'suspended' => 'Suspendu',
                                    ])
                                    ->required()
                                    ->default('pending'),

                                Toggle::make('is_online')
                                    ->label('Partenaire en ligne')
                                    ->helperText('Le partenaire a une boutique en ligne'),

                                Toggle::make('is_geolocation_enabled')
                                    ->label('Géolocalisation active')
                                    ->helperText('Afficher dans la recherche à proximité'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('contract_start_date')
                                    ->label('Début du contrat'),

                                DatePicker::make('contract_end_date')
                                    ->label('Fin du contrat'),
                            ]),

                        TextInput::make('commission_percentage')
                            ->label('Commission CSS (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->helperText('Pourcentage de commission sur les ventes'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('capacity_daily')
                                    ->label('Capacité journalière')
                                    ->numeric()
                                    ->helperText('Nombre maximum de codes par jour'),

                                TextInput::make('capacity_weekly')
                                    ->label('Capacité hebdomadaire')
                                    ->numeric()
                                    ->helperText('Nombre maximum de codes par semaine'),
                            ]),

                        TextInput::make('featured_order')
                            ->label('Ordre d\'affichage mis en avant')
                            ->numeric()
                            ->default(0)
                            ->helperText('0 = non mis en avant, 1+ = position dans les partenaires vedettes'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
