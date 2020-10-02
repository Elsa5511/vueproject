<div class="modal fade" role='dialog' id="modalShowImage">
    <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">

        <!-- Modal content-->
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">{{ $title or 'No title' }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
                <a href="" target="_blank" class="url">
                    <img class="img" src="" width="100%" />
                </a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('generics.close') }}</button>
            </div>
        </div>

    </div>
</div>