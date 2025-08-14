# Architecture Atomique avec Shadcn/ui

Ce projet utilise l'architecture de design atomique combinée avec Shadcn/ui pour créer une interface utilisateur cohérente et maintenable.

## Structure des Composants

### 🧪 Atoms (Composants de base)
Les composants atomiques sont les éléments les plus basiques de l'interface.

**Localisation:** `src/components/atoms/`

- **BaseButton** - Bouton avec variantes (default, destructive, outline, etc.)
- **BaseInput** - Champ de saisie avec gestion d'erreurs
- **BaseCard** - Conteneur avec header, content et footer
- **BaseBadge** - Badge avec variantes de couleur et taille

### 🧬 Molecules (Composants composés)
Les molécules combinent plusieurs atomes pour créer des fonctionnalités spécifiques.

**Localisation:** `src/components/molecules/`

- **FormField** - Champ de formulaire avec label et description
- **TaskCard** - Carte de tâche avec actions
- **LoginForm** - Formulaire de connexion complet

### 🦠 Organisms (Composants complexes)
Les organismes sont des sections complètes de l'interface.

**Localisation:** `src/components/organisms/`

- **TaskList** - Liste de tâches avec filtres et pagination
- **TaskForm** - Formulaire complet de création/édition de tâche

### 🎨 UI Components (Styles Shadcn)
Les composants UI contiennent les styles et variantes Shadcn/ui.

**Localisation:** `src/components/ui/`

- **button.js** - Variantes de boutons
- **input.js** - Variantes de champs de saisie

## Utilisation

### Import des composants
```javascript
// Import individuel
import BaseButton from '@/components/atoms/BaseButton.vue'
import TaskList from '@/components/organisms/TaskList.vue'

// Import depuis l'index
import { BaseButton, TaskList } from '@/components'
```

### Exemple d'utilisation
```vue
<template>
  <BaseCard>
    <template #header>
      <h2>Titre de la carte</h2>
    </template>
    
    <div class="space-y-4">
      <FormField label="Nom" required>
        <BaseInput
          v-model="name"
          placeholder="Votre nom"
          :error="errors.name"
        />
      </FormField>
      
      <BaseButton
        variant="default"
        size="lg"
        :loading="loading"
        @click="handleSubmit"
      >
        Soumettre
      </BaseButton>
    </div>
  </BaseCard>
</template>
```

## Variantes et Props

### BaseButton
```vue
<BaseButton
  variant="default|destructive|outline|secondary|ghost|link"
  size="default|sm|lg|icon"
  :loading="boolean"
  :disabled="boolean"
  @click="handler"
>
  Contenu du bouton
</BaseButton>
```

### BaseInput
```vue
<BaseInput
  v-model="value"
  type="text|email|password|date"
  placeholder="Placeholder"
  :error="string"
  :disabled="boolean"
  @input="handler"
  @blur="handler"
  @focus="handler"
/>
```

### BaseCard
```vue
<BaseCard
  padding="none|sm|default|lg"
  class="custom-classes"
>
  <template #header>
    <!-- Contenu du header -->
  </template>
  
  <!-- Contenu principal -->
  
  <template #footer>
    <!-- Contenu du footer -->
  </template>
</BaseCard>
```

### BaseBadge
```vue
<BaseBadge
  variant="default|secondary|destructive|outline"
  size="default|sm|lg"
>
  Texte du badge
</BaseBadge>
```

## Thème et Personnalisation

### Variables CSS
Le thème utilise des variables CSS personnalisables dans `src/style.css`:

```css
:root {
  --background: 0 0% 100%;
  --foreground: 222.2 84% 4.9%;
  --primary: 222.2 47.4% 11.2%;
  --primary-foreground: 210 40% 98%;
  /* ... autres variables */
}
```

### Mode sombre
Le thème supporte automatiquement le mode sombre via la classe `.dark`.

### Personnalisation des couleurs
Pour modifier les couleurs, éditez les variables CSS dans `src/style.css` ou ajoutez de nouvelles variantes dans les composants UI.

## Bonnes Pratiques

1. **Réutilisabilité** - Créez des composants génériques et réutilisables
2. **Props validation** - Validez toujours les props avec des types et valeurs par défaut
3. **Événements** - Émettez des événements pour la communication parent-enfant
4. **Accessibilité** - Incluez les attributs ARIA appropriés
5. **Responsive** - Utilisez les classes Tailwind pour la responsivité
6. **Performance** - Utilisez `v-memo` pour les listes longues

## Ajout de Nouveaux Composants

### Pour ajouter un nouvel atome:
1. Créez le fichier dans `src/components/atoms/`
2. Utilisez les variantes Shadcn/ui si approprié
3. Ajoutez la validation des props
4. Exportez depuis `src/components/index.js`

### Pour ajouter une nouvelle molécule:
1. Créez le fichier dans `src/components/molecules/`
2. Composez avec les atomes existants
3. Ajoutez la logique métier spécifique
4. Exportez depuis `src/components/index.js`

### Pour ajouter un nouvel organisme:
1. Créez le fichier dans `src/components/organisms/`
2. Composez avec les molécules et atomes
3. Gèrez l'état et les interactions complexes
4. Exportez depuis `src/components/index.js` 