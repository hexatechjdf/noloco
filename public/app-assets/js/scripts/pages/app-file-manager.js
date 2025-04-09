/*=========================================================================================
    File Name: app-file-manager.js
    Description: app-file-manager js
==========================================================================================*/
console.log('start of app-file-manger.js');
import * as helpers from '../helper.js';

$(function () {
    'use strict';



    var sidebarFileManager = $('.sidebar-file-manager'),
        sidebarToggler = $('.sidebar-toggle'),
        fileManagerOverlay = $('.body-content-overlay'),
        filesTreeView = $('.my-drive'),
        sidebarRight = $('.right-sidebar'),
        viewContainer = $('.view-container'),
        noResult = $('.no-result'),
        viewToggle = $('.view-toggle'),
        sidebarMenuList = $('.sidebar-list'),
        fileContentBody = $('.file-manager-content-body');






    // Select File
        // if (hasPermission()) {

        $('body').on('change', '.form-check-input', function () {
            var $this = $(this);
            let fileActions = $('.file-actions');
            let parent = $this.closest('.file, .folder');
            if ($this.is(':checked')) {
                parent.addClass('selected');
            } else {
                parent.removeClass('selected');
            }
            if ($('.file-manager-item').find('.form-check-input:checked').length) {
                fileActions.addClass('show');
            } else {
                fileActions.removeClass('show');
            }
        });

    // Toggle View
    if (viewToggle.length) {
        viewToggle.find('input').on('change', function () {
            var input = $(this);
            viewContainer.each(function () {
                if (!$(this).hasClass('view-container-static')) {
                    if (input.is(':checked') && input.data('view') === 'list') {
                        $(this).addClass('list-view');
                    } else {
                        $(this).removeClass('list-view');
                    }
                }
            });
        });
    }

    // Filter
    // if (filterInput.length) {
        $('.files-filter').on('keyup', function () {
            var value = $(this).val().toLowerCase();

            $('.file-manager-item').filter(function () {
                var $this = $(this);

                if (value.length) {
                    $this.closest('.file, .folder').toggle(-1 < $this.text().toLowerCase().indexOf(value));
                    $.each(viewContainer, function () {
                        var $this = $(this);
                        if ($this.find('.file:visible, .folder:visible').length === 0) {
                            $this.find('.no-result').removeClass('d-none').addClass('d-flex');
                        } else {
                            $this.find('.no-result').addClass('d-none').removeClass('d-flex');
                        }
                    });
                } else {
                    $this.closest('.file, .folder').show();
                    noResult.addClass('d-none').removeClass('d-flex');
                }
            });
        });
    // }

    // sidebar file manager list scrollbar
    if ($(sidebarMenuList).length > 0) {
        var sidebarLeftList = new PerfectScrollbar(sidebarMenuList[0], {
            suppressScrollX: true
        });
    }

    if ($(fileContentBody).length > 0) {
        var rightContentWrapper = new PerfectScrollbar(fileContentBody[0], {
            cancelable: true,
            wheelPropagation: false
        });
    }

    // Files Treeview
    if (filesTreeView.length) {
        filesTreeView.jstree({
            core: {
                themes: {
                    dots: false
                },
                data: [
                    {
                        text: 'My Drive',
                        children: [
                            {
                                text: 'photos',
                                children: [
                                    {
                                        text: 'image-1.jpg',
                                        type: 'jpg'
                                    },
                                    {
                                        text: 'image-2.jpg',
                                        type: 'jpg'
                                    }
                                ]
                            }
                        ]
                    }
                ]
            },
            plugins: ['types'],
            types: {
                default: {
                    icon: 'far fa-folder font-medium-1'
                },
                jpg: {
                    icon: 'far fa-file-image text-info font-medium-1'
                }
            }
        });
    }

    // click event for show sidebar
    sidebarToggler.on('click', function () {
        sidebarFileManager.toggleClass('show');
        fileManagerOverlay.toggleClass('show');
    });

    // remove sidebar
    $('.body-content-overlay, .sidebar-close-icon').on('click', function () {
        sidebarFileManager.removeClass('show');
        fileManagerOverlay.removeClass('show');
        sidebarRight.removeClass('show');
    });

    // on screen Resize remove .show from overlay and sidebar
    $(window).on('resize', function () {
        if ($(window).width() > 768) {
            if (fileManagerOverlay.hasClass('show')) {
                sidebarFileManager.removeClass('show');
                fileManagerOverlay.removeClass('show');
                sidebarRight.removeClass('show');
            }
        }
    });

    // making active to list item in links on click
    sidebarMenuList.find('.list-group a').on('click', function () {
        if (sidebarMenuList.find('.list-group a').hasClass('active')) {
            sidebarMenuList.find('.list-group a').removeClass('active');
        }
        $(this).addClass('active');
    });



    // Handle DataProcessing
    function handleDataProcessing(action, itemData) {

        // console.log(action,itemData);

            //TODO: maybe moe these variables into copyItems function
            const fromPath = itemData.path; // Get the current path
            const name = itemData.name; // Get the item name


            if (!helpers.hasPermission(helpers.currentActivePath)) { // Stop execution if No permisssion
                return;
        }


         if (!assertValidAction(action))  return; // Stop execution if the action is invalid


        if (action == 'copy-self') {

            let payload = {
                entries: [
                    {
                        from_path: fromPath,
                        to_path: fromPath //+ ' (Copy)'
                    }
                ]
            };

            helpers.processBatchAction(action, payload);
        }

        if (action == 'rename')
        {
            console.log(action, itemData);
            let type = itemData.type || '';
            // Populate modal for renaming
            $('#item-name').val(name).attr('placeholder', `Enter ${type} name`); // Set the current name in the input
            $('#new-folder-modal .modal-title').text(`Rename ${type}`); // Update modal title

            let btnCreateFolder = '#new-folder-modal .btn-create';
            $(btnCreateFolder)
            .text('Rename')
            .attr('data-action', action)
            .attr('data-item', JSON.stringify(itemData)); // Attach item data to the button

             $('#new-folder-modal').modal('show'); // Show the modal

            // payload.entries.to_path = fromPath + newName;
            // alert(action); return;
            //  processBatchAction(action, payload);
        }
    }


    function getEntries(action,newPath)
    {
        const entries = appFileManager.getSelectedItems().map(function () {

            const item = $(this); // Current selected item
            const fromPath = item.data('path');
            const name = item.data('name');
            const type = item.data('type');

            // const parentPath = fromPath.substring(0, fromPath.lastIndexOf('/'));
            // const toPath = `${parentPath}/Copy of ${name}`;

            let entry;

            switch (action) {
                case 'delete':
                    entry = { path: fromPath };
                    break;
                case 'download':
                    entry = { path: fromPath, type };
                    break;
                case 'copy':
                case 'move':
                    entry = {
                        from_path: fromPath,
                        to_path: `${newPath}/${name}`.replace(/\/+/g, '/')
                    };
                    break;
                default:
                    return;
            }

            return entry ?? false;

        }).get();   //.filter(Boolean);

        return entries;
    }


    function assertValidAction(action)
    {
        if (!['copy', 'move', 'delete', 'download', 'copy-self', 'rename', 'preview'].includes(action)) {
            toastr.error('Invalid action specified.');
            return;
        }

        return true;
    }

    // Handle sidebar action button click, Handle click events for file action icons (download, delete, etc.)

    $('body').off('click').on('click', '#sidebar-action-btn, .file-actions > i, .file-actions > svg', handleActionButtonClick);

    function handleActionButtonClick() {

        if (!helpers.hasPermission(helpers.currentActivePath)) return;

        let action = $(this).attr('data-action');

        const entries = getEntries(action, helpers.newPath);

        if (entries.length === 0) {
            toastr.warning('No items selected for processing.');
            return;
        }

         if (!assertValidAction(action))  return; // Stop execution if the action is invalid

        helpers.processBatchAction(action, { entries });
    }

  // Toggle Dropdown
    $('body').on('click', '.file-logo-wrapper .dropdown', function (e) {
        e.preventDefault();
            //TODO: issue when second time get data attr for rename check it
                const $this = $(this);
                const actionBox = $this.closest('.actionBox');

                $('.view-container').find('.file-dropdown').remove();
                if ($this.closest('.dropdown').find('.dropdown-menu').length === 0) {
                    let type = actionBox.data('type');
                    let cloneDrop = $('.file-dropdown') //fileDropdown
                        .clone();

                        // if (type == 'folder') {
                            cloneDrop.find('.preview').remove();
                        // }

                        $.each(actionBox.data(), function(key, value) {
                            cloneDrop.attr('data-' + key, value);
                        });

                    cloneDrop.appendTo($this.closest('.dropdown'))
                        .addClass('show');

                    // .find('.dropdown-item')
                    // .on('click', function () {
                    //     e.preventDefault();
                    //     e.stopPropagation();
                    //     let action = $(this).data('action');
                    //     let data =  $(this).closest('.dropdown-menu').data();
                    //     handleDataProcessing(action, data);

                    // //$(this).closest('.dropdown-menu').remove();
                    // });
                }
    });


        // Use event delegation for dynamically added dropdown items
        $('body').on('click', '.file-dropdown .dropdown-item', function (e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent bubbling to parent elements

            let action = $(this).data('action');
            let data = $(this).closest('.dropdown-menu').data();
            handleDataProcessing(action, data);
        });


        $(document).on('click', function (e) {
            if (!$(e.target).hasClass('toggle-dropdown')) {
                // filesWrapper
                $('.file-manager-main-content').find('.file-dropdown').remove();
        }
        });

        if (viewContainer.length) {
            $('.file, .folder').on('mouseleave', function () {
                $(this).find('.file-dropdown').remove();
            });
        }

});
