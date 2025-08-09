<?php



namespace App\Enums\File;


enum TableColumnMap:string {

    /**
     * Map of tables to their respective file URL column names
     *
     * @return array<string, string>
     */
    public static function getMappings(): array
    {
        return [
            'brand_images' => 'image',
            'vendor_attachments' => 'file_url',
            'product_images' => 'image',
            'sellers' => 'logo',
            'seller_attachments' => 'file_url',
            'seller_groups' => 'image',
            'support_ticket_attachments' => 'file_url',
            'wallets' => 'receipt_image',

            // Vendor related
            // 'vendors' => 'logo',

            // Admin related
            // 'users' => 'avatar',

            // Brand related

            // Category related
            // 'categories' => 'image',

            // Country related
            // 'countries' => 'flag',

            // Customer related
            // 'customers' => 'image',

            // Home sections related
            // 'home_section_translations' => 'image',

            // Items related
            // 'in_review_brands' => 'image',
            // 'in_review_categories' => 'image',
            // 'in_review_products' => 'image',

            // Language related
            // 'languages' => 'image',

            // Loyalty program related
            // 'loyalty_programs' => 'image',

            // Products related
            // 'products' => 'image',
            // 'product_serials' => 'file',

            // Settings related
            // 'settings' => 'value',

            // Slider related
            // 'slider_translations' => 'image',
        ];
    }

    /**
     * Get column name for a specific table
     *
     * @param string $tableName
     * @return string|null
     */
    public static function getColumnName(string $tableName): ?string
    {
        return self::getMappings()[$tableName] ?? null;
    }

}
