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
</div>

<div class="modal fade" id="cropImageModal" tabindex="-1" aria-labelledby="cropImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropImageModalLabel">Crop Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <img id="imageToCrop" src="#" alt="Image to crop">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="cropAndUpload">Crop and Upload</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.5/axios.min.js"></script>
<script>
    $(document).ready(function() {
        const dropContainer = document.getElementById("dropcontainer")
        const fileInput = document.getElementById("image")

        dropContainer.addEventListener("dragover", (e) => {
            // prevent default to allow drop
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

        // Initialize the Cropper.js instance when the modal is shown
        $('#cropImageModal').on('shown.bs.modal', function() {
            cropper = new Cropper($('#imageToCrop')[0], {
                aspectRatio: 1 / 1,
                viewMode: 1,
                autoCropArea: 0.8,
            });
        });

        // Destroy the Cropper.js instance when the modal is hidden
        $('#cropImageModal').on('hidden.bs.modal', function() {
            cropper.destroy();
            cropper = null;
        });

        // Show the image cropping modal when an image is selected
        $('#image').on('change', function(event) {
            const file = event.target.files[0];
            const fileReader = new FileReader();

            fileReader.onload = function(e) {
                $('#imageToCrop').attr('src', e.target.result);
                $('#cropImageModal').modal('show');
            };

            fileReader.readAsDataURL(file);
        });

        // Handle the "Crop and Upload" button click
        $('#cropAndUpload').on('click', function() {
            croppedImageDataURL = cropper.getCroppedCanvas().toDataURL();
            uploadCroppedImage();
            $('#cropImageModal').modal('hide');
        });

        // Upload the cropped image to the server
        function uploadCroppedImage() {
            let  csrfToken = "{{ csrf_token() }}";;

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('image', dataURLtoFile(croppedImageDataURL, 'cropped-image.png'));

            axios.post('{{ route('upload1') }}',formData).then((res)=>{
                if (res.data.status === 'success') {
                    $('#croppedImage').attr('src', '{{ env('APP_UPLOADS_URL') }}/' + res.data.filename);
                    $('#croppedImage').show();
                }
            })

        }

        // Helper function to convert a data URL to a File object
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
    });
</script>

</body>
</html>
