/*
 * Safhesaz - Page Builder module
 * Handles initialization of the drag-and-drop page builder
 * without requiring external CDN dependencies.
 */
(function (global) {
    'use strict';

    var Safhesaz = {
        initialized: false,
        builderContainer: null,
        blockTypes: [],

        // Initialize the builder
        init: function (options) {
            options = options || {};
            var container = options.container || document.getElementById('builder-container');

            if (!container) {
                console.warn('Safhesaz: Container element not found');
                return false;
            }

            this.builderContainer = container;

            // Load block types
            if (typeof options.blockTypes !== 'undefined') {
                this.blockTypes = options.blockTypes;
            }

            // Check if Sortable is available
            var Sortable = global.Sortable;
            if (typeof Sortable === 'undefined') {
                console.warn('Safhesaz: Sortable.js not loaded. Drag-and-drop may not work.');
            } else {
                this._initSortable(Sortable);
            }

            // Initialize event handlers
            this._initEventHandlers();

            // Load saved blocks data
            if (options.blocksData) {
                this.loadBlocks(options.blocksData);
            }

            // Setup save mechanism
            this._setupSave(options.saveEndpoint);

            this.initialized = true;
            return true;
        },

        // Initialize Sortable for drag-and-drop
        _initSortable: function (Sortable) {
            var self = this;

            // Sort existing blocks
            var blocksContainer = this.builderContainer.querySelector('.builder-blocks-container');
            if (blocksContainer) {
                Sortable.create(blocksContainer, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    handle: '.block-header',
                    onEnd: function (evt) {
                        self._reorderBlocks();
                    }
                });
            }

            // Add block list sortable
            var blockList = this.builderContainer.querySelector('.block-list');
            if (blockList) {
                Sortable.create(blockList, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    group: 'blocks',
                    draggable: '.block-item',
                    handle: '.block-item',
                    onAdd: function (evt) {
                        var blockType = evt.item ? evt.item.dataset.blockType : 'text';
                        self.addBlock(blockType);
                        // Remove from block list temporarily
                        evt.item.remove();
                    }
                });
            }
        },

        // Add a new block to the builder
        addBlock: function (blockType) {
            blockType = blockType || 'text';

            var block = this._createBlockElement(blockType);
            var container = this.builderContainer.querySelector('.builder-blocks-container');

            if (container) {
                container.appendChild(block);
            }
        },

        // Create a block element
        _createBlockElement: function (blockType) {
            var block = document.createElement('div');
            block.className = 'builder-block';
            block.setAttribute('data-block-type', blockType);
            block.setAttribute('data-block-id', 'block-' + Date.now() + '-' + Math.floor(Math.random() * 1000));

            var header = document.createElement('div');
            header.className = 'block-header';
            header.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#f8f9fa;border-bottom:1px solid #e9ecef;cursor:move;';

            var typeLabel = this._getBlockTypeLabel(blockType);
            var title = document.createElement('span');
            title.className = 'block-title';
            title.style.cssText = 'font-weight:600;font-size:13px;color:#495057;';
            title.textContent = typeLabel;

            var actions = document.createElement('div');
            actions.className = 'block-actions';
            actions.style.cssText = 'display:flex;gap:4px;';

            var editBtn = document.createElement('button');
            editBtn.type = 'button';
            editBtn.className = 'block-edit-btn';
            editBtn.innerHTML = '<i class="fa-solid fa-pen"></i>';
            editBtn.title = 'ویرایش';
            editBtn.style.cssText = 'padding:4px 8px;border:1px solid #ced4da;border-radius:4px;background:#fff;cursor:pointer;font-size:12px;';
            editBtn.addEventListener('click', (function () {
                this.editBlock(block, blockType);
            }).bind(this));
            actions.appendChild(editBtn);

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'block-remove-btn';
            removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            removeBtn.title = 'حذف';
            removeBtn.style.cssText = 'padding:4px 8px;border:1px solid #dc3545;border-radius:4px;background:#fff;color:#dc3545;cursor:pointer;font-size:12px;';
            removeBtn.addEventListener('click', function () {
                block.remove();
            });
            actions.appendChild(removeBtn);

            header.appendChild(title);
            header.appendChild(actions);

            var content = document.createElement('div');
            content.className = 'block-content';
            content.style.cssText = 'padding:12px;';

            // Initialize based on block type
            if (blockType === 'text' || blockType === 'heading') {
                var textarea = document.createElement('textarea');
                textarea.className = 'block-text-input';
                textarea.setAttribute('data-sadast-editor', 'true');
                textarea.rows = 6;
                textarea.style.cssText = 'width:100%;padding:8px;border:1px solid #ced4da;border-radius:4px;font-family:Tahoma,sans-serif;font-size:13px;direction:rtl;';
                content.appendChild(textarea);

                // Initialize SadastEditor
                textarea._sadastEditor = new global.SadastEditor(textarea);
            } else if (blockType === 'image') {
                var imgInput = document.createElement('input');
                imgInput.type = 'text';
                imgInput.placeholder = 'آدرس تصویر';
                imgInput.className = 'block-image-url';
                imgInput.style.cssText = 'width:100%;padding:8px;border:1px solid #ced4da;border-radius:4px;font-size:13px;margin-bottom:8px;';
                content.appendChild(imgInput);
            }

            block.appendChild(header);
            block.appendChild(content);

            return block;
        },

        // Get label for block type
        _getBlockTypeLabel: function (blockType) {
            var labels = {
                'heading': 'سرصفحه',
                'text': 'متن',
                'image': 'تصویر',
                'video': 'ویدیو',
                'button': 'دکمه',
                'gallery': 'گالری',
                'services': 'خدمات',
                'products': 'محصولات',
                'separator': 'جداکننده',
                'columns': 'ستون‌ها',
                'custom': 'HTML سفارشی'
            };
            return labels[blockType] || blockType;
        },

        // Edit a block
        editBlock: function (block, blockType) {
            var modal = this._createEditModal(block, blockType);
            document.body.appendChild(modal);
        },

        // Create edit modal for a block
        _createEditModal: function (block, blockType) {
            var modal = document.createElement('div');
            modal.className = 'safhesaz-modal';
            modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:10000;';

            var panel = document.createElement('div');
            panel.className = 'modal-panel';
            panel.style.cssText = 'background:#fff;border-radius:8px;padding:24px;max-width:600px;width:90%;max-height:80vh;overflow-y:auto;';

            var title = document.createElement('h3');
            title.textContent = 'ویرایش بلوک: ' + this._getBlockTypeLabel(blockType);
            title.style.cssText = 'margin-top:0;margin-bottom:20px;font-size:18px;color:#333;';
            panel.appendChild(title);

            // Close handler
            var self = this;
            var closeModal = function () {
                modal.remove();
            };

            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });

            // Get current content
            var contentEl = block.querySelector('.block-content');

            if (blockType === 'text' || blockType === 'heading') {
                var editor = contentEl.querySelector('[data-sadast-editor]');
                var editorInstance = editor && editor._sadastEditor;
                var currentContent = editorInstance ? editorInstance.getContent() : (editor ? editor.value : '');

                var label = document.createElement('label');
                label.textContent = 'متن:';
                label.style.cssText = 'display:block;margin-bottom:8px;font-weight:600;';
                panel.appendChild(label);

                var textInput = document.createElement('textarea');
                textInput.value = currentContent;
                textInput.rows = 8;
                textInput.style.cssText = 'width:100%; padding:10px; border:1px solid #ced4da; border-radius:4px; font-family: Tahoma, sans-serif; font-size: 13px; direction: rtl; resize: vertical;';
                panel.appendChild(textInput);

                var formActions = document.createElement('div');
                formActions.style.cssText = 'display:flex;gap:8px;justify-content:flex-end;margin-top:20px;';

                var saveBtn = document.createElement('button');
                saveBtn.type = 'button';
                saveBtn.textContent = 'ذخیره';
                saveBtn.style.cssText = 'padding:8px 16px;border:none;border-radius:6px;background:#28a745;color:#fff;cursor:pointer;font-weight:600;';
                saveBtn.addEventListener('click', function () {
                    textInput.value = textInput.value;

                    // Update in SadastEditor
                    if (editorInstance) {
                        editorInstance.setContent(textInput.value);
                        editorInstance._syncToTextarea();
                    } else if (editor) {
                        editor.value = textInput.value;
                    }

                    closeModal();
                    self._syncAllBlocks();
                });
                formActions.appendChild(saveBtn);

                var cancelBtn = document.createElement('button');
                cancelBtn.type = 'button';
                cancelBtn.textContent = 'لغو';
                cancelBtn.style.cssText = 'padding:8px 16px;border:1px solid #6c757d;border-radius:6px;background:#fff;color:#6c757d;cursor:pointer;font-weight:600;';
                cancelBtn.addEventListener('click', closeModal);
                formActions.appendChild(cancelBtn);

                panel.appendChild(formActions);

            } else if (blockType === 'image') {
                var urlInput = contentEl.querySelector('.block-image-url');
                var currentUrl = urlInput ? urlInput.value : '';

                var label = document.createElement('label');
                label.textContent = 'آدرس تصویر (محلی یا اینترنتی):';
                label.style.cssText = 'display:block;margin-bottom:8px;font-weight:600;';
                panel.appendChild(label);

                var urlField = document.createElement('input');
                urlField.type = 'text';
                urlField.value = currentUrl;
                urlField.style.cssText = 'width:100%; padding:8px; border:1px solid #ced4da; border-radius:4px; font-family: Tahoma, sans-serif; font-size: 13px; direction: ltr;';
                panel.appendChild(urlField);

                var formActions = document.createElement('div');
                formActions.style.cssText = 'display:flex;gap:8px;justify-content:flex-end;margin-top:20px;';

                var saveBtn = document.createElement('button');
                saveBtn.type = 'button';
                saveBtn.textContent = 'ذخیره';
                saveBtn.style.cssText = 'padding:8px 16px;border:none;border-radius:6px;background:#28a745;color:#fff;cursor:pointer;font-weight:600;';
                saveBtn.addEventListener('click', function () {
                    if (urlInput) urlInput.value = urlField.value;
                    closeModal();
                    self._syncAllBlocks();
                });
                formActions.appendChild(saveBtn);

                var cancelBtn = document.createElement('button');
                cancelBtn.type = 'button';
                cancelBtn.textContent = 'لغو';
                cancelBtn.style.cssText = 'padding:8px 16px;border:1px solid #6c757d;border-radius:6px;background:#fff;color:#6c757d;cursor:pointer;font-weight:600;';
                cancelBtn.addEventListener('click', closeModal);
                formActions.appendChild(cancelBtn);

                panel.appendChild(formActions);
            } else {
                var label = document.createElement('label');
                label.textContent = 'محتوا:';
                label.style.cssText = 'display:block;margin-bottom:8px;font-weight:600;';
                panel.appendChild(label);

                var textArea = document.createElement('textarea');
                textArea.rows = 8;
                textArea.style.cssText = 'width:100%; padding:10px; border:1px solid #ced4da; border-radius:4px; font-family: Tahoma, sans-serif; font-size: 13px; direction: rtl; resize: vertical;';
                panel.appendChild(textArea);

                var formActions = document.createElement('div');
                formActions.style.cssText = 'display:flex;gap:8px;justify-content:flex-end;margin-top:20px;';

                var saveBtn = document.createElement('button');
                saveBtn.type = 'button';
                saveBtn.textContent = 'ذخیره';
                saveBtn.style.cssText = 'padding:8px 16px;border:none;border-radius:6px;background:#28a745;color:#fff;cursor:pointer;font-weight:600;';
                saveBtn.addEventListener('click', function () {
                    closeModal();
                    self._syncAllBlocks();
                });
                formActions.appendChild(saveBtn);

                var cancelBtn = document.createElement('button');
                cancelBtn.type = 'button';
                cancelBtn.textContent = 'لغو';
                cancelBtn.style.cssText = 'padding:8px 16px;border:1px solid #6c757d;border-radius:6px;background:#fff;color:#6c757d;cursor:pointer;font-weight:600;';
                cancelBtn.addEventListener('click', closeModal);
                formActions.appendChild(cancelBtn);

                panel.appendChild(formActions);
            }

            return modal;
        },

        // Initialize event handlers for add block buttons
        _initEventHandlers: function () {
            var self = this;

            // Add block buttons
            var addButtons = this.builderContainer.querySelectorAll('.add-block-btn');
            addButtons.forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var blockType = btn.dataset.blockType;
                    self.addBlock(blockType);
                });
            });

            // Save button
            var saveBtn = this.builderContainer.querySelector('.builder-save-btn');
            if (saveBtn) {
                saveBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    self.saveBlocks();
                });
            }
        },

        // Reorder blocks (update data order)
        _reorderBlocks: function () {
            this._syncAllBlocks();
        },

        // Sync all blocks to hidden textarea
        _syncAllBlocks: function () {
            var blocks = this.builderContainer.querySelectorAll('.builder-block');
            var blocksData = [];

            blocks.forEach(function (block) {
                var blockData = {
                    id: block.getAttribute('data-block-id'),
                    type: block.getAttribute('data-block-type'),
                    content: self._extractBlockContent(block)
                };
                blocksData.push(blockData);
            });

            var hiddenInput = this.builderContainer.querySelector('.builder-blocks-data');
            if (hiddenInput) {
                hiddenInput.value = JSON.stringify(blocksData);
            }
        },

        // Extract content from a block element
        _extractBlockContent: function (block) {
            var content = {};
            var contentEl = block.querySelector('.block-content');

            if (!contentEl) return content;

            var blockType = block.getAttribute('data-block-type');

            if (blockType === 'text' || blockType === 'heading') {
                var editor = contentEl.querySelector('[data-sadast-editor]');
                if (editor && editor._sadastEditor) {
                    content.html = editor._sadastEditor.getContent();
                } else if (editor) {
                    content.html = editor.value;
                }
            } else if (blockType === 'image') {
                var imgInput = contentEl.querySelector('.block-image-url');
                if (imgInput) {
                    content.url = imgInput.value;
                }
            }

            return content;
        },

        // Load blocks from data
        loadBlocks: function (blocksData) {
            var container = this.builderContainer.querySelector('.builder-blocks-container');
            if (!container) return;

            container.innerHTML = '';

            try {
                var blocks = typeof blocksData === 'string' ? JSON.parse(blocksData) : blocksData;
                if (Array.isArray(blocks)) {
                    blocks.forEach(function (blockData) {
                        var block = this._createBlockElement(blockData.type);
                        block.setAttribute('data-block-id', blockData.id || 'block-' + Date.now());

                        // Restore content
                        var contentEl = block.querySelector('.block-content');
                        if (blockData.content) {
                            if (blockData.type === 'text' || blockData.type === 'heading') {
                                var editor = contentEl.querySelector('[data-sadast-editor]');
                                if (editor && editor._sadastEditor) {
                                    editor._sadastEditor.setContent(blockData.content.html || '');
                                }
                            } else if (blockData.type === 'image') {
                                var imgInput = contentEl.querySelector('.block-image-url');
                                if (imgInput && blockData.content.url) {
                                    imgInput.value = blockData.content.url;
                                }
                            }
                        }

                        container.appendChild(block);
                    }, this);
                }
            } catch (e) {
                console.error('Safhesaz: Error loading blocks:', e);
            }
        },

        // Save blocks data
        saveBlocks: function () {
            this._syncAllBlocks();

            var hiddenInput = this.builderContainer.querySelector('.builder-blocks-data');
            if (!hiddenInput) {
                console.warn('Safhesaz: No hidden input found for saving data');
                return;
            }

            // Trigger form submission or AJAX save
            var saveEvent = new global.CustomEvent('safhesazSave', {
                detail: {
                    blocksData: hiddenInput.value
                }
            });
            document.dispatchEvent(saveEvent);
        },

        // Setup save endpoint
        _setupSave: function (saveEndpoint) {
            if (!saveEndpoint) return;

            var self = this;
            document.addEventListener('safhesazSave', function (e) {
                self._performSave(saveEndpoint, e.detail.blocksData);
            });
        },

        // Perform AJAX save
        _performSave: function (endpoint, data) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', endpoint, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        var event = new global.CustomEvent('safhesazSaved', {
                            detail: { response: xhr.responseText }
                        });
                        document.dispatchEvent(event);
                    } else {
                        console.error('Safhesaz: Save failed with status:', xhr.status);
                    }
                }
            };

            xhr.send('blocks_data=' + encodeURIComponent(data));
        }
    };

    // Export to global
    global.Safhesaz = Safhesaz;

    // Auto-init when called explicitly
    global.initSafhesaz = function (options) {
        return Safhesaz.init(options);
    };

})(window);
