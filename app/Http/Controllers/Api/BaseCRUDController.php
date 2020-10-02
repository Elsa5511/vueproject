<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

/**
 * Description of BaseCRUDController
 *
 * @author pramirez
 */
class BaseCRUDController extends BaseController {

    protected $modelClass = null;
    protected $resourceKey = null;
    protected $defaultLimit = 50;

    public function __construct($modelClass, $resourceKey) {
        $this->modelClass = $modelClass;
        $this->resourceKey = $resourceKey;
    }

    protected function getMessageKey($subKey) {
        return 'messages.' . $this->resourceKey . '.' . $subKey;
    }

    protected function getPKName() {
        return 'id';
    }

    protected function getFieldsFromRequest(Request $request, $model, $response) {
        return $request->all();
    }

    protected function parseSaveRequest(Request $request, $model, $response) {
        $fields = $this->getFieldsFromRequest($request, $model, $response);

        $validator = $model::getValidator('CREATE', $fields, $model);
        $response->setValidator($validator);
        if ($validator->fails()) {
            throw new ValidationException('Validation fails!');
        }
        foreach ($fields as $key => $value) {
            if ($key != $this->getPKName() && ($value != 'null' || $value == 'true' || $value == 'false')) {
                $model->{$key} = $value;
            }
        }

        return $model;
    }

    /**
     * 
     * @param Request $request
     * @param mixed $model
     * @param \App\Lib\Api\JSONResponse $response
     * @return type
     */
    protected function parseUpdateRequest(Request $request, $model, $response) {

        $fields = $this->getFieldsFromRequest($request, $model, $response);

        $validator = $model::getValidator('UPDATE', $fields, $model);
        $response->setValidator($validator);
        if ($validator->fails()) {
            throw new ValidationException('Validation fails!');
        }
        foreach ($fields as $key => $value) {
            if ($key != $this->getPKName() && ($value != 'null' || $value == 'true' || $value == 'false')) {
                $model->{$key} = $value;
            }
        }

        return $model;
    }

    /**
     * 
     * @param Request $request
     * @param type $query
     * @return type
     */
    protected function changeQuery(Request $request, $query) {
        return $query;
    }

    protected function getDefaultColumns(Request $request) {
        return '[]';
    }

    protected function getColumns(Request $request) {
        $columns = json_decode($request->get('columns', $this->getDefaultColumns($request)));

        $pk = $this->getPKName();
        if (!in_array($pk, $columns)) {
            array_unshift($columns, $pk);
        }

        if (in_array('*', $columns)) {
            $columns = [];
        }

        return $columns;
    }

    protected function getFilters(Request $request) {
        $filters = json_decode($request->get('filter', '[]'), true);

        return $filters;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $per_page = $request->get('limit', $this->defaultLimit);
        if ($per_page == -1) {
            $per_page = 10000;
        }


        $columns = $this->getColumns($request);
        $filters = $this->getFilters($request);
        $search = $request->get("search", null);

        if (!count($columns)) {
            $columns = '*';
        }
        $sort = json_decode($request->get('sort'), true);
        $query = $this->modelClass::select($columns);

        if (($filters)) {
            foreach ($filters as $column => $value) {
                if ($value != '') {
                    $query->where($column, $value);
                }
            }
        }


        if ($sort) {
            $sf = [];
            foreach ($sort as $s) {
                $query->orderBy($s['key'], $s['dir']);
            }
        }

        if ($search) {
            foreach ($columns as $col) {
                $query->orWhere($col, 'LIKE', "%$search%");
            }
        }


        $query = $this->changeQuery($request, $query);
        $results = $query->paginate($per_page);   
        
        
        //return new \App\Http\Resources\GenericResourceCollection($results); 
        
        $data= $this->transformListResult($request, $results);
        $response = $this->getResponseInstance();
        $response->setPayload($data);
        return $this->renderResponse();
    }

    protected function transformSingleResult($request, $model) {
        return new \App\Http\Resources\GenericResource($request);
    }

    protected function transformListResult($request, $list) {
        $list->data = \App\Http\Resources\GenericResource::collection($list);
        return $list;
    }

    protected function getNewInstance() {
        return new $this->modelClass();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $response = $this->getResponseInstance();
        $response->setPayload($this->transformSingleResult($request, $this->getNewInstance()));
        return $this->renderResponse();
    }

    protected function beforeSave(Request$request, &$model, $response) {
        
    }

    protected function afterSave(Request $request, &$model, $response) {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $response = $this->getResponseInstance();
        $text = 'Unknown error';
        DB::beginTransaction();
        try {
            $model = $this->getNewInstance();
            $model = $this->parseSaveRequest($request, $model, $response);
            $this->beforeSave($request, $model, $response);
            $model->save();
            $this->afterSave($request, $model, $response);
            $response->setPayload($this->transformSingleResult($request, $model));
            $text = trans($this->getMessageKey('update-ok'));
            $response->addMessage(new \App\Lib\Message($text));
            DB::commit();
        } catch (ValidationException $vex) {
            DB::rollback();
            $text = trans($this->getMessageKey('save-error'), ['error' => $vex->getMessage()]);
            $response->setHttpCode(400);
        } catch (\Exception $ex) {
            DB::rollback();
            $text = trans($this->getMessageKey('save-error'), ['error' => $ex->getMessage()]);
            $response->setHttpCode(500);
        }
        $response->setStatusMessage($text);
        return $response->render();
    }

    /**
     * Display the specified resource.
     *
     * @param  int id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $model = $this->modelClass::findOrFail($id);
        $response = $this->getResponseInstance();
        $response->setPayload($this->transformSingleResult($request, $model));
        return $this->renderResponse();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $model
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
    }

    protected function beforeUpdate(Request $request, &$model, $response) {
        
    }

    protected function afterUpdate(Request $request, &$model, $original, $response) {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $model
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $response = $this->getResponseInstance();
        DB::beginTransaction();
        $text = 'Unknown error';
        try {
            $model = $this->modelClass::findOrFail($id);
            $original = $this->modelClass::findOrFail($id);
            $model = $this->parseUpdateRequest($request, $model, $response);
            $this->beforeUpdate($request, $model, $response);
            $model->save();
            $this->afterUpdate($request, $model, $original, $response);
            $response->setPayload($this->transformSingleResult($request, $model));
            $text = trans($this->getMessageKey('update-ok'));
            $response->addMessage(new \App\Lib\Message($text));
            DB::commit();
        } catch (ValidationException $vex) {
            DB::rollback();
            $text = trans($this->getMessageKey('update-error'), ['error' => $vex->getMessage()]);
            $response->setHttpCode(400);
        } catch (\Exception $ex) {
            DB::rollback();
            $text = trans($this->getMessageKey('update-error'), ['error' => $ex->getMessage()]);
            $response->setHttpCode(500);
        }
        $response->setStatusMessage($text);
        return $response->render();
    }

    protected function beforeDelete(Request $request, &$model, $response) {
        
    }

    protected function afterDelete(Request $request, &$model, $response) {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
