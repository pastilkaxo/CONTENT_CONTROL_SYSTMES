function runNicepage(autoStart)
{
    if (window.dataBridge) {
        (function($){
            var iframe = $('<iframe>', {
                    src: window.cmsVars.editorUrl,
                    id: 'editor-frame'
                }),
                header = $('body > header');

            if (!header.length) {
                $('body').prepend(iframe);
            } else {
                header.before(iframe);
            }

            //iframe.css('height', 'calc(100vh - ' + header.height() + 'px)');
            iframe.css('height', '100vh');
            iframe.css('width', '100%');
            iframe.css('max-width', '100%');

            $(document).scroll(function() {
                $(this).scrollTop(0);
            });

            $('body').addClass('editor');
        })(jQuery);
    } else {
        alert('Unable to start the Editor. Please contact the Support.');
        if (autoStart) {
            window.location.href = window.cmsVars.adminUrl;
        }
    }
}

SqueezeBox.extend({
    applyContent: function(content, size) {
        if (!this.isOpen && !this.applyTimer) return;
        this.applyTimer = clearTimeout(this.applyTimer);
        this.hideContent();
        if (!content) {
            this.toggleLoading(true);
        } else {
            if (this.isLoading) this.toggleLoading(false);
            this.fireEvent('onUpdate', [this.content], 20);
        }
        if (content) {
            if (['string', 'array'].contains(typeOf(content))) {
                this.content.set('html', content);
            } else if (!(content !== this.content && this.content.contains(content))) {
                this.content.adopt(content);
            }
        }
        this.callChain();
        if (!this.isOpen) {
            this.toggleListeners(true);
            this.resize(size, true);
            this.isOpen = true;
            this.win.setProperty('aria-hidden', 'false');
            this.fireEvent('onOpen', [this.content]);
        } else {
            this.resize(size);
        }
    }
});

function sendRequest(data, callback) {
    var xhr = new XMLHttpRequest();

    function onError() {
        callback(new Error('Failed to send a request to ' + data.url + ' ' + JSON.stringify({
            responseText: xhr.responseText,
            readyState: xhr.readyState,
            status: xhr.status
        }, null, 4)));
    }

    xhr.onerror = onError;
    xhr.onload = function () {
        if (this.readyState === 4 && this.status === 200) {
            callback(null, this.response);
        } else {
            onError();
        }
    };
    xhr.open(data.method || 'GET', data.url);

    if (data.data) {
        var formData = new FormData();
        formData.append("pageType", data.data.pageType);
        formData.append("passwordProtection", data.data.passwordProtection);
        formData.append("pageId", data.data.pageId);
        xhr.send(formData);
    } else {
        xhr.send();
    }
}

function postMessageListener(event) {
    if (event.origin !== location.origin) {
        return;
    }
    var data;
    try {
        data = JSON.parse(event.data);
    }
    catch(e){
    }
    if (!data) {
        return;
    }
    if (data.action === 'close') {
        window.location.href = data.closeUrl;
    } else if (data.action === 'editLinkDialogOpen') {
        window.dataForDialog = data.data;
        openEditLinkDialog(data.data);
    }
}

if (window.addEventListener) {
    window.addEventListener("message", postMessageListener);
} else {
    window.attachEvent("onmessage", postMessageListener); // IE8
}

jQuery(function($) {
    var dataInfo = window.dataBridge && window.dataBridge.getInfo();
    if (dataInfo && !dataInfo['themeColorScheme']) {
        var loadCallback;
        var forceRefresh = window.cmsVars.forceRefresh;
        var needResetCache = !localStorage.np_theme_typography_cache ||
            forceRefresh === '1';

        if (needResetCache) {
            delete localStorage.np_theme_typography_cache;
        }

        window.loadAppHook = function (load) {
            if (localStorage.np_theme_typography_cache) {
                jQuery.extend(dataBridgeData.info, JSON.parse(localStorage.np_theme_typography_cache));
                console.log('Regular load app.js');
                load();
                return;
            }
            loadCallback = load;
        };
        var iframe = $('<iframe>', { id: 'np-loader', 'style': 'position:absolute, visibility: hidden, width: 1800px; height:0; border:0;' }),
            header = $('header');

        if (!header.length) {
            header = $('div[id="header"]');
        }

        header.before(iframe);

        var loaderIframe = document.getElementById('np-loader');
        loaderIframe.addEventListener("load", function() {
            localStorage.np_theme_typography_cache = JSON.stringify(NpTypographyParser.parse(loaderIframe));
            $(loaderIframe).remove();
            console.log('Typography cache updated');
            $.extend(dataBridgeData.info, JSON.parse(localStorage.np_theme_typography_cache));
            if (loadCallback) {
                console.log('Deferred load app.js');
                loadCallback();
            }
        });

        if (location.protocol === "https:" && dataBridgeData.info.typographyPageHtmlUrl.indexOf('http://') !== -1) {
            console.log('Regular load app.js due to CORS');
            delete window.loadAppHook;
        } else {
            loaderIframe.src = dataBridgeData.info.typographyPageHtmlUrl;
        }
    }
    $.post(window.cmsVars.infoDataUrl, { id: window.cmsVars.pageId}).done(function(json) {
        var result = null;
        try {
            result = JSON.parse(json);
        } catch(e) {}
        if (result && result.result && dataBridgeData.info) {
            var infoData = result.result;
            $.extend(dataBridgeData.info, infoData);
        }
    }, 'json');

    // autostart nicepage from cms admin main menu
    if (window.cmsVars.startParam == '1' || window.cmsVars.autoStartParam == '1' || window.cmsVars.viewParam == 'theme') {
        runNicepage(true);
    }
});

if (window.cmsVars.npButtonText) {
    jQuery(function ($) {
        var nicepageButton = $('<a href="#" class="btn nicepage-button">' + window.cmsVars.npButtonText + '</a>'),
            nicepageArea = $('<div class="' + window.cmsVars.buttonAreaClass + '"></div>');

        nicepageArea.append(nicepageButton);

        if ($('form').length) {
            $('form').eq(0).before(nicepageArea);
        } else {
            $('section[id="content"]').prepend(nicepageArea);
        }

        nicepageButton.click(function (e) {
            e.preventDefault();
            runNicepage();
        });
        if (window.cmsVars.buttonAreaClass !== '') {
            Joomla.originalsubmitbutton = Joomla.submitbutton;
            Joomla.submitbutton = function npsubmitbutton(action) {
                var adminForm = $('.adminform');
                var fakeNode = $('<textarea name="jform[articletext]" id="jform_articletext"></textarea>');
                if (window.cmsVars.jEditor !== 'none' && window.cmsVars.jEditor !== 'codemirror') {
                    adminForm.html('');//remove custom editor content
                }
                if (action && action == 'article.save2copy') {
                    $.post(window.cmsVars.duplicatePageUrl, {postId: window.cmsVars.pageId}).done(function (data) {
                        if (data && data.indexOf('ok') !== -1) {
                            adminForm.append(fakeNode);
                            Joomla.originalsubmitbutton(action);
                        }
                    }, 'json')
                        .always(function() {
                            fakeNode.remove();
                        });
                } else {
                    Joomla.originalsubmitbutton(action);
                }

            }

            var selectObj = $(window.cmsVars.templateOptions);
            nicepageButton.after(selectObj);
            $('#toolbar-apply button, #save-group-children-save button').click(function () {
                var pageType = $('.nicepage-select-template').val();
                var passwordProtection = $('.nicepage-password-input').val();
                sendRequest({
                    url: window.cmsVars.savePageTypeUrl,
                    method: 'POST',
                    data: {
                        passwordProtection: passwordProtection,
                        pageType: pageType,
                        pageId: window.cmsVars.pageId
                    }
                }, function (error, response) {
                    if (error) {
                        console.error(e);
                        alert('Save page type error.');
                    }
                });
            });
        }

        if (window.cmsVars.autoSaveMsg !== '') {
            nicepageButton.after($(window.cmsVars.autoSaveMsg));
        }


        $.post(window.cmsVars.frontUrl, {uid: window.cmsVars.userId}).done(function (data) {
            if (data && data.indexOf('ok') !== -1) {
                var nicepagePreviewButton = $('<a href="' + window.cmsVars.previewPageUrl + '" target="_blank" class="btn nicepage-preview-button">Preview page</a>');
                nicepageButton.after(nicepagePreviewButton);
            }
        }, 'json');
    });
}
