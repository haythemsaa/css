## 📝 Description

<!-- Décrivez brièvement les changements apportés -->

## 🎯 Type de changement

<!-- Cochez les cases appropriées -->

- [ ] 🐛 Bug fix (correction non-breaking)
- [ ] ✨ Nouvelle fonctionnalité (non-breaking)
- [ ] 💥 Breaking change (correction ou fonctionnalité qui casse le code existant)
- [ ] 📝 Documentation uniquement
- [ ] 🎨 Style/formatage (sans changement fonctionnel)
- [ ] ♻️ Refactoring (sans changement fonctionnel)
- [ ] ⚡ Amélioration de performance
- [ ] ✅ Tests (ajout ou modification)
- [ ] 🔧 Configuration/Chore

## 🔗 Issue liée

<!-- Liez l'issue GitHub concernée (ex: #123) -->

Fixes #(issue)

## 📊 Composants affectés

<!-- Cochez les composants modifiés -->

- [ ] 🔧 Backend (Laravel)
- [ ] 🎨 Frontend (React)
- [ ] 📱 Mobile (React Native)
- [ ] 🗄️ Base de données (migrations)
- [ ] 📚 Documentation
- [ ] 🚀 CI/CD
- [ ] 🔐 Sécurité

## ✅ Checklist

### Tests

- [ ] Les tests existants passent (`php artisan test`, `npm test`)
- [ ] Nouveaux tests ajoutés pour les nouvelles fonctionnalités
- [ ] Tests unitaires couvrent les cas limites
- [ ] Tests d'intégration couvrent les flux complets

### Qualité du Code

- [ ] Code conforme aux standards du projet
- [ ] Pas de `console.log` ou code de debug
- [ ] Variables et fonctions bien nommées
- [ ] Commentaires ajoutés pour la logique complexe
- [ ] Pas de code mort (unused imports, variables)

### Backend (si applicable)

- [ ] Laravel Pint: `./vendor/bin/pint --test`
- [ ] PHPStan: `./vendor/bin/phpstan analyse`
- [ ] Migrations testées (up et down)
- [ ] Seeders mis à jour si nécessaire
- [ ] API Resources utilisés pour les réponses

### Frontend/Mobile (si applicable)

- [ ] ESLint: `npm run lint`
- [ ] Build réussit: `npm run build`
- [ ] Composants testés
- [ ] Responsive design vérifié
- [ ] Accessibilité considérée

### Documentation

- [ ] README mis à jour (si nécessaire)
- [ ] Commentaires de code ajoutés
- [ ] API documentée (Postman/Swagger)
- [ ] CHANGELOG mis à jour (si applicable)

## 🧪 Comment tester

<!-- Décrivez comment tester vos changements -->

1. Étape 1
2. Étape 2
3. Étape 3

## 📸 Screenshots (si applicable)

<!-- Ajoutez des captures d'écran pour les changements UI -->

## 📝 Notes pour les reviewers

<!-- Informations supplémentaires pour les reviewers -->

## 🚀 Déploiement

<!-- Instructions spéciales pour le déploiement (si nécessaire) -->

- [ ] Nécessite des migrations de base de données
- [ ] Nécessite des changements de configuration
- [ ] Nécessite des variables d'environnement
- [ ] Impact sur les données existantes

---

## ✅ Validation CI/CD

<!-- Les checks CI/CD doivent passer avant le merge -->

- [ ] Backend Tests (47 tests)
- [ ] Frontend Build
- [ ] Mobile Tests (65 tests)
- [ ] Code Quality (Lint + PHPStan)

<!-- Les checks s'exécutent automatiquement lors de la création de la PR -->
