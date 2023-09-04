<?php

class Camlissession
{
    private static $CI;
    public function __construct()
    {
        static::$CI = & get_instance();
    }

    /**
     * Get User assigned laboratory
     * @return mixed
     */
    public static function getUserLaboratory() {
        return (array)static::$CI->session->userdata('user_laboratories');
    }

    /**
     * Get Current Laboratory Info
     * @param $key
     * @return mixed
     */
    public static function getLabSession($key = NULL) {
        $laboratory = static::$CI->session->userdata('laboratory');

        if (!empty($key)) {
            return isset($laboratory->$key) ? $laboratory->$key : NULL;
        }

        return $laboratory;
    }
}