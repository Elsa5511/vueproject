<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Page extends Model {

    protected $guarded = [];

    use ImageOwnerModel;

    public static function getValidator($data = [], $instance = null) {

        $rules = [
            'title' => 'required|string|unique:pages,title,' . (($instance) ? $instance->id : ''),
            'slug' => 'required|ascii_only|alpha_dash|unique:pages,slug,' . (($instance) ? $instance->id : ''),
                //'positions' => 'required|string',
        ];
        $validator = Validator::make($data, $rules);
        return $validator;
    }

    public function getFileName() {
        return $this->image ? $this->image : 'no-image.jpg';
    }

    public function getOwnerId() {
        return $this->id;
    }

    public function getSection() {
        return 'pages';
    }

}
