# Guide d'Intégration Freemius

## 🎯 Informations de Configuration

**Votre compte Freemius est créé avec :**
- **Plans configurés** :
  - Pro Annual : $39.99/an
  - Pro Lifetime : $119.99 (paiement unique)

## 📦 Étapes d'Installation Freemius SDK

### 1. Télécharger le SDK Freemius

```bash
cd /home/user/product-editor
git clone https://github.com/Freemius/wordpress-sdk.git freemius
```

### 2. Initialiser Freemius dans le Plugin

Ajoutez ce code au début de `/home/user/product-editor/product-editor.php` (après les `require` existants) :

```php
// Freemius Integration
if ( ! function_exists( 'pe_fs' ) ) {
    // Create a helper function for easy SDK access.
    function pe_fs() {
        global $pe_fs;

        if ( ! isset( $pe_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $pe_fs = fs_dynamic_init( array(
                'id'                  => 'VOTRE_PLUGIN_ID',  // Remplacer par l'ID depuis Freemius Dashboard
                'slug'                => 'product-editor',
                'type'                => 'plugin',
                'public_key'          => 'pk_VOTRE_CLE_PUBLIQUE',  // Depuis Freemius Dashboard
                'is_premium'          => true,
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'product-editor',
                    'override_exact' => true,
                    'parent'         => array(
                        'slug' => 'edit.php?post_type=product',
                    ),
                ),
            ) );
        }

        return $pe_fs;
    }

    // Init Freemius.
    pe_fs();
    // Signal that SDK was initiated.
    do_action( 'pe_fs_loaded' );
}
```

### 3. Récupérer vos Clés Freemius

1. Connectez-vous à [Freemius Dashboard](https://dashboard.freemius.com/)
2. Allez dans **Product Editor Pro** > **Settings** > **Keys & Ids**
3. Copiez :
   - **Plugin ID** (ex: 12345)
   - **Public Key** (ex: pk_xxxxxxxxxxxxx)

### 4. Remplacer la Classe License Actuelle

Remplacez le contenu de `includes/class-product-editor-license.php` par :

```php
<?php
/**
 * License Management via Freemius
 *
 * @package Product-Editor
 * @since   2.0.0
 */

class Product_Editor_License {

	const FREE_PRODUCT_LIMIT = 50;
	const FREE_UNDO_LIMIT = 3;

	/**
	 * Check if premium
	 */
	public static function is_premium() {
		if ( defined( 'PRODUCT_EDITOR_FORCE_PREMIUM' ) && PRODUCT_EDITOR_FORCE_PREMIUM ) {
			return true;
		}

		// Utiliser Freemius pour vérifier
		if ( function_exists( 'pe_fs' ) ) {
			return pe_fs()->is_premium();
		}

		return false;
	}

	/**
	 * Get product limit
	 */
	public static function get_product_limit() {
		return self::is_premium() ? PHP_INT_MAX : self::FREE_PRODUCT_LIMIT;
	}

	/**
	 * Get undo limit
	 */
	public static function get_undo_limit() {
		return self::is_premium() ? 50 : self::FREE_UNDO_LIMIT;
	}

	/**
	 * Check if scheduling is available
	 */
	public static function can_use_scheduler() {
		return self::is_premium();
	}

	/**
	 * Get upgrade URL
	 */
	public static function get_upgrade_url() {
		if ( function_exists( 'pe_fs' ) ) {
			return pe_fs()->get_upgrade_url();
		}
		return admin_url( 'edit.php?post_type=product&page=product-editor-pricing' );
	}

	// Garder les autres méthodes pour compatibilité si besoin
	// ...
}
```

### 5. Configuration des Plans dans Freemius

Dans le Dashboard Freemius :

**Plan "Pro Annual"**
- Prix : $39.99
- Billing : Annual
- Trial : 14 jours (optionnel)
- Features :
  - Unlimited product editing
  - Schedule price changes
  - 50 undo operations
  - Email notifications

**Plan "Pro Lifetime"**
- Prix : $119.99
- Billing : Lifetime
- Badge : "Best Value"
- Toutes les features du plan Annual

### 6. Supprimer les Pages Personnalisées (Optionnel)

Si vous utilisez Freemius, vous pouvez supprimer les pages personnalisées :
- `admin/partials/product-editor-license-page.php`
- Les fonctions `license_page()` et `scheduler_page()` dans `admin/class-product-editor-admin.php`

Freemius gérera automatiquement :
- Page d'activation de licence
- Page de pricing
- Page de contact/support
- Auto-updates

### 7. Tester l'Intégration

**Mode Sandbox (Test)**
```php
'is_live'      => false,  // Mode test
```

**Mode Production**
```php
'is_live'      => true,   // Mode production
```

## 🔄 Migration depuis le Système Actuel

### Option 1 : Garder les Deux Systèmes

Vous pouvez garder votre système actuel ET Freemius :

```php
public static function is_premium() {
    // Force premium mode en dev
    if ( defined( 'PRODUCT_EDITOR_FORCE_PREMIUM' ) && PRODUCT_EDITOR_FORCE_PREMIUM ) {
        return true;
    }

    // Vérifier Freemius d'abord
    if ( function_exists( 'pe_fs' ) && pe_fs()->is_premium() ) {
        return true;
    }

    // Fallback sur votre système
    $license_status = get_option( 'product_editor_license_status', 'invalid' );
    return $license_status === 'valid';
}
```

### Option 2 : 100% Freemius

Supprimez complètement `class-product-editor-license.php` et utilisez uniquement l'API Freemius.

## 📊 Configuration des Prix dans Freemius

Vos prix actuels :
| Plan | Prix | Type |
|------|------|------|
| Pro Annual | 39.99€ | Abonnement |
| Pro Lifetime | 119.99€ | Paiement unique |

**Note** : Freemius gère automatiquement :
- Les devises (USD, EUR, GBP, etc.)
- Les taxes (TVA européenne)
- Les remboursements
- Les renouvellements

## 🚀 Avantages de Freemius

✅ **Automatique**
- Gestion des licences
- Updates automatiques
- Checkout intégré

✅ **Analytics**
- Dashboard de revenus
- Taux de conversion
- Statistiques détaillées

✅ **Support**
- Système de tickets intégré
- Forum optionnel
- Knowledge base

✅ **Marketing**
- Emails automatiques
- Upsells
- Trials

## 🔐 Sécurité

Freemius gère automatiquement :
- ✅ Validation des licences
- ✅ Prévention du piratage
- ✅ Domain binding
- ✅ License deactivation

## 📝 Checklist de Migration

- [ ] Télécharger Freemius SDK
- [ ] Récupérer Plugin ID et Public Key
- [ ] Ajouter le code d'initialisation
- [ ] Configurer les plans dans Freemius Dashboard
- [ ] Tester en mode sandbox
- [ ] Mettre à jour `class-product-editor-license.php`
- [ ] Supprimer `PRODUCT_EDITOR_FORCE_PREMIUM` (prod)
- [ ] Tester l'activation/désactivation
- [ ] Tester les restrictions free/premium
- [ ] Passer en mode live

## 🆘 Support

- Documentation : https://freemius.com/help/
- Forum : https://freemius.com/forums/
- Email : support@freemius.com

## 💡 Alternative : Easy Digital Downloads

Si vous préférez héberger vous-même :

1. Installer EDD sur votre site
2. Créer le produit "Product Editor Pro"
3. Utiliser EDD Software Licensing
4. API REST pour validation

**Avantages** : Contrôle total, pas de commission
**Inconvénients** : Plus de travail technique

---

**Recommandation** : Commencez avec Freemius pour le lancement, c'est le plus rapide et professionnel.
