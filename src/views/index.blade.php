<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<style>
    .modal-dialog {
        max-width: 100%;
        margin: 1rem;
    }

    .img-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 500px;
        background-color: #f7f7f7;
        overflow: hidden;
    }

    .drop-container {
        position: relative;
        display: flex;
        gap: 10px;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 200px;
        padding: 20px;
        border-radius: 10px;
        border: 2px dashed #555;
        color: #444;
        cursor: pointer;
        transition: background .2s ease-in-out, border .2s ease-in-out;
    }

    .drop-container:hover,
    .drop-container.drag-active {
        background: #eee;
        border-color: #111;
    }

    .drop-container:hover .drop-title,
    .drop-container.drag-active .drop-title {
        color: #222;
    }

    .drop-title {
        color: #444;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        transition: color .2s ease-in-out;
    }

    input[type=file] {
        width: 350px;
        max-width: 100%;
        color: #444;
        padding: 5px;
        background: #fff;
        border-radius: 10px;
        border: 1px solid #555;
    }

    input[type=file]::file-selector-button {
        margin-right: 20px;
        border: none;
        background: #084cdf;
        padding: 10px 20px;
        border-radius: 10px;
        color: #fff;
        cursor: pointer;
        transition: background .2s ease-in-out;
    }

    input[type=file]::file-selector-button:hover {
        background: #0d45a5;
    }
</style>

<div class="row">
    <label for="images" class="drop-container" id="dropcontainer">
        <span class="drop-title">Drop files here</span>
        or
        <input type="file" name="image" id="image" accept="image/*">
    </label>
    <span style="display: none" class="text-danger error-file-text">This error</span>
</div>

<div class="modal fade" id="cropImageModal" tabindex="-1" aria-labelledby="cropImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropImageModalLabel">Crop Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 drawInputArea" style="display: none">
                        <input type="color" id="colorPicker">
                        <input type="range" id="lineWidth" min="1" max="10">
                    </div>
                    <div class="col-md-9">
                        <div class="img-container imageInputArea">
                            <img id="imageToCrop" src="#" class="" alt="Image to crop">
                        </div>
                        <canvas id="drawingCanvas"></canvas>
                    </div>
                    <div class="col-md-3 imageInputArea">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Width</label>
                            <input type="number" class="form-control" id="imageWidth">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Height</label>
                            <input type="number" class="form-control" id="imageHeight">
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Optimize Image</label>
                            <select class="form-select" aria-label="Default select example" name="is_optimize" id="isOptimize">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="cropAndUpload">Apply Crop</button>
                <button style="display: none;" type="button" class="btn btn-primary finalSubmitButton">Draw Upload</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="lastRecordId">
<input type="hidden" name="lastRecordFilename">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.5/axios.min.js"></script>
<script>
    $(document).ready(function() {
        const dropContainer = document.getElementById("dropcontainer")
        const fileInput = document.getElementById("image")

        dropContainer.addEventListener("dragover", (e) => {
            e.preventDefault()
        }, false)

        dropContainer.addEventListener("dragenter", () => {
            dropContainer.classList.add("drag-active")
        })

        dropContainer.addEventListener("dragleave", () => {
            dropContainer.classList.remove("drag-active")
        })

        dropContainer.addEventListener("drop", (e) => {
            e.preventDefault()
            dropContainer.classList.remove("drag-active")
            fileInput.files = e.dataTransfer.files;
            $("#image").trigger("change")
        })

        ////////////////////////////////////////////////////////////////////////////////////////////


        let cropper;
        let croppedImageDataURL;

        $('#cropImageModal').on('shown.bs.modal', function() {
            cropper = new Cropper($('#imageToCrop')[0], {
                aspectRatio: 1 / 1,
                viewMode: 1,
                autoCropArea: 0.8,
            });
        });

        $('#cropImageModal').on('hidden.bs.modal', function() {
            cropper.destroy();
            cropper = null;
        });

        $('#image').on('change', function(event) {
            const file = event.target.files[0];
            const fileReader = new FileReader();

            fileReader.onload = function(e) {
                const image = new Image();
                let width = null;
                let height = null;
                image.onload = function() {
                     width = image.width;
                     height = image.height;
                    $("#imageWidth").val(width);
                    $("#imageHeight").val(height);
                };
                image.src = e.target.result;
                $('#imageToCrop').attr('src', e.target.result);
                $('#imageToCrop').css("width",width / 2);
                $('#imageToCrop').css("height",height / 2);
                $('#cropImageModal').modal('show');
            };

            fileReader.readAsDataURL(file);
        });

        $('#cropAndUpload').on('click', function() {
            croppedImageDataURL = cropper.getCroppedCanvas().toDataURL();
            //uploadCroppedImage();
            //$('#cropImageModal').modal('hide');
            $(".drawInputArea").show();
            $(".imageInputArea").hide()
            $("#drawingCanvas").show();
            $(".finalSubmitButton").show();
            $("#cropAndUpload").hide();
        });

        function uploadCroppedImage() {
            //Not usage this function by emin
            let  csrfToken = "{{ csrf_token() }}";;

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('image', dataURLtoFile(croppedImageDataURL, 'cropped-image.png'));
            formData.append('width',$("#imageWidth").val());
            formData.append('height',$("#imageHeight").val());
            formData.append('optimize',$("#isOptimize").val())

            axios.post('{{ route('upload1') }}',formData).then((res)=>{
                if (res.data.status === 'success') {

                }else{
                    $(".error-file-text").show();
                    $(".error-file-text").html(res.data.message);
                }
            }).catch((err)=>{
                $(".error-file-text").show();
                $(".error-file-text").html(err);
            })

        }

        function dataURLtoFile(dataURL, filename) {
            const arr = dataURL.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);

            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }

            return new File([u8arr], filename, { type: mime });
        }

        let finalImageDataURL = null;
        $('#cropAndUpload').on('click', function() {
            let croppedCanvas = cropper.getCroppedCanvas();
            let drawingCanvas = document.getElementById('drawingCanvas');
            let ctx = drawingCanvas.getContext('2d');

            drawingCanvas.width = croppedCanvas.width;
            drawingCanvas.height = croppedCanvas.height;

            ctx.drawImage(croppedCanvas, 0, 0);

            let drawing = false;
            drawingCanvas.addEventListener('mousedown', (e) => {
                drawing = true;
                ctx.beginPath();
                ctx.moveTo(e.offsetX, e.offsetY);
            });
            drawingCanvas.addEventListener('mousemove', (e) => {
                if (drawing) {
                    ctx.lineTo(e.offsetX, e.offsetY);
                    ctx.stroke();
                }
            });
            drawingCanvas.addEventListener('mouseup', () => {
                drawing = false;
                finalImageDataURL = drawingCanvas.toDataURL();
            });

            document.getElementById('colorPicker').addEventListener('change', (e) => {
                ctx.strokeStyle = e.target.value;
            });
            document.getElementById('lineWidth').addEventListener('change', (e) => {
                ctx.lineWidth = e.target.value;
            });
        });

        $(document).on("click",".finalSubmitButton",function (){
            let  csrfToken = "{{ csrf_token() }}";;

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('image', dataURLtoFile(finalImageDataURL, 'cropped-image.png'));
            formData.append('width',$("#imageWidth").val());
            formData.append('height',$("#imageHeight").val());
            formData.append('optimize',$("#isOptimize").val())

            axios.post('{{ route('upload1') }}',formData).then((res)=>{
                if (res.data.status === 'success') {
                    $("input[name=lastRecordId]").val(res.data.record.id);
                    $("input[name=lastRecordFilename]").val(res.data.record.filename)
                    $('#cropImageModal').modal('hide');
                }else{
                    $(".error-file-text").show();
                    $(".error-file-text").html(res.data.message);
                }
            }).catch((err)=>{
                $(".error-file-text").show();
                $(".error-file-text").html(err);
            })
        })


    });
</script>

</body>
</html>
