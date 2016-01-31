<?php

class Convert {


    /**
     * Рекурсивный вариант функции iconv()
     *
     * Источник: https://gist.github.com/gridsane/2155631
     */
    public function iconv_deep ($fromCodepage, $toCodepage, $data)
    {
        if(is_array($data)) {
            foreach($data as $key => $value) {
                if(is_array($value)) {
                    $data[$key] = $this->iconv_deep($fromCodepage, $toCodepage, $value);
                } else {
                    $data[$key] = iconv($fromCodepage, $toCodepage, $value);
                }
            }
        } else {
            $data = iconv($fromCodepage, $toCodepage, $data);
        }
        return $data;
    }


    /**
     * Генерация ЧПУ
     */
    public function generateSefUrl ($string, $addUniqueKey=false)
    {
        // "Подготовка" строки
        $string = trim($string);
        $string = mb_strtolower($string);

        // Удаление спец.символов
        $specSymbFrom = array('.',',',':',';','?','!','(',')','\\','/','\'','~','`','+','=','&','№',
                              '&quot',chr(171),chr(187),chr(34),chr(147),chr(150)
        );
        $specSymbTo = array_fill(0, count($specSymbFrom), '');
        $string = str_replace($specSymbFrom, $specSymbTo, $string);

        // Замена "сложных" букв
        $rus = array('Ё','Ж','Ц','Ч','Ш','Щ','Ъ','Ь','Ю','Я','ё','ж','ц','ч','ш','щ','ъ','ь','ю','я');
        $lat = array('yo','zh','tc','ch','sh','sh','','','yu','ya','yo','zh','tc','ch','sh','sh','','','yu','ya');
        $string = str_replace($rus, $lat, $string);

        // Замена "простых" букв
        $string = strtr($string,
            "АБВГДЕЗИЙКЛМНОПРСТУФХЫЭабвгдезийклмнопрстуфхыэ",
            "abvgdeziyklmnoprstufhieabvgdeziyklmnoprstufhie"
        );

        // Замена "пробелов"
        $string = trim($string);
        $spacesFrom = array(' - ','   ','  ',' ');
        $spacesTo = array_fill(0, count($spacesFrom), '_');
        $string = str_replace($spacesFrom, $spacesTo, $string);

        if ($addUniqueKey) {
            $string .= '_'. rand(100,999) . (time() % 1000);
        }

        return $string;
    }


    /**
     * ====================================================================================================
     * Склонение слов, употребляемых с числительными
     *
     * @param int $digit - числительное, с которым употребляется слово
     * @param array $declin_arr - массив слов для склонения
     *      Example (with word "объявление"):
     *      $declin_arr[0] = "ие";   // 1, 21, 31, ..., 101, 121, 1001, ... объявленИЕ
     *      $declin_arr[1] = "ия";   // 2, 3, 4, ..., 22, 23, 24, ..., 102, 103, 104, ... объявленИЯ
     *      $declin_arr[2] = "ий";   // 5, 6-10, 11-20, 25, 25, ... объявленИЙ
     * @return array - число и слова со склонением
     */
    function declinationWord ($digit, $declin_arr)
    {
        if (($digit%10 == 1) && ($digit%100 != 11)) {
            $declin = $declin_arr[0];
        } elseif(($digit%10 >= 2) && ($digit%10 <= 4) && (($digit%100 < 11) || ($digit%100 > 14))) {
            $declin = $declin_arr[1];
        } else {
            $declin = $declin_arr[2];
        }

        return "{$digit} {$declin}";
    }


    /**
     * Генерация случайной строки (hash)
     */
    function generateHash($length=32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $numChars = strlen($chars);
        $string = '';
        for ($i=0; $i<$length; $i++) {
            $string .= substr($chars, rand(0, $numChars-1), 1);
        }
        return $string;
    }
}
