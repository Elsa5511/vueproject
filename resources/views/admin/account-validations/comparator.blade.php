<div class="row">
    <div class="col-sm-10">
        <div class="box box-warning box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Comparación de datos</h3>

                <div class="box-tools pull-right">

                </div>
                <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="my-20px clearfix">
                    <div class="badge pull-right status status-{{$updateRequest->status}}">{{ trans('fields.update-requests.' . $updateRequest->status) }}</div>
                </div>
                <table class="table table-bordered table-striped comparator">
                    <colgroup>
                        <col style="width: 20%" />
                        <col style="width: 40%" />
                        <col style="width: 40%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Datos actuales</th>
                            <th>Nuevos datos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">{{ trans('fields.first_name') }}</th>
                            <td class="old-data">{{ $currentProfile->first_name or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->first_name or 'Sin completar'}}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.last_name') }}</th>
                            <td class="old-data">{{ $currentProfile->last_name or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->last_name or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.birthdate') }}</th>
                            <td class="old-data">{{ $currentProfile->birthdate or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->birthdate or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.cuit') }}</th>
                            <td class="old-data">{{ $currentProfile->cuit or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->cuit or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.nationality') }}</th>
                            <td class="old-data">{{ $currentProfile->nationality or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->nationality or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.address_street') }}</th>
                            <td class="old-data">{{ $currentProfile->address_street or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->address_street or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.address_number') }}</th>
                            <td class="old-data">{{ $currentProfile->address_number or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->address_number or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.address_floor') }}</th>
                            <td class="old-data">{{ $currentProfile->address_floor or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->address_floor or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.address_apartament') }}</th>
                            <td class="old-data">{{ $currentProfile->address_apartament or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->address_apartament or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.address_city') }}</th>
                            <td class="old-data">{{ $currentProfile->address_city or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->address_city or 'Sin completar' }}</td>
                        </tr>   
                        <tr>
                            <th scope="row">{{ trans('fields.address_state') }}</th>
                            <td class="old-data">{{ $currentProfile->address_state or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->address_state or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.zip_code') }}</th>
                            <td class="old-data">{{ $currentProfile->zip_code or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->zip_code or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.phone') }}</th>
                            <td class="old-data">{{ $currentProfile->phone or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->phone or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.cellphone') }}</th>
                            <td class="old-data">{{ $currentProfile->cellphone or 'Sin completar' }}</td>
                            <td class="new-data">{{ $newData->cellphone or 'Sin completar' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ trans('fields.identity_images') }}</th>
                            <td class="old-data">
                                <a href="{{ $currentProfile->getIdentityImageUrl('01', '0x0') }}" class="imageOpener">
                                    <img src="{{ $currentProfile->getIdentityImageUrl('01', '150x150') }}" />
                                </a>
                                <a href="{{ $currentProfile->getIdentityImageUrl('02', '0x0') }}" class="imageOpener">
                                    <img src="{{ $currentProfile->getIdentityImageUrl('02', '150x150') }}" />
                                </a>
                            </td>
                            <td class="new-data">
                                <a href="{{ $newData->getIdentityImageUrl('01', '0x0') }}" class="imageOpener">
                                    <img src="{{ $newData->getIdentityImageUrl('01', '150x150') }}" />
                                </a>
                                <a href="{{ $newData->getIdentityImageUrl('02', '0x0') }}" class="imageOpener">
                                    <img src="{{ $newData->getIdentityImageUrl('02', '150x150') }}" />
                                </a>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <a href='{{ route('admin.account-validations.index') }}' class="btn btn-default pull-left"><i class='fa fa-reply'></i> {{ trans('generics.cancel') }}</a>

                @if($updateRequest->status=='pending')
                @can('account-validations.accept', $updateRequest)
                <form class="" action='{{ route('admin.account-validations.save', ['id'=>$updateRequest->id]) }}' method="post">
                    {{ method_field('DELETE') }}
                    @csrf
                    <button type="submit" class="btn btn-danger pull-right btn-confirm" data-text='{{trans('generics.confirm-reject-changes')}}'><i class="fa fa-ban"></i> {{ trans('generics.reject') }}</button>
                </form>
                @endcan
                @can('account-validations.reject', $updateRequest)
                <form class="" action='{{ route('admin.account-validations.save', ['id'=>$updateRequest->id]) }}' method="post">
                    {{ method_field('PUT') }}
                    @csrf
                    <button type="submit" class="btn btn-success pull-right btn-confirm" data-text='{{trans('generics.confirm-accept-changes')}}' style="margin-right: 10px"><i class="fa fa-check"></i> {{ trans('generics.accept-changes') }}</button>
                </form>
                @endcan
                @endif
            </div>
        </div>
    </div>

</div>

@include('admin.components.image-popup', ['title'=>trans('fields.identity_images')])


