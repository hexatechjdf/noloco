
'use strict';

import { ssoToken, ssoData } from './sso.js';
import { initFeather } from './dropbox-integration.js';


        export const getFolderContainer = () => $('#folders-container');
        export const getFileContainer = () => $('#files-container');


        export let baseRoot = appConfig.baseRoot;
        export let newPath = baseRoot;
        export let currentActivePath = '';

        // export function setBaseRootPath(path) {
        //     baseRoot = path;
        // }

        export function setNewPath(path) {
            newPath = path;
        }

        export function setCurrentActivePath(path) {
            currentActivePath = path;
        }


        export function appendFolders(folders, isInitial=false)
        {
            // const noResult = container.find('.no-result');
            if (isInitial) $('#folders-container').prepend(`<h6 class="files-section-title mt-25 mb-75">Folders</h6>`);

            if (folders && folders.length > 0) {
                 $('#folders-container .no-result').remove();
                // noResult.addClass('d-none');



                folders.forEach((folder, index) => {
                    let fId = folder.id||"";
                    let path =folder.path_display;
                    const folderCard = `
                        <div class="card file-manager-item folder actionBox" data-name="${folder.name}" data-type="folder" data-folder-id="${fId}"  data-id="${fId}"  data-path="${path}">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" data-type="folder" id="folderCheck${fId}" />
                                <label class="form-check-label" for="folderCheck${fId}"></label>
                            </div>
                            <div class="card-img-top file-logo-wrapper">
                                <div class="dropdown float-end" >
                                    <i data-feather="more-vertical" class="toggle-dropdown mt-n25" title="More actions"></i>
                                </div>
                                <div class="d-flex align-items-center justify-content-center w-100">
                                    <i data-feather="folder"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="content-wrapper">
                                    <p class="card-text file-name mb-0">${folder.name}</p>
                                </div>
                            </div>
                        </div>`;
                    // $('#folders-container').append(folderCard);
                    getFolderContainer().append(folderCard);
                });
            }
            else {
                if (isInitial) {
                    getFolderContainer().append(nofoundHtml('No Folders Found'));
                }

                // noResult.removeClass('d-none');

            }
        }


        // export const fileContainer = $('#files-container');

        export function appendFiles(files, isInitial=false)
        {
            if (isInitial) $('#files-container').prepend(`<h6 class="files-section-title mt-25 mb-75">Files</h6>`);

            if (files && files.length > 0) {

                $('#files-container .no-result').remove();

                files.forEach((file, index) => {
                    let ext  = file.name.split('.');
                    const extension = ext.length>1 ? ext[ext.length-1] : 'unknown';
                    let path = file.path_display;
                    let fId = file.id;
                    const fileCard = `
                        <div class="card file-manager-item file actionBox" data-name="${file.name}" data-path="${path}" data-file-id="${fId}" data-id="${fId}" data-type="file">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="fileCheck${fId}" />
                                <label class="form-check-label" for="fileCheck${fId}"></label>
                            </div>
                            <div class="card-img-top file-logo-wrapper">
                                <div class="dropdown float-end"  ">
                                    <i data-feather="more-vertical" class="toggle-dropdown mt-n25" title="More actions"></i>
                                </div>
                                <div class="d-flex align-items-center justify-content-center w-100">
                                    <img src="/app-assets/images/icons/${extension}.png" alt="file-icon" height="35" onerror="this.onerror=null; this.src='https://placehold.co/32x32?text=${extension}';" />
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="content-wrapper">
                                    <p class="card-text file-name mb-0">${file.name}</p>
                                </div>
                            </div>
                        </div>`;

                    getFileContainer().append(fileCard);
                });
            }
            else{
                if(isInitial){
                    getFileContainer().append(nofoundHtml('No Files Found'));
                }
            }
        }



        export function nofoundHtml(msg)
        {
            return `<div class="d-flex flex-grow-1 justify-content-center align-items-center no-result mb-3">
                        <i data-feather="alert-circle" class="me-50"></i>
                            ${msg}
                    </div>`;
        }

        export function loadingHtml(msg='Loading ...')
        {
            return `<div class="loading"><i class="fa fa-spinner fa-spin"></i>${msg}</div>`;
        }



export function processBatchAction(action, payload) {




        // const actionMessages = {
        //     copy: 'copy',
        //     'copy-self': 'Are you sure you want to copy (self) the selected item?',
        //     move: 'move',
        //     delete: 'Are you sure you want to ${action} the selected items?',
        // };

        // console.log('processBatchAction:', payload); return;
        const urls = {
            'copy-self': routes.copyBatchUrl,
            copy: routes.copyBatchUrl,
            move: routes.moveBatchUrl,
            rename: routes.moveBatchUrl,
            delete: routes.deleteBatchUrl,
            download: routes.downloadBatchUrl,
        };



        const url = urls[action];
        console.log(action, url, payload);

        if (!url) {
            return toastr.error('API endpoint not found for the action.');
        }

        // Confirm delete, 'rename' and copy-self actions before proceeding

        if (['copy-self', 'rename', 'delete', 'download'].includes(action)) {
            let itemCount = payload.entries.length ?? 0;
            let itemOrItems = `${itemCount > 1 ? 'items': 'item'}`;
            Swal.fire({
                title: `Are you sure?`,
                html: `This action will <strong>${action}</strong> ${itemCount}  selected ${itemOrItems} ${action == 'delete' ? 'permanently' : ''}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.value) {
                    executeBatchAction(action, url, payload);
                    // action === 'download' ? downloadItems(action, url, payload) :  executeBatchAction(action, url, payload);
                }
            });
        } else {
            // For other actions like move or copy, proceed directly
            executeBatchAction(action, url, payload);
        }
 }


    export const actionHandlers = {
            rename: handleRenameResponse,
            delete: handleDeleteResponse,
            download: handleDownloadResponse,
            move: handleMoveResponse,
            copy: handleCopyResponse,
            'copy-self': handleCopySelfResponse

        };

        // Execute batch action and handle the corresponding response
        export function executeBatchAction(action, url, payload) {
            axiosInstance.post(url, payload)
                .then(response => {

                    // console.log('response entries:', response.data.entries);

                    const handler = actionHandlers[action];     // Find the appropriate handler for the action

                    // console.log(action,handler)
                    if (!handler) {
                        toastr.error(`No response handler for action: ${action}`);
                    }

                    if (handler(response.data.entries) || action == 'copy') {
                        toastr.success(`Batch ${action} completed successfully.`);
                        // console.log(`Batch ${action} completed successfully.:`, response.data);
                    }
                    else {
                        toastr.success(`Batch ${action} completed successfully.`);
                        toastr.error(`Opps! some thing went wrong in handle Response.`);
                    }
                })
                .catch(error => {

                    console.error(`Error during ${action} action:`, error);
                    let title = error.response?.status == 422 ? 'Permission denied!' : 'Error';
                    handleError(action, error, title); //'Permission denied!'
                });
        }


        function handleError(action, error, title = 'Error') {
            toastr.error(error.response?.data?.error || `An error occurred while ${action}ing the items.`);

            Swal.fire({
                icon: 'warning',
                title: title,
                text: error.response?.data?.error ?? `There was an error ${action}ing the items.`,
            });
        }

        // Handle the response for renaming files or folders
        export function handleRenameResponse(response) {
            let succesTag = assertSuccessTag(response);

            if (succesTag) {
                response.forEach(item => {
                    if (item.success['.tag'] === 'file') {
                        const renamedItem = item.success;
                        updateElement(renamedItem.id, 'file', { name: renamedItem.name, path: renamedItem.path_display });
                    } else if (item.success['.tag'] === 'folder') {
                        const renamedItem = item.success;
                        updateElement(renamedItem.id, 'folder', { name: renamedItem.name, path: renamedItem.path_display });
                    }
                });
            }

            return succesTag;
        }

        function assertSuccessTag(response)
        {
            if (response && response[0]['.tag'] == 'success')
            {
                return true
            }
            else {
                return false;
            }
        }

        // Handle the response for deleting files or folders
        export function handleDeleteResponse(response) {
            let succesTag = assertSuccessTag(response);
            // console.log('response in handleDeleteResponse:', response);
            if (succesTag) {
                response.forEach(item => {
                    updateDom(item);
                });

                checkItemLengthAndAppendMsg();
            }

            return succesTag;
        }

        // Handle the response for moving files or folders
        export function handleMoveResponse(response) {

            let succesTag = assertSuccessTag(response);
            // console.log('response in handleMoveResponse',response);

            if (succesTag) {
                response.forEach(item => {
                    updateDom(item);
                });

                checkItemLengthAndAppendMsg();
            }

            return succesTag;
        }

        export function handleCopyResponse(response) {

            let succesTag = assertSuccessTag(response);

            // console.log('assertsuccesTag:',succesTag);
            // if (succesTag) {
                // let folderSideBar = $('.folderList');
                // response.forEach(item => {
                //     const copiedItem = item.success;

                //         if (item.success['.tag'] === 'folder') {
                //             const folderHtml = `
                //                 <li class="movesidebar-item card p-1 w-100" data-path="${copiedItem.path_display}">
                //                     <div class="d-flex align-items-center w-100">
                //                         <i data-feather="folder"></i>
                //                         <p class="card-text pl-2">${copiedItem.name}</p>
                //                     </div>
                //                 </li>
                //             `;

                //             folderSideBar.append(folderHtml);
                //             // sidebarRight.removeClass('show');
                //         }
                // });
            // }

            return succesTag;
        }

        // Handle the response for copying files or folders (self-copy)
        export function handleCopySelfResponse(response) {

            let succesTag = assertSuccessTag(response);
            console.log('assertsuccesTag:',succesTag);
            if (succesTag) {
                response.forEach(item => {
                        const copiedItem = item.success;

                        if (item.success['.tag'] === 'file') {
                            appendFiles([copiedItem]);
                        } else if (item.success['.tag'] === 'folder') {
                            appendFolders([copiedItem]);
                        }
                });

                initFeather();
            }

            return succesTag;
        }

        // Helper function to find and update the DOM element based on item ID
        export function updateElement(itemId, itemType, updates) {
            const element = $(`.file-manager-item[data-id="${itemId}"][data-type="${itemType}"]`);
            if (element.length) {
                Object.keys(updates).forEach(key => {
                    if (key === 'name') {
                        element.find('.file-name').text(updates[key]);
                    }
                    // else {
                    element.data(key, updates[key]); // Todo: teest its
                     element.attr(`data-${key}`, updates[key]); // may be don.t need this to updade in dom

                    // }
                });
            } else {
                console.warn(`${itemType.charAt(0).toUpperCase() + itemType.slice(1)} element with ID ${itemId} not found.`);
                toastr.warn(`${itemType.charAt(0).toUpperCase() + itemType.slice(1)} element with ID ${itemId} not found.`);
                toastr.warn(`Please hard Refresh and try again.`);
            }
        }

        function updateDom(item)
        {
            let itemMetaData = item.metadata ?? item.success ?? null;

            if (!itemMetaData) return;

            // console.log('inside UpdateDom:', itemMetaData);

            let type = itemMetaData['.tag'] ?? 'noitem';

            if (type === 'file') {
                const fileId = itemMetaData.id;
                const fileElement = $(`.file-manager-item[data-id="${fileId}"][data-type="file"]`);

                if (fileElement.length > 0) { //TODO: Testing;
                    fileElement.remove();  // Remove the file from the DOM
                } else {
                    // console.warn(`File element with ID ${fileId} not found for deletion.`);
                    toastr.warn(`File element with ID ${fileId} not found for deletion.`);
                    toastr.warn(`Please hard Refresh and try again.`);
                }

            }
            else if (type === 'folder') {
                const folderId = itemMetaData.id;
                const folderElement = $(`.file-manager-item[data-id="${folderId}"][data-type="folder"]`);

                if (folderElement.length) {
                    folderElement.remove();  // Remove the folder from the DOM
                } else {// TODO: testing.
                    // console.warn(`Folder element with ID ${folderId} not found for deletion.`);
                    toastr.warn(`Folder element with ID ${folderId} not found for deletion.`);
                    toastr.warn(`Please hard Refresh and try again.`);
                }
            }

        }


        // Handle the response for download files or folders
        function handleDownloadResponse(response) {

            // console.log('link rspon:', response);
            toastr.success("Downloading...");

            response.forEach((item, index) => {
                // console.log('item', item);

                setTimeout(() => {
                        let a = document.createElement('a');
                        a.href = item.link;
                        a.download = item.name; // Sets correct filename
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    }, index * 900)
                });

            return true; // TODO
        }



        // Create Axios instance
        export const axiosInstance = axios.create({
            // baseURL: 'https://abc.com',
            headers: {
                'Content-Type': 'application/json'
            }
        });


        export function setAjaxHeaders(ssoToken){
            try {
                $.ajaxSetup({
                    beforeSend: function(jqxhr) {


                        if (ssoToken) {
                            console.log(ssoToken,'done');
                            jqxhr.setRequestHeader("ghlAuthorization", ssoToken);
                        }
                    }
                });
            } catch (error) {
                setTimeout(setAjaxHeaders,1000,ssoToken);
            }
        }
        setAjaxHeaders(ssoToken);

        // Axios request interceptor to dynamically add SSO token
        axiosInstance.interceptors.request.use(async (config) => {
                if (ssoToken) {
                    config.headers["ghlAuthorization"] = ssoToken; // Set token in headers
                }
            return config;
        }, (error) => Promise.reject(error));



        // // Function to set GHL Authorization token dynamically
        // export function setGhlAuthorization(token) {
        //     axiosInstance.defaults.headers.common['ghlAuthorization'] = token;
// }


// *===================================== has Permisssion =====================================*

        export function hasPermission(path, swAlert = true)
        {
            if (assertRoleUserAndRootPath(path))
            {
                if (swAlert)
                {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Permisssion Denied',
                        text: 'No permission to perform this action in the root directory.',
                    });
                }

                return false;
            }

            return true;
        }

        export function assertRoleUserAndRootPath(path)
        {
            return assertRoleUser() && path === baseRoot;
        }

        export function assertRoleUser()
        {
            return ssoData.role.toLowerCase() === 'user';
        }


//============================================================================================


    function checkItemLengthAndAppendMsg()
    {
        const files = $(`#files-container .file-manager-item`);
        const folders = $(`#folders-container .file-manager-item`);

        if (!files.length)
        {
            let fileContainer = getFileContainer();
            if (!fileContainer.find('.no-result').length) {
                fileContainer.append(nofoundHtml('No Files Found'));
            }
        }

        if (!folders.length)
        {
            let folderContainer = getFolderContainer();

            if (!folderContainer.find('.no-result').length) {
                folderContainer.append(nofoundHtml('No Folders Found'));
            }
        }

        initFeather();
    }
