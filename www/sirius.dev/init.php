<?php

require  './vendor/autoload.php';

use Krugozor\Database\Mysql\Mysql as Mysql;


final class Init {

    /**
     * Стандартный объект соединения сервером MySQL.
     * @var mysqli
     */
    private $db;

    /**
     * Массив значений критериев
     * @var array
     */
    private $result = [
        'normal',
        'illegal',
        'failed',
        'success'
    ];

    /**
     * Конструктор класса
     */
    function __construct()
    {
        // Создаем поключение к БД
        $this->db = Mysql::create('localhost', 'testuser', '123456')
            ->setDatabaseName('test')
            ->setCharset('utf8');

        // Создаем таблицу в БД
        $this->create();

        // Заполняем таблицу данными
        $this->fill();
    }

    /**
     * Создание таблицы test
     */
    private function create()
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS test(
                `id` int(11) unsigned NOT NULL primary key auto_increment,
                `script_name` varchar(25) NOT NULL,
                `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `sort_index` int(3) NOT NULL,
                `result` enum("' . implode('","', $this->result). '") NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    /**
     * Заполнение таблицы test случайными данными
     */
    private function fill()
    {
        // заполняем 50 новых строк
        for($i = 0; $i < 50; $i++) {
            // получаем название script_name
            $scriptName = substr(md5(rand()), 0, 25);

            // выбираем случайное значение из массива result
            $random = rand(0, (count($this->result) - 1));

            // получаем случайное значение индекса
            $randomIndex = rand(1, 999);

            // формируем массив данных
            $data = [
                'script_name' => $scriptName,
                'sort_index' => $randomIndex,
                'result' => $this->result[$random]
            ];

            // вставляем запись в таблицу
            $this->db->query('INSERT INTO `test` SET ?As', $data);
        }
    }

    /**
     * Получение данных из таблицы test
     * @param string $result - значение критерия
     * @return array
     */
    public function get($result)
    {
        // Получаем данные из таблицы test
        $res = $this->db->query('SELECT * FROM `test` WHERE `result` = \'?s\'', $result);

        $arrData = [];
        while (($data = $res->fetch_assoc()) !== null) {
            $arrData[] = $data;
        }

        return $arrData;
    }
}

$test = new Init;

// выводит все данные из таблицы test, по критерию success
var_dump($test->get('success'));

// выводит все данные из таблицы test, по критерию normal
var_dump($test->get('normal'));
