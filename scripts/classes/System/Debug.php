<?php

/**
 * Debug
 */
class Debug
{
    /**
     * Выводит на страницу результат var_dump() в форматированном виде
     */
    public function printDump($value)
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
    }

    /**
     * Выводит на страницу массив в форматированном виде
     */
    public function printArray($value)
    {
        echo '<pre>';
        print_r($value);
        echo '</pre>';
    }

}