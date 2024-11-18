function openEditLinkDialog(data) {
    //override joomla action for modal close
    window.jModalClose = function () {};
    window.jSelectArticle = function () {};
    (function($) {
        var editorFrame = $('#editor-frame')[0].contentWindow;

        function selectItemByUrl(list, url) {
            var matchUrl = function (index, element) {
                return $(element).children('a').attr('href') === url;
            }
            var listItems = list.find('li'),
                matchItem = listItems.filter(matchUrl);
            if (matchItem.hasClass('selected')) {
                return;
            }
            listItems.removeClass('selected');
            matchItem.addClass('selected');
        }

        function buildPageOptions(dialog, options) {
            if (options && options.url) {
                $('.url-option input', dialog).val(options.url);
                $('#page-link', dialog).prop('checked', true);
            }
            dialog.find('#adminForm').removeClass('hidden');
            dialog.find('.url-option, .target-option').removeClass('hidden');

            // hide other options
            dialog.find('#anchors-list').addClass('hidden');
            dialog.find('#files-list').addClass('hidden');
            dialog.find('#dialogs-list').addClass('hidden');
            dialog.find('#category-list').addClass('hidden');
            dialog.find('#products-list').addClass('hidden');
            dialog.find('.phone-option').addClass('hidden');
            dialog.find('.email-option, .email-subject-option').addClass('hidden');
            dialog.find('.list-container').addClass('hidden');
            dialog.find('.page-option').addClass('hidden');
        }

        function buildBlogOptions(dialog, options) {
            if (options && options.url) {
                $('.url-option input', dialog).val(options.url);
                $('#blog-link', dialog).prop('checked', true);
            }
            var urlField = dialog.find('.url-option input'),
                url = urlField.val(),
                categoryList = dialog.find('#category-list');
            categoryList.removeClass('hidden');
            selectItemByUrl(categoryList, url);
            dialog.find('.url-option, .target-option').removeClass('hidden');
            dialog.find('.list-container').removeClass('hidden');

            // hide other options
            dialog.find('#anchors-list').addClass('hidden');
            dialog.find('#files-list').addClass('hidden');
            dialog.find('#dialogs-list').addClass('hidden');
            dialog.find('#products-list').addClass('hidden');
            dialog.find('.phone-option').addClass('hidden');
            dialog.find('.email-option, .email-subject-option').addClass('hidden');
            dialog.find('.page-option').addClass('hidden');
        }

        function buildAnchorsOptions(dialog, options) {
            if (options && options.url) {
                $('.url-option input', dialog).val(options.url);
                $('#anchor-link', dialog).prop('checked', true);
            }
            var urlField = dialog.find('.url-option input'),
                url = urlField.val(),
                anchorsList = dialog.find('#anchors-list');
            anchorsList.removeClass('hidden');

            dialog.find('.url-option, .target-option').removeClass('hidden');
            dialog.find('.list-container').removeClass('hidden');
            if (data.isMenu) {
                dialog.find('.page-option').addClass('hidden');
            } else {
                dialog.find('.page-option').removeClass('hidden');
            }

            // hide other options
            dialog.find('#adminForm').addClass('hidden');
            dialog.find('#files-list').addClass('hidden');
            dialog.find('#dialogs-list').addClass('hidden');
            dialog.find('#products-list').addClass('hidden');
            dialog.find('#category-list').addClass('hidden');
            dialog.find('.phone-option').addClass('hidden');
            dialog.find('.email-option, .email-subject-option').addClass('hidden');
        }

        function buildFilesOptions(dialog, options) {
            if (options && options.url) {
                $('.url-option input', dialog).val(options.url);
                $('#file-link', dialog).prop('checked', true);
            }
            var filesList = dialog.find('#files-list');
            filesList.removeClass('hidden');
            dialog.find('.url-option, .target-option').removeClass('hidden');
            dialog.find('.list-container').removeClass('hidden');

            // hide other options
            dialog.find('#adminForm').addClass('hidden');
            dialog.find('#anchors-list').addClass('hidden');
            dialog.find('.phone-option').addClass('hidden');
            dialog.find('.email-option, .email-subject-option').addClass('hidden');
            dialog.find('.page-option').addClass('hidden');
        }

        function buildPhoneOptions(dialog, options) {
            if (options && options.url) {
                $('#phone-link', dialog).prop('checked', true);
                $('.phone-option input', dialog).val(options.url.replace('tel:', ''));
            }
            dialog.find('.phone-option').removeClass('hidden');
            dialog.find('.url-option, .target-option').removeClass('hidden');

            // hide other options
            dialog.find('#adminForm').addClass('hidden');
            dialog.find('#anchors-list').addClass('hidden');
            dialog.find('#files-list').addClass('hidden');
            dialog.find('#dialogs-list').addClass('hidden');
            dialog.find('#products-list').addClass('hidden');
            dialog.find('#category-list').addClass('hidden');
            dialog.find('.email-option, .email-subject-option').addClass('hidden');
            dialog.find('.url-option, .target-option').addClass('hidden');
            dialog.find('.list-container').addClass('hidden');
            dialog.find('.page-option').addClass('hidden');
        }

        function buildEmailOptions(dialog, options) {
            if (options && options.url) {
                $('#email-link', dialog).prop('checked', true);
                var emailParts = options.url.replace('mailto:', '').split('?subject=');
                if (emailParts.length) {
                    $('.email-option input', dialog).val(emailParts[0]);
                    $('.email-subject-option input', dialog).val(emailParts[1] || '');
                }
            }
            dialog.find('.email-option, .email-subject-option').removeClass('hidden');
            // hide other options
            dialog.find('#adminForm').addClass('hidden');
            dialog.find('#anchors-list').addClass('hidden');
            dialog.find('#files-list').addClass('hidden');
            dialog.find('#products-list').addClass('hidden');
            dialog.find('#dialogs-list').addClass('hidden');
            dialog.find('#category-list').addClass('hidden');
            dialog.find('.phone-option').addClass('hidden');
            dialog.find('.url-option, .target-option').addClass('hidden');
            dialog.find('.list-container').addClass('hidden');
            dialog.find('.page-option').addClass('hidden');
        }

        function buildDialogsOptions(dialog, options) {
            if (options && options.url) {
                $('.url-option input', dialog).val(options.url);
                $('#dialog-link', dialog).prop('checked', true);
            }
            var urlField = dialog.find('.url-option input'),
                url = urlField.val(),
                dialogsList = dialog.find('#dialogs-list');
            dialogsList.removeClass('hidden');
            selectItemByUrl(dialogsList, url);
            dialog.find('.list-container').removeClass('hidden');

            // hide other options
            dialog.find('#adminForm').addClass('hidden');
            dialog.find('#files-list').addClass('hidden');
            dialog.find('#products-list').addClass('hidden');
            dialog.find('#category-list').addClass('hidden');
            dialog.find('#anchors-list').addClass('hidden');
            dialog.find('.phone-option').addClass('hidden');
            dialog.find('.email-option, .email-subject-option').addClass('hidden');
            dialog.find('.page-option').addClass('hidden');
        }

        function buildProductsOptions(dialog, options) {
            if (options && options.url) {
                $('.url-option input', dialog).val(options.url);
                $('#product-link', dialog).prop('checked', true);
            }
            var urlField = dialog.find('.url-option input'),
                url = urlField.val(),
                productsList = dialog.find('#products-list');
            productsList.removeClass('hidden');
            selectItemByUrl(productsList, url);
            dialog.find('.url-option, .target-option').removeClass('hidden');
            dialog.find('.list-container').removeClass('hidden');

            // hide other options
            dialog.find('#adminForm').addClass('hidden');
            dialog.find('#files-list').addClass('hidden');
            dialog.find('#dialogs-list').addClass('hidden');
            dialog.find('#category-list').addClass('hidden');
            dialog.find('#anchors-list').addClass('hidden');
            dialog.find('.phone-option').addClass('hidden');
            dialog.find('.email-option, .email-subject-option').addClass('hidden');
            dialog.find('.url-option, .target-option').addClass('hidden');
            dialog.find('.page-option').addClass('hidden');
        }

        function handleListItemClick(event) {
            event.preventDefault();
            var listItem = $(event.target),
                list;
            if (listItem.is('li')) {
                listItem = listItem.children();
            }
            if (!listItem.is('.item-link') || listItem.length !== 1) {
                return;
            }
            list = listItem.closest('ul');
            var value = listItem.attr('href'),
                customUrlOptions = listItem.closest('body').find('.custom-url-options'),
                urlField = customUrlOptions.find('.url-option input');
            urlField.val(value ? value : '');
            var isProductList = event.target.closest('.products-list');
            if (isProductList) {
                customUrlOptions.find('.caption-option input').val(listItem.text());
            }
            selectItemByUrl(list, value);
        }

        function handleLinkDestinationChange(event) {
            var target = $(event.target),
                customUrlOptions = target.closest('body').find('.custom-url-options'),
                dialog = customUrlOptions.parent(),
                value = target.attr('value');
            switch (value) {
                case 'page':
                    buildPageOptions(dialog);
                    break;
                case 'blog':
                    buildBlogOptions(dialog);
                    break;
                case 'section':
                    buildAnchorsOptions(dialog);
                    break;
                case 'file':
                    buildFilesOptions(dialog);
                    break;
                case 'phone':
                    buildPhoneOptions(dialog);
                    break;
                case 'email':
                    buildEmailOptions(dialog);
                    break;
                case 'dialog':
                    buildDialogsOptions(dialog);
                    break;
                case 'product':
                    buildProductsOptions(dialog);
                    break;
                default:
            }
            window.dataForDialog.customizeLinkType = value;
        }

        function createListItem(item) {
            if (!item) {
                return item;
            }
            var result = $('<li><a class="item-link" href=""></a></li>');
            result.children('.item-link').attr('href', item.url);
            result.children('.item-link').text(item.title);
            return result;
        }

        function appendItemLinks(list, items, isPrepend) {
            if (!items || !items.length) {
                return;
            }

            items = items.filter(function (item) {
                return !$('.item-link[href="' + item.url + '"]', list).length;
            });

            var listItems = $.map(items, createListItem);
            $.each(listItems, function (i, item) {
                if (isPrepend) {
                    list.prepend(item);
                } else {
                    list.append(item);
                }
            });
        }

        function buildData(ifrDoc) {
            var result = {
                value: $('.caption-option input', ifrDoc).val(),
                url: '',
                blank: !!$('.target-option input', ifrDoc).is(":checked")
            };
            var linkType = $('.link-destination input[type=radio]:checked', ifrDoc).val();
            switch(linkType) {
                case 'page':
                case 'blog':
                case 'section':
                case 'dialog':
                case 'product':
                case 'file':
                    if (linkType === 'file' || linkType === 'dialog') {
                        result.destination = linkType;
                    }
                    result.url = $('.url-option input', ifrDoc).val();
                    break;
                case 'phone':
                    result.url = 'tel:' + $('.phone-option input', ifrDoc).val();
                    break;
                case 'email':
                    result.url = 'mailto:' + $('.email-option input', ifrDoc).val();
                    var subject = $('.email-subject-option input', ifrDoc).val();
                    if (subject) {
                        result.url += '?subject=' + subject;
                    }
                    break;
                default:
            }

            var urlPart = (result.url || '').match(/index.php\?option=com_content&view=article&id=\d+/);
            if (dataBridge && dataBridge.getSite && urlPart) {
                var site = dataBridge.getSite() || {};
                var items = site.items || [];
                var filteredItems = items.filter(function (item) {
                        return item.publicUrl && item.publicUrl.indexOf(urlPart[0]) !== -1;
                    }
                );
                if (filteredItems.length > 0) {
                    result.pageId = filteredItems[0].id;
                }
            }

            return result;
        }

        SqueezeBox.fromElement(window.phpVars.editLinkUrl + '&u=' + Date.now(), {
            size : {x : 900, y : 500},
            iframePreload: true,
            handler : 'iframe',
            onUpdate : function (container) {
                if (container.firstChild) {
                    $('.sbox-content-iframe').css('display', '');
                    var ifrDoc = container.firstChild.contentDocument;
                    var data = window.dataForDialog;
                    if (!$('.custom-url-options', ifrDoc).length && data) {
                        setDialogOptions(ifrDoc, data);

                        $('#save-options', ifrDoc).on('click', function() {
                            var linkType = $('.link-destination input[type=radio]:checked', ifrDoc).val();
                            if (linkType == 'email') {
                                var emailValue = $('.email-option input', ifrDoc).val();
                                var valid = /^[\w-\.]+@[\w-.]+$/i.test(emailValue);
                                if (!valid) {
                                    alert('Email is invalid');
                                    return false;
                                }
                            }
                            if (linkType == 'phone') {
                                var phoneValue = $('.phone-option input', ifrDoc).val();
                                var valid = /[^\d\s\+\-\(\)]/.test(phoneValue);
                                if (valid) {
                                    alert('Phone is invalid');
                                    return false;
                                }
                            }
                            $(ifrDoc).data('close-after-save', true);
                            editorFrame.postMessage(JSON.stringify({
                                action: 'editLinkDialogClose',
                                data: buildData(ifrDoc),
                            }), window.location.origin);
                            SqueezeBox.close();
                        });
                    }
                    var menuHomeId = (window.dataBridgeData && window.dataBridgeData.info && window.dataBridgeData.info.homeItemId) || 0;
                    var links = $('#adminForm table tbody a', ifrDoc);
                    links.each(function () {
                        var link = $(this);
                        var uriAttr = link.attr('data-uri') || link.attr('onclick');
                        var newUriAttr = uriAttr.replace(/(index.php[^,'"]+)/, '$1' + '&Itemid=' + menuHomeId);
                        link.attr('data-uri', newUriAttr);
                        link.attr('onclick', newUriAttr);
                    });
                    links.on('click', function () {
                        var uriAttr = $(this).attr('data-uri') || $(this).attr('onclick'),
                            url = uriAttr.match(/index.php[^,'"]+/),
                            text = $(this).text().trim();
                        var oldValue = ($('.caption-option input', ifrDoc).val() || '').trim();

                        var defaultContentList = [].concat(window.dataBridgeData && window.dataBridgeData.site && window.dataBridgeData.site.items && window.dataBridgeData.site.items.map(function (el) {
                            return el.title;
                        }) || [], window.dataForDialog && window.dataForDialog.defaultContentList || [], $(this).closest('.table').find('.select-link').toArray().map(function (el) {
                            return $(el).attr('data-title');
                        }));
                        if (!oldValue || defaultContentList.includes(oldValue)) {
                            $('.caption-option input', ifrDoc).val(text);
                        }
                        $('.url-option input', ifrDoc).val(url ? url[0] : '');
                    });

                    $('.anchors-list, .files-list, .dialogs-list, .products-list, .category-list', ifrDoc).on('click', handleListItemClick);
                    $('.link-destination input[type=radio]', ifrDoc).on('change', handleLinkDestinationChange);
                }
            },
            onClose : function (container) {
                if (!$(container.firstChild.contentDocument).data('close-after-save'))
                    editorFrame.postMessage(JSON.stringify({action: 'editLinkDialogClose'}), window.location.origin);
            }
        });


        function ChunkedUploader(file, callback) {
            var _file = file;
            if (_file instanceof Uint8Array) {
                _file = new Blob([_file]);
            }
            var maxChunkLength = 1024 * 1024; // 1 Mb
            var CHUNK_SIZE = parseInt(window.phpVars.maxRequestSize || maxChunkLength, 10);
            var uploadedChunkNumber = 0, allChunks;
            var fileName = (_file.name || window.createGuid()).replace(/[^A-Za-z0-9\._]/g, '');
            var fileSize = _file.size || _file.length;
            var total = Math.ceil(fileSize / CHUNK_SIZE);

            var rangeStart = 0;
            var rangeEnd = CHUNK_SIZE;
            validateRange();

            var sliceMethod;

            if ('mozSlice' in _file) {
                sliceMethod = 'mozSlice';
            }
            else if ('webkitSlice' in _file) {
                sliceMethod = 'webkitSlice';
            }
            else {
                sliceMethod = 'slice';
            }

            this.upload = upload;

            function upload() {
                var data;

                setTimeout(function () {
                    var requests = [];

                    for (var chunk = 0; chunk < total - 1; chunk++) {
                        data = _file[sliceMethod](rangeStart, rangeEnd);
                        requests.push(createChunk(data));
                        incrementRange();
                    }

                    allChunks = requests.length;

                    $.when.apply($, requests).then(
                        function success() {
                            var lastChunkData = _file[sliceMethod](rangeStart, rangeEnd);

                            createChunk(lastChunkData, {last: true})
                                .done(onUploadCompleted)
                                .fail(onUploadFailed);
                        },
                        onUploadFailed
                    );
                }, 0);
            }

            function createChunk(data, params) {
                var formData = new FormData();
                formData.append('filename', fileName);
                formData.append('chunk', new Blob([data], { type: 'application/octet-stream' }), 'blob');

                if (typeof params === 'object') {
                    for (var i in params) {
                        if (params.hasOwnProperty(i)) {
                            formData.append(i, params[i]);
                        }
                    }
                }

                var headers = {
                    'Content-Range': ('bytes ' + rangeStart + '-' + rangeEnd + '/' + fileSize)
                };
                if (rangeEnd > fileSize || (rangeStart === 0 && rangeEnd === fileSize)) {
                    headers = {};
                }
                return $.ajax({
                    url: window.phpVars.uploadFileLink,
                    data: formData,
                    type: 'POST',
                    mimeType: 'application/octet-stream',
                    processData: false,
                    contentType: false,
                    headers: headers,
                    success: onChunkCompleted,
                    error: function (xhr, status) {
                        alert('Failed  chunk saving');
                    }
                });
            }

            function validateRange() {
                if (rangeEnd > fileSize) {
                    rangeEnd = fileSize;
                }
            }

            function incrementRange() {
                rangeStart = rangeEnd;
                rangeEnd = rangeStart + CHUNK_SIZE;
                validateRange();
            }

            function onUploadCompleted(response, status, xhr) {
                callback(response);
            }

            function onUploadFailed(xhr, status) {
                alert('onUploadFailed');
            }

            function onChunkCompleted() {
                if (uploadedChunkNumber >= allChunks)
                    return;
                ++uploadedChunkNumber;
            }
        }

        function setDialogOptions(ifrDoc, data) {
            var customHtml = atob(window.phpVars.customUrlOptions).replace(/\{\{(\w+?)\}\}/g, function(str, p1) {
                return data.l[p1] || data[p1];
            });
            $('body', ifrDoc).prepend(customHtml);
            if (!data.caption) {
                $('.caption-option', ifrDoc).addClass('hidden');
            }
            $('.caption-option input', ifrDoc).val(data.caption && data.value ? data.value : '');
            $('.target-option input', ifrDoc).prop('checked', data.blank);

            $('#files-list, #dialogs-list, #products-list, #category-list', ifrDoc).empty();

            if (data.dialogList && data.dialogList.length) {
                appendItemLinks($('#dialogs-list', ifrDoc), data.dialogList);
            }

            if (data.productList && data.productList.length) {
                appendItemLinks($('#products-list', ifrDoc), data.productList);
            }

            if (window.phpVars.mediaFiles && window.phpVars.mediaFiles.length) {
                appendItemLinks($('#files-list', ifrDoc), window.phpVars.mediaFiles);
            }

            if (window.phpVars.categoryList && window.phpVars.categoryList.length) {
                appendItemLinks($('#category-list', ifrDoc), window.phpVars.categoryList);
            }

            $('.link-destination', ifrDoc).removeClass('hidden');
            if (data.isMenu) {
                $('#category-list', ifrDoc).parent().addClass('hidden');
            }

            var dialog = $(ifrDoc);
            var linkType = data.customizeLinkType ? data.customizeLinkType : data.linkType;

            var url = data.url ? data.url : '#';

            var foundCategoryList = window.phpVars.categoryList.filter(function (category) {
                return category.url === url;
            });
            if (foundCategoryList.length) {
                linkType = 'blog';
            }

            setPageDropdownActions(ifrDoc, url);
            switch (linkType) {
                case 'page':
                    buildPageOptions(dialog, {url: url});
                    break;
                case 'blog':
                    buildBlogOptions(dialog, {url: url});
                    break;
                case 'section':
                    buildAnchorsOptions(dialog, {url: url});
                    break;
                case 'file':
                    buildFilesOptions(dialog, {url: url});
                    break;
                case 'phone':
                    buildPhoneOptions(dialog, {url: url});
                    break;
                case 'email':
                    buildEmailOptions(dialog, {url: url});
                    break;
                case 'dialog':
                    buildDialogsOptions(dialog, {url: url});
                    break;
                case 'product':
                    buildProductsOptions(dialog, {url: url});
                    break;
                default:
            }
            setFileActions(ifrDoc);
        }

        function generateAnchorsListItems(doc, url, pageUrl, page) {
            var anchors = getAnchors(page);
            anchors.forEach(function (anchor) {
                anchor.url = pageUrl + anchor.url;
            });
            var anchorsList = $('#anchors-list', doc);
            anchorsList.empty();
            if (anchors && anchors.length) {
                appendItemLinks($('#anchors-list', doc), anchors);
            }
            selectItemByUrl(anchorsList, url);
        }

        function getAnchors(dom) {
            var anchors = [];
            if (dom) {
                var sectionsDom = dom.find('section:not(.u-slide)[id], .u-slider[id]');
                var sectionsDomArray = $.makeArray(sectionsDom);
                anchors = $.map(sectionsDomArray, getSectionAnchor);
            } else {
                anchors = (window.dataForDialog && window.dataForDialog.anchorsList) || [];
            }
            return anchors;
        }

        function getSectionAnchor(sectionDom, num) {
            sectionDom = $(sectionDom);
            var id = getSectionId(sectionDom);
            var title = getSectionTitle(sectionDom, id, num);
            var url = '#' + (id || '');
            return {
                title: title,
                url: url,
            };
        }

        function getSectionId(sectionDom) {
            return $(sectionDom).attr('id');
        }

        function getSectionTitle(sectionDom, sectionId, num) {
            var title = ['Block'];
            var headerText;
            var headingPriority = ['h1', 'h2', 'h3'];
            title.push(' ' + (num + 1));
            for (var i = 0; !headerText && i < headingPriority.length; i++) {
                headerText = sectionDom.find(headingPriority[i] + ':eq(0)').text().trim();
            }
            if (headerText !== '') {
                title.push(' (');
                title.push(headerText);
                title.push(')');
            }
            return title.join('');
        }

        function setPageDropdownActions(doc, linkUrl) {
            if (dataBridge && dataBridge.getSite) {
                var site = dataBridge.getSite() || {};
                var items = site.items || [];
                items.forEach(function (item) {
                    var link = $("<a>");
                    link.attr('href', item.publicUrl || '');
                    link.text(item.title);
                    link.attr('data-ajax-url', item.htmlUrl);
                    $('.a-list', doc).append(link);
                });
            }

            $(doc).on('click', function (event) {
                if (!$(event.target).closest(".page-dropdown").length) {
                    $('#myDropdown', doc).removeClass('show');
                }
            });

            $('.a-list a', doc).bind('click', function(event) {
                event.preventDefault();
                var selectedItem = $(this);
                $('.dropbtn-value', doc).html(selectedItem.html());
                $('#myInput', doc).val('');
                $('#myDropdown', doc).removeClass('show');
                $('.a-list a', doc).removeClass('selected').css('display', '');
                selectedItem.addClass('selected');
                var ajaxUrl = selectedItem.attr('data-ajax-url');
                var href = selectedItem.attr('href');
                if (ajaxUrl) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        dataType: 'text',
                        success: function (response) {
                            var $body = $(new DOMParser().parseFromString(response, 'text/html').body);
                            generateAnchorsListItems(doc, linkUrl, href, $body);
                        },
                        error: function (xhr, status) {
                            alert('Request is failed');
                        }
                    });
                } else {
                    generateAnchorsListItems(doc, linkUrl, '');
                }
            });

            /* When the user clicks on the button,
            toggle between hiding and showing the dropdown content */
            $('.dropbtn', doc).bind('click', function () {
                $('#myDropdown', doc).addClass('show');
            });
            $('#myInput', doc).bind('keyup', function () {
                var input = $('#myInput', doc);
                var filter = input.val().toUpperCase();
                var divDropdown = $('#myDropdown', doc);
                var aList = divDropdown.find('a');
                aList.each(function (index) {
                    var a = $(this);
                    var txtValue = a.html();
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        a.css('display', '');
                    } else {
                        a.css('display', 'none');
                    }
                });
            });
            var pageUrl = linkUrl.substr(0, linkUrl.indexOf('#')) || '#';
            var selectedPage = $('.a-list a[href="' + pageUrl + '"]', doc);
            selectedPage.click();
        }

        function setFileActions(doc) {

            var fileInput = $('#file-field', doc);
            var uploadBtn = $('#upload-btn', doc);
            var allowedExtensions = window.phpVars.allowedExtensions || ['pdf'];
            var regEx = new RegExp('\.' + allowedExtensions.join('|\.'));
            function displayFiles(files) {
                $.each(files, function(i, file) {
                    if (regEx.test(file.name) === false) {
                        alert('Upload Failed. File is not allowed: "' + file.name + '" (type ' + file.type + ')');
                        fileInput.val('');
                        uploadBtn.removeClass('disabled');
                        return true;
                    }
                    uploadBtn.addClass('disabled');
                    var uploader = new ChunkedUploader(file, function (response) {
                        var result;
                        try {
                            result = JSON.parse(response);
                        } catch (e) {}
                        if (result) {
                            fileInput.val('');
                            uploadBtn.removeClass('disabled');
                            appendItemLinks($('#files-list', doc), [result], true);
                            var filteredItems = window.phpVars.mediaFiles.filter(function (item) {
                                return item.url === result.url;
                            });
                            if (!filteredItems.length) {
                                window.phpVars.mediaFiles.unshift(result);
                            }
                            $('#files-list a[href="' + result.url + '"]', doc).click();
                            $('#file-link', doc).prop('checked', true);
                            buildFilesOptions($(doc));
                        }
                    });
                    uploader.upload();
                });
            }

            fileInput.bind({
                change: function() {
                    displayFiles(this.files);
                }
            });

            uploadBtn.click(function (e) {
                e.preventDefault();
                fileInput.click();
            });
        }
    })(jQuery);
}
