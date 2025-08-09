<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Permission;
use App\Models\PermissionTranslation;
use App\Models\Role;
use App\Models\RoleTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndPermissionSellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $langs = Language::all();
        $sellerPermissions = [
            'home-section' => [
                'translations' => [
                    'ar' => 'الصفحة الرئيسية',
                    'en' => 'Home Section',
                ]
            ],
            'wallet-add-balance' => [
                'translations' => [
                    'ar' => 'شحن الرصيد',
                    'en' => 'add balance',
                ]
            ],
            'wallet-balance-list' => [
                'translations' => [
                    'ar' => 'عمليات شحن الرصيد',
                    'en' => 'balance list',
                ]
            ],
            'products-list' => [
                'translations' => [
                    'ar' => 'قائمة المنتجات',
                    'en' => 'products list',
                ]
            ],
            'store' => [
                'translations' => [
                    'ar' => 'المتجر',
                    'en' => 'store',
                ]
            ],
            'favorites-products-list' => [
                'translations' => [
                    'ar' => 'المنتجات المفضلة',
                    'en' => 'favorites products',
                ]
            ],
            'delete-product-fav' => [
                'translations' => [
                    'ar' => 'حذف المنتجات من المفضلة',
                    'en' => 'deletes product from fav',
                ]
            ],
            'add-product-fav' => [
                'translations' => [
                    'ar' => 'اضافة المنتج للمفضلة',
                    'en' => 'add product from fav',
                ]
            ],
            'orders-list' => [
                'translations' => [
                    'ar' => 'الطلبات',
                    'en' => 'orders',
                ]
            ],
            'add-order' => [
                'translations' => [
                    'ar' => 'اضافة طلب',
                    'en' => 'add order',
                ]
            ],
            'view-order' => [
                'translations' => [
                    'ar' => 'عرض طلب',
                    'en' => 'view order',
                ]
            ],
            'reports' => [
                'translations' => [
                    'ar' => 'التقارير',
                    'en' => 'reports',
                ]
            ],
            'setting-view-profile' => [
                'translations' => [
                    'ar' => 'عرض الملف الشخصى',
                    'en' => 'view profile',
                ]
            ],
            'setting-update-profile' => [
                'translations' => [
                    'ar' => 'تحديث الملف الشخصى',
                    'en' => 'update profile',
                ]
            ],
            'setting-admin-list' => [
                'translations' => [
                    'ar' => 'موظفو النظام',
                    'en' => 'admin list',
                ]
            ],
            'setting-add-admin-list' => [
                'translations' => [
                    'ar' => 'اضافة موظف',
                    'en' => 'add admin list',
                ]
            ],
            'setting-edit-admin-list' => [
                'translations' => [
                    'ar' => 'تعديل موظف',
                    'en' => 'edit admin list',
                ]
            ],
            'setting-delete-admin-list' => [
                'translations' => [
                    'ar' => 'حذف موظف',
                    'en' => 'delete admin list',
                ]
            ],
            'setting-change-status-admin-list' => [
                'translations' => [
                    'ar' => 'تحديث حالة الموظف',
                    'en' => 'delete admin list',
                ]
            ],
            'setting-roles-list' => [
                'translations' => [
                    'ar' => 'ادوار الموظفين',
                    'en' => 'roles list',
                ]
            ],
            'setting-add-role' => [
                'translations' => [
                    'ar' => 'اضافة دور',
                    'en' => 'add role',
                ]
            ],
            'setting-edit-role' => [
                'translations' => [
                    'ar' => 'تعديل دور',
                    'en' => 'edit role',
                ]
            ],
            'setting-delete-role' => [
                'translations' => [
                    'ar' => 'حذف دور',
                    'en' => 'delete role',
                ]
            ],
            'setting-show-role' => [
                'translations' => [
                    'ar' => 'حذف دور',
                    'en' => 'delete role',
                ]
            ],
            'setting-change-status-role' => [
                'translations' => [
                    'ar' => 'تحديث حالة الدور',
                    'en' => 'change status role',
                ]
            ],
            'tickets-list' => [
                'translations' => [
                    'ar' => 'الدعم الفنى',
                    'en' => 'tickets list',
                ]
            ],
            'add-ticket' => [
                'translations' => [
                    'ar' => 'اضافة تذكرة دعم فنى',
                    'en' => 'add ticket',
                ]
            ],
            'view-ticket' => [
                'translations' => [
                    'ar' => 'عرض تذكرة الدعم الفنى',
                    'en' => 'show ticket',
                ]
            ],
        ];
        // add permissions with its translations
        $permissionsArray = [];
        foreach ($sellerPermissions as $permission => $transArr) {
            $permission = Permission::create(['guard_name' => 'sellerApi', 'name' => $permission]);
            $permissionsArray[] = $permission;
            foreach ($langs as $language) {
                PermissionTranslation::create([
                    'permission_id' => $permission->id,
                    'language_id' => $language->id,
                    'display_name' => $transArr['translations'][$language->code],
                ]);
            }
        }


        $rolesTranslations = [
            'ar' => 'تاجر خاص',
            'en' => 'super seller',
        ];

        // add roles with its translations
        $adminRole = Role::create(['guard_name' => 'sellerApi', 'name' => 'Super Seller']);
        $adminRole->givePermissionTo($permissionsArray);
        foreach ($langs as $language) {
            RoleTranslation::create([
                'role_id' => $adminRole->id,
                'language_id' => $language->id,
                'display_name' => $rolesTranslations[$language->code],
            ]);
        }
    }
}
