// src/public/js/profile.js
document.addEventListener('DOMContentLoaded', function() {
    let cropper;
    const imageInput = document.getElementById('profile_picture_input');
    const cropperImage = document.getElementById('cropper-image');
    const cropperWrapper = document.getElementById('cropper-wrapper');
    const croppedDataInput = document.getElementById('cropped_image_data');
    const cropButton = document.getElementById('crop-button');
    const profileForm = document.getElementById('profile-form');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    cropperImage.src = event.target.result;
                    cropperWrapper.style.display = 'block';
                    
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 1, 
                        viewMode: 1,    
                        dragMode: 'move', 
                        autoCropArea: 1, // FILL the entire 1:1 container
                        restore: false,
                        guides: false, // Hide guides for a cleaner "box" look
                        center: false,
                        highlight: false,
                        cropBoxMovable: false, // Box doesn't move
                        cropBoxResizable: false, // Box doesn't resize
                        toggleDragModeOnDblclick: false,
                        ready: function() {
                            // Automatically zoom out to fit perfectly
                            this.cropper.zoomTo(this.cropper.getContainerData().width / this.cropper.getImageData().naturalWidth);
                        }
                    });
                };
                reader.readAsDataURL(files[0]);
            }
        });
    }

    if (cropButton) {
        cropButton.addEventListener('click', function() {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400
                });
                croppedDataInput.value = canvas.toDataURL('image/jpeg');
                cropperWrapper.style.display = 'none';
                alert('Image cropped successfully! Click Update Profile to save.');
            }
        });
    }

    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            // If the user picked a file but didn't click "Confirm Crop", do it for them automatically
            if (cropper && !croppedDataInput.value && imageInput.files.length > 0) {
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400
                });
                croppedDataInput.value = canvas.toDataURL('image/jpeg');
            }
        });
    }
});
