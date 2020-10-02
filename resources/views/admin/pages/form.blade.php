<input type="hidden" value="{{ $model->id }}" name="id" />
<div class="form-group row">
    <label for="title" class="col-12 col-md-2 col-form-label">Title</label>
    <div class="col-12 col-md-10">
        <input id="title" type="text" class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}" required name="title" value="{{ old('title', $model->title) }}" autofocus>
        @if ($errors->has('title'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('title') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <label for="title" class="col-12 col-md-2 col-form-label">Slug</label>
    <div class="col-12 col-md-10">
        <input id="slug" type="text" class="form-control slug {{ $errors->has('slug') ? ' is-invalid' : '' }}" data-source="title" required name="slug" value="{{ old('slug', $model->slug) }}">
        <input type="checkbox" @if(old('auto_slug', true)) checked @endif class="slug-control icheck" data-control="slug" /> Auto?
               @if ($errors->has('slug'))
               <span class="invalid-feedback">
            <strong>{{ $errors->first('slug') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <label for="body" class="col-12 col-md-2 col-form-label">Body</label>
    <div class="col-12 col-md-10">
        <textarea data-id="{{ $model->id }}" data-section="{{ $model->getSection() }}" class="form-control editor {{ $errors->has('body') ? ' is-invalid' : '' }}" name="body">{{ old('body', $model->body) }}</textarea>
        @if ($errors->has('body'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('body') }}</strong>
        </span>
        @endif
    </div>
</div>



