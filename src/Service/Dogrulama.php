<?php
namespace App\Service;



class Dogrulama
{
    public static function isEmpty(array $data): array
    {
        $errors = [];
        foreach ($data as $key => $value) {
            if (empty($value) || $value == null || $value == '' || $value == 'null' || $value == 'undefined') {
                $errors[$key] = 'Bu alan zorunludur';
            }
        }
        return $errors;
         
    }

    public static function isImage(string $data): bool
    {
        if(is_string($data) && $data != null && $data != '' && $data != 'null' && $data != 'undefined'){
            return true;
        }
        return false;
    }

    public static function isString(string $data): bool
    {
        if(is_string($data) && $data != null && $data != '' && $data != 'null' && $data != 'undefined'){
            return true;
        }
        return false;
    }

    public static function isNumber(int $data): bool
    {
        if(is_numeric($data) && $data > 0){
            return true;
        }
        return false;
    }

    public static function isMailValid(string $data): bool
    {
        if(filter_var($data, FILTER_VALIDATE_EMAIL)){
            return true;
        }
        return false;
    }

    public static function isPhoneValid(string $data): bool
    {
        if(preg_match('/^[0-9]{10}$/', $data)){
            return true;
        }
        return false;
    }

    public static function isUrlValid(string $data): bool
    {
        if(filter_var($data, FILTER_VALIDATE_URL)){
            return true;
        }
        return false;
    }
    
}