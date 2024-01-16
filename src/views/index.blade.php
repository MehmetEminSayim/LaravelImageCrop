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
</style>

<div class="container">
    <div class="row">
        <div class="col-md-12 mt-5">
            <h3 class="text-center">Upload and Crop Image</h3>
            <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <input type="file" name="image" id="image" accept="image/*">
                </div>
            </form>
        </div>
    </div>
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

<div class="row">
    <div class="col-md-12 mt-5">
        <h5 class="text-center">Cropped Image</h5>
        <div class="d-flex justify-content-center">
            <img id="croppedImage" src="#" alt="Cropped image" style="display: none;">
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.5/axios.min.js"></script>
<script>
    $(document).ready(function() {
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
                $('#imageToCrop').attr('src', e.target.result);
                $('#cropImageModal').modal('show');
            };

            fileReader.readAsDataURL(file);
        });

        $('#cropAndUpload').on('click', function() {
            croppedImageDataURL = cropper.getCroppedCanvas().toDataURL();
            uploadCroppedImage();
            $('#cropImageModal').modal('hide');
        });

        function uploadCroppedImage() {
            let  csrfToken = "{{ csrf_token() }}";;
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('image', dataURLtoFile(croppedImageDataURL, 'cropped-image.png'));

            axios.post('{{ route('upload1') }}',formData).then((res)=>{
                if (res.data.status === 'success') {
                    $('#croppedImage').attr('src', '{{ env('APP_UPLOADS_URL') }}/' + res.data.filename);
                    $('#croppedImage').show();
                    console.log(res.data);
                }
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
    });
</script>
