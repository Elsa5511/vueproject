var DTLang = {
    "decimal": "",
    "emptyTable": "No hay datos para mostrar",
    "info": "Mostrando _START_ a _END_ de _TOTAL_ de registros",
    "infoEmpty": "",
    "infoFiltered": "(Filtrado de _MAX_ total de registros)",
    "infoPostFix": "",
    "thousands": ",",
    "lengthMenu": "Por página _MENU_",
    "loadingRecords": "Cargando...",
    "processing": "Procesando...",
    "search": "Buscar:",
    "zeroRecords": "No se encontraron registros",
    "processing": "<span class='fa fa-refresh fa-spin'></span>",
    "paginate": {
        "first": "Primera",
        "last": "Ultima",
        "next": "Sig",
        "previous": "Ant"
    },
    "aria": {
        "sortAscending": ": activar para ordenar ascendente",
        "sortDescending": ": activar para ordenar descendente"
    },
    buttons: {
        'export': 'Exportar',
        'copy': 'Copiar',
        'print': 'Imprimir',
        'reset': 'Limpiar',
        'reload': 'Refrescar'
    }
}





$.extend(true, $.fn.dataTable.defaults, {
//language: DTLang,
    dom: '<"html5buttons"B>lTfrtip',
    autoWidth: false,
    stateSave: true,
    stateSaveCallback: function (settings, data) {
        sessionStorage.setItem('DataTables_' + settings.sInstance, JSON.stringify(data))
    },
    stateLoadCallback: function (settings) {
        return JSON.parse(sessionStorage.getItem('DataTables_' + settings.sInstance))
    }
});
$(document).on("keypress", ".form-enterkey :input:not(textarea):not([type=submit])", function (event) {
    if (event.keyCode == 13) {
        var fields = $(this).closest("form").find("input, textarea, button");
        var index = fields.index(this) + 1;
        var field;
        fields.eq(
                fields.length <= index
                ? 0
                : index
                ).focus();
        event.preventDefault();
    }
});
$(document).ajaxStart(function () {
    if (typeof Pace !== 'undefined') {
        Pace.restart()
    }

})



$(document).ready(function () {

    if (Echo) {
        var conf = {
            broadcaster: 'socket.io',
            host: ECHO_HOST,
        };
        if (USER_TOKEN) {
            conf.auth = {
                headers: {
                    Authorization: 'Bearer ' + USER_TOKEN
                }
            };
        }
        console.log(conf);  
        window.EchoClient = new Echo(conf);

    }

    activateImageFileFields();


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('form').submit(function (e) {
        $('.editor').each(function (e) {
            var markupStr = $(this).summernote('code');
            $(this).val(markupStr);
            $(this).summernote('triggerEvent', 'change');
        });
    });
    /*$('.editor').summernote({
        height: 200,
        callbacks: {
            onImageUpload: function (files, editor, welEditable) {

                var id = $(this).data('id');
                var section = $(this).data('section');
                sendFile(section, id, files[0], $(this), welEditable);
            }
        }
    });*/
    function sendFile(section, ownerid, file, editor, welEditable) {
        data = new FormData();
        data.append("file", file);
        data.append("owner", ownerid);
        data.append("section", section);
        $.ajax({
            data: data,
            type: "POST",
            url: "/admin/uploader",
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                editor.summernote("insertImage", res.url);
            }
        });
    }

    $('.s-chk').each(function (i) {
        var switchery = new Switchery($(this)[0]);
    });
    $('[data-toggle="tooltip"]').tooltip();
    $('.date').datepicker({
        language: 'es',
        dateFormat: 'yyyy-mm-dd',
        locale: {
            format: 'yyyy-mm-dd'
        }
    });
    $('.date-range').datepicker({
        language: 'en',
        format: 'mm/dd/yyyy  hh:mm A',
        timeFormat: 'hh:ii AA',
        dateFormat: 'mm/dd/yyyy',
        timepicker: true,
        locale: {
            format: 'MM/DD/YYYY hh:mm A'
        }
    });
    //$('.select2').select2({});

    /*$('.select2.tags').select2({
     tags: true,
     createTag: function (params) {
     
     return {
     id: 'new_' + params.term,
     text: params.term,
     newOption: true
     }
     }
     });*/
    $('.slug').each(function (i) {
        var source = $(this).data('source');
        var sourceField = $('#' + source);
        var _that = $(this);
        sourceField.keyup(function () {
            if (!_that.data('disabled')) {
                _that.val(slugify($(this).val()));
            }
        });
    });
    $('.slug-control').each(function (i) {
        function control(el) {
            var controlled = $('#' + el.data('control'));
            if (el.is(':checked')) {
                controlled.attr('readonly', true);
                controlled.data('disabled', false);
            } else {
                controlled.removeAttr('readonly');
                controlled.data('disabled', true);
            }
        }

        $(this).change(function () {
            control($(this));
        });
        var controlled = $('#' + $(this).data('control'));
        if (controlled.val() === '') {
            control($(this));
        } else {
            controlled.attr('readonly', false);
            controlled.data('disabled', true);
            $(this).removeAttr("checked");
        }

    });
    $('.show-hide-control').each(function (e) {

        var _that = $(this);
        function control(el) {
            var show_true_el = $('#' + el.data('true-show'));
            var show_false_el = $('#' + el.data('false-show'));
            if (el.is(':checked')) {
                show_true_el.addClass('show');
                show_false_el.removeClass('show');
            } else {
                show_false_el.addClass('show');
                show_true_el.removeClass('show');
            }
        }

        $(this).change(function (e) {
            control($(this));
        });
        control($(this));
    })

    $('body').on('click', '.btn-confirm', function (e) {
        e.preventDefault();
        var title = $(this).data('title') || 'Do you want to do this?';
        var text = $(this).data('text');
        var confirm = $(this).data('confirm-text') || "Ok";
        var cancel = $(this).data('cancel-text') || "Cancel";
        var el = $(this);
        swal({
            title: title,
            text: text,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirm,
            cancelButtonText: cancel,
        }).then((result) => {
            if (result.value) {
                $('body').off('click', el);
            }
        })





        //$(this).unbind('click').click();
    });
    $('body').on('click', '.dataTable .btn-delete', function (e) {
        e.preventDefault();
        var title = $(this).data('title') || 'Delete?';
        var text = $(this).data('text') || 'Do you want to delete this item?';
        var confirm = $(this).data('confirm-text') || "Ok";
        var cancel = $(this).data('cancel-text') || "Cancel";
        var el = $(this);
        swal({
            title: title,
            text: text,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirm,
            cancelButtonText: cancel,
        }).then((result) => {
            if (result.value) {
                var url = $(this).data('action');
                var table = $(this).parents('.dataTable').first().DataTable();
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    data: {method: '_DELETE', submit: true}
                }).always(function (data) {
                    table.draw(true);
                });
            }
        })


        //$('.select2').select2();


        //$(this).unbind('click').click();
    });
    $('.imageOpener').click(function (e) {
        e.preventDefault();
        $('#modalShowImage').find('.modal-body .img').attr('src', $(this).attr('href'));
        $('#modalShowImage').find('.modal-body .url').attr('href', $(this).attr('href'));
        $('#modalShowImage').modal('show');
    })


});

function activateImageFileFields() {

    $('.imageFileField').each(function () {
        var _this = $(this);
        if (_this.hasClass('ready') || _this.hasClass('skip')) {
            return;
        }
        _this.addClass('ready');
        var fileField = $('input[type="file"]', _this);
        if (fileField.prop('disabled')) {
            fileField.width('1px');
            _this.addClass('disabled');
            return;
        }

        $('.deletebutton', _this).click(function (e) {
            e.preventDefault();
            e.stopPropagation();
        });

        var uploadIcon = $("<span class='uploadIcon'><i class='fa fa-camera'></i></span>");
        uploadIcon.css({'line-height': _this.outerHeight() + 'px'});
        _this.prepend(uploadIcon);
        var showId = _this.data('show-in');
        var h = fileField.data('height');
        var w = fileField.data('width');
        var showEl = $(showId);
        if (undefined == showEl || showEl.length == 0) {
            showEl = $('.showFile', _this);
            showEl.css({'height': h + 'px', 'width': w + 'px'});
        }

        var dimensions = {h: showEl.innerHeight(), w: showEl.innerWidth()};
        if (undefined != h && undefined != w) {
            dimensions.h = h;
            dimensions.w = w;
        }

        var currentValueField = $('input.current_value', _this);

        $('.deletebutton', _this).click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var message = $(this).data('confirmation');
            if (message) {
                var confirmed = confirm(message);
                if (confirmed) {
                    fileField.val('');
                    showEl.remove();
                    if ($(this).data('delete') == 'parent') {
                        _this.remove();
                    }
                }
            } else {
                fileField.val('');
                showEl.remove();
                if ($(this).data('delete') == 'parent') {
                    _this.remove();
                }
            }
        });


        fileField.change(function () {
            if (undefined != showEl && showEl != null) {
                ImageUploadField.resize(this.files[0], {
                    width: dimensions.w, // maximum width
                    height: dimensions.h // maximum height
                }, function (blob, didItResize) {
                    // didItResize will be true if it managed to resize it, otherwise false (and will return the original file as 'blob')
                    if (blob) {
                        if (currentValueField) {
                            currentValueField.val('');
                        }
                        if (showEl.is("img")) {
                            showEl.attr('src', window.URL.createObjectURL(blob));
                        } else {
                            showEl.remove();
                            var img = $("#" + $(this).attr("id") + '_show');
                            if (img.length == 0) {
                                img = $('<img alt="image" id="' + $(this).attr("id") + '_show" class="img" src="" />');
                                _this.append(img);
                            }
                            img.attr('src', window.URL.createObjectURL(blob));
                            showEl = img;
                        }

                        // you can also now upload this blob using an XHR.
                    } else {

                    }
                })
            }
            /*
             if($(this).hasData('show-in'))
             ImageTools.resize(this.files[0], {
             width: 150, // maximum width
             height: 150 // maximum height
             }, function (blob, didItResize) {
             // didItResize will be true if it managed to resize it, otherwise false (and will return the original file as 'blob')
             document.getElementById('profile_picture_img').src = window.URL.createObjectURL(blob);
             // you can also now upload this blob using an XHR.
             });*/
        })
    });
}

function slugify(text)
{
    return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, ''); // Trim - from end of text
}