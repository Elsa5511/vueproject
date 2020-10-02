<form class="form form-horizontal form-enterkey" method="POST" action="{{ route('admin.providers.identity-store', ['id'=>$model->id, 'action'=>'approve']) }}" autocomplete="off" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    @csrf    
    @if($verification)
    @php
    $images=$verification->getIdImages();
    @endphp

    <div class="form-group has-feedback  @if($errors->has('status')) has-error @endif">
        <div class="col-sm-2"></div>

        <div class="col-sm-10">

            <div class="clearfix my-20px">
                <div class="badge pull-right status status-{{ strtolower($verification->status) }}">{{ trans('fields.verification-statuses.' . $verification->status) }}</div>
            </div>

        </div>
    </div>


    <div class="form-group">
        <label for="image" class="col-sm-2 control-label">Numero de documento: </label>
        <div class="col-sm-10">
            <input name="identity_id" class="form-control col-12" type="text"  value="{{ old('identity_id', $verification->identity_id) }}" />
        </div>
    </div>

    <div class="form-group">
        <label for="inputCountry" class="col-sm-2 control-label">Pais: </label>
        <div class="col-sm-10">
            @php
            $countries=\App\Models\Country::all();
            @endphp
            <select class="select2 form-control" name="country_id" id="inputCountry">
                @foreach($countries as $country)
                <option value="{{ $country->id }}"  @if(old('country_id', $verification->country_id)==$country->id) selected @endif> {{ $country->name }}</option>
                @endforeach
            </select>
            @if ($errors->has('country_id'))
            <span class="help-block">
                {{ $errors->first('country_id') }}
            </span>
            @endif
        </div>
    </div>



    <div class="form-group has-feedback">
        <label for="image" class="col-sm-2 control-label">Fotos: </label>
        <div class="col-sm-10">
            @if(count($images))
            <div class="col-sm-6">
                <div>Frente</div>
                <figure class="imageFileField display" data-href="{{  $verification->getIdImageUrl($images['front']) }}">

                    <img id="image-01" class="img showFile" src="{{$verification->getIdImageUrl($images['front'], '200x200')}}" width="250" height="250" />
                    <input type="file" class="" name="id_images[front]" data-width="250" data-height="250" />

                </figure>  
                <div>
                    <a href="{{  $verification->getIdImageUrl($images['front']) }}" target="_blank">Ver</a>

                </div>
            </div>
            <div class="col-sm-6">
                <div>Atras</div>
                <figure class="imageFileField display" data-href="{{  $verification->getIdImageUrl($images['front']) }}">

                    <img id="image-01" class="img showFile" src="{{$verification->getIdImageUrl($images['back'], '200x200')}}" width="250" height="250" />
                    <input type="file" class="" name="id_images[back]" data-width="250" data-height="250" />

                </figure>
                 <div>
                    <a href="{{  $verification->getIdImageUrl($images['back']) }}" target="_blank">Ver</a>

                </div>
            </div>       
            @else

            <div class="col-sm-6">
                <div>Frente</div>
                <figure class="imageFileField display" data-href="">

                    <img id="image-01" class="img showFile" src="" width="250" height="250" />
                    <input type="file" class="" name="id_images[front]" data-width="250" data-height="250" />

                </figure>
            </div>
            <div class="col-sm-6">
                <div>Atras</div>
                <figure class="imageFileField display" data-href="">

                    <img id="image-01" class="img showFile" src="" width="250" height="250" />
                    <input type="file" class="" name="id_images[back]" data-width="250" data-height="250" />

                </figure>
                @endif
            </div>
        </div>
    </div>


    <div class="form-actions">
        <div class="col-sm-10 pull-right">
            <a href='{{ route('admin.providers.index') }}' style="margin-right: 10px;" class="btn btn-default pull-left"><i class='fa fa-reply'></i> {{ trans('generics.cancel') }}</a>
            @if($verification->exists)
            @can('user.update', $model)
            <button type="submit" class="btn btn-success pull-left"><i class='fa fa-check'></i> ACEPTAR</button>
            <a href="{{ route('admin.providers.identity', ['id'=>$model->id]) . '?action=reject' }}"  class="btn btn-danger pull-right"><i class='fa fa-times'></i> RECHAZAR</a>
            @endcan
            @else
            <button type="submit" class="btn btn-success pull-left">Guardar</button>
            @endif
        </div>
    </div>
    @else
    <p class="alert alert-warning">Este usuario aún no ha solicitado verificación de identidad.</p>
    @endif

</form>
