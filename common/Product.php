<?php
require("const.php");

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
        if (!isset($this->db_record['manufacturer_name']) && isset($this->db_record['manufacturer_id'])){
            $this->db_record['manufacturer_name'] = MANUFACTURER[$this->db_record['manufacturer_id']];
        }
    }

    /**
     * @return mixed|string null or image path "images/DENSO/abc.jpg"
     */
    public function default_img()
    {
        $path = "images/products/big/" . strtolower($this->db_record['manufacturer_name']) . '/' . $this->db_record['item_code'] . '.jpg';

        if (file_exists(dirname(__DIR__) . '/' . $path)){
            return $path;
        } else {
            return 'images/no_image.gif';
        }

    }
}