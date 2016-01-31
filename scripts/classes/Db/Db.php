<?php

/**
 * Класс для работы с БД
 *
 * @category
 * @package    _Db
 * @version    1.0
 * @since      File available since Release 1.0
 */
class Db_Db extends mysqli
{

    /**
     * Установка соединения с БД
     */
    public function __construct($host, $user, $pass, $db, $port)
    {
        parent::__construct($host, $user, $pass, $db, $port);

        if ($this->connect_errno) {
            throw new Exception('Connect Error (' . $this->connect_errno . ') ' . $this->connect_error);
        } else {
            parent::query('SET NAMES cp1251');
        }
    }

    /**
     * Выполнение запроса к БД (приватный метод)
     */
    private function _query($query)
    {
        return parent::query($query);
    }

    /**
     * Выполнение запроса к БД (публичный метод)
     */
    public function setQuery($query)
    {
        return $this->_query($query);
    }

    /**
     * Выполнение запроса к БД с возвратом количества затронутых строк (публичный метод)
     */
    public function setQueryAffectedRows($query)
    {
        $this->_query($query);

        return $this->affected_rows;
    }

    /**
     * Выполнение запроса к БД с возвратом сгенерированного ID (публичный метод)
     */
    public function setQueryLastInsertId($query)
    {
        $this->_query($query);

        return $this->insert_id;
    }

    /**
     * Получение ID, сгенерированного при последнем INSERT-запросе.
     */
    public function getLastInsertId()
    {
        return $this->insert_id;
    }

    /**
     * Получение нумерованного массива данных результата выполнения запроса к БД
     */
    public function getArray($query)
    {
        $data = array();

        if ($result = $this->_query($query)) {

            while ($row = $result->fetch_row()) {
                array_push($data, $row);
            }

            $result->free();
        }

        return $data;
    }

    /**
     * Получение ассоциативного массива данных результата выполнения запроса к БД
     */
    public function getAssoc($query)
    {
        $data = array();

        if ($result = $this->_query($query)) {

            while ($row = $result->fetch_assoc()) {
                array_push($data, $row);
            }

            $result->free();
        }

        return $data;
    }

    /**
     * Получение первой записи нумерованного массива данных результата выполнения запроса к БД
     */
    public function getArrayFirst($query)
    {
        if ($result = $this->_query($query)) {

            return $result->fetch_row();

            $result->free();
        }
    }

    /**
     * Получение первой записи ассоциативного массива данных результата выполнения запроса к БД
     *
     * @param string $query
     * @return array
     */
    public function getAssocFirst($query)
    {
        if ($result = $this->_query($query)) {

            return $result->fetch_assoc();

            $result->free();
        }
    }
}
