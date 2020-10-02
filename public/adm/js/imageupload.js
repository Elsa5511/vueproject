'use strict';

if (typeof exports === "undefined") {
    var exports = {};
}

if (typeof module === "undefined") {
    var module = {};
}

Object.defineProperty(exports, '__esModule', {
    value: true
});

var _createClass = (function () {
    function defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
            var descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || false;
            descriptor.configurable = true;
            if ('value' in descriptor)
                descriptor.writable = true;
            Object.defineProperty(target, descriptor.key, descriptor);
        }
    }
    return function (Constructor, protoProps, staticProps) {
        if (protoProps)
            defineProperties(Constructor.prototype, protoProps);
        if (staticProps)
            defineProperties(Constructor, staticProps);
        return Constructor;
    };
})();

function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
        throw new TypeError('Cannot call a class as a function');
    }
}

var hasBlobConstructor = typeof Blob !== 'undefined' && (function () {
    try {
        return Boolean(new Blob());
    } catch (e) {
        return false;
    }
})();

var hasArrayBufferViewSupport = hasBlobConstructor && typeof Uint8Array !== 'undefined' && (function () {
    try {
        return new Blob([new Uint8Array(100)]).size === 100;
    } catch (e) {
        return false;
    }
})();

var hasToBlobSupport = typeof HTMLCanvasElement !== "undefined" ? HTMLCanvasElement.prototype.toBlob : false;

var hasBlobSupport = hasToBlobSupport || typeof Uint8Array !== 'undefined' && typeof ArrayBuffer !== 'undefined' && typeof atob !== 'undefined';

var hasReaderSupport = typeof FileReader !== 'undefined' || typeof URL !== 'undefined';

var ImageUploadField = (function () {
    function ImageUploadField() {
        _classCallCheck(this, ImageUploadField);
    }

    _createClass(ImageUploadField, null, [{
            key: 'resize',
            value: function resize(file, maxDimensions, callback) {
                if (typeof maxDimensions === 'function') {
                    callback = maxDimensions;
                    maxDimensions = {
                        width: 640,
                        height: 480
                    };
                }

                var maxWidth = maxDimensions.width;
                var maxHeight = maxDimensions.height;

                if (!ImageUploadField.isSupported() || !file.type.match(/image.*/)) {
                    callback(file, false);
                    return false;
                }

                if (file.type.match(/image\/gif/)) {
                    // Not attempting, could be an animated gif
                    callback(file, false);
                    // TODO: use https://github.com/antimatter15/whammy to convert gif to webm
                    return false;
                }

                var image = document.createElement('img');

                image.onload = function (imgEvt) {
                    var originalWidth = image.width;
                    var originalHeight = image.height;
                    var requestWidth = maxDimensions.width;
                    var requestHeight = maxDimensions.height;
                    var noCrop = false;

                    var isTooLarge = false;


                    // Calculo relacion de aspecto de la imagen original y la solicitada.
                    var rRel = 0;
                    if (requestHeight > 0 && requestWidth > 0) {
                        rRel = (requestWidth / requestHeight);
                    }
                    var oRel = (originalWidth / originalHeight);
                    var isWide = rRel < oRel;

                    if (noCrop) {
                        // Determino el aspecto de la imagen original.
                        if (isWide) {
                            if (originalWidth < requestWidth) {
                                requestWidth = originalWidth;
                            }
                            requestHeight = 0;
                        } else {
                            if (originalHeight < requestHeight) {
                                requestHeight = originalHeight;
                            }
                            requestWidth = 0;
                        }
                    }

                    // Calculo dimensiones
                    if (requestWidth == 0 && requestHeight == 0) {
                        // early exit; no need to resize
                        callback(file, false);
                        return;
                    } else if (requestWidth == 0) {
                        requestWidth = Math.round(originalWidth * (requestHeight / originalHeight));
                    } else if (requestHeight == 0) {
                        requestHeight = round(originalHeight * (requestWidth / originalWidth));
                    }


                    // Calculo el las dimensiones de la imagen segun se solicita
                    var srcHeight = 0;
                    var srcWidth = 0;
                    var srcPositionX = 0;
                    var srcPositionY = 0;
                    if (isWide) {
                        srcHeight = requestHeight;
                        srcWidth = (originalWidth / originalHeight) * srcHeight;
                        srcPositionX = (requestWidth - srcWidth) / 2;
                        srcPositionY = 0;
                    } else {
                        srcWidth = requestWidth;
                        srcHeight = (originalHeight / originalWidth) * srcWidth;
                        srcPositionX = 0;
                        srcPositionY = (requestHeight - srcHeight) / 5;
                        console.log(srcPositionY);
                    }

                    var canvas = document.createElement("canvas");
                    var ctx = canvas.getContext("2d");

                    canvas.height = requestHeight;
                    canvas.width = requestWidth;

                    /*ctx.drawImage(oc, 0, 0, oc.width * 0.5, oc.height * 0.5,
                     0, 0, canvas.width, canvas.height);*/

                    ctx.drawImage(image, srcPositionX, srcPositionY, srcWidth, srcHeight);

                    if (hasToBlobSupport) {
                        canvas.toBlob(function (blob) {
                            callback(blob, true);
                        }, file.type);
                    } else {
                        var blob = ImageUploadField._toBlob(canvas, file.type);
                        callback(blob, true);
                    }

                    return;
///return

                    if (width > height && width > maxDimensions.width) {
                        // width is the largest dimension, and it's too big.
                        height *= maxDimensions.width / width;
                        width = maxDimensions.width;
                        isTooLarge = true;
                    } else if (height > maxDimensions.height) {
                        // either width wasn't over-size or height is the largest dimension
                        // and the height is over-size
                        width *= maxDimensions.height / height;
                        height = maxDimensions.height;
                        isTooLarge = true;
                    }

                    if (!isTooLarge) {
                        // early exit; no need to resize
                        callback(file, false);
                        return;
                    }


                    var canvas = document.createElement("canvas");
                    var ctx = canvas.getContext("2d");

                    canvas.height = height;

                    /// step 1
                    var oc = document.createElement('canvas'),
                            octx = oc.getContext('2d');

                    oc.width = image.width * 0.5;
                    oc.height = image.height * 0.5;
                    octx.drawImage(image, 0, 0, oc.width, oc.height);

                    /// step 2
                    octx.drawImage(oc, 0, 0, oc.width * 0.5, oc.height * 0.5);

                    ctx.drawImage(oc, 0, 0, oc.width * 0.5, oc.height * 0.5,
                            0, 0, canvas.width, canvas.height);



                    if (hasToBlobSupport) {
                        canvas.toBlob(function (blob) {
                            callback(blob, true);
                        }, file.type);
                    } else {
                        var blob = ImageUploadField._toBlob(canvas, file.type);
                        callback(blob, true);
                    }
                };
                ImageUploadField._loadImage(image, file);

                return true;
            }
        }, {
            key: '_toBlob',
            value: function _toBlob(canvas, type) {
                var dataURI = canvas.toDataURL(type);
                var dataURIParts = dataURI.split(',');
                var byteString = undefined;
                if (dataURIParts[0].indexOf('base64') >= 0) {
                    // Convert base64 to raw binary data held in a string:
                    byteString = atob(dataURIParts[1]);
                } else {
                    // Convert base64/URLEncoded data component to raw binary data:
                    byteString = decodeURIComponent(dataURIParts[1]);
                }
                var arrayBuffer = new ArrayBuffer(byteString.length);
                var intArray = new Uint8Array(arrayBuffer);

                for (var i = 0; i < byteString.length; i += 1) {
                    intArray[i] = byteString.charCodeAt(i);
                }

                var mimeString = dataURIParts[0].split(':')[1].split(';')[0];
                var blob = null;

                if (hasBlobConstructor) {
                    blob = new Blob([hasArrayBufferViewSupport ? intArray : arrayBuffer], {type: mimeString});
                } else {
                    var bb = new BlobBuilder();
                    bb.append(arrayBuffer);
                    blob = bb.getBlob(mimeString);
                }

                return blob;
            }
        }, {
            key: '_loadImage',
            value: function _loadImage(image, file, callback) {
                if (typeof URL === 'undefined') {
                    var reader = new FileReader();
                    reader.onload = function (evt) {
                        image.src = evt.target.result;
                        if (callback) {
                            callback();
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    image.src = URL.createObjectURL(file);
                    if (callback) {
                        callback();
                    }
                }
            }
        }, {
            key: 'isSupported',
            value: function isSupported() {
                return typeof HTMLCanvasElement !== 'undefined' && hasBlobSupport && hasReaderSupport;
            }
        }]);

    return ImageUploadField;
})();

exports['default'] = ImageUploadField;
module.exports = exports['default'];