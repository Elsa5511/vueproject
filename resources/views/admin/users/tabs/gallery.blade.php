<form action="/" method="post" id="dropzoneForm" class="dropzone" enctype="multipart/form-data">

</form>

@push('scripts')
<script>
    var user_id='{{$model->id}}';
    Dropzone.autoDiscover = false;
    var files = {!! json_encode($gallery->resources) !!};

    var ready=false;


    var mydropzone = new Dropzone("#dropzoneForm", {
        url: "{{ route('admin.users.gallery-store', ['id'=>$model->id]) }}",
        uploadMultiple: true,
        paramName: 'image',
        addRemoveLinks: true,
    });

    function openGallery(index=0) {
        window.open(files[index].url);
    }
    
    /*dropzone.on("removedfile", function(file) {
        var server_file = $(file.previewTemplate).children('.server_file').text();
        alert(server_file);
        // Do a post request and pass this path and use server-side language to delete the file
        $.post("delete.php", { file_to_be_deleted: server_file } );
    });*/

    function putImagesFromServer() {
        var ready=false; 
        mydropzone.removeAllFiles(true);
        
       mydropzone.on("removedfile", function (file) {
            var server_file = file.id;
            swal({
                title: 'Está seguro?',
                text: 'Está seguro que quiere eliminar esta imagen?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar!',
                cancelButtonText: 'No, cancelar',
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        'url': '/admin/users/delete-image/' + file.gallery_id,
                        'method': 'DELETE',
                        'data': {
                            'delete': [file.id]
                        }
                    });
                } else {
                    location.reload();
                }
            });
        });

        
        var index = 0;
        files.map(r => {
            const file = {name: r.url, size: r.size, gallery_id: r.owner_id, id: r.id, previewElement: null, index: index};
            
            mydropzone.emit('addedfile', file);
            mydropzone.emit('thumbnail', file, r.medium_image_url);
            mydropzone.emit('complete', file);

            //launch gallery
            file.previewElement.onclick = (f) => {
                this.openGallery(file.index);
            };
            mydropzone.files.push(file);
            //launch gallery
            file.previewElement.onclick = (f) => {
                openGallery(file.index);
            };
            index++;
        });
        ready=true;
    }

    putImagesFromServer();

</script>
@endpush

