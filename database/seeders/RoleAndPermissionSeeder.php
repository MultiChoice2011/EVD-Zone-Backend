<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Permission;
use App\Models\PermissionTranslation;
use App\Models\Role;
use App\Models\RoleTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

//use Spatie\Permission\Models\Permission;
//use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();

        $adminPermissions = [
            // Countries
            'view-countries' => [
                'translations' => [
                    'ar' => 'عرض البلدان',
                    'en' => 'view countries',
                ]
            ],
            'create-countries' => [
                'translations' => [
                    'ar' => 'إضافة بلد',
                    'en' => 'create country',
                ]
            ],
            'update-countries' => [
                'translations' => [
                    'ar' => 'تعديل بلد',
                    'en' => 'update country',
                ]
            ],
            'delete-countries' => [
                'translations' => [
                    'ar' => 'حذف بلد',
                    'en' => 'delete country',
                ]
            ],
            'view-all-countries-form' => [
                'translations' => [
                    'ar' => 'عرض جميع البلدان',
                    'en' => 'view all countries form',
                ]
            ],

            // Regions
            'view-regions' => [
                'translations' => [
                    'ar' => 'عرض المناطق',
                    'en' => 'view regions',
                ]
            ],
            'create-regions' => [
                'translations' => [
                    'ar' => 'إضافة منطقة',
                    'en' => 'create region',
                ]
            ],
            'update-regions' => [
                'translations' => [
                    'ar' => 'تعديل منطقة',
                    'en' => 'update region',
                ]
            ],
            'delete-regions' => [
                'translations' => [
                    'ar' => 'حذف منطقة',
                    'en' => 'delete region',
                ]
            ],
            'view-all-regions-form' => [
                'translations' => [
                    'ar' => 'عرض جميع المناطق',
                    'en' => 'view all regions form',
                ]
            ],

            // Cities
            'view-cities' => [
                'translations' => [
                    'ar' => 'عرض المدن',
                    'en' => 'view cities',
                ]
            ],
            'create-cities' => [
                'translations' => [
                    'ar' => 'إضافة مدينة',
                    'en' => 'create city',
                ]
            ],
            'update-cities' => [
                'translations' => [
                    'ar' => 'تعديل مدينة',
                    'en' => 'update city',
                ]
            ],
            'delete-cities' => [
                'translations' => [
                    'ar' => 'حذف مدينة',
                    'en' => 'delete city',
                ]
            ],
            'view-all-cities-form' => [
                'translations' => [
                    'ar' => 'عرض جميع المدن',
                    'en' => 'view all cities form',
                ]
            ],

            // Languages
            'view-languages' => [
                'translations' => [
                    'ar' => 'عرض اللغات',
                    'en' => 'view languages',
                ]
            ],
            'create-languages' => [
                'translations' => [
                    'ar' => 'إضافة لغة',
                    'en' => 'create language',
                ]
            ],
            'update-languages' => [
                'translations' => [
                    'ar' => 'تعديل لغة',
                    'en' => 'update language',
                ]
            ],
            'delete-languages' => [
                'translations' => [
                    'ar' => 'حذف لغة',
                    'en' => 'delete language',
                ]
            ],

            // Currencies
            'view-currencies' => [
                'translations' => [
                    'ar' => 'عرض العملات',
                    'en' => 'view currencies',
                ]
            ],
            'create-currencies' => [
                'translations' => [
                    'ar' => 'إضافة عملة',
                    'en' => 'create currency',
                ]
            ],
            'update-currencies' => [
                'translations' => [
                    'ar' => 'تعديل عملة',
                    'en' => 'update currency',
                ]
            ],
            'delete-currencies' => [
                'translations' => [
                    'ar' => 'حذف عملة',
                    'en' => 'delete currency',
                ]
            ],

            // Brands
            'view-brands' => [
                'translations' => [
                    'ar' => 'عرض العلامات التجارية',
                    'en' => 'view brands',
                ]
            ],
            'create-brands' => [
                'translations' => [
                    'ar' => 'إضافة علامة تجارية',
                    'en' => 'create brand',
                ]
            ],
            'update-brands' => [
                'translations' => [
                    'ar' => 'تعديل علامة تجارية',
                    'en' => 'update brand',
                ]
            ],
            'delete-brands' => [
                'translations' => [
                    'ar' => 'حذف علامة تجارية',
                    'en' => 'delete brand',
                ]
            ],
            'change-brand-status' => [
                'translations' => [
                    'ar' => 'تغيير حالة العلامة التجارية',
                    'en' => 'change brand status',
                ]
            ],
            'destroy-selected-brands' => [
                'translations' => [
                    'ar' => 'حذف العلامات التجارية المحددة',
                    'en' => 'destroy selected brands',
                ]
            ],

            // Vendors
            'view-vendors' => [
                'translations' => [
                    'ar' => 'عرض الموردين',
                    'en' => 'view vendors',
                ]
            ],
            'create-vendors' => [
                'translations' => [
                    'ar' => 'إضافة مورد',
                    'en' => 'create vendor',
                ]
            ],
            'update-vendors' => [
                'translations' => [
                    'ar' => 'تعديل مورد',
                    'en' => 'update vendor',
                ]
            ],
            'delete-vendors' => [
                'translations' => [
                    'ar' => 'حذف مورد',
                    'en' => 'delete vendor',
                ]
            ],
            'destroy-selected-vendors' => [
                'translations' => [
                    'ar' => 'حذف الموردين المحددين',
                    'en' => 'destroy selected vendors',
                ]
            ],
            'view-vendor-trashes' => [
                'translations' => [
                    'ar' => 'عرض سلة المهملات الخاصة بالموردين',
                    'en' => 'view vendor trashes',
                ]
            ],
            'restore-vendor' => [
                'translations' => [
                    'ar' => 'استعادة مورد',
                    'en' => 'restore vendor',
                ]
            ],
            'update-vendor-status' => [
                'translations' => [
                    'ar' => 'تحديث حالة المورد',
                    'en' => 'update vendor status',
                ]
            ],

            // Attribute Groups
            'view-attribute-groups' => [
                'translations' => [
                    'ar' => 'عرض مجموعات السمات',
                    'en' => 'view attribute groups',
                ]
            ],
            'create-attribute-groups' => [
                'translations' => [
                    'ar' => 'إضافة مجموعة سمات',
                    'en' => 'create attribute group',
                ]
            ],
            'update-attribute-groups' => [
                'translations' => [
                    'ar' => 'تعديل مجموعة سمات',
                    'en' => 'update attribute group',
                ]
            ],
            'destroy-selected-attribute-groups' => [
                'translations' => [
                    'ar' => 'حذف مجموعات السمات المحددة',
                    'en' => 'destroy selected attribute groups',
                ]
            ],

            // Options
            'view-options' => [
                'translations' => [
                    'ar' => 'عرض الخيارات',
                    'en' => 'view options',
                ]
            ],
            'create-options' => [
                'translations' => [
                    'ar' => 'إضافة خيار',
                    'en' => 'create option',
                ]
            ],
            'update-options' => [
                'translations' => [
                    'ar' => 'تعديل خيار',
                    'en' => 'update option',
                ]
            ],
            'delete-options' => [
                'translations' => [
                    'ar' => 'حذف خيار',
                    'en' => 'delete option',
                ]
            ],
            'destroy-selected-options' => [
                'translations' => [
                    'ar' => 'حذف الخيارات المحددة',
                    'en' => 'destroy selected options',
                ]
            ],

            // Attributes
            'view-attributes' => [
                'translations' => [
                    'ar' => 'عرض السمات',
                    'en' => 'view attributes',
                ]
            ],
            'create-attributes' => [
                'translations' => [
                    'ar' => 'إضافة سمة',
                    'en' => 'create attribute',
                ]
            ],
            'update-attributes' => [
                'translations' => [
                    'ar' => 'تعديل سمة',
                    'en' => 'update attribute',
                ]
            ],
            'destroy-selected-attributes' => [
                'translations' => [
                    'ar' => 'حذف السمات المحددة',
                    'en' => 'destroy selected attributes',
                ]
            ],

            // Categories
            'view-categories' => [
                'translations' => [
                    'ar' => 'عرض الفئات',
                    'en' => 'view categories',
                ]
            ],
            'create-categories' => [
                'translations' => [
                    'ar' => 'إضافة فئة',
                    'en' => 'create category',
                ]
            ],
            'update-categories' => [
                'translations' => [
                    'ar' => 'تعديل فئة',
                    'en' => 'update category',
                ]
            ],
            'delete-categories' => [
                'translations' => [
                    'ar' => 'حذف فئة',
                    'en' => 'delete category',
                ]
            ],
            'change-category-status' => [
                'translations' => [
                    'ar' => 'تغيير حالة الفئة',
                    'en' => 'change category status',
                ]
            ],
            'destroy-selected-categories' => [
                'translations' => [
                    'ar' => 'حذف الفئات المحددة',
                    'en' => 'destroy selected categories',
                ]
            ],
            'view-categories-trashes' => [
                'translations' => [
                    'ar' => 'عرض سلة المهملات الخاصة بالفئات',
                    'en' => 'view categories trashes',
                ]
            ],
            'restore-categories' => [
                'translations' => [
                    'ar' => 'استعادة فئة',
                    'en' => 'restore categories',
                ]
            ],
            'view-all-categories-form' => [
                'translations' => [
                    'ar' => 'عرض جميع الفئات',
                    'en' => 'view all categories form',
                ]
            ],

            // Products
            'view-products' => [
                'translations' => [
                    'ar' => 'عرض المنتجات',
                    'en' => 'view products',
                ]
            ],
            'create-products' => [
                'translations' => [
                    'ar' => 'إضافة منتج',
                    'en' => 'create product',
                ]
            ],
            'update-products' => [
                'translations' => [
                    'ar' => 'تعديل منتج',
                    'en' => 'update product',
                ]
            ],
            'delete-products' => [
                'translations' => [
                    'ar' => 'حذف منتج',
                    'en' => 'delete product',
                ]
            ],
            'change-product-status' => [
                'translations' => [
                    'ar' => 'تغيير حالة المنتج',
                    'en' => 'change product status',
                ]
            ],
            'destroy-selected-products' => [
                'translations' => [
                    'ar' => 'حذف المنتجات المحددة',
                    'en' => 'destroy selected products',
                ]
            ],
            'view-products-trashes' => [
                'translations' => [
                    'ar' => 'عرض سلة المهملات الخاصة بالمنتجات',
                    'en' => 'view products trashes',
                ]
            ],
            'restore-products' => [
                'translations' => [
                    'ar' => 'استعادة منتج',
                    'en' => 'restore products',
                ]
            ],
            'view-products-serials' => [
                'translations' => [
                    'ar' => 'عرض سريالات المنتجات',
                    'en' => 'view products serials',
                ]
            ],
            'apply-products-price-all' => [
                'translations' => [
                    'ar' => 'تسعير المنتجات الكلى',
                    'en' => 'apply products price all',
                ]
            ],
            'apply-products-price-all-groups' => [
                'translations' => [
                    'ar' => 'تسعير المنتجات الكلى للمجموعات',
                    'en' => 'apply products price all groups',
                ]
            ],
            'view-products-prices' => [
                'translations' => [
                    'ar' => 'عرض اسعار المنتجات',
                    'en' => 'view products prices',
                ]
            ],
            'view-products-by-brands' => [
                'translations' => [
                    'ar' => 'عرض المنتجات عن طريق العلامات التجارية',
                    'en' => 'view products by brands',
                ]
            ],
            'delete-products-images' => [
                'translations' => [
                    'ar' => 'حذف صور المنتجات',
                    'en' => 'delete products images',
                ]
            ],

            // Product Serials
            'manual-filling-products' => [
                'translations' => [
                    'ar' => 'ملء المنتجات يدويًا',
                    'en' => 'manual filling products',
                ]
            ],
            'auto-filling-products' => [
                'translations' => [
                    'ar' => 'ملء المنتجات تلقائيًا',
                    'en' => 'auto filling products',
                ]
            ],
            'change-status-products-serials' => [
                'translations' => [
                    'ar' => 'تغيير حالة تسلسل المنتجات',
                    'en' => 'change status of product serials',
                ]
            ],
            'stock-logs-products-serials' => [
                'translations' => [
                    'ar' => 'سجلات المخزون لتسلسل المنتجات',
                    'en' => 'stock logs for product serials',
                ]
            ],
            'stock-logs-products-serials-invoice' => [
                'translations' => [
                    'ar' => 'فاتورة سجلات المخزون لتسلسل المنتجات',
                    'en' => 'stock logs for product serials invoice',
                ]
            ],
            'update-stock-logs-products-serials' => [
                'translations' => [
                    'ar' => 'تحديث سجلات المخزون لتسلسل المنتجات',
                    'en' => 'update stock logs for product serials',
                ]
            ],

            // Customer Groups
            'view-customerGroups' => [
                'translations' => [
                    'ar' => 'عرض مجموعات العملاء',
                    'en' => 'view customer groups',
                ]
            ],
            'create-customerGroups' => [
                'translations' => [
                    'ar' => 'إضافة مجموعة عملاء',
                    'en' => 'create customer group',
                ]
            ],
            'update-customerGroups' => [
                'translations' => [
                    'ar' => 'تعديل مجموعة عملاء',
                    'en' => 'update customer group',
                ]
            ],
            'delete-customerGroups' => [
                'translations' => [
                    'ar' => 'حذف مجموعة عملاء',
                    'en' => 'delete customer group',
                ]
            ],
            'change-status-customerGroups' => [
                'translations' => [
                    'ar' => 'تغيير حالة مجموعة عملاء',
                    'en' => 'change status of customer group',
                ]
            ],
            'change-auto-assign-customerGroups' => [
                'translations' => [
                    'ar' => 'تغيير التعيين التلقائي لمجموعة عملاء',
                    'en' => 'change auto assign of customer group',
                ]
            ],
            'multi-delete-customerGroups' => [
                'translations' => [
                    'ar' => 'حذف مجموعات عملاء متعددة',
                    'en' => 'multi-delete customer groups',
                ]
            ],

            // Customers
            'view-customers' => [
                'translations' => [
                    'ar' => 'عرض العملاء',
                    'en' => 'view customers',
                ]
            ],
            'create-customers' => [
                'translations' => [
                    'ar' => 'إضافة عميل',
                    'en' => 'create customer',
                ]
            ],
            'update-customers' => [
                'translations' => [
                    'ar' => 'تعديل عميل',
                    'en' => 'update customer',
                ]
            ],
            'delete-customers' => [
                'translations' => [
                    'ar' => 'حذف عميل',
                    'en' => 'delete customer',
                ]
            ],
            'change-status-customers' => [
                'translations' => [
                    'ar' => 'تغيير حالة عميل',
                    'en' => 'change status of customer',
                ]
            ],
            'add-balance-customers' => [
                'translations' => [
                    'ar' => 'إضافة رصيد للعميل',
                    'en' => 'add balance to customer',
                ]
            ],

            // Orders
            'view-orders' => [
                'translations' => [
                    'ar' => 'عرض الطلبات',
                    'en' => 'view orders',
                ]
            ],
            'create-orders' => [
                'translations' => [
                    'ar' => 'إضافة طلب',
                    'en' => 'create order',
                ]
            ],
            'update-status-orders' => [
                'translations' => [
                    'ar' => 'تحديث حالة الطلب',
                    'en' => 'update status of order',
                ]
            ],
            'save-notes-orders' => [
                'translations' => [
                    'ar' => 'حفظ الملاحظات',
                    'en' => 'save notes for order',
                ]
            ],
            'get-customer-orders' => [
                'translations' => [
                    'ar' => 'عرض طلبات العميل',
                    'en' => 'get customer orders',
                ]
            ],
            'pull-top-up-order-orders' => [
                'translations' => [
                    'ar' => 'سحب طلب إعادة شحن',
                    'en' => 'pull top-up order',
                ]
            ],
            'change-status-top-up-orders' => [
                'translations' => [
                    'ar' => 'تغيير حالة طلب إعادة الشحن',
                    'en' => 'change status of top-up order',
                ]
            ],

            // Order Complaints
            'view-complaints' => [
                'translations' => [
                    'ar' => 'عرض الشكاوى',
                    'en' => 'view complaints',
                ]
            ],
            'change-status-complaints' => [
                'translations' => [
                    'ar' => 'تغيير حالة الشكوى',
                    'en' => 'change status of complaint',
                ]
            ],

            // Sellers
            'view-sellers' => [
                'translations' => [
                    'ar' => 'عرض التجار',
                    'en' => 'view sellers',
                ]
            ],
            'view-not-approved-sellers' => [
                'translations' => [
                    'ar' => 'عرض التجار الذين لم يتم الموافقة عليهم',
                    'en' => 'view not approved sellers',
                ]
            ],
            'create-sellers' => [
                'translations' => [
                    'ar' => 'إضافة تاجر',
                    'en' => 'create seller',
                ]
            ],
            'update-sellers' => [
                'translations' => [
                    'ar' => 'تعديل تاجر',
                    'en' => 'update seller',
                ]
            ],
            'delete-sellers' => [
                'translations' => [
                    'ar' => 'حذف تاجر',
                    'en' => 'delete seller',
                ]
            ],
            'add-balance-sellers' => [
                'translations' => [
                    'ar' => 'إضافة رصيد للتاجر',
                    'en' => 'add balance to seller',
                ]
            ],
            'change-status-sellers' => [
                'translations' => [
                    'ar' => 'تغيير حالة تاجر',
                    'en' => 'change status of seller',
                ]
            ],
            'change-approval-status-sellers' => [
                'translations' => [
                    'ar' => 'تغيير حالة الموافقة للتاجر',
                    'en' => 'change approval status of seller',
                ]
            ],
            'delete-attachments-sellers' => [
                'translations' => [
                    'ar' => 'حذف المرفقات',
                    'en' => 'delete attachments',
                ]
            ],
            'view-trashes-sellers' => [
                'translations' => [
                    'ar' => 'عرض المحذوفات',
                    'en' => 'view trashes',
                ]
            ],
            'restore-sellers' => [
                'translations' => [
                    'ar' => 'استعادة تاجر',
                    'en' => 'restore seller',
                ]
            ],

            // Seller Groups
            'view-sellerGroups' => [
                'translations' => [
                    'ar' => 'عرض مجموعات التجار',
                    'en' => 'view seller groups',
                ]
            ],
            'create-sellerGroups' => [
                'translations' => [
                    'ar' => 'إضافة مجموعة تجار',
                    'en' => 'create seller group',
                ]
            ],
            'update-sellerGroups' => [
                'translations' => [
                    'ar' => 'تعديل مجموعة تجار',
                    'en' => 'update seller group',
                ]
            ],
            'delete-sellerGroups' => [
                'translations' => [
                    'ar' => 'حذف مجموعة تجار',
                    'en' => 'delete seller group',
                ]
            ],
            'change-status-sellerGroups' => [
                'translations' => [
                    'ar' => 'تغيير حالة مجموعة تجار',
                    'en' => 'change status of seller group',
                ]
            ],
            'auto-assign-sellerGroups' => [
                'translations' => [
                    'ar' => 'تعيين تلقائي لمجموعة تجار',
                    'en' => 'auto assign seller group',
                ]
            ],
            'destroy-selected-sellerGroups' => [
                'translations' => [
                    'ar' => 'حذف مجموعات تجار مختارة',
                    'en' => 'destroy selected seller groups',
                ]
            ],
            'view-trashes-sellerGroups' => [
                'translations' => [
                    'ar' => 'عرض المحذوفات',
                    'en' => 'view trashed seller groups',
                ]
            ],
            'restore-sellerGroups' => [
                'translations' => [
                    'ar' => 'استعادة مجموعة تاجر',
                    'en' => 'restore seller groups',
                ]
            ],

            // Seller Group Levels
            'view-sellerGroupLevels' => [
                'translations' => [
                    'ar' => 'عرض مستويات مجموعة التجار',
                    'en' => 'view seller group levels',
                ]
            ],
            'create-sellerGroupLevels' => [
                'translations' => [
                    'ar' => 'إضافة مستوى مجموعة تجار',
                    'en' => 'create seller group level',
                ]
            ],
            'update-sellerGroupLevels' => [
                'translations' => [
                    'ar' => 'تعديل مستوى مجموعة تجار',
                    'en' => 'update seller group level',
                ]
            ],
            'delete-sellerGroupLevels' => [
                'translations' => [
                    'ar' => 'حذف مستوى مجموعة تجار',
                    'en' => 'delete seller group level',
                ]
            ],
            'change-status-sellerGroupLevels' => [
                'translations' => [
                    'ar' => 'تغيير حالة مستوى مجموعة تجار',
                    'en' => 'change status of seller group level',
                ]
            ],
            'view-trashes-sellerGroupLevels' => [
                'translations' => [
                    'ar' => 'عرض المحذوفات',
                    'en' => 'view trashed seller groups levels',
                ]
            ],
            'restore-sellerGroupLevels' => [
                'translations' => [
                    'ar' => 'استعادة مستوى مجموعة تاجر',
                    'en' => 'restore seller groups levels',
                ]
            ],

            // Home
            'view-home' => [
                'translations' => [
                    'ar' => 'عرض الصفحة الرئيسية',
                    'en' => 'view home',
                ]
            ],

            // Settings
            'view-main-settings' => [
                'translations' => [
                    'ar' => 'عرض الإعدادات',
                    'en' => 'view settings',
                ]
            ],
            'update-main-settings' => [
                'translations' => [
                    'ar' => 'تحديث الإعدادات الرئيسية',
                    'en' => 'update main settings',
                ]
            ],
            'view-static-pages' => [
                'translations' => [
                    'ar' => 'عرض الصفحات الثابتة',
                    'en' => 'view static pages',
                ]
            ],
            'create-static-pages' => [
                'translations' => [
                    'ar' => 'إضافة صفحة ثابتة',
                    'en' => 'create static page',
                ]
            ],
            'update-static-pages' => [
                'translations' => [
                    'ar' => 'تعديل صفحة ثابتة',
                    'en' => 'update static page',
                ]
            ],
            'delete-static-pages' => [
                'translations' => [
                    'ar' => 'حذف صفحة ثابتة',
                    'en' => 'delete static page',
                ]
            ],
            'change-status-static-pages' => [
                'translations' => [
                    'ar' => 'تغيير حالة صفحة ثابتة',
                    'en' => 'change status of static pages',
                ]
            ],
            'view-notifications' => [
                'translations' => [
                    'ar' => 'عرض الإشعارات',
                    'en' => 'view notifications',
                ]
            ],
            'update-notifications' => [
                'translations' => [
                    'ar' => 'تحديث إعدادات الإشعارات',
                    'en' => 'update notification settings',
                ]
            ],
            'view-store-appearance' => [
                'translations' => [
                    'ar' => 'عرض مظهر المتجر',
                    'en' => 'view store appearance',
                ]
            ],
            'view-sliders' => [
                'translations' => [
                    'ar' => 'عرض شريط تمرير',
                    'en' => 'view slider',
                ]
            ],
            'create-sliders' => [
                'translations' => [
                    'ar' => 'إضافة شريط تمرير',
                    'en' => 'create slider',
                ]
            ],
            'update-sliders' => [
                'translations' => [
                    'ar' => 'تعديل شريط تمرير',
                    'en' => 'update slider',
                ]
            ],
            'delete-sliders' => [
                'translations' => [
                    'ar' => 'حذف شريط تمرير',
                    'en' => 'delete slider',
                ]
            ],
            'change-status-sliders' => [
                'translations' => [
                    'ar' => 'تغيير حالة شريط تمرير',
                    'en' => 'change status of sliders',
                ]
            ],
            'move-sliders' => [
                'translations' => [
                    'ar' => 'تحريك شريط تمرير',
                    'en' => 'move slider',
                ]
            ],
            'view-home-sections' => [
                'translations' => [
                    'ar' => 'عرض أقسام الصفحة الرئيسية',
                    'en' => 'view home sections',
                ]
            ],
            'create-home-sections' => [
                'translations' => [
                    'ar' => 'إضافة قسم إلى الصفحة الرئيسية',
                    'en' => 'create home section',
                ]
            ],
            'update-home-sections' => [
                'translations' => [
                    'ar' => 'تعديل قسم الصفحة الرئيسية',
                    'en' => 'update home section',
                ]
            ],
            'delete-home-sections' => [
                'translations' => [
                    'ar' => 'حذف قسم من الصفحة الرئيسية',
                    'en' => 'delete home section',
                ]
            ],
            'move-home-sections' => [
                'translations' => [
                    'ar' => 'تحريك قسم الصفحة الرئيسية',
                    'en' => 'move home section',
                ]
            ],
            'change-status-home-sections' => [
                'translations' => [
                    'ar' => 'تغيير حالة الصفحة الرئيسية',
                    'en' => 'change status of home sections',
                ]
            ],

            // Taxes
            'view-taxes' => [
                'translations' => [
                    'ar' => 'عرض الضرائب',
                    'en' => 'view taxes',
                ]
            ],
            'create-taxes' => [
                'translations' => [
                    'ar' => 'إضافة ضريبة',
                    'en' => 'create tax',
                ]
            ],
            'update-taxes' => [
                'translations' => [
                    'ar' => 'تعديل ضريبة',
                    'en' => 'update tax',
                ]
            ],
            'change-status-taxes' => [
                'translations' => [
                    'ar' => 'تغيير حالة الضريبة',
                    'en' => 'change status of taxes',
                ]
            ],
            'delete-taxes' => [
                'translations' => [
                    'ar' => 'حذف ضريبة',
                    'en' => 'delete tax',
                ]
            ],
            'update-prices-display' => [
                'translations' => [
                    'ar' => 'تحديث عرض الأسعار',
                    'en' => 'update prices display',
                ]
            ],
            'update-tax-number' => [
                'translations' => [
                    'ar' => 'تحديث رقم الضريبة',
                    'en' => 'update tax number',
                ]
            ],

            // Roles and Permissions
            'view-roles-permissions' => [
                'translations' => [
                    'ar' => 'عرض الأدوار والصلاحيات',
                    'en' => 'view roles and permissions',
                ]
            ],
            'create-roles' => [
                'translations' => [
                    'ar' => 'إضافة دور',
                    'en' => 'create role',
                ]
            ],
            'update-roles' => [
                'translations' => [
                    'ar' => 'تعديل دور',
                    'en' => 'update role',
                ]
            ],
            'delete-roles' => [
                'translations' => [
                    'ar' => 'حذف دور',
                    'en' => 'delete role',
                ]
            ],
            'change-status-roles' => [
                'translations' => [
                    'ar' => 'تغيير حالة دور',
                    'en' => 'change status of role',
                ]
            ],

            // Sub Admins
            'view-subAdmins' => [
                'translations' => [
                    'ar' => 'عرض المسؤولين الفرعيين',
                    'en' => 'view sub admins',
                ]
            ],
            'create-subAdmins' => [
                'translations' => [
                    'ar' => 'إضافة مسؤول فرعي',
                    'en' => 'create sub admin',
                ]
            ],
            'update-subAdmins' => [
                'translations' => [
                    'ar' => 'تعديل مسؤول فرعي',
                    'en' => 'update sub admin',
                ]
            ],
            'delete-subAdmins' => [
                'translations' => [
                    'ar' => 'حذف مسؤول فرعي',
                    'en' => 'delete sub admin',
                ]
            ],
            'change-status-subAdmins' => [
                'translations' => [
                    'ar' => 'تغيير حالة مسؤول فرعي',
                    'en' => 'change status of sub admin',
                ]
            ],

            // Ratings
            'view-ratings' => [
                'translations' => [
                    'ar' => 'عرض التقييمات',
                    'en' => 'view ratings',
                ]
            ],
            'delete-ratings' => [
                'translations' => [
                    'ar' => 'حذف تقييم',
                    'en' => 'delete rating',
                ]
            ],
            'create-replays' => [
                'translations' => [
                    'ar' => 'إضافة رد',
                    'en' => 'create replay',
                ]
            ],
            'update-replays' => [
                'translations' => [
                    'ar' => 'تعديل رد',
                    'en' => 'update replay',
                ]
            ],
            'delete-replays' => [
                'translations' => [
                    'ar' => 'حذف رد',
                    'en' => 'delete replay',
                ]
            ],
            'create-reactions' => [
                'translations' => [
                    'ar' => 'إضافة رد فعل',
                    'en' => 'create reaction',
                ]
            ],
            'delete-reactions' => [
                'translations' => [
                    'ar' => 'حذف رد فعل',
                    'en' => 'delete reaction',
                ]
            ],

            // Integrations
            'view-integrations' => [
                'translations' => [
                    'ar' => 'عرض التكاملات',
                    'en' => 'view integrations',
                ]
            ],
            'update-integrations' => [
                'translations' => [
                    'ar' => 'تحديث التكاملات',
                    'en' => 'update integration',
                ]
            ],
            'change-status-integrations' => [
                'translations' => [
                    'ar' => 'تغيير حالة التكامل',
                    'en' => 'change status of integration',
                ]
            ],

            // Vendor Products
            'view-vendorProducts' => [
                'translations' => [
                    'ar' => 'عرض منتجات الموردين',
                    'en' => 'view vendor products',
                ]
            ],
            'create-vendorProducts' => [
                'translations' => [
                    'ar' => 'إضافة منتج مورد',
                    'en' => 'create vendor product',
                ]
            ],
            'update-vendorProducts' => [
                'translations' => [
                    'ar' => 'تعديل منتج مورد',
                    'en' => 'update vendor product',
                ]
            ],
            'delete-vendorProducts' => [
                'translations' => [
                    'ar' => 'حذف منتج مورد',
                    'en' => 'delete vendor product',
                ]
            ],

            // Direct Purchase Priorities
            'view-purchasePriorities' => [
                'translations' => [
                    'ar' => 'عرض أولويات الشراء المباشر',
                    'en' => 'view direct purchase priorities',
                ]
            ],
            'create-purchasePriorities' => [
                'translations' => [
                    'ar' => 'إضافة أولوية شراء مباشر',
                    'en' => 'create direct purchase priority',
                ]
            ],
            'change-status-purchasePriorities' => [
                'translations' => [
                    'ar' => 'تغيير حالة أولوية الشراء المباشر',
                    'en' => 'change status of direct purchase priority',
                ]
            ],

            // notifications
            'notifications-new-orders' => [
                'translations' =>
                [
                    'ar' => 'اشعارات للطلبات الجديده',
                    'en' => 'notifications for new orders',
                ]
            ],
            'notifications-new-customers' => [
                'translations' =>
                [
                    'ar' => 'اشعارات للعملاء الجدد',
                    'en' => 'notifications for new customers',
                ]
            ],
            'notifications-new-ratings' => [
                'translations' =>
                [
                    'ar' => 'اشعارات للتقييمات الجديدة',
                    'en' => 'notifications for new ratings',
                ]
            ],
            'notifications-new-complaints' => [
                'translations' =>
                [
                    'ar' => 'اشعارات للشكاوى الجديدة',
                    'en' => 'notifications for new complaints',
                ]
            ],
            'notifications-low-stock' => [
                'translations' =>
                [
                    'ar' => 'اشعارات لنقص المخزون',
                    'en' => 'notifications for low stock',
                ]
            ],

            // Reports
            'reports-product-sales' => [
                'translations' => [
                    'ar' => 'تقارير مبيعات المنتجات',
                    'en' => 'reports product sales',
                ]
            ],
            'reports-order-sales' => [
                'translations' => [
                    'ar' => 'تقارير مبيعات الطلبات',
                    'en' => 'reports order sales',
                ]
            ],
            'reports-detailed-payments' => [
                'translations' => [
                    'ar' => 'تقارير المدفوعات التفصيلى',
                    'en' => 'reports detailed payments',
                ]
            ],
            'reports-total-payments' => [
                'translations' => [
                    'ar' => 'تقارير المدفوعات الاجمالى',
                    'en' => 'reports total payments',
                ]
            ],
            'view-bank-commissions' => [
                'translations' => [
                    'ar' => 'عرض عمولات البنوك',
                    'en' => 'view bank commissions',
                ]
            ],
            'set-bank-commissions' => [
                'translations' => [
                    'ar' => 'اضافة قيمة لخاصية العملاوت ',
                    'en' => 'set bank commissions',
                ]
            ],
        ];


        // add permissions with its translations
        $permissionsArray = [];
        foreach ($adminPermissions as $permission => $transArr) {
            $permission = Permission::firstOrCreate(['guard_name' => 'adminApi', 'name' => $permission]);
            $permissionsArray[] = $permission;
            foreach ($languages as $language) {
                PermissionTranslation::firstOrCreate([
                    'permission_id' => $permission->id,
                    'language_id' => $language->id,
                    'display_name' => $transArr['translations'][$language->code],
                ]);
            }
        }


        $rolesTranslations = [
            'ar' => 'ادمن خاص',
            'en' => 'admin 1',
        ];

        // add roles with its translations
        $adminRole = Role::firstOrCreate(['guard_name' => 'adminApi', 'name' => 'Admin1']);
        $adminRole->givePermissionTo($permissionsArray);
        foreach ($languages as $language) {
            RoleTranslation::firstOrCreate([
                'role_id' => $adminRole->id,
                'language_id' => $language->id,
                'display_name' => $rolesTranslations[$language->code],
            ]);
        }
    }
}
