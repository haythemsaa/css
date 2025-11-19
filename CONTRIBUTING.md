# 🤝 Guide de Contribution - CSS Platform

Merci de votre intérêt pour contribuer au projet CSS Platform! Ce guide vous aidera à démarrer.

---

## 📋 Table des matières

- [Code de conduite](#code-de-conduite)
- [Comment contribuer](#comment-contribuer)
- [Standards de code](#standards-de-code)
- [Workflow Git](#workflow-git)
- [Tests](#tests)
- [Documentation](#documentation)
- [Questions et support](#questions-et-support)

---

## 📜 Code de conduite

En participant à ce projet, vous acceptez de respecter notre code de conduite:

- Être respectueux envers tous les contributeurs
- Accepter les critiques constructives
- Se concentrer sur ce qui est meilleur pour la communauté
- Faire preuve d'empathie envers les autres membres

---

## 🚀 Comment contribuer

### 1. Fork et Clone

```bash
# Fork le projet sur GitHub
# Puis clonez votre fork
git clone https://github.com/VOTRE-USERNAME/css.git
cd css
```

### 2. Installation

```bash
# Installation automatique
./setup.sh

# Ou avec Make
make install
make setup
```

### 3. Créer une branche

```bash
# Créez une branche pour votre fonctionnalité
git checkout -b feature/ma-nouvelle-fonctionnalite

# Ou pour un bug fix
git checkout -b fix/correction-bug-xyz
```

### 4. Développer

Développez votre fonctionnalité en suivant les [standards de code](#standards-de-code).

### 5. Tester

```bash
# Lancez tous les tests
make test

# Tests spécifiques
make test-backend
make test-frontend
make test-mobile

# Vérifier la qualité du code
make quality
```

### 6. Commit

```bash
# Ajoutez vos changements
git add .

# Commit avec un message descriptif
git commit -m "feat: ajout de la fonctionnalité X"
```

Voir [Convention de commit](#convention-de-commit) pour plus de détails.

### 7. Push et Pull Request

```bash
# Push vers votre fork
git push origin feature/ma-nouvelle-fonctionnalite

# Ouvrez une Pull Request sur GitHub
```

---

## 💻 Standards de code

### Backend (Laravel/PHP)

#### Style de code

- **Standard**: PSR-12
- **Linter**: Laravel Pint
- **Analyse statique**: PHPStan niveau 5

```bash
# Formater le code
cd backend
./vendor/bin/pint

# Analyse statique
./vendor/bin/phpstan analyse

# Ou avec Make
make lint
make phpstan
```

#### Conventions

- **Noms de classes**: PascalCase (ex: `UserController`)
- **Noms de méthodes**: camelCase (ex: `getUserById()`)
- **Noms de variables**: camelCase (ex: `$userName`)
- **Noms de tables**: snake_case pluriel (ex: `user_subscriptions`)
- **Noms de colonnes**: snake_case (ex: `created_at`)

#### Structure

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Récupère un utilisateur par ID
     *
     * @param int $id L'ID de l'utilisateur
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'data' => $user,
            'message' => 'Utilisateur récupéré avec succès'
        ]);
    }
}
```

### Frontend (React/JavaScript)

#### Style de code

- **Standard**: ESLint + Prettier
- **Linter**: ESLint avec config React

```bash
# Linter
cd frontend
npm run lint

# Fix automatique
npm run lint -- --fix
```

#### Conventions

- **Noms de composants**: PascalCase (ex: `UserProfile.jsx`)
- **Noms de fonctions**: camelCase (ex: `handleSubmit`)
- **Noms de variables**: camelCase (ex: `userName`)
- **Noms de constantes**: UPPER_SNAKE_CASE (ex: `API_BASE_URL`)
- **Noms de fichiers**: PascalCase pour composants, camelCase pour utilitaires

#### Structure des composants

```jsx
import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';

/**
 * Composant pour afficher le profil utilisateur
 * @param {Object} props - Les props du composant
 * @param {Object} props.user - L'utilisateur à afficher
 */
const UserProfile = ({ user }) => {
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    // Effect logic
  }, []);

  const handleSubmit = (e) => {
    e.preventDefault();
    // Submit logic
  };

  return (
    <div className="user-profile">
      {/* Component JSX */}
    </div>
  );
};

UserProfile.propTypes = {
  user: PropTypes.shape({
    id: PropTypes.number.isRequired,
    name: PropTypes.string.isRequired,
  }).isRequired,
};

export default UserProfile;
```

### Mobile (React Native)

- Mêmes conventions que Frontend
- Utiliser les composants natifs quand possible
- Tester sur iOS ET Android

---

## 🌿 Workflow Git

### Branches

- **main**: Production (protégée)
- **develop**: Développement
- **feature/**: Nouvelles fonctionnalités
- **fix/**: Corrections de bugs
- **hotfix/**: Corrections urgentes production
- **refactor/**: Refactoring de code
- **docs/**: Documentation

### Convention de commit

Utilisez [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <description>

[corps optionnel]

[footer optionnel]
```

#### Types

- **feat**: Nouvelle fonctionnalité
- **fix**: Correction de bug
- **docs**: Documentation uniquement
- **style**: Formatage, point-virgules manquants, etc.
- **refactor**: Refactoring de code
- **test**: Ajout ou modification de tests
- **chore**: Maintenance (dépendances, config, etc.)
- **perf**: Amélioration de performance

#### Exemples

```bash
feat(partners): ajout de la recherche par catégorie

Permet aux utilisateurs de filtrer les partenaires par catégorie.
Ajoute un nouveau composant CategoryFilter.

Closes #123

---

fix(auth): correction du refresh token

Le token n'était pas correctement rafraîchi après expiration.

---

docs: mise à jour du README avec instructions Docker

---

test(api): ajout tests pour endpoint /codes/generate
```

### Pull Request

#### Checklist

Avant de soumettre une PR, assurez-vous que:

- [ ] Le code suit les standards du projet
- [ ] Tous les tests passent (`make test`)
- [ ] Le linter ne rapporte aucune erreur (`make lint`)
- [ ] La documentation est mise à jour si nécessaire
- [ ] Les commits suivent la convention
- [ ] La branche est à jour avec `develop`

#### Template

```markdown
## Description

Décrivez les changements effectués.

## Type de changement

- [ ] Bug fix
- [ ] Nouvelle fonctionnalité
- [ ] Breaking change
- [ ] Documentation

## Comment tester

1. Étape 1
2. Étape 2
3. Étape 3

## Screenshots (si applicable)

![Screenshot](url)

## Checklist

- [ ] Mon code suit les standards du projet
- [ ] J'ai testé mes changements
- [ ] J'ai mis à jour la documentation
- [ ] Tous les tests passent
```

---

## 🧪 Tests

### Écrire des tests

#### Backend (PHPUnit)

```php
<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * Test de récupération d'un utilisateur
     */
    public function test_can_retrieve_user(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ]
            ]);
    }
}
```

#### Frontend (Vitest)

```javascript
import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import UserProfile from './UserProfile';

describe('UserProfile', () => {
  it('affiche le nom de l\'utilisateur', () => {
    const user = { id: 1, name: 'John Doe' };

    render(<UserProfile user={user} />);

    expect(screen.getByText('John Doe')).toBeInTheDocument();
  });
});
```

#### Mobile (Jest)

```javascript
import React from 'react';
import { render } from '@testing-library/react-native';
import UserProfile from './UserProfile';

describe('UserProfile', () => {
  it('affiche le nom de l\'utilisateur', () => {
    const user = { id: 1, name: 'John Doe' };

    const { getByText } = render(<UserProfile user={user} />);

    expect(getByText('John Doe')).toBeTruthy();
  });
});
```

### Exécuter les tests

```bash
# Tous les tests
make test

# Backend uniquement
make test-backend

# Frontend uniquement
make test-frontend

# Mobile uniquement
make test-mobile

# Avec coverage
make test-coverage
```

### Objectifs de coverage

- **Minimum**: 70%
- **Cible**: 80%
- **Excellent**: >90%

---

## 📚 Documentation

### Code

- Commentez le code complexe
- Utilisez des docblocks pour les fonctions/méthodes
- Gardez les commentaires à jour

### README et guides

Si vous ajoutez une fonctionnalité majeure, mettez à jour:

- `README.md` - Vue d'ensemble
- `API_DOCUMENTATION.md` - Si vous ajoutez des endpoints
- `QUICKSTART.md` - Si cela affecte l'installation
- `DEPLOYMENT.md` - Si cela affecte le déploiement

### Exemples

Ajoutez des exemples d'utilisation pour les nouvelles fonctionnalités.

---

## ❓ Questions et support

### Besoin d'aide?

- **Documentation**: Lisez d'abord la [documentation complète](README.md)
- **Issues**: Recherchez dans les [issues existantes](https://github.com/haythemsaa/css/issues)
- **Nouvelle issue**: [Créez une issue](https://github.com/haythemsaa/css/issues/new)
- **Email**: dev@css.tn

### Proposer une fonctionnalité

1. Vérifiez qu'elle n'existe pas déjà
2. Ouvrez une issue "Feature Request"
3. Décrivez clairement le besoin et la solution proposée
4. Attendez les retours avant de coder

---

## 🏆 Reconnaissance

Tous les contributeurs seront mentionnés dans:

- Le fichier `CONTRIBUTORS.md`
- Les release notes
- Le README du projet

Merci de contribuer au projet CSS Platform! 🎉

---

<div align="center">

**⚽ يا CSS يا نجوم السما ⚽**

*Développé avec ❤️ pour le Club Sportif Sfaxien*

</div>
