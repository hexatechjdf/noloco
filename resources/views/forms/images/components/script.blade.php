<script>
    const el = document.getElementById("imagePreview");
    const saveChangingButton = document.querySelector(".saveChanging");
    const submitImagesButton = document.getElementById("submitImagesButton");

    let selectedFiles = [];
    let featuredFiles = [];
    let newImages = [];
    let draggedBox = null;
    let imagesWindow = null;
    let imgBoxClass = '.image-box';
    let featuredClass = 'featured';
    let imgURL = 'https://link.apisystem.tech/widget/form/Rh1pCfjapbvj8yRQfsGd';

    function handleWindow() {
        if (imagesWindow) {
            imagesWindow.src = imgURL;
            return;
        }

        const uploader = document.querySelector(".uploaderModal");
        imagesWindow = document.createElement('iframe');
        imagesWindow.src = imgURL;
        imagesWindow.style = `width:100%; height:400px; border: none;`;
        uploader.innerHTML = ''; // Clear any previous iframe content
        uploader.appendChild(imagesWindow);
    }

    const imageUploaderModal = document.getElementById('imageUploaderModal');
    imageUploaderModal.addEventListener('shown.bs.modal', handleWindow);

    window.addEventListener('message', function(e) {
        let data = e.data;
        if (typeof data === 'object') {
            if (data.action === 'uploaded') {
                console.log("Uploaded data:", data);
                // selectedFiles = [...data.allImages];
                previewImages(data.allImages);
                closeModal();

                let imageBoxes = el.querySelectorAll(imgBoxClass);
                selectedFiles = Array.from(imageBoxes).map(box => box.querySelector("img").src);
                submitImages(data.allImages); // Automatically submit after preview
            } else if (data.action === 'closed') {
                imagesWindow = null;
            }
        }
    });



    const alertPlaceholder = document.querySelector(".alertContainer");
    const appendAlert = (message, type) => {
        const wrapper = document.createElement("div");
        alertPlaceholder.innerHTML = [
            `<div class="alert alert-${type} alert-dismissible w-100"  role="alert">`,
            `   <div>${message}</div>`,
            '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
            "</div>",
        ].join("");
    };


    const debounce = (func, delay) => {
        let timeoutId;
        return (...args) => {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            timeoutId = setTimeout(() => {
                func.apply(null, args);
            }, delay);
        };
    };

    function handleFeatureChange(imageBox, imageUrl, featureCheckbox, featuredLabel) {
        document.querySelectorAll("." + featuredClass).forEach((t) => {
            let checkbox = t.querySelector('[type="checkbox"]');

            if (checkbox) {
                checkbox.checked = false;
            }
            let label = t.querySelector(".featured-label");
            if (label) {

                console.log(featuredClass)
                label.style.display = "none";
            }
            t.classList.remove(featuredClass);
        })
        featuredFiles = [];
        if (featureCheckbox.checked) {
            // featureCheckbox.classList.add("featured");
            imageBox.classList.add(featuredClass);
            featuredLabel.style.display = "block";
            featuredFiles = [imageUrl];

        } else {
            featuredFiles = featuredFiles.filter((url) => url !== imageUrl);
            imageBox.classList.remove(featuredClass);
            featuredLabel.style.display = "none";
        }


        console.log("Featured Files:", featuredFiles);
        submitFeaturedImages();
    }

    const debouncedHandleFeatureChange = debounce(handleFeatureChange, 300);

    function previewImages(files, featured = []) {
        // el.innerHTML = "";
        files.forEach((file, index) => {
            const imageUrl = file;
            const imageBox = document.createElement("div");
            let isFeatured = featured.includes(file);
            imageBox.classList.add(imgBoxClass.replaceAll(".", ""));
            imageBox.setAttribute("draggable", true);
            imageBox.innerHTML = `
            <div class="card position-relative ${isFeatured ? featuredClass : ''} box-${index} overflow-hidden" style="width: 18rem; height:300px">
             <div class="${featuredClass}-label" style="display: ${isFeatured ? 'block' : 'none'};">Featured</div>
                <img src="${imageUrl}" class="card-img-top image" alt="uploaded image">
                <div class="card-body p-0">
                    <div class="button-container">
                        <button class="delete-image-button text-danger" data-index="box-${index}"><i class="material-icons text">delete</i></button>
                    </div>
                    <div>
                        <label class="feature-checkbox-label ">
                            <input type="checkbox" class="feature-checkbox" ${isFeatured ? 'checked' : ''}>
                            Mark as Featured
                        </label>
                    </div>
                </div>
            </div>
        `;
            el.appendChild(imageBox);

            const featureCheckbox = imageBox.querySelector(".feature-checkbox");
            const featuredLabel = imageBox.querySelector(".featured-label");
            featureCheckbox.addEventListener("change", function() {
                debouncedHandleFeatureChange(imageBox, imageUrl, featureCheckbox, featuredLabel);
            });


            const deleteImageButton = imageBox.querySelector(".delete-image-button");
            deleteImageButton.addEventListener("click", function() {

                if (confirm("Are you sure you want to delete? once deleted can not recovered.")) {
                    imageBox.remove();

                    selectedFiles = selectedFiles.filter(file => file !== imageUrl);

                    submitImages([], featuredFiles.includes(imageUrl));
                }

            });
        });

    }
    handleDragAndDrop(el)

    function handleDragAndDrop(el) {
        el.addEventListener("dragstart", function(e) {
            draggedBox = e.target.closest(imgBoxClass);

        });

        el.addEventListener("dragover", function(e) {
            e.preventDefault(); // Prevent default to allow drop
        });

        el.addEventListener("drop", function(e) {
            e.preventDefault();
            const droppedBox = e.target.closest(imgBoxClass);
            console.log(droppedBox, draggedBox);
            if (draggedBox !== droppedBox) {
                // Reorder the images in the DOM
                const draggedIndex = [...el.children].indexOf(draggedBox);
                const droppedIndex = [...el.children].indexOf(droppedBox);
                if (draggedIndex < droppedIndex) {
                    el.insertBefore(draggedBox, droppedBox.nextSibling);
                } else {
                    el.insertBefore(draggedBox, droppedBox);
                }

                // Reorder the featuredFiles list based on new positions in the DOM
                let imageBoxes = el.querySelectorAll(imgBoxClass);
                selectedFiles = Array.from(imageBoxes).map(box => box.querySelector("img").src);
                submitImagesButton.style.display = "none";
                saveChangingButton.style.display = "block";
            }
        });
    }

    function submitFeaturedImages() {
        submitImages();

    }


    let lastImagesKey = 'inventoryImageKey';

    function submitImages(newImg = [], featuredDeleted = false) {
        if (featuredDeleted) {
            featuredFiles = [];
        }
        const dataToSend = {
            newImages: newImg,
            featuredFile: featuredFiles[0] ?? "",
            locationId,
            uuid: nolocoUUID,
            id: mainId,
            allImages: selectedFiles,

        };


        let data = JSON.stringify(dataToSend);
        //localStorage.setItem('lastImagesKey' + nolocoUUID, data);
        $.ajax({
            url: '{{ route('forms.image-uploader.store') }}',
            type: 'POST',
            data: {
                data: data,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {

                toastr.success('Successfully Submitted ');

            }
        });

    }

    let queryParams = new URLSearchParams(location.search);
    let locationId = queryParams.get('locationId') ?? null;
    let nolocoUUID = queryParams.get('uuid') ?? null;
    let mainId = queryParams.get('mainId') ?? null;
    (() => {
        if (!locationId || !nolocoUUID) {
            return;
        }

        //let data = localStorage.getItem('lastImagesKey' + nolocoUUID) ?? "{}";

        fetch(`{{ route('inventory.fetch', '') }}/${nolocoUUID}?locationId=${locationId}`)
            .then(t => t.json()).then(data => {

                let inventoryCollection = data?.data?.inventoryCollection ?? null;
                inventoryData = ((inventoryCollection?.edges ?? [])[0] ?? {}).node ?? null;
                if (inventoryData) {
                    if (inventoryData.featuredPhoto?.url) {
                        featuredFiles = [inventoryData.featuredPhoto?.url]
                    }
                    let images = inventoryData.photosUrls ?? [];
                    if (typeof images == 'string') {
                        images = images.split(',').map(t => t.trim());
                    }
                    selectedFiles = images;
                    mainId = inventoryData.id;
                    previewImages(images, featuredFiles);
                }

            });
    })();


    saveChangingButton.addEventListener("click", function() {
        submitImages();
        submitImagesButton.style.display = "block";
        saveChangingButton.style.display = "none";
    });



    function closeModal() {
        const modalElement = document.getElementById('imageUploaderModal');
        const modal = bootstrap.Modal.getInstance(modalElement);

        if (modal) {
            modal.hide();
        }


        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();

        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        submitImagesButton.addEventListener("click", function() {
            const modal = new bootstrap.Modal(imageUploaderModal);
            modal.show();
            closeModal();
        });
    });
</script>
