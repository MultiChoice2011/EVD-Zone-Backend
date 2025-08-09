<?php



namespace App\Enums\Integration;


enum IntegrationTypeEnum:string {

    case OPTION = 'option';
    case OPTION_VALUE = 'option_value';

    public static function getList(): array
    {
        return [
            self::getOptionType(),
            self::getOptionValueType(),
        ];
    }
    public static function getOptionType(): string
    {
        return self::OPTION->value;
    }
    public static function getOptionValueType(): string
    {
        return self::OPTION_VALUE->value;
    }

}
