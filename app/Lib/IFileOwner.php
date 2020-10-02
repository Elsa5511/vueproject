<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Lib;

/**
 *
 * @author pramirez
 */
interface IFileOwner {

    public function getSection();

    public function getOwnerId();

    public function getFileName();

}
