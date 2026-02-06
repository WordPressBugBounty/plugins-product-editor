# Guide de Monétisation - Product Editor Pro

## 📋 Vue d'ensemble

Product Editor Pro version 2.0.0 intègre maintenant un système de **freemium** avec des fonctionnalités premium et un système de licensing.

## 🎯 Modèle de Monétisation

### Version GRATUITE
- Édition limitée à **50 produits** par opération
- **3 opérations d'annulation** (undo)
- Modifications immédiates uniquement
- Support communautaire

### Version PREMIUM
- Édition **illimitée** de produits
- **50 opérations d'annulation**
- **Planification** de changements futurs
- Notifications email
- Support prioritaire

## 💰 Prix Suggérés

### Option 1 : Abonnement Annuel
- **Personal** : 39.99€/an (1 site)
- **Business** : 89.99€/an (5 sites)
- **Agency** : 199.99€/an (illimité)

### Option 2 : Licence à Vie (Recommandé)
- **Personal** : 119.99€ (1 site)
- **Business** : 229.99€ (5 sites)
- **Agency** : 399.99€ (illimité)

## 🔧 Configuration du Système de Licensing

### 1. Mode Développement

Par défaut, le plugin est en mode développement avec `PRODUCT_EDITOR_FORCE_PREMIUM = true` dans `product-editor.php`.

```php
// Pour tester en mode gratuit, changez en false :
define('PRODUCT_EDITOR_FORCE_PREMIUM', false);

// Pour tester en mode premium, changez en true :
define('PRODUCT_EDITOR_FORCE_PREMIUM', true);
```

### 2. Configuration Production

Pour la production, vous devez :

1. **Supprimer ou mettre à false** la constante `PRODUCT_EDITOR_FORCE_PREMIUM` dans `product-editor.php`

2. **Configurer votre serveur de licensing** dans `includes/class-product-editor-license.php` :

```php
private static function call_license_api( $action, $args = array() ) {
    // Remplacez cette URL par votre serveur de licensing
    $api_url = 'https://votre-site.com/api/v1/license';

    $response = wp_remote_post( $api_url, array(
        'timeout' => 15,
        'body' => array_merge( array( 'action' => $action ), $args )
    ) );

    // Traiter la réponse...
}
```

3. **Configurer l'URL d'upgrade** dans `includes/class-product-editor-license.php` :

```php
public static function get_upgrade_url() {
    return 'https://votre-site.com/product-editor-pro/';
}
```

## 🚀 Fonctionnalités Implémentées

### ✅ Système de Licensing
- Activation/Désactivation de licence
- Validation du format de clé (PE-XXXX-XXXX-XXXX-XXXX)
- Interface d'administration
- Vérification des permissions

### ✅ Restrictions Version Gratuite
- Limite de 50 produits par opération
- Message d'erreur avec lien upgrade
- Limite de 3 opérations d'annulation

### ✅ Scheduler de Tâches (Premium)
- Base de données dédiée
- Planification via WP-Cron
- Interface de gestion des tâches
- Notifications email
- Statuts : pending, running, completed, failed, cancelled

### ✅ Interface Admin
- Page de gestion de licence
- Page de tâches planifiées
- Comparaison des fonctionnalités
- Bannières d'upgrade

## 📊 Utilisation du Scheduler (Fonctionnalité Premium)

### Exemple de Planification

```php
// Planifier une réduction de 20% pour Black Friday
$product_ids = [123, 456, 789]; // IDs des produits

$actions = array(
    'change_sale_price' => array(
        'action' => 4, // Réduire par rapport au prix régulier
        'value' => 20, // 20%
        'is_percentage' => true
    ),
    'change_date_on_sale_from' => array(
        'value' => '2024-11-29 00:00:00'
    ),
    'change_date_on_sale_to' => array(
        'value' => '2024-12-02 23:59:59'
    )
);

$task_id = Product_Editor_Scheduler::schedule_task(
    'Black Friday Sale 2024',
    '2024-11-29 00:00:00',
    $product_ids,
    $actions
);
```

### Vérifier le Statut

```php
$task = Product_Editor_Scheduler::get_task( $task_id );
echo $task->status; // pending, running, completed, failed
```

### Annuler une Tâche

```php
Product_Editor_Scheduler::cancel_task( $task_id );
```

## 🎨 Personnalisation de l'Interface

Les styles sont définis dans `/admin/css/product-editor-premium.css` :

- Bannières upgrade
- Badges premium
- Overlays de fonctionnalités verrouillées
- Interface de planification
- Comparaisons de fonctionnalités

## 📧 Notifications Email

Les notifications sont envoyées automatiquement :

```php
// Personnaliser le contenu des emails dans :
// includes/class-product-editor-scheduler.php
// Méthode : send_task_notification()
```

## 🔐 Sécurité

### Validation des Licences

Le système vérifie :
- Format de la clé (PE-XXXX-XXXX-XXXX-XXXX)
- Email associé
- Domaine du site
- Statut de la licence (active/inactive)

### Permissions WordPress

Toutes les fonctionnalités nécessitent :
```php
current_user_can( 'manage_woocommerce' )
```

## 📈 Stratégies de Conversion

### 1. Bannières Contextuelles
Affichées quand l'utilisateur atteint une limite :
- Message clair sur la limitation
- Bouton CTA visible
- Lien direct vers l'upgrade

### 2. Page de Comparaison
`/admin/?page=product-editor-license` montre :
- Tableau comparatif détaillé
- Prix et options
- Formulaire d'activation

### 3. Fonctionnalités Visibles Mais Verrouillées
L'utilisateur voit le menu "Scheduled Tasks" mais :
- Nécessite une licence premium
- Encourage l'upgrade par la découverte

## 🛠️ Installation & Activation

### En développement

1. Activer le plugin
2. Les tables sont créées automatiquement
3. Mode premium activé par défaut (PRODUCT_EDITOR_FORCE_PREMIUM = true)

### En production

1. Désactiver PRODUCT_EDITOR_FORCE_PREMIUM
2. Configurer le serveur de licensing
3. Tester l'achat et l'activation de licence
4. Vérifier les restrictions version gratuite

## 🧪 Tests Recommandés

### Version Gratuite
- [ ] Bloquer l'édition de >50 produits
- [ ] Limiter l'undo à 3 opérations
- [ ] Cacher le menu Scheduled Tasks
- [ ] Afficher les bannières upgrade

### Version Premium
- [ ] Édition illimitée de produits
- [ ] 50 opérations d'undo
- [ ] Accès au scheduler
- [ ] Planification et exécution automatique
- [ ] Notifications email

## 📝 Notes Importantes

### Base de Données

Deux tables sont créées :
1. `wp_pe_reverse_steps` - Historique des modifications
2. `wp_pe_scheduled_tasks` - Tâches planifiées

### WP-Cron

Le scheduler utilise WP-Cron :
```php
// Hook : product_editor_execute_scheduled_task
// Vérification : product_editor_check_scheduled_tasks (toutes les heures)
```

### Nettoyage

Les anciennes tâches sont conservées 30 jours par défaut :
```php
Product_Editor_Scheduler::cleanup_old_tasks( 30 );
```

## 🎯 Prochaines Étapes

Pour lancer la version commerciale :

1. **Créer un site de vente**
   - Landing page professionnelle
   - Système de paiement (Stripe, PayPal)
   - Génération automatique de licences

2. **Serveur de Licensing**
   - API REST pour validation
   - Base de données des licences
   - Gestion des renouvellements

3. **Marketing**
   - SEO pour "WooCommerce bulk editor"
   - Contenu (blog, vidéos, tutoriels)
   - Présence sur marketplaces (CodeCanyon)

4. **Support**
   - Documentation complète
   - Forum ou système de tickets
   - Email support premium

## 💡 Conseils de Monétisation

### Prix Psychologique
- 79€/an est perçu comme raisonnable
- 129€ lifetime crée l'urgence
- Badge "Best Value" sur lifetime

### Essai Gratuit Étendu
- Version gratuite fonctionnelle (pas de limitation de temps)
- Frustration calculée (50 produits = assez pour tester, pas pour produire)
- Upgrade facile (1 clic depuis l'interface)

### Upsells
- Support prioritaire (+29€/an)
- Sites multiples (packages)
- Fonctionnalités additionnelles futures

## 📞 Support

Pour toute question sur l'implémentation :
- Email : dev.hedgehog.core@gmail.com
- Forum WordPress : https://wordpress.org/support/plugin/product-editor/

---

**Version** : 2.0.0
**Date** : Janvier 2026
**Licence** : GPL-2.0+
