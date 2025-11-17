# Panel Admin Filament - CSS Platform

## Accès au Panel Admin

### URL d'accès
```
http://votre-domaine/admin
```

### Identifiants de test

**Administrateur Principal**
```
Email: admin@css.tn
Password: password
```

### Permissions d'accès

Le panel admin est accessible uniquement aux utilisateurs:
- Email: `admin@css.tn` (admin principal)
- Type: Socios vérifiés (`user_type = 'socios'` ET `socios_verified = true`)

## Ressources disponibles

### 1. **Partenaires** (CSS Privilèges)
- **Catégories de Partenaires**: Gestion des 8 catégories (Restauration, Shopping, etc.)
- **Partenaires**: CRUD complet avec:
  - Informations générales (nom, logo, description)
  - Réductions CSS Privilèges (Premium et Socios)
  - Coordonnées et géolocalisation
  - Configuration avancée (contrats, capacités, statut)
- **Offres Partenaires**: Gestion des offres spéciales

### 2. **Utilisateurs**
- Liste complète des utilisateurs
- Filtres par type (Free/Premium/Socios)
- Gestion des abonnements
- Vérification Socios
- Points de fidélité

### 3. **Contenu**
- Articles
- Vidéos (avec qualité SD/HD/4K)
- Galeries
- Podcasts
- Gestion du contenu premium

### 4. **Équipe** (Joueurs)
- Effectif complet
- Statistiques joueurs
- Contrats

### 5. **Matchs**
- Calendrier des matchs
- Résultats
- Compétitions (Ligue 1, Coupe, CAF)

## Fonctionnalités du Panel

### Navigation organisée

- **CSS Privilèges**: Partenaires, Catégories, Offres, Codes de réduction
- **Contenu**: Articles, Vidéos, Galeries
- **Club**: Joueurs, Matchs, Saison
- **Utilisateurs**: Gestion des membres
- **Paramètres**: Configuration générale

### Formulaires intelligents

- Auto-génération des slugs
- Upload d'images (logos, couvertures)
- Validation en temps réel
- Sections collapsibles pour les options avancées
- Helper texts pour guider la saisie

### Tables optimisées

- Colonnes configurables (show/hide)
- Filtres multiples (statut, catégorie, ville)
- Recherche instantanée
- Actions groupées (suppression, restauration)
- Soft deletes activés

### Badges et indicateurs

- Statut des partenaires (Actif/Inactif/En attente/Suspendu)
- Réductions colorées (Premium en vert, Socios en orange)
- Partenaires vedettes avec étoiles
- Indicateurs visuels (en ligne, géolocalisation)

## Modification des données

### Exemple: Ajouter un nouveau partenaire

1. Naviguer vers **CSS Privilèges > Partenaires**
2. Cliquer sur **Nouveau partenaire**
3. Remplir les sections:
   - **Informations Générales**: Nom (slug généré auto), catégorie, logo, description
   - **Réductions CSS Privilèges**: Type (pourcentage/fixe), valeur Premium, valeur Socios
   - **Coordonnées**: Téléphone, email, site web, adresse, géolocalisation
   - **Configuration Avancée**: Statut, contrat, capacités, mise en vedette
4. Sauvegarder

### Personnalisation

Le formulaire s'adapte dynamiquement:
- Le label des réductions change selon le type sélectionné (% ou TND)
- Les suffixes s'affichent automatiquement
- Les limites min/max sont validées
- Les sections avancées peuvent être repliées

## Statistiques Dashboard

Le dashboard affiche:
- Nombre total de partenaires actifs
- Codes de réduction générés aujourd'hui
- Revenus CSS Privilèges du mois
- Nouveaux membres Socios
- Utilisateurs actifs
- Matchs à venir

## Développement

### Créer une nouvelle ressource Filament

```bash
php artisan make:filament-resource NomModele --generate
```

### Structure des ressources

```
app/Filament/Resources/
├── Partners/
│   ├── PartnerResource.php         # Configuration principale
│   ├── Schemas/
│   │   └── PartnerForm.php          # Formulaire
│   ├── Tables/
│   │   └── PartnersTable.php        # Table de liste
│   └── Pages/
│       ├── CreatePartner.php
│       ├── EditPartner.php
│       └── ListPartners.php
```

### Personnaliser une ressource

Dans `PartnerResource.php`:
```php
protected static ?string $navigationLabel = 'Partenaires';
protected static ?string $navigationGroup = 'CSS Privilèges';
protected static ?int $navigationSort = 1;
protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;
```

### Filament v4 Features utilisées

- Schemas & Forms avec sections
- Live updates (reactive forms)
- Helper texts
- Image uploads
- Relationship selects
- Badge columns
- Filtres avancés
- Actions groupées
- Soft deletes

## Sécurité

- Authentification obligatoire
- Accès limité aux Socios vérifiés + admin
- Protection CSRF
- Validation des formulaires
- Soft deletes pour historique
- Logs d'activité

## Performance

- Eager loading des relations
- Pagination automatique (15 items/page)
- Colonnes toggleable pour réduire les données
- Cache des relations (preload)
- Indexes sur les colonnes fréquentes

## Support

Pour toute question ou problème:
1. Consulter la documentation Filament: https://filamentphp.com/docs
2. Vérifier les logs Laravel: `storage/logs/laravel.log`
3. Contacter l'équipe technique CSS

---

**Version**: 1.0
**Date**: Novembre 2025
**Framework**: Filament v4 + Laravel 12
