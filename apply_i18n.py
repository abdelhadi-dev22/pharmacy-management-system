import os

base_dir = r"c:\xampp\htdocs\gestion de pharmaci"

replacements = {
    "index.php": [
        ('<html lang="fr">', '<?php require_once "includes/i18n.php"; ?>\n<html lang="<?php echo htmlspecialchars($_SESSION["lang"] ?? "fr"); ?>" dir="<?php echo htmlspecialchars(__("dir")); ?>">'),
        ('Tableau de bord - PharmaGest', '<?php echo htmlspecialchars(__("app_title")); ?>'),
        ('<h1><i class="fas fa-tachometer-alt"></i> Tableau de bord</h1>', '<h1><i class="fas fa-tachometer-alt"></i> <?php echo htmlspecialchars(__("dashboard")); ?></h1>'),
        ('Médicaments en stock', '<?php echo htmlspecialchars(__("total_medications")); ?>'),
        ('Stocks bas', '<?php echo htmlspecialchars(__("low_stock")); ?>'),
        ('Expire bientôt', '<?php echo htmlspecialchars(__("expire_soon")); ?>'),
        ("Ventes aujourd'hui", "<?php echo htmlspecialchars(__('sales')); ?> (Aujourd'hui)"),
        ('Alertes urgentes', '<?php echo htmlspecialchars(__("stock_alerts")); ?>'),
        ('Ruptures de stock', '<?php echo htmlspecialchars(__("out_of_stock")); ?>'),
        ('Médicaments périmés', '<?php echo htmlspecialchars(__("expired")); ?>'),
        ('Alertes urgentes', '<?php echo htmlspecialchars(__("stock_alerts")); ?>'),
        ('Dernières ventes', '<?php echo htmlspecialchars(__("recent_sales")); ?>'),
        (' Client', ' <?php echo htmlspecialchars(__("client")); ?>'),
        (' Total', ' <?php echo htmlspecialchars(__("amount")); ?>'),
        (' Date', ' <?php echo htmlspecialchars(__("date")); ?>'),
        (' €', ' <?php echo htmlspecialchars(__("currency")); ?>')
    ],
    "login.php": [
        ('<html lang="fr">', '<?php require_once "includes/i18n.php"; ?>\n<html lang="<?php echo htmlspecialchars($_SESSION["lang"] ?? "fr"); ?>" dir="<?php echo htmlspecialchars(__("dir")); ?>">'),
        ('PharmaGest - Connexion', '<?php echo htmlspecialchars(__("login_title")); ?>'),
        ('<h1><i class="fas fa-clinic-medical"></i> PharmaGest</h1>', '<h1><i class="fas fa-clinic-medical"></i> <?php echo htmlspecialchars(__("pharma_gest")); ?></h1>'),
        ('Système de gestion de pharmacie moderne', '<?php echo htmlspecialchars(__("login_subtitle")); ?>'),
        ('Connexion\n                </div>', 'Connexion\n                </div>'),
        ('Identifiant</label>', '<?php echo htmlspecialchars(__("username")); ?></label>'),
        ('Mot de passe</label>', '<?php echo htmlspecialchars(__("password")); ?></label>'),
        ('Se connecter <i', '<?php echo htmlspecialchars(__("login_btn")); ?> <i'),
        ('Créer mon compte <i', '<?php echo htmlspecialchars(__("create_account")); ?> <i')
    ],
    "pages/medicaments.php": [
        ('<html lang="fr">', '<?php require_once "../includes/i18n.php"; ?>\n<html lang="<?php echo htmlspecialchars($_SESSION["lang"] ?? "fr"); ?>" dir="<?php echo htmlspecialchars(__("dir")); ?>">'),
        ('<h1>\n                    <i class="fas fa-clinic-medical"></i>\n                    PharmaGest - Gestion des Médicaments\n                </h1>', '<h1><i class="fas fa-clinic-medical"></i> <?php echo htmlspecialchars(__("medications")); ?></h1>'),
        ('Total Médicaments', '<?php echo htmlspecialchars(__("total_medications")); ?>'),
        ('Stock Bas', '<?php echo htmlspecialchars(__("low_stock")); ?>'),
        ('Rupture Stock', '<?php echo htmlspecialchars(__("out_of_stock")); ?>'),
        ('Stock Normal', '<?php echo htmlspecialchars(__("normal_stock")); ?>'),
        ('Alertes Stock', '<?php echo htmlspecialchars(__("stock_alerts")); ?>'),
        ('> Imprimer', '> <?php echo htmlspecialchars(__("print")); ?>'),
        ('Liste des Médicaments', '<?php echo htmlspecialchars(__("medications")); ?>'),
        ('Ajouter un médicament', '<?php echo htmlspecialchars(__("add")); ?>'),
        ('<th>Nom</th>', '<th><?php echo htmlspecialchars(__("name")); ?></th>'),
        ('<th>Prix Achat</th>', '<th><?php echo htmlspecialchars(__("price")); ?> Achat</th>'),
        ('<th>Prix Vente</th>', '<th><?php echo htmlspecialchars(__("price")); ?> Vente</th>'),
        ('<th>Quantité</th>', '<th><?php echo htmlspecialchars(__("quantity")); ?></th>'),
        ('<th>Catégorie</th>', '<th><?php echo htmlspecialchars(__("category")); ?></th>'),
        ('<th>Actions</th>', '<th><?php echo htmlspecialchars(__("actions")); ?></th>'),
        (' €', ' <?php echo htmlspecialchars(__("currency")); ?>')
    ],
    "pages/clients.php": [
        ('<html lang="fr">', '<?php require_once "../includes/i18n.php"; ?>\n<html lang="<?php echo htmlspecialchars($_SESSION["lang"] ?? "fr"); ?>" dir="<?php echo htmlspecialchars(__("dir")); ?>">'),
        ('Gestion des Clients', '<?php echo htmlspecialchars(__("clients")); ?>'),
        ('Liste des Clients', '<?php echo htmlspecialchars(__("clients")); ?>'),
        ('Ajouter un client', '<?php echo htmlspecialchars(__("add")); ?>'),
        ('<th>Actions</th>', '<th><?php echo htmlspecialchars(__("actions")); ?></th>')
    ],
    "pages/fournisseurs.php": [
        ('<html lang="fr">', '<?php require_once "../includes/i18n.php"; ?>\n<html lang="<?php echo htmlspecialchars($_SESSION["lang"] ?? "fr"); ?>" dir="<?php echo htmlspecialchars(__("dir")); ?>">'),
        ('Gestion des Fournisseurs', '<?php echo htmlspecialchars(__("suppliers")); ?>'),
        ('Liste des Fournisseurs', '<?php echo htmlspecialchars(__("suppliers")); ?>'),
        ('Ajouter un fournisseur', '<?php echo htmlspecialchars(__("add")); ?>'),
        ('<th>Actions</th>', '<th><?php echo htmlspecialchars(__("actions")); ?></th>')
    ],
    "pages/ventes.php": [
        ('<html lang="fr">', '<?php require_once "../includes/i18n.php"; ?>\n<html lang="<?php echo htmlspecialchars($_SESSION["lang"] ?? "fr"); ?>" dir="<?php echo htmlspecialchars(__("dir")); ?>">'),
        ('Historique des Ventes', '<?php echo htmlspecialchars(__("sales")); ?>'),
        ('<th>Client</th>', '<th><?php echo htmlspecialchars(__("client")); ?></th>'),
        ('<th>Date & Heure</th>', '<th><?php echo htmlspecialchars(__("date")); ?></th>'),
        ('<th>Statut</th>', '<th><?php echo htmlspecialchars(__("status")); ?></th>'),
        ('<th>Actions</th>', '<th><?php echo htmlspecialchars(__("actions")); ?></th>'),
        (' €', ' <?php echo htmlspecialchars(__("currency")); ?>')
    ],
    "pages/utilisateurs.php": [
        ('<html lang="fr">', '<?php require_once "../includes/i18n.php"; ?>\n<html lang="<?php echo htmlspecialchars($_SESSION["lang"] ?? "fr"); ?>" dir="<?php echo htmlspecialchars(__("dir")); ?>">'),
        ('Gestion des Utilisateurs', '<?php echo htmlspecialchars(__("users")); ?>'),
        ('<th>Actions</th>', '<th><?php echo htmlspecialchars(__("actions")); ?></th>')
    ],
    "pages/rapports.php": [
        ('<html lang="fr">', '<?php require_once "../includes/i18n.php"; ?>\n<html lang="<?php echo htmlspecialchars($_SESSION["lang"] ?? "fr"); ?>" dir="<?php echo htmlspecialchars(__("dir")); ?>">'),
        ('Rapports et Analyses', '<?php echo htmlspecialchars(__("reports")); ?>'),
        ('Alertes Stock', '<?php echo htmlspecialchars(__("stock_alerts")); ?>'),
        (' €', ' <?php echo htmlspecialchars(__("currency")); ?>')
    ],
    "assets/css/style.css": [
        ('/* Responsive */', '[dir="rtl"] {\n    direction: rtl;\n    text-align: right;\n}\n[dir="rtl"] .nav-menu { padding-right: 0; }\n[dir="rtl"] .nav-item { margin-left: 10px; margin-right: 0; }\n[dir="rtl"] .btn i, [dir="rtl"] .nav-link i { margin-left: 8px; margin-right: 0; }\n[dir="rtl"] .stat-card { flex-direction: row-reverse; }\n[dir="rtl"] .input-wrapper i.icon-left { right: 16px; left: auto; }\n[dir="rtl"] .form-group input { padding: 14px 44px 14px 16px; }\n[dir="rtl"] .table-header { flex-direction: row-reverse; }\n[dir="rtl"] th, [dir="rtl"] td { text-align: right; }\n\n/* Responsive */')
    ]
}

for rel_path, reps in replacements.items():
    filepath = os.path.join(base_dir, rel_path)
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        for old_text, new_text in reps:
            content = content.replace(old_text, new_text)
            
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated {rel_path}")
    else:
        print(f"Failed to find {rel_path}")
