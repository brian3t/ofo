<?php

/**
 * Class Product
 * @property string item_code
 * @property int manufacturer_name
 * @property array $db_record
 */
class Product
{

    public function __construct($db_record = null)
    {
        $this->db_record = $db_record;
    }

    /**
     * @return mixed|string null or image path "images/DENSO/abc.jpg"
     */
    public function default_img()
    {
        $path = "images/products/big/" . $this->db_record['manufacturer_name'] . '/' . $this->db_record['item_code'] . '.jpg';

        if (file_exists(dirname(__DIR__) . '/' . $path)){
            return $path;
        } else {
            return false;
        }

    }
}