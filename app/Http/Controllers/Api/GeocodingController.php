<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Api;

use \Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * Description of GeocodingController
 *
 * @author pablo
 */
class GeocodingController extends BaseController {

    public function reverse(Request $request) {
        $lat = (float) $request->get('lat', null);
        $lng = (float) $request->get('lng', null);
        $address = [
            'address' => null,
            'city' => null,
            'street' => null,
            'state' => null,
            'street_number' => null,
            'country' => null,
            'postal_code' => null,
        ];

        $response = $this->getResponseInstance();
        if (!$lat || !$lng) {
            $response->setHttpCode(400);
            $response->setStatusMessage('Coordenadas incorrectas!');
            return $response->render();
        }

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&sensor=true&key=' . config('geocoding.google_maps_key');
        $json = @file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        if ($status == "OK") {
            $result = $data->results[0];
            $address['address'] = $result->formatted_address;
            //Get address from json data
            for ($j = 0; $j < count($result->address_components); $j++) {
                $cn = array($result->address_components[$j]->types[0]);
                if (in_array("locality", $cn)) {
                    $address['city'] = $result->address_components[$j]->long_name;
                }
                if (in_array("route", $cn)) {
                    $address['street'] = $result->address_components[$j]->long_name;
                }
                if (in_array("street_number", $cn)) {
                    $address['street_number'] = $result->address_components[$j]->long_name;
                }
                if (in_array("administrative_area_level_1", $cn)) {
                    $address['state'] = $result->address_components[$j]->long_name;
                }
                if (in_array("country", $cn)) {
                    $address['country'] = $result->address_components[$j]->long_name;
                }
                if (in_array("postal_code", $cn)) {
                    $address['postal_code'] = $result->address_components[$j]->long_name;
                }
            }
        } else {
            $response->setHttpCode(404);
            $response->setStatusMessage('Dirección no encontrada!');
            return $response->render();
        }
        //Print city 


        $response->setPayload($address);
        return $response->render();
    }

    public function search(Request $request) {
        $query = $request->get('q', null);
        $response = $this->getResponseInstance();
        if (!$query) {
            $response->setHttpCode(400);
            $response->setStatusMessage('No hay nada que buscar!');
            return $response->render();
        }

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($query) . '&key=' . config('geocoding.google_maps_key');
        $json = @file_get_contents($url);
        $data = json_decode($json);
        $results = [];
        $status = $data->status;
        if ($status == "OK") {
            foreach ($data->results as $r) {

                $result = ['address' => $r->formatted_address];
                $viewport = [
                    'southwest' => ['latitude' => $r->geometry->viewport->southwest->lat, 'longitude' => $r->geometry->viewport->southwest->lng],
                    'northeast' => ['latitude' => $r->geometry->viewport->northeast->lat, 'longitude' => $r->geometry->viewport->northeast->lng],
                ];
                $result['viewport'] = $viewport;

                $result['location'] = ['latitude' => $r->geometry->location->lat, 'longitude' => $r->geometry->location->lng];

                $results[] = $result;
            }
        } else {
            $response->setHttpCode(404);
            $response->setStatusMessage('Dirección no encontrada!');
            return $response->render();
        }

        $response->setPayload($results);
        return $response->render();
    }

    public function staticMap(Request $request) {
        $size = $request->get('size', '600x600');
        $maptype = $request->get('maptype', 'roadmap');
        $markers = $request->get('markers', null);
        $zoom = $request->get('zoom', '16');

        $mapUrl = 'https://maps.googleapis.com/maps/api/staticmap?size=' . $size . '&maptype=' . $maptype . '&markers=' . $markers . '&zoom=' . $zoom . '&key=' . config('geocoding.google_maps_key');
        $fileName = md5($mapUrl) . '.png';

        $dir_cache = public_path('cache/images/staticmaps');
        $file = File::makeDirectory($dir_cache, $mode = 0777, true, true);
        //Storage::makeCacheDir("$sectionName/$id/$thumb/");
        // Si existe la imagen redirecciono a la version cacheada
        $img_cache = $dir_cache . DIRECTORY_SEPARATOR . $fileName;
        if (!isset($_GET['no-cache']) && is_file($img_cache)) {
            header("HTTP/1.1 302 Found");
            header("location: " . url("cache/images/staticmaps/", $fileName));
            exit();
        }

        // Defino la cabecera de la imagen
        header('Content-Type: image/png');

        $file = fopen($img_cache, 'wb');
        // Create File
        fwrite($file, @file_get_contents($mapUrl));
        fclose($file);
        echo @file_get_contents($img_cache);
    }

}
