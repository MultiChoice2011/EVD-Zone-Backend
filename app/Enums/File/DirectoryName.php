<?php



namespace App\Enums\File;


enum DirectoryName:string {

    case VENDORS = 'vendors';
    case ADMINS = 'admins';
    case BRANDS = 'brands';
    case CATEGORIES = 'categories';
    case COUNTRIES = 'countries';
    case CUSTOMERS = 'customers';
    case SELLERS = 'sellers';
    case HOME_SECTIONS = 'homeSections';
    case ITEMS = 'items';
    case LANGUAGES = 'languages';
    case LOYALTY_PROGRAM = 'LoyaltyProgram';
    case ORDERS = 'orders';
    case PAYMENT = 'payment';
    case PRODUCTS = 'products';
    case SETTINGS = 'settings';
    case SLIDERS = 'sliders';
    case ORDER_RECEIPTS = 'Order_Receipts';
    case RECEIPTS = 'receipts';
    case SUPPORT_TICKETS = 'supportTickets';

    public static function getList(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getTableNames(string $name): array
    {
        return match ($name) {
            self::VENDORS->value => ['vendor_attachments'],
            self::BRANDS->value => ['brand_images'],
            self::PRODUCTS->value => ['product_images'],
            self::RECEIPTS->value => ['wallets'],
            self::SUPPORT_TICKETS->value => ['support_ticket_attachments'],
            self::SELLERS->value => ['sellers', 'seller_attachments', 'seller_groups'],
            default => [],

            // self::VENDORS->value => ['vendors', 'vendor_attachments'],
            // self::ADMINS->value => ['admins'],
            // self::CATEGORIES->value => ['categories'],
            // self::COUNTRIES->value => ['countries'],
            // self::CUSTOMERS->value => ['customers'],
            // self::HOME_SECTIONS->value => ['home_section_translations'],
            // self::ITEMS->value => ['in_review_brands', 'in_review_categories', 'in_review_products'],
            // self::LANGUAGES->value => ['languages'],
            // self::LOYALTY_PROGRAM->value => ['loyalty_programs'],
            // self::ORDERS->value => ['orders', 'order_gifts'],
            // self::PAYMENT->value => ['payments'],
            // self::PRODUCTS->value => ['products', 'product_images', 'product_serials'],
            // self::SETTINGS->value => ['settings'],
            // self::SLIDERS->value => ['slider_translations'],
            // self::ORDER_RECEIPTS->value => ['order_receipts'],
        };
    }

    public static function getVendorName(): string
    {
        return self::VENDORS->value;
    }

    public static function getAdminName(): string
    {
        return self::ADMINS->value;
    }

    public static function getBrandName(): string
    {
        return self::BRANDS->value;
    }

    public static function getCategoryName(): string
    {
        return self::CATEGORIES->value;
    }

    public static function getCountryName(): string
    {
        return self::COUNTRIES->value;
    }

    public static function getCustomerName(): string
    {
        return self::CUSTOMERS->value;
    }

    public static function getHomeSectionName(): string
    {
        return self::HOME_SECTIONS->value;
    }

    public static function getItemName(): string
    {
        return self::ITEMS->value;
    }

    public static function getLanguageName(): string
    {
        return self::LANGUAGES->value;
    }

    public static function getLoyaltyProgramName(): string
    {
        return self::LOYALTY_PROGRAM->value;
    }

    public static function getOrderName(): string
    {
        return self::ORDERS->value;
    }

    public static function getPaymentName(): string
    {
        return self::PAYMENT->value;
    }

    public static function getProductName(): string
    {
        return self::PRODUCTS->value;
    }

    public static function getSettingName(): string
    {
        return self::SETTINGS->value;
    }

    public static function getSliderName(): string
    {
        return self::SLIDERS->value;
    }

    public static function getOrderReceiptName(): string
    {
        return self::ORDER_RECEIPTS->value;
    }

}
