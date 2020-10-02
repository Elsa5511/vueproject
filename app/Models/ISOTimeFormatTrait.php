<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

/**
 * Description of ISOTimeFormatTrait
 *
 * @author pablo
 */
trait ISOTimeFormatTrait {

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('Y/m/d H:i:s');
    }

}
