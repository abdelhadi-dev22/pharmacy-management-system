import os
import json

base_dir = r"c:\xampp\htdocs\gestion de pharmaci"
lang_dir = os.path.join(base_dir, "includes", "lang")
if not os.path.exists(lang_dir):
    os.makedirs(lang_dir)

# Define translation dictionaries
# Common keys across the application
translations = {
    "fr": {
        "app_title": "Gestion des Médicaments - PharmaGest",
        "pharma_gest": "PharmaGest",
        "dashboard": "Tableau de bord",
        "medications": "Médicaments",
        "sales": "Ventes",
        "clients": "Clients",
        "suppliers": "Fournisseurs",
        "reports": "Rapports",
        "users": "Utilisateurs",
        "logout": "Déconnexion",
        "confirm_logout": "Êtes-vous sûr de vouloir vous déconnecter ?",
        
        "total_medications": "Total Médicaments",
        "low_stock": "Stock Bas",
        "out_of_stock": "Rupture Stock",
        "normal_stock": "Stock Normal",
        
        "stock_alerts": "Alertes Stock",
        "print": "Imprimer",
        "available": "Disponible",
        "expire_soon": "Expire bientôt",
        "expired": "Périmé",
        
        "recent_sales": "Ventes Récentes",
        "view_all": "Voir tout",
        "invoice": "Facture",
        "client": "Client",
        "amount": "Montant",
        "status": "Statut",
        "date": "Date",
        
        "add": "Ajouter",
        "edit": "Modifier",
        "delete": "Supprimer",
        "save": "Sauvegarder",
        "cancel": "Annuler",
        "actions": "Actions",
        
        "name": "Nom",
        "price": "Prix",
        "quantity": "Quantité",
        "category": "Catégorie",
        
        "login_title": "Bienvenue sur PharmaGest",
        "login_subtitle": "Connectez-vous pour accéder à votre espace de gestion de pharmacie.",
        "username": "Nom d'utilisateur",
        "password": "Mot de passe",
        "login_btn": "Se connecter",
        "create_account": "Créer un compte",
        
        "currency": "DA",
        "dir": "ltr"
    },
    "en": {
        "app_title": "Medication Management - PharmaGest",
        "pharma_gest": "PharmaGest",
        "dashboard": "Dashboard",
        "medications": "Medications",
        "sales": "Sales",
        "clients": "Clients",
        "suppliers": "Suppliers",
        "reports": "Reports",
        "users": "Users",
        "logout": "Logout",
        "confirm_logout": "Are you sure you want to log out?",
        
        "total_medications": "Total Medications",
        "low_stock": "Low Stock",
        "out_of_stock": "Out of Stock",
        "normal_stock": "Normal Stock",
        
        "stock_alerts": "Stock Alerts",
        "print": "Print",
        "available": "Available",
        "expire_soon": "Expiring Soon",
        "expired": "Expired",
        
        "recent_sales": "Recent Sales",
        "view_all": "View All",
        "invoice": "Invoice",
        "client": "Client",
        "amount": "Amount",
        "status": "Status",
        "date": "Date",
        
        "add": "Add",
        "edit": "Edit",
        "delete": "Delete",
        "save": "Save",
        "cancel": "Cancel",
        "actions": "Actions",
        
        "name": "Name",
        "price": "Price",
        "quantity": "Quantity",
        "category": "Category",
        
        "login_title": "Welcome to PharmaGest",
        "login_subtitle": "Log in to access your pharmacy management workspace.",
        "username": "Username",
        "password": "Password",
        "login_btn": "Login",
        "create_account": "Create Account",
        
        "currency": "DA",
        "dir": "ltr"
    },
    "ar": {
        "app_title": "إدارة الأدوية - فارماجيست",
        "pharma_gest": "فارماجيست",
        "dashboard": "لوحة القيادة",
        "medications": "الأدوية",
        "sales": "المبيعات",
        "clients": "العملاء",
        "suppliers": "الموردين",
        "reports": "التقارير",
        "users": "المستخدمين",
        "logout": "تسجيل الخروج",
        "confirm_logout": "هل أنت متأكد أنك تريد تسجيل الخروج؟",
        
        "total_medications": "إجمالي الأدوية",
        "low_stock": "انخفاض المخزون",
        "out_of_stock": "نفاد المخزون",
        "normal_stock": "مخزون طبيعي",
        
        "stock_alerts": "تنبيهات المخزون",
        "print": "طباعة",
        "available": "متاح",
        "expire_soon": "تنتهي قريباً",
        "expired": "منتهية الصلاحية",
        
        "recent_sales": "المبيعات الأخيرة",
        "view_all": "عرض الكل",
        "invoice": "فاتورة",
        "client": "العميل",
        "amount": "المبلغ",
        "status": "الحالة",
        "date": "التاريخ",
        
        "add": "إضافة",
        "edit": "تعديل",
        "delete": "حذف",
        "save": "حفظ",
        "cancel": "إلغاء",
        "actions": "إجراءات",
        
        "name": "الاسم",
        "price": "السعر",
        "quantity": "الكمية",
        "category": "الفئة",
        
        "login_title": "مرحبًا بك في فارماجيست",
        "login_subtitle": "تسجيل الدخول للوصول إلى مساحة إدارة الصيدلية الخاصة بك.",
        "username": "اسم المستخدم",
        "password": "كلمة المرور",
        "login_btn": "تسجيل الدخول",
        "create_account": "إنشاء حساب",
        
        "currency": "د.ج",
        "dir": "rtl"
    }
}

for lang, data in translations.items():
    with open(os.path.join(lang_dir, f"{lang}.json"), "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=4)
    print(f"Created {lang}.json")

# Create i18n.php
i18n_content = """<?php
// includes/i18n.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle language change
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if (in_array($lang, ['fr', 'en', 'ar'])) {
        $_SESSION['lang'] = $lang;
    }
    
    // Remove lang parameter from URL to avoid infinite loops or sticky parameters
    $url = preg_replace('/([?&])lang=[^&]+(&|$)/', '$1', $_SERVER['REQUEST_URI']);
    $url = rtrim($url, '?&');
    header("Location: $url");
    exit();
}

// Default language
$current_lang = $_SESSION['lang'] ?? 'fr';

// Load translation file
$lang_file = __DIR__ . "/lang/{$current_lang}.json";
if (file_exists($lang_file)) {
    $translations = json_decode(file_get_contents($lang_file), true);
} else {
    $translations = [];
}

// Helper function to translate keys
function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}
?>"""

with open(os.path.join(base_dir, "includes", "i18n.php"), "w", encoding="utf-8") as f:
    f.write(i18n_content)
    print("Created i18n.php")
