/*
 * SadastEditor - Simple Rich Text Editor
 * A lightweight, dependency-free rich text editor for internal use.
 * This editor provides basic formatting tools without external dependencies.
 */
(function (global) {
    'use strict';

    function SadastEditor(textarea, options) {
        this.textarea = textarea;
        this.options = options || {};
        this.id = textarea.id || 'sadast-editor-' + Math.floor(Math.random() * 100000);
        this.isDestroyed = false;

        // Initialize
        this._createWrapper();
        this._createToolbar();
        this._createEditorFrame();
        this._bindEvents();

        // Set initial content
        if (textarea.value) {
            this.setContent(textarea.value);
        }
    }

    SadastEditor.prototype._createWrapper = function () {
        var self = this;
        var wrapper = document.createElement('div');
        wrapper.className = 'sadast-editor-wrapper';
        wrapper.style.cssText = 'display:block;border:1px solid #ddd;border-radius:6px;overflow:hidden;';

        this.wrapper = wrapper;
        this.textarea.parentNode.insertBefore(wrapper, this.textarea);
        wrapper.appendChild(this.textarea);

        // Hide original textarea visually but keep it in form
        this.textarea.style.cssText = 'display:none;width:100%;height:300px;';
    };

    SadastEditor.prototype._createToolbar = function () {
        var self = this;
        var toolbar = document.createElement('div');
        toolbar.className = 'sadast-toolbar';
        toolbar.style.cssText = 'background:#f5f5f5;padding:8px;border-bottom:1px solid #ddd;display:flex;flex-wrap:wrap;gap:4px;';

        var buttons = [
            { label: 'بولد', title: 'Bold', command: 'bold', icon: 'B' },
            { label: 'ایتالیک', title: 'Italic', command: 'italic', icon: 'I' },
            { label: 'زیرخط', title: 'Underline', command: 'underline', icon: 'U' },
            { type: 'separator' },
            { label: 'عنوان ۱', title: 'H1', command: 'formatBlock', value: 'h1' },
            { label: 'عنوان ۲', title: 'H2', command: 'formatBlock', value: 'h2' },
            { label: 'عنوان ۳', title: 'H3', command: 'formatBlock', value: 'h3' },
            { label: 'پاراگراف', title: 'Paragraph', command: 'formatBlock', value: 'p' },
            { type: 'separator' },
            { label: 'لیست عددی', title: 'Ordered List', command: 'orderedlist', icon: '1.' },
            { label: 'لیست نقطه‌ای', title: 'Unordered List', command: 'unorderedlist', icon: '•' },
            { type: 'separator' },
            { label: 'چپ', title: 'Align Left', command: 'justifyLeft', icon: '←' },
            { label: 'وسط', title: 'Align Center', command: 'justifyCenter', icon: '↑' },
            { label: 'راست', title: 'Align Right', command: 'justifyRight', icon: '→' },
            { label: 'هم‌تراز', title: 'Justify', command: 'justifyFull', icon: '⇔' },
            { type: 'separator' },
            { label: 'لینک', title: 'Insert Link', command: 'createLink', icon: '🔗' },
            { label: 'حذف لینک', title: 'Unlink', command: 'unlink', icon: '✕' },
            { type: 'separator' },
            { label: 'کد', title: 'Code', command: 'formatBlock', value: 'code' },
            { type: 'separator' },
            { label: 'خط افقی', title: 'Horizontal Rule', command: 'insertHorizontalRule', icon: '—' },
            { label: 'خروج به خط جدید', title: 'Insert Line Break', command: 'insertLineBreak', icon: '↵' }
        ];

        buttons.forEach(function (btnConfig) {
            if (btnConfig.type === 'separator') {
                var sep = document.createElement('span');
                sep.style.cssText = 'width:1px;height:24px;background:#ccc;border-radius:1px;display:inline-block;';
                toolbar.appendChild(sep);
            } else {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'sadast-btn';
                btn.title = btnConfig.title || btnConfig.label;
                btn.innerHTML = btnConfig.icon || btnConfig.label;
                btn.style.cssText = 'padding:4px 8px;font-size:12px;border:1px solid #ccc;border-radius:3px;background:#fff;cursor:pointer;direction:ltr;text-align:center;min-width:24px;';
                btn.dataset.command = btnConfig.command;
                if (btnConfig.value) btn.dataset.value = btnConfig.value;
                toolbar.appendChild(btn);
            }
        });

        // دکمه تمام‌صفحه در انتهای نوار ابزار
        var spacer = document.createElement('span');
        spacer.style.cssText = 'flex:1;';
        toolbar.appendChild(spacer);

        var fsBtn = document.createElement('button');
        fsBtn.type = 'button';
        fsBtn.className = 'sadast-btn sadast-fullscreen-btn';
        fsBtn.title = 'تمام‌صفحه';
        fsBtn.innerHTML = '⛶';
        fsBtn.style.cssText = 'padding:4px 8px;font-size:12px;border:1px solid #ccc;border-radius:3px;background:#fff;cursor:pointer;';
        fsBtn.addEventListener('click', (function () {
            this.toggleFullscreen();
        }).bind(this));
        toolbar.appendChild(fsBtn);

        this.toolbar = toolbar;
        this.wrapper.insertBefore(toolbar, this.textarea);
    };

    SadastEditor.prototype._createEditorFrame = function () {
        var self = this;
        var iframeContainer = document.createElement('div');
        iframeContainer.className = 'sadast-editor-frame-container';
        iframeContainer.style.cssText = 'position:relative;';

        var iframe = document.createElement('iframe');
        iframe.id = this.id + '-frame';
        iframe.frameBorder = '0';
        iframe.style.cssText = 'width:100%;height:300px;display:block;border:none;direction:rtl;';

        iframeContainer.appendChild(iframe);
        this.wrapper.appendChild(iframeContainer);

        this.iframe = iframe;
        this.iframeDoc = null;
        this.iframeWindow = null;

        var onLoad = function () {
            try {
                self.iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                self.iframeWindow = iframe.contentWindow;

                // Setup document structure
                var docContent = [
                    '<!DOCTYPE html>',
                    '<html>',
                    '<head>',
                    '<meta charset="UTF-8">',
                    '<style>',
                    'body{font-family:Tahoma,Arial,sans-serif;margin:0;padding:10px;font-size:14px;line-height:1.6;direction:rtl;text-align:right;min-height:300px;box-sizing:border-box;}',
                    'p{margin:0 0 10px 0;}',
                    'h1,h2,h3,h4,h5,h6{margin:0 0 10px 0;}',
                    'ul,ol{margin:0 0 10px 0;padding-right:20px;}',
                    'li{margin:0 0 5px 0;}',
                    'code{background:#f5f5f5;padding:2px 4px;border-radius:3px;font-family:monospace;}',
                    'hr{border:none;border-top:1px solid #ccc;margin:10px 0;}',
                    '</style>',
                    '</head>',
                    '<body contenteditable="true" spellcheck="false"></body>',
                    '</html>'
                ].join('\n');

                self.iframeDoc.open();
                self.iframeDoc.write(docContent);
                self.iframeDoc.close();

                var body = self.iframeDoc.body;
                body.addEventListener('input', function () {
                    self._syncToTextarea();
                });

                // Handle click to show selection state
                body.addEventListener('click', function () {
                    self._updateButtonStates();
                });

                // Handle key up for button state updates
                body.addEventListener('keyup', function () {
                    self._updateButtonStates();
                });

            } catch (e) {
                console.error('SadastEditor: Error initializing frame:', e);
            }
        };

        if (iframe.contentDocument && iframe.contentDocument.body) {
            onLoad();
        } else {
            iframe.onload = onLoad;
        }
    };

    SadastEditor.prototype._bindEvents = function () {
        var self = this;
        var buttons = this.toolbar.querySelectorAll('.sadast-btn');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var command = btn.dataset.command;
                var value = btn.dataset.value || null;
                self.execCommand(command, value);
            });
        });
    };

    SadastEditor.prototype.execCommand = function (command, value) {
        if (this.iframeWindow && this.iframeDoc) {
            try {
                this.iframeWindow.focus();
                document.execCommand(command, false, value);
                this._syncToTextarea();
                this.iframeWindow.focus();
            } catch (e) {
                console.error('SadastEditor: Error executing command:', e);
            }
        }
    };

    SadastEditor.prototype._syncToTextarea = function () {
        if (!this.iframeDoc) return;
        var body = this.iframeDoc.body;
        this.textarea.value = body.innerHTML;
    };

    SadastEditor.prototype.getBody = function () {
        if (!this.iframeDoc) return null;
        return this.iframeDoc.body;
    };

    SadastEditor.prototype._updateButtonStates = function () {
        // Optional: Update toolbar button active states based on selection
        // Kept simple for now
    };

    SadastEditor.prototype.setContent = function (html) {
        if (!this.iframeDoc) {
            // Try again after iframe loads
            var self = this;
            setTimeout(function () {
                self.setContent(html);
            }, 100);
            return;
        }

        var body = this.getBody();
        if (!body) return;

        // Set content preserving HTML
        body.innerHTML = html;
        this._syncToTextarea();
    };

    SadastEditor.prototype.getContent = function () {
        if (this.iframeDoc && this.iframeDoc.body) {
            this._syncToTextarea();
        }
        return this.textarea.value;
    };

    SadastEditor.prototype.insertImage = function () {
        // Simple prompt-based image insertion
        var url = window.prompt('آدرس تصویر را وارد کنید:', '');
        if (url && this.iframeWindow) {
            this.iframeWindow.focus();
            document.execCommand('insertImage', false, url);
        }
    };

    SadastEditor.prototype.toggleFullscreen = function () {
        var self = this;
        var wrapper = this.wrapper;
        var iframeContainer = this.wrapper.querySelector('.sadast-editor-frame-container');
        var iframe = this.iframe;

        if (!wrapper.classList.contains('sadast-fullscreen')) {
            // ذخیره وضعیت فعلی برای بازگشت
            this._prevWrapperStyle = wrapper.style.cssText;
            wrapper.classList.add('sadast-fullscreen');

            wrapper.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;width:100%;height:100%;z-index:999999;background:#fff;display:flex;flex-direction:column;border:none;border-radius:0;';

            if (iframeContainer) {
                iframeContainer.style.cssText = 'position:relative;flex:1;display:flex;';
                iframeContainer.style.height = 'auto';
            }
            if (iframe) {
                iframe.style.cssText = 'width:100%;height:100%;display:block;border:none;flex:1;';
                iframe.style.height = '100%';
            }

            // فکوس روی بدنه ویرایشگر
            setTimeout(function () {
                if (self.iframeDoc && self.iframeDoc.body) {
                    self.iframeDoc.body.focus();
                }
            }, 50);

            // ذخیره حالت دکمه
            var btn = this.toolbar.querySelector('.sadast-fullscreen-btn');
            if (btn) btn.title = 'خروج از تمام‌صفحه';
        } else {
            wrapper.classList.remove('sadast-fullscreen');
            wrapper.style.cssText = this._prevWrapperStyle || 'display:block;border:1px solid #ddd;border-radius:6px;overflow:hidden;';

            if (iframeContainer) {
                iframeContainer.style.cssText = 'position:relative;';
            }
            if (iframe) {
                iframe.style.cssText = 'width:100%;height:300px;display:block;border:none;direction:rtl;';
            }

            var btn = this.toolbar.querySelector('.sadast-fullscreen-btn');
            if (btn) btn.title = 'تمام‌صفحه';
        }
    };

    SadastEditor.prototype.destroy = function () {
        if (this.isDestroyed) return;
        this.isDestroyed = true;

        if (this.wrapper && this.wrapper.parentNode) {
            // Restore textarea visibility
            this.textarea.style.display = '';
            this.wrapper.parentNode.insertBefore(this.textarea, this.wrapper);
            this.wrapper.parentNode.removeChild(this.wrapper);
        }
    };

    // Global factory
    global.SadastEditor = SadastEditor;

    // Auto-initialize on elements with data-sadast-editor
    global.initSadastEditors = function () {
        var elements = document.querySelectorAll('textarea[data-sadast-editor]');
        elements.forEach(function (el) {
            // Store reference on element
            el._sadastEditor = new SadastEditor(el);
        });
    };

    // Auto init if document is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            global.initSadastEditors();
        });
    } else {
        global.initSadastEditors();
    }

})(window);
