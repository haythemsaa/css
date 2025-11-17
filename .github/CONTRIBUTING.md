# 🤝 Guide de Contribution - CSS Platform

Merci de contribuer à la plateforme CSS ! Ce guide vous aidera à soumettre des contributions de qualité.

## 🚀 Démarrage Rapide

1. **Fork le repository**
2. **Clone votre fork**
   ```bash
   git clone https://github.com/VOTRE-USERNAME/css.git
   cd css
   ```

3. **Créer une branche**
   ```bash
   git checkout -b feature/ma-fonctionnalite
   # ou
   git checkout -b fix/mon-correctif
   ```

## 📋 Checklist Avant Soumission

### ✅ Code

- [ ] Le code suit les conventions du projet
- [ ] Les tests passent localement
- [ ] Nouveaux tests ajoutés pour les nouvelles fonctionnalités
- [ ] La documentation est mise à jour si nécessaire
- [ ] Pas de console.log ou code de debug

### ✅ Tests

**Backend (Laravel):**
```bash
cd backend
php artisan test              # Tous les tests
php artisan test --coverage   # Avec couverture
./vendor/bin/pint --test      # Style de code
./vendor/bin/phpstan analyse  # Analyse statique
```

**Mobile (React Native):**
```bash
cd mobile
npm test                      # Tests Jest
npm run test:coverage         # Avec couverture
npm run lint                  # Linting (si configuré)
```

**Frontend (React):**
```bash
cd frontend
npm run lint                  # ESLint
npm run build                 # Vérifier le build
npm test                      # Tests (si configurés)
```

### ✅ Commit

Suivez la convention [Conventional Commits](https://www.conventionalcommits.org/):

```
type(scope): description courte

[corps optionnel]

[footer optionnel]
```

**Types:**
- `feat`: Nouvelle fonctionnalité
- `fix`: Correction de bug
- `docs`: Documentation uniquement
- `style`: Formatage, sans changement de code
- `refactor`: Refactoring de code
- `test`: Ajout ou modification de tests
- `chore`: Maintenance, dépendances

**Exemples:**
```bash
feat(auth): add password reset functionality
fix(partners): correct filtering by category
docs(readme): update installation instructions
test(mobile): add tests for Button component
```

## 🔄 Processus de Pull Request

1. **Assurez-vous que votre branche est à jour**
   ```bash
   git fetch origin
   git rebase origin/main
   ```

2. **Push votre branche**
   ```bash
   git push origin feature/ma-fonctionnalite
   ```

3. **Créer la Pull Request sur GitHub**
   - Titre clair et descriptif
   - Description détaillée des changements
   - Lier les issues concernées

4. **Le CI vérifie automatiquement:**
   - ✅ Tests backend (47 tests)
   - ✅ Tests mobile (65 tests)
   - ✅ Qualité du code (lint, PHPStan)
   - ✅ Build frontend

5. **Review du code**
   - Attendre l'approbation d'un mainteneur
   - Répondre aux commentaires
   - Apporter les modifications demandées

6. **Merge** 🎉
   - Le mainteneur merge la PR
   - Suppression de la branche

## 🎨 Standards de Code

### Backend (Laravel/PHP)

- **Style:** Laravel Pint (basé sur PSR-12)
- **Analyse statique:** PHPStan niveau 5
- **Tests:** PHPUnit avec Feature et Unit tests
- **Couverture:** Minimum 80%

```php
// ✅ Bon
public function store(Request $request): JsonResponse
{
    $validated = $request->validated();
    $partner = Partner::create($validated);

    return response()->json([
        'success' => true,
        'data' => new PartnerResource($partner),
    ], 201);
}

// ❌ Mauvais
public function store($request) {
    $partner = Partner::create($request->all());
    return response()->json($partner);
}
```

### Frontend (React/JavaScript)

- **Style:** ESLint configuration du projet
- **Formatage:** Prettier (si configuré)
- **Composants:** Fonctionnels avec Hooks

```jsx
// ✅ Bon
import React, { useState } from 'react';

export const Button = ({ title, onPress, variant = 'primary' }) => {
  const [loading, setLoading] = useState(false);

  return (
    <button
      onClick={onPress}
      disabled={loading}
      className={`btn btn-${variant}`}
    >
      {loading ? 'Chargement...' : title}
    </button>
  );
};

// ❌ Mauvais
export const Button = (props) => {
  return <button onClick={props.onPress}>{props.title}</button>
}
```

### Mobile (React Native)

- **Style:** ESLint React Native
- **Tests:** Jest + React Native Testing Library
- **State:** Zustand pour global state

```jsx
// ✅ Bon
import { render, fireEvent } from '@testing-library/react-native';

test('button calls onPress when pressed', () => {
  const onPress = jest.fn();
  const { getByText } = render(
    <Button title="Click" onPress={onPress} />
  );

  fireEvent.press(getByText('Click'));
  expect(onPress).toHaveBeenCalled();
});
```

## 📝 Documentation

### Documenter les Fonctionnalités

- **API Endpoints:** Ajouter dans la documentation Postman/Swagger
- **Composants React:** Props et exemples d'utilisation
- **Fonctions complexes:** Commentaires JSDoc/PHPDoc

```php
/**
 * Generate a reduction code for a given offer
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  string  $offerSlug
 * @return \Illuminate\Http\JsonResponse
 */
public function generate(Request $request, string $offerSlug): JsonResponse
{
    // Implementation
}
```

```javascript
/**
 * Button component with various styles and states
 *
 * @param {Object} props
 * @param {string} props.title - Button text
 * @param {Function} props.onPress - Click handler
 * @param {string} [props.variant='primary'] - Button style variant
 * @param {boolean} [props.loading=false] - Loading state
 */
export const Button = ({ title, onPress, variant = 'primary', loading = false }) => {
  // Implementation
};
```

## 🐛 Rapporter un Bug

Créer une issue avec:

1. **Titre clair:** "[Bug] Impossible de se connecter"
2. **Description:** Que se passe-t-il ?
3. **Reproduction:** Étapes pour reproduire
4. **Attendu vs Obtenu:** Ce qui devrait se passer
5. **Environnement:** OS, navigateur, version PHP/Node
6. **Screenshots:** Si applicable

## 💡 Proposer une Fonctionnalité

Créer une issue avec:

1. **Titre:** "[Feature] Ajouter notification push"
2. **Problème:** Quel problème résout-elle ?
3. **Solution proposée:** Comment l'implémenter ?
4. **Alternatives:** Autres approches considérées
5. **Contexte:** Informations supplémentaires

## 🎯 Priorités

### Haute Priorité
- 🔴 Bugs critiques (blocants)
- 🟠 Problèmes de sécurité
- 🟡 Corrections de tests

### Priorité Normale
- 🔵 Nouvelles fonctionnalités
- 🟢 Améliorations de performance
- 🟣 Refactoring

### Basse Priorité
- ⚪ Documentation
- ⚫ Optimisations mineures

## 📞 Contact

- **Issues:** [GitHub Issues](https://github.com/haythemsaa/css/issues)
- **Discussions:** [GitHub Discussions](https://github.com/haythemsaa/css/discussions)
- **Email:** support@css-platform.com (si configuré)

## 📜 Code de Conduite

- Respecter tous les contributeurs
- Être constructif dans les reviews
- Accepter les critiques avec professionnalisme
- Se concentrer sur le code, pas sur les personnes

## 🙏 Merci !

Merci de contribuer à la plateforme CSS ! Chaque contribution, grande ou petite, est précieuse.

---

**Note:** Ce guide est maintenu par l'équipe CSS Platform. Pour toute question, n'hésitez pas à ouvrir une discussion.
