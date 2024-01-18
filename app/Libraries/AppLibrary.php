<?php

namespace App\Libraries;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\CurrencyPositionEnum;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Smartisan\Settings\Facades\Settings;

class AppLibrary
{
    public static function formatDate($date, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('DATE_FORMAT');
        }
        return Carbon::parse($date)->format($pattern);
    }

    public static function formatTime($time, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('TIME_FORMAT');
        }
        return Carbon::parse($time)->format($pattern);
    }

    public static function formatDateTime($dateTime, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('TIME_FORMAT') . ', ' . env('DATE_FORMAT');
        }
        return Carbon::parse($dateTime)->format($pattern);
    }

    public static function increaseDate($dateTime, $days, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('DATE_FORMAT');
        }
        return Carbon::parse($dateTime)->addDays($days)->format($pattern);
    }

    public static function deliveryTime($dateTime, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('TIME_FORMAT');
        }
        $explode = explode('-', $dateTime);
        if (count($explode) == 2) {
            return Carbon::parse(trim($explode[0]))->format($pattern) . ' - ' . Carbon::parse(trim($explode[1]))->format($pattern);
        }
        return '';
    }

    public static function associativeToNumericArrayBuilder($array): array
    {
        $i = 1;
        $buildArray = [];
        if (count($array)) {
            foreach ($array as $arr) {
                if (isset($arr['children'])) {
                    $children = $arr['children'];
                    unset($arr['children']);

                    $arr['parent'] = 0;
                    $buildArray[$i] = $arr;
                    $parentId = $i;
                    $i++;
                    foreach ($children as $child) {
                        $child['parent'] = $parentId;
                        $buildArray[$i] = $child;
                        $i++;
                    }
                } else {
                    $arr['parent'] = 0;
                    $buildArray[$i] = $arr;
                    $i++;
                }
            }
        }
        return $buildArray;
    }

    public static function numericToAssociativeArrayBuilder($array): array
    {
        $i = 0;
        $parentId = null;
        $parentIncrementId = null;
        $buildArray = [];
        if (count($array)) {
            foreach ($array as $arr) {
                if (!$arr['parent']) {
                    $parentId = $arr['id'];
                    $parentIncrementId = $i;
                    $buildArray[$i] = $arr;
                    $i++;
                }

                if ($arr['parent'] == $parentId) {
                    $buildArray[$parentIncrementId]['children'][] = $arr;
                }
            }
        }
        if ($buildArray) {
            foreach ($buildArray as $key => $build) {
                if ($build['url'] == "#" && !isset($build['children'])) {
                    unset($buildArray[$key]);
                }
            }
        }

        return $buildArray;
    }

    public static function pluck($array, $value, $key = null, $type = 'object'): array
    {
        $returnArray = [];
        if ($array) {
            foreach ($array as $item) {
                if ($key != null) {
                    if ($type == 'array') {
                        $returnArray[$item[$key]] = strtolower($value) == 'obj' ? $item : $item[$value];
                    } else {
                        $returnArray[$item[$key]] = strtolower($value) == 'obj' ? $item : $item->$value;
                    }
                } elseif ($value == 'obj') {
                    $returnArray[] = $item;
                } elseif ($type == 'array') {
                    $returnArray[] = $item[$value];
                } else {
                    $returnArray[] = $item->$value;
                }
            }
        }
        return $returnArray;
    }

    public static function generateUsername($name)
    {
        if ($name) {
            $username = strtolower(str_replace(' ', '', $name)) . rand(1, 999999);
            if (User::where(['username' => $username])->first()) {
                self::generateUsername($name);
            }
            return $username;
        }
    }

    public static function amountCheck($amount, $attr = 'price'): object
    {
        $response = [
            'status' => true,
            'message' => ''
        ];

        if (!is_numeric($amount)) {
            $response['status'] = false;
            $response['message'] = "This {$attr} must be integer.";
        }

        if ($amount <= 0) {
            if ($response['status'] == false) {
                return (object)$response;
            } else {
                $response['status'] = false;
                $response['message'] = "This {$attr} negative amount not allow.";
            }
        }

        $replaceValue = str_replace('.', '', $amount);
        if (strlen($replaceValue) > 12) {
            if ($response['status'] == false) {
                return (object)$response;
            } else {
                $response['status'] = false;
                $response['message'] = "This {$attr} length can't be greater than 12 digit.";
            }
        }

        if (!preg_match("/^\d{1,10}(\.\d{1,2})?$/", $amount)) {
            if ($response['status'] == false) {
                return (object)$response;
            } else {
                $response['status'] = false;
                $response['message'] = "This {$attr} amount provide invalid.";
            }
        }

        return (object)$response;
    }

    public static function currencyAmountFormat($amount): string
    {
        if (env('CURRENCY_POSITION') == CurrencyPositionEnum::LEFT) {
            return env('CURRENCY_SYMBOL') . number_format($amount, env('CURRENCY_DECIMAL_POINT'), '.', '');
        }
        return number_format($amount, env('CURRENCY_DECIMAL_POINT'), '.', '') . env('CURRENCY_SYMBOL');
    }

    public static function flatAmountFormat($amount): string
    {
        return number_format($amount, env('CURRENCY_DECIMAL_POINT'), '.', '');
    }

    public static function deleteDir($dirPath): void
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}
