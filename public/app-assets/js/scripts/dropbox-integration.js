import * as helpers from './helper.js';
import { assertRoleUser, assertRoleUserAndRootPath } from './helper.js';

import { ssoToken, ssoData } from './sso.js';


    let baseRoot = helpers.baseRoot;
    let FolderPath='';
    let lastSideBarPath = '';
    const contactId = appConfig.contactId;
    let baseFilteredFolders = [];
    let cachedData = {
        folders: [],
        files: [],
    };

    let sidebarActionBtn = $('#sidebar-action-btn');
    let copyMsg = $('.copy-msg');


    function innerLoader(action='hide')
    {
        const loader  =  $('#loader');
        const content = $('.file-manager-content-body');

        if (action == 'show')
        {
            loader.removeClass('d-none');
            content.addClass('d-none');
            content.removeAttr('hidden');
        }else{
            loader.addClass('d-none');
            content.removeClass('d-none');
        }
    }



    // document.addEventListener("DOMContentLoaded", function () {

        document.querySelectorAll('.file-picker').forEach(t=>{
            t.onclick= function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (helpers.hasPermission(helpers.currentActivePath)) {
                    let data = this.getAttribute('data-file');
                    document.querySelector('#'+data).click();
                }
            }
        })

        let dropzoneSelector =document.querySelector('#dropzone');
        const dropzone = new Dropzone("#dropzone", {
            url: routes.mediaUploadUrl, // Laravel route for file upload
            method: "post",
            chunking: true, // Enable chunked uploads
            forceChunking: true,
            chunkSize: 2 * 1024 * 1024, // 2 MB per chunk
            parallelUploads: 3, // Number of files to upload in parallel
            // Accept all file types
            addRemoveLinks: false, // Add a remove link for uploaded files
            autoProcessQueue: true, // Automatically upload files
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // Include CSRF token
                'ghlAuthorization' : ssoToken ?? null // Set token in headers
            },
            init: function () {
                this.on("addedfile", function (file) {
                    // console.log("File added:", file);
                    // toastr.success("File added:", file);
                });

                this.on("uploadprogress", function (file, progress) {
                    // toastr.success(`Uploading ${file.name}: ${progress.toFixed(2)}%`);
                });

                this.on("success", function (file, response) {

                    var inProcessCount = dropzone.getQueuedFiles().length;
                    if(inProcessCount==0){
                        setTimeout(function(){
                            dropzoneSelector.style.display='none';
                        },2000);
                    }

                    let folder = file.webkitRelativePath || "";
                    if (folder == "") {
                        // TODO: issue in append data name to UI when same named files or folders uploaded because at now we are not geting the metaData of that file/folder from dropDox
                    appendData({
                            files:[{
                                id:crypto.randomUUID(),
                            name:file.name,
                            path_display:helpers.currentActivePath+'/'+file.name
                        }   ]
                        },true);
                    }
                    toastr.success(`File ${file.name} uploaded successfully`, response);
                });

                this.on("error", function (file, errorMessage) {
                    toastr.error(`Error uploading ${file.name}:`, errorMessage);
                });

                this.on("sending", function (file, xhr, formData) {
                    // Add additional data to the request
                    formData.append("user_id", "12345"); // Replace with dynamic user_id if needed
                    formData.append("path", helpers.currentActivePath+"/");
                    let folder = file.webkitRelativePath || "";
                    if(folder!=""){
                        // folder = folder.split('/')[0];
                        formData.append("folder", folder);
                    }

                });
            },
        });

        function processFiles(event){
            const files = event.target.files;

            // Check permission before processing files
            if (!helpers.hasPermission(helpers.currentActivePath)) return;

            let isFound=false;
            for (const file of files) {

                if(!isFound){
                let folder = file.webkitRelativePath || "";
                if(folder!=""){
                    folder = folder.split('/')[0];


                    // TODO: issue in append data name to UI when same named files or folders uploaded because at now we are not geting the metaData of that file/folder from dropDox
                    appendData({
                        folders:[{
                        name:folder,
                        path_display: helpers.currentActivePath+'/'+folder
                    }   ]
                    },true);


                }
                    isFound=true;
                }
                dropzone.addFile(file);
            }


            dropzoneSelector.style.display='block';
            dropzone.processQueue(); // Start uploading
        }
        // Handle folder input
        const folderInput = document.getElementById("folder-upload");
        folderInput.onchange=processFiles;
        const fileUpload = document.getElementById("file-upload");
        fileUpload.onchange=processFiles;

    // });

        function setMoveFolder(folders,showLoader=false,onlyAppend=false){

            let htmlForm = '';
            if(showLoader){
                htmlForm = appFileManager.loaderHtml;
            }
            let folderSideBar = $('.folderList');

            if(!onlyAppend){
                folderSideBar.html(htmlForm);
            }

            // Create a Set of selected paths from the selected items (just folder)
            let selectedPaths = new Set(
                appFileManager.getSelectedItems('folder').map(function () {
                    return $(this).data('path');
                })
            );

            // Filter folders only if selectedPaths has any items
            let filteredFolders = selectedPaths.size > 0
                ? folders.filter(folder => !selectedPaths.has(folder.path_display))
                : folders;

            if (!showLoader) {
                const htmlForm = filteredFolders.map(folder => `
                    <li class="movesidebar-item card p-1 w-100" data-path="${folder.path_display}">
                        <div class="d-flex align-items-center w-100">
                            <i data-feather="folder"></i>
                            <p class="card-text pl-2">${folder.name}</p>
                        </div>
                    </li>
                `).join('');

                folderSideBar.append(htmlForm);
                initFeather();
            }
        }



        $('.nav.folderList').on('click','.movesidebar-item',function(e){
            $('.movesidebar-item').removeClass('active');
            $(this).addClass('active');

             helpers.setNewPath($(this).attr('data-path'));

            showHideCopyMsg(); // show / hide copy msg
        });


        $('.nav.folderList').on('dblclick','.movesidebar-item',function(e){
            let path = $(this).attr('data-path');

            helpers.setNewPath(path);

            fetchFolderData(path,false,true);
        });




        $('.file-actions .dropdown').on('click', '.changePath', function (e) {

            if (!helpers.hasPermission(helpers.currentActivePath)) {
                e.preventDefault;
                e.stopPropagation;
                return;
            }

            let action=$(this).data('action');
            let ucaction = action.toUpperCase();
            $('.move-sidebar-title').html(ucaction)

            sidebarActionBtn.attr('data-action',action);
            sidebarActionBtn.html(ucaction);

            setMoveFolder(currentActiveRecords.folders||[]);
            sideBarBreadCrumb(helpers.currentActivePath);
        })


        $(window).on('load', function() {
            if (feather) {
                initFeather();
            }
        })



function updateBreadcrumbs(path, newBreadCrumb = '#breadcrumbs') {


        let isDefault = newBreadCrumb=='#breadcrumbs';

        const breadcrumbsContainer = $(newBreadCrumb);
        breadcrumbsContainer.empty(); // Clear existing breadcrumbs
        let parts = path;//.replaceAll(baseRoot,'/Home');
        // Split the path into segments and clean up unnecessary parts
        parts = parts.split('/').filter(part => part); // Remove empty segments


        // Add the base breadcrumb manually
        // let cumulativePath = '/Transactions/';
        let cumulativePath = `/${parts[0]}`;

        const baseBreadcrumb = (parts.length === 1 || contactId)
            ? $('<span>').addClass('breadcrumb-item active').text(parts[0]) // Non-clickable if already on "Transactions"
            : $('<span>')
                .addClass('breadcrumb-item')
                .html(`<a href="javascript:void(0);" class="breadcrumb-link" data-def="${isDefault}" data-path="${baseRoot}">${parts[0]}</a>`);

            breadcrumbsContainer.append(baseBreadcrumb);

            // Generate breadcrumbs for remaining folders
            parts.slice(1).forEach((folderName, index, array) => {
                // Fix the folder name (capitalize and retain the original format)
                // const displayName = folderName.charAt(0).toUpperCase() + folderName.slice(1);

                // Build the cumulative path
                cumulativePath += `/${folderName}`;

                // Create the breadcrumb link or active text
                const isLast = index === array.length - 1;
                const breadcrumb = isLast
                    ? $('<span>').addClass('breadcrumb-item active').text(folderName) // Active breadcrumb for self
                    : $('<span>')
                        .addClass('breadcrumb-item')
                        .html(`<a href="javascript:void(0);" class="breadcrumb-link" data-def="${isDefault}" data-path="${cumulativePath}">${folderName}</a>`);

                breadcrumbsContainer.append(breadcrumb);
            });


            // currentActivePath = path;


            if (!isDefault)
            {
                showHideCopyMsg(); // show / hide copy msg
            }


            if (isDefault) { // I removed this condition contactId.trim() == '' &&
                helpers.setCurrentActivePath(path);
                localStorage.setItem('currentPath', helpers.currentActivePath); // Save the current path to localStorage
            }
    }

    // Bind click event to breadcrumb links (not for the last part)
    $('#breadcrumbs, #sidebarbreadcrumbs').on('click', '.breadcrumb-link', function () {
        const path = $(this).data('path');
        const def = $(this).data('def');
        let inital = def=='true' || def==true;
        let moveOnly = !inital;
        // console.log(moveOnly, def == 'true', def);
        // console.log('click on bread', path, inital, moveOnly); return;
        fetchFolderData(path,inital,moveOnly); // Fetch data for the clicked breadcrumb folder
    });

let currentActiveRecords=[];


// ==================================== fetch Folder Data ====================================
function fetchFolderData(path, isInitial = true,onlyMoveBox=false, page=1) {

    $('.file-actions').removeClass('show'); //hide file actions icons when path changed. (or reload)

    let actionButton = $('.add-file-btn');
    actionButton.addClass('d-none');

    const folderContainer = helpers.getFolderContainer(); //$('#folders-container');
    const fileContainer  =   helpers.getFileContainer(); //$('#files-container');

    // Show loader during the request
    if (isInitial) {
        innerLoader('show');
    }


    // Clear existing content before making a new request
    // folderContainer.html(loadingHtml('Loading folders...'));
    // fileContainer.html(loadingHtml('Loading files...'));

    if(!onlyMoveBox){
        folderContainer.empty();
        fileContainer.empty();
        $('#breadcrumbs').empty(); // Clear breadcrumbs
    }else{
        setMoveFolder([],true)
        sideBarBreadCrumb(path);
    }


    // Check cache first
    //  console.log('outside if cachedData: ', cachedData);
    if (assertRoleUserAndRootPath(path) && cachedData && cachedData.folders.length > 0) { // removed && $initial

        // console.log('inside if cachedData ZEE: ', cachedData);
        if (onlyMoveBox)
            setMoveFolder(cachedData.folders || []);
        else {
            appendData(cachedData, path);
        }
        currentActiveRecords = cachedData;
        innerLoader('hide');
        return;
    }
    // console.log('not return cacheData');


        const url = routes.mediaListUrl;

        // Make API request if data not found in cache
        helpers.axiosInstance.post(url, {
            'contact_id': contactId,
            path: path,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // Add CSRF token
        },{ hideLoader: true })
        .then(response => {
            const data = response.data;

            if (data.error) {
                console.log(data.error);
                toastr.error(data.error);
                return;
            }

            let responsePath = data.path ?? null;

            if (responsePath && responsePath != path)
            {
                path = responsePath;
            }


            // Cache the data for future use
            //localStorage.setItem(path, JSON.stringify(data));

            if (!onlyMoveBox) {
                currentActiveRecords = data;  // TODO: test
                appendData(data, path, isInitial);
                if (assertRoleUserAndRootPath(path) && isInitial) //TODO: manage this properly may be remove cache for admin on root page OR mange propperly when adding and remving items
                {
                    // baseFilteredFolders = data.folders;
                    cachedData.folders = data.folders;
                    cachedData.files = data.files;
                }

            }else{
                setMoveFolder(data.folders || []);   //appendToMoveFoldersOnly
            }

            if (isInitial) {
                innerLoader('hide');
            }


            // console.log('before loadmore', data);
            if (data.hasMore || data.hasMoreContacts) { //data.hasMore ||
                // console.log('has loadMore');
                loadMore(
                    path,
                    data.hasMoreContacts ? page + 1 : page,  // Increment page if hasMoreContacts
                    data.hasMore ? data.cursor : null       // Pass cursor if hasMore
                );
            }
            actionButton.removeClass('d-none');

        })
        .catch(error => {
            console.error('Error fetching folder data:', error);
            // alert('Failed to load data. Please try again later.');
            toastr.error('Failed to load data. Please try again later.');
             innerLoader('hide');
             actionButton.removeClass('d-none');
        });
}



// ==================================== load more ====================================
    // Function to load next pages in the background
    function loadMore(path, page, cursor) {
        setTimeout(() => {
            // if (helpers.currentActivePath !== path) return; // Stop if user moved away

            helpers.axiosInstance.post(routes.mediaListUrl, {
                'contact_id': contactId,
                path: path,
                page: page,
                cursor: cursor,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }, { hideLoader: true })
                .then(response => {
                    const data = response.data;
                    // if (helpers.currentActivePath !== path) return; // Stop if user moved away

                    if (data.error) {
                        console.warn('Background loadMore stopped:', data.error);
                        toastr('Background loadMore stopped:', data.error);
                        return;
                    }




                    if (assertRoleUserAndRootPath(path))
                    {
                        let newFolders = data.folders.filter(f => !cachedData.folders.some(cached => cached.id === f.id));
                        let newFiles = data.files.filter(f => !cachedData.files.some(cached => cached.id === f.id));

                        if (newFolders.length > 0)
                            cachedData.folders.push(...newFolders);

                        if (newFiles.length > 0)
                            cachedData.files.push(...newFiles);
                    }


                    // appendData({ folders: newFolders, files: newFiles }, path);
                    // appendData(data, path);

                    //  If user is still on this path, then update the UI
                    if (helpers.currentActivePath === path) {
                        //TODO: if this not work then -> if still page ==1 on loadmore request then empty and reappendData
                        appendData({
                            folders: newFolders,
                            files: newFiles,
                        }, path);
                    }

                    console.log('inside loadmore setTimeout ', data);

                    if (data.hasMore || data.hasMoreContacts) {
                        loadMore(
                            path,
                            data.hasMoreContacts ? page + 1 : page,  // Increment page if hasMoreContacts
                            data.hasMore ? data.cursor : null       // Pass cursor if hasMore
                        );
                    }
                })
                .catch(error => {
                    console.error('Error loading more data:', error);
                    toastr.error('Error loading more data:', error);
                });

        }, 1000);
    }


//========================================================================

    function sideBarBreadCrumb(path) {
        if (typeof path === 'boolean') return;

        lastSideBarPath = path;
        helpers.setNewPath(path);

        updateBreadcrumbs(path,'#sidebarbreadcrumbs');
    }


    function showHideCopyMsg()
    {
        if (helpers.newPath == helpers.currentActivePath)
        {
            copyMsg.show();
            sidebarActionBtn.attr('disabled','disabled');
        }
        else {
            copyMsg.hide();
            sidebarActionBtn.removeAttr('disabled');
        }
    }

    function appendData(data,path='',isInitial=false){
        let isBool = typeof path === 'boolean';
        // console.log('typeof path: ', isBool)

        helpers.appendFolders(data.folders || [], isInitial);
        helpers.appendFiles(data.files || [], isInitial);

        if(path!='' && !isBool){
            updateBreadcrumbs(path);
        } else {
            sideBarBreadCrumb(path);
        }

        reInitlizeScripts();

    }



    function reInitlizeScripts()
    {
        initFeather(); // Reinitialize Feather icons
        // reloadScript(); // reloadScript for events on appended elements
    }




        export function initFeather()
        {
            feather.replace({
                        width: 14,
                        height: 14
            });

                        // Initialize Bootstrap tooltips
            const tooltipTriggerList = document.querySelectorAll('[title]');
            const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
        }


        $('#folders-container').on('click', '.folder', function (event) {
                // Prevent click if the target is inside .form-check or .dropdown
                if ($(event.target).closest('.form-check').length || $(event.target).hasClass('dropdown-item') || $(event.target).closest('.dropdown').length || $('.file-manager-item').find('.form-check-input:checked').length) {
                    return;
                }

            const path = $(this).data('path');

            // if(contactId==''){
            helpers.setCurrentActivePath(path);
            localStorage.setItem('currentPath', path);
            // }

            // Clear previous content and load the new folder data
            fetchFolderData(path, true); // Fetch data based on clicked folder path
        });



        //------------------------------------- Create Folder------------------------------------------------------
            let btnCreateFolder = '#new-folder-modal .btn-create';
            let modalInput = '#new-folder-modal #item-name';

            $('.newFolder').on('click', function () {
                $(btnCreateFolder).attr('data-action', $(this).data('action')).text('Create');
                $('#new-folder-modal .modal-title').text('New Folder');
                $(modalInput).val('').attr('placeholder', 'Enter folder name');
                // $('#new-folder-modal').modal('show'); // Show the modal
            })

        $(btnCreateFolder).on('click', function () {

            const $this = $(this);

            $this.attr('disabled','disabled');
            // Get the folder name from the input field
            const newName = $(modalInput).val().trim();

            if (!newName) {
                $this.removeAttr('disabled');
                // Show an alert if the folder name is empty
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please enter a valid name!',
                });
                return;
            }



            let action = $this.attr('data-action');

            if (action === 'rename') {

                const itemData = JSON.parse($this.attr('data-item')); // Retrieve item data for rename
                // console.log('itemData:', itemData);

                if (!itemData) {
                    // console.error('No item data found for renaming.');
                    toastr.warning('No item data found for renaming.');
                    return;
                }

                const fromPath = itemData.path;
                const oldName = itemData.name;

                // Check if the new name is the same as the current name
                if (newName === oldName) {
                    $this.removeAttr('disabled');
                    toastr.warning('The new name is the same as the current name.');
                    return;
                }




                // console.log('fromPath:', fromPath);
                // Trigger rename action
                helpers.processBatchAction('rename', {
                    entries: [
                        {
                            from_path: fromPath,
                            to_path: fromPath.replace(oldName, newName),
                        },
                    ],
                });

                $('#new-folder-modal').modal('hide'); // Close modal
            }
            else
            {
                let path = action=='normal' ? helpers.currentActivePath  : lastSideBarPath;
                let appendtomove = (action!='normal');
                // console.log(action);
                createDropboxFolder(path, newName, appendtomove);
            }

            $this.removeAttr('disabled'); // TODO: test it
        });


        function createDropboxFolder(currentPath, folderName,appendToMoveBox=false) {
            const url = routes.createFolderUrl; // Endpoint for creating folder

            const data = {
                // path: currentPath + folderName
                path : currentPath.endsWith('/') ? currentPath + folderName : currentPath + '/' + folderName // Full path including parent folder and new folder name
            };

            //Todo: use executeBatchAction method code in helper.js instead of this separeate axios
            helpers.axiosInstance.post(url, data)
                .then(response => {
                     $(btnCreateFolder).removeAttr('disabled');
                    if (response.data.success) {
                        $('#new-folder-modal input').val('');
                        // Success notification using Toastr
                        toastr.success('Folder created successfully!', 'Success');

                        // Get the metadata of the newly created folder
                        const folderMetadata = response.data.folderMetadata;

                        //------------ also update in cachedData data for this path -----------------

                             // Add the new folder to the folders array in folders cachedData
                            // cachedData.folders.push(folderMetadata);
                        //---------------------------------------------------------------------------
                        if(appendToMoveBox){
                            setMoveFolder([folderMetadata],false,true);
                            if(lastSideBarPath == helpers.currentActivePath){
                                helpers.appendFolders([folderMetadata]);
                            }
                        }else{
                            helpers.appendFolders([folderMetadata]);
                        }
                        reInitlizeScripts();

                    } else {
                        // Error notification using Toastr
                        toastr.error('Error creating folder: ' + response.data.error, 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error(error.response.data.error ?? 'There was an error creating the folder. Please try again.');

                    // Show a SweetAlert error if something went wrong
                    Swal.fire({
                        icon: 'warning',
                        title: 'Permission denied!',
                        text: error.response?.data?.error ?? 'There was an error creating the folder. Please try again.',
                    });
                });
        }


//--------------------------------------------------------------------------------------------


        $(function () {
            // let isLoading = false;

            // Initial Load
            // if (contactId.trim() == '') {
            //     helpers.setCurrentActivePath(localStorage.getItem('currentPath') || baseRoot);
            // }else{
            //     helpers.setCurrentActivePath(baseRoot);
            // }

            if (assertRoleUser() || contactId) {
                helpers.setCurrentActivePath(baseRoot);
            }else{
                helpers.setCurrentActivePath(localStorage.getItem('currentPath') || baseRoot);
            }

            fetchFolderData(helpers.currentActivePath, true);

            $('body').on('show.bs.modal show.bs.dropdown', '#app-file-manager-move-sidebar, #addNewFile, #new-folder-modal', function (e) {
                if (!helpers.hasPermission(helpers.currentActivePath)) {
                    // alert('Permission Denied - Blocking Action');
                    e.preventDefault(); // Prevent modal/dropdown from opening
                    return false;
                }
            });
        });
