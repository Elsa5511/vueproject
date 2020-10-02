<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Lib;

/**
 * Description of GeoLocation
 *
 * @author pablo
 */
class GeoLocation {

    public static function distanceOfPoints(\Geometry $point1, \Geometry $point2, $unit = 'MT') {
        return self::distanceOfLatLng($point1->getY(), $point1->getX(), $point2->getY(), $point2->getX(), $unit);
    }

    public static function distanceOfLatLng($lat1, $lon1, $lat2, $lon2, $unit = 'MT') {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "MT") {
            return ($miles * 1.609344) * 1000;
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public static function contains(\Geometry $figure, \Geometry $container) {
        return $container->contains($figure);
    }

}
