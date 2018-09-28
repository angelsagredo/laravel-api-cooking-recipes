<?php

namespace App\Validators;

class Base64Validator {
    /**
     * Check if the file is in base64 image
     */
    public function validateBase64($attribute, $value, $parameters, $validator)
    {
        $explode = $this->explodeString($value);
        $allow = $this->allowedFormat();
        $format = $this->dataFormat($explode);
        // check file format
        if (!in_array($format, $allow)) {
            return false;
        }
        // check base64 format
        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
            return false;
        }
        return true;
    }
    /**
     * Check the data format
     */
    public function dataFormat($explode)
    {
        return str_replace(
            [
                'data:image/',
                ';',
                'base64',
            ],
            [
                '', '', '',
            ],
            $explode[0]
        );
    }
    /**
     * The allowed format in base 64 image
     */
    public function allowedFormat()
    {
        return ['gif', 'jpg', 'jpeg', 'png'];
    }
    /**
     * Explode base 64 image
     */
    public function explodeString($value)
    {
        return explode(',', $value);
    }
}