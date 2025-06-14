<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('markdownEditor', () => ({
            // ----------------------------
            // State variables
            // ----------------------------
            content: '',
            showMenu: false,
            showPopover: false,
            activePopover: null,
            selected: 0,
            menuY: 48,
            popoverY: 48,
            charCount: 0,
            wordCount: 0,
            savedTextareaHeight: null,
            debounceTimer: null,
            // ----------------------------
            // Error alert system
            // ----------------------------
            errorMessage: '',
            showError: false,
            errorTimeout: null,
            // ----------------------------
            // Popover data
            // ----------------------------
            linkText: '',
            linkUrl: '',
            youtubeUrl: '',
            // ----------------------------
            // File upload
            // ----------------------------
            fileInput: null,
            isUploading: false,
            dropFile: false,
            // ----------------------------
            // Bound event handlers
            // ----------------------------
            boundPopoverKeydownHandler: null,
            boundPopoverClickOutsideHandler: null,
            // ----------------------------
            handleInput()
            {
                this.autoGrow();
                this.updateCounts();

                if (this.debounceTimer)
                {
                    clearTimeout(this.debounceTimer);
                }

                this.debounceTimer = setTimeout(() => {
                    this.$wire.updateMarkdown(this.content);
                }, {!! (int) config('livewire-markdown-editor.editor.debounce', 1000) !!});
            },
            init()
            {
                this.$nextTick(() =>
                {
                   if (!this.$refs.textarea || !this.$refs.mirror)
                   {
                       return;
                   }

                   this.content = this.$wire.markdown;
                   this.setupImageUploads();

                   this.$refs.textarea.addEventListener('keydown', (e) =>
                   {
                       if (e.target === this.$refs.textarea && e.key === '/' && !this.showMenu && !this.showPopover)
                       {
                           e.preventDefault();
                           e.stopPropagation();

                           this.handleSlashCommand();
                       }

                       if (e.target === this.$refs.textarea)
                       {
                            this.handleKeydown(e);
                        }
                   });

                   this.$refs.textarea.addEventListener('input', () =>
                   {
                       if (this.showMenu)
                       {
                           this.closeMenu();
                       }
                   });

                   this.updateCounts();

                   this.$refs.textarea.addEventListener('input', () => this.updateCounts());

                   this.$watch('$wire.markdown', (value) =>
                   {
                       if (this.content !== value)
                       {
                           this.content = value;
                           this.updateCounts();
                           this.autoGrow();
                       }
                   });

                   this.$watch('$wire.activeTab', (newTab, oldTab) =>
                   {
                       if (oldTab === 'write' && newTab === 'preview')
                       {
                           this.saveTextareaHeight();
                       }
                       else if (oldTab === 'preview' && newTab === 'write')
                       {
                           this.$nextTick(() => this.restoreTextareaHeight());
                       }
                   });

                   this.autoGrow();
               });
            },
            setupImageUploads()
            {
                this.fileInput = document.createElement('input');
                this.fileInput.type  = 'file';
                this.fileInput.accept = '{{ collect(array_keys(config('markdown-editor.image.types', [])))->implode(",") }}';
                this.fileInput.style.display = 'none';

                document.body.appendChild(this.fileInput);

                this.fileInput.addEventListener(
                    'change',
                    this.handleFileUpload.bind(this)
                );
            },
            updateCounts()
            {
                const textarea = this.$refs.textarea;

                if (!textarea) return;

                const text = textarea.value || '';
                this.charCount = text.length;

                const textWithoutLinksAndImages = text
                    .replace(/\[([^\]]+)\]\([^)]+\)/g, '$1')
                    .replace(/<(https?:\/\/[^>]+)>/g, '')
                    .replace(/!\[([^\]]+)\]\([^)]+\)/g, '$1');

                const words = textWithoutLinksAndImages.trim()
                    .split(/\s+/)
                    .filter(word => word.length > 0);

                this.wordCount = words.length;
            },
            handleKeydown(e)
            {
                if (e.target !== this.$refs.textarea)
                {
                    return;
                }

                if (this.showMenu)
                {
                    switch (e.key)
                    {
                        case 'ArrowDown':
                            this.selected = (this.selected + 1) % 12;
                            this.scrollCommandIntoView();
                            e.preventDefault();
                            e.stopPropagation();
                        break;
                        case 'ArrowUp':
                            this.selected = (this.selected - 1 + 12) % 12;
                            this.scrollCommandIntoView();
                            e.preventDefault();
                            e.stopPropagation();
                        break;
                        case 'Enter':
                            this.selectCommand(this.selected);
                            e.preventDefault();
                            e.stopPropagation();
                        break;
                        case 'Escape':
                        case 'Backspace':
                            this.closeMenu();
                            e.preventDefault();
                            e.stopPropagation();
                        break;
                    }
                }
                else if (this.showPopover)
                {
                    if (e.key === 'Escape')
                    {
                        this.closePopover();
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }
                else if (e.key === 'Enter')
                {
                    this.showCommandHint();
                }
            },
            handleSlashCommand()
            {
                this.setMenuPosition();
                this.openMenu();
            },
            setMenuPosition()
            {
                const textarea = this.$refs.textarea;

                if (!textarea)
                {
                    this.menuY = 100;
                    return;
                }

                const cursorPosition = textarea.selectionEnd;
                const textBeforeCursor = textarea.value.substring(0, cursorPosition);
                const lines = textBeforeCursor.split('\n');
                this.menuY = lines.length * 24;
            },
            openMenu()
            {
                this.showMenu = true;
                this.selected = 0;

                setTimeout(() => {
                    this.$el.ownerDocument.addEventListener('mousedown', this.handleClickOutside.bind(this), { capture: true });
                }, 0);
            },
            closeMenu()
            {
                const commandMenu = document.querySelector('.command-menu');

                if (commandMenu)
                {
                    commandMenu.scrollTop = 0;
                }

                this.showMenu = false;

                this.$el.ownerDocument.removeEventListener('mousedown', this.handleClickOutside, { capture: true });
            },
            handleClickOutside(event)
            {
                if (!this.$el.contains(event.target))
                {
                    this.closeMenu();
                }
            },
            selectCommand(index)
            {
                const textarea = this.$refs.textarea;

                if (!textarea) return;

                switch (index)
                {
                    case 1: // Heading 1
                        this.insertText('# ', '');
                    break;
                    case 2: // Heading 2
                        this.insertText('## ', '');
                    break;
                    case 3: // Heading 3
                        this.insertText('### ', '');
                    break;
                    case 4: // Link - show link popover
                        this.openPopover('link');
                        this.closeMenu();
                        return;
                    break;
                    case 5: // Divider
                        this.insertText('\n---\n', '');
                    break;
                    case 6: // Bullet List
                        this.insertText('- ', '');
                    break;
                    case 7: // Number List
                        this.insertText('1. ', '');
                    break;
                    case 8: // Quote
                        this.insertText('> ', '');
                    break;
                    case 9: // Code block
                        this.insertText('```\n', '\n```');
                    break;
                    case 10: // Image
                        this.closeMenu();
                        this.fileInput.click();
                        return;
                    break;
                    case 11: // YouTube - show YouTube popover
                        this.openPopover('youtube');
                        this.closeMenu();
                        return;
                    break;
                }

                this.closeMenu();
                textarea.focus();
            },
            showCommandHint()
            {
                this.showHint = true;

                setTimeout(() => {
                   this.showHint = false;
                }, 3000);
            },
            autoGrow()
            {
                const textarea = this.$refs.textarea;

                if (!textarea) return;

                textarea.style.height = 'auto';
                const newHeight       = Math.max(100, textarea.scrollHeight);
                const editorContainer = textarea.closest('.flex-col');
                textarea.style.height = newHeight + 'px';

                if (editorContainer)
                {
                    editorContainer.style.minHeight = newHeight + 'px';
                }
            },
            saveTextareaHeight()
            {
                const textarea = this.$refs.textarea;

                if (textarea)
                {
                    this.savedTextareaHeight = textarea.style.height;
                }
            },
            restoreTextareaHeight()
            {
                const textarea = this.$refs.textarea;

                if (textarea && this.savedTextareaHeight)
                {
                    textarea.style.height = this.savedTextareaHeight;
                    const editorContainer = textarea.closest('.flex-col');

                    if (editorContainer)
                    {
                        editorContainer.style.minHeight = this.savedTextareaHeight;
                    }
                }
                else
                {
                    this.autoGrow();
                }
            },
            initLinkPopoverData()
            {
                this.linkText = '';
                this.linkUrl = '';

                setTimeout(() => document.getElementById('link-text').focus(), 50);
            },
            initYoutubePopoverData()
            {
                this.youtubeUrl = '';

                setTimeout(() => document.getElementById('youtube-url').focus(), 50);
            },
            scrollCommandIntoView()
            {
                setTimeout(() => {
                   const commandMenu  = document.querySelector('.command-menu');
                   const selectedItem = commandMenu?.querySelector(`[data-index="${this.selected}"]`);

                   if (commandMenu && selectedItem)
                   {
                       selectedItem.scrollIntoView({
                           block: 'nearest',
                           inline: 'nearest',
                           behavior: 'smooth'
                       });
                   }
               }, 10);
            },
            calculateCursorPosition()
            {
                const textarea = this.$refs.textarea;

                if (!textarea)
                {
                    return 100;
                }

                const cursorPosition = textarea.selectionEnd;
                const text = textarea.value;
                const textBeforeCursor = text.substring(0, cursorPosition);
                const lines = textBeforeCursor.split('\n');
                const currentLineNumber = lines.length;
                const lineHeight = 24;
                const editorRect = textarea.getBoundingClientRect();
                const scrollTop = document.documentElement.scrollTop;

                return editorRect.top + scrollTop + ((currentLineNumber - 1) * lineHeight) + 30;
            },
            openPopover(name)
            {
                this.setMenuPosition();

                this.activePopover = name;
                this.popoverY      = this.menuY;
                this.showPopover   = true;

                switch (name)
                {
                    case 'link':
                        this.initLinkPopoverData();
                    break;
                    case 'youtube':
                        this.initYoutubePopoverData();
                    break;
                }

                this.boundPopoverClickOutsideHandler = this.handlePopoverClickOutside.bind(this);
                this.boundPopoverKeydownHandler      = this.handlePopoverKeydown.bind(this);

                setTimeout(() => this.$el.ownerDocument.addEventListener(
                    'mousedown',
                    this.boundPopoverClickOutsideHandler,
                    { capture: true }
                ), 0);

                this.$el.addEventListener(
                    'keydown',
                    this.boundPopoverKeydownHandler
                );
            },
            handlePopoverKeydown(event)
            {
                if (!this.showPopover)
                {
                    return;
                }

                if (!this.$el.contains(event.target))
                {
                    return;
                }

                if (event.key === 'Escape' || event.key === 'Delete')
                {
                    this.closePopover();

                    event.preventDefault();
                    event.stopPropagation();
                }
            },
            handlePopoverClickOutside(event)
            {
                if (!this.showPopover)
                {
                    return;
                }

                if (event.target.type === 'submit' ||
                    event.target.closest('button[type="submit"]') ||
                    event.target.closest('form button')
                )
                {
                    return;
                }

                const linkPopover = document.getElementById('link-popover');
                const youtubePopover = document.getElementById('youtube-popover');
                const linkEl = (!linkPopover || !linkPopover.contains(event.target));
                const youtubeEl = (!youtubePopover || !youtubePopover.contains(event.target));
                const clickedOutside = (linkEl && youtubeEl && !this.$refs.textarea.contains(event.target));

                if (clickedOutside)
                {
                    this.closePopover();
                }
            },
            closePopover()
            {
                this.showPopover  = false;
                this.activePopover = null;

                this.$refs.textarea.focus();

                if (this.boundPopoverClickOutsideHandler)
                {
                    this.$el.ownerDocument.removeEventListener(
                        'mousedown',
                        this.boundPopoverClickOutsideHandler,
                        { capture: true }
                    );
                }

                if (this.boundPopoverKeydownHandler)
                {
                    this.$el.removeEventListener(
                        'keydown',
                        this.boundPopoverKeydownHandler
                    );
                }

                this.boundPopoverClickOutsideHandler = null;
                this.boundPopoverKeydownHandler      = null;
            },
            insertLink()
            {
                const text = this.linkText?.trim();
                const url  = this.linkUrl?.trim();

                if (!text)
                {
                    this.showErrorMessage(
                        '{{ trans('livewire-markdown-editor::markdown-editor.validation.link.text') }}'
                    );

                    return;
                }

                if (!url)
                {
                    this.showErrorMessage(
                        '{{ trans('livewire-markdown-editor::markdown-editor.validation.link.url') }}'
                    );

                    return;
                }

                try
                {
                    new URL(url.startsWith('http') ? url : `https://${url}`);
                }
                catch (e)
                {
                    this.showErrorMessage(
                        '{{ trans('livewire-markdown-editor::markdown-editor.validation.link.invalid') }}'
                    );

                    return;
                }

                const formattedUrl = url.startsWith('http') ? url : `https://${url}`;
                const markdownLink = `[${text}](${formattedUrl})`;

                this.insertText(markdownLink);

                this.linkText = '';
                this.linkUrl = '';

                this.closePopover();
            },
            insertYoutube()
            {
                const url = this.youtubeUrl?.trim();

                if (!url)
                {
                    this.showErrorMessage(
                        '{{ trans('livewire-markdown-editor::markdown-editor.validation.youtube.empty') }}'
                    );

                    return;
                }

                try
                {
                    if (!url.includes('youtube.com') && !url.includes('youtu.be'))
                    {
                        this.showErrorMessage(
                            '{{ trans('livewire-markdown-editor::markdown-editor.validation.youtube.invalid') }}'
                        );

                        return;
                    }

                    this.insertText(url);
                    this.closePopover();

                    this.youtubeUrl = '';
                }
                catch (e)
                {
                    this.showErrorMessage(
                        '{{ trans('livewire-markdown-editor::markdown-editor.validation.youtube.invalid') }}'
                    );
                }
            },
            showErrorMessage(message)
            {
                if (this.errorTimeout)
                {
                    clearTimeout(this.errorTimeout);
                }

                this.errorMessage = message;
                this.showError    = true;
                this.errorTimeout = setTimeout(() => {
                   this.showError = false;
                   setTimeout(() => {
                      this.errorMessage = '';
                  }, {!! (int) config('livewire-markdown-editor.editor.messages.fadeout', 300) !!});
               }, {!! (int) config('livewire-markdown-editor.editor.messages.display', 2500) !!});
            },
            insertText(before, after = '')
            {
                const textarea = this.$refs.textarea;

                if (!textarea) return;

                const start = textarea.selectionStart || 0;
                const end = textarea.selectionEnd || 0;
                const text = this.content || '';
                const newPosition = start + before.length + (end - start);

                this.content = text.substring(0, start)
                    + before
                    + text.substring(start, end)
                    + after
                    + text.substring(end);

                this.handleInput();

                this.$nextTick(() => {
                       textarea.setSelectionRange(newPosition, newPosition);
                       textarea.focus();
                   });

                this.closeMenu();
            },
            validateImageFile(file)
            {
                if (!file)
                {
                    return {
                        valid: false,
                        message: '{{ trans('livewire-markdown-editor::markdown-editor.validation.image.nofile') }}'
                    };
                }

                const validTypes = {!! json_encode(
                    array_keys(config('markdown-editor.image.types', [])),
                    JSON_THROW_ON_ERROR
                ) !!}

                if(!validTypes.includes(file.type))
                {
                    return {
                        valid: false,
                        message: '{{ trans('livewire-markdown-editor::markdown-editor.uploads.errors.unsupported') }}'
                    };
                }

                const maxSize = {!! (int)config('markdown-editor.image.max_size', 5000) * 1024 !!};

                if (file.size > maxSize)
                {
                    return {
                        valid: false,
                        message: '{{ trans('livewire-markdown-editor::markdown-editor.uploads.errors.filesize', [
                            'size' => config('markdown-editor.image.max_size', 5000)
                        ]) }}'
                    };
                }

                return {valid: true};
            },
            fileUploadFailed(message)
            {
                this.isUploading = false;

                this.showErrorMessage(message);
            },
            processFileUploadResponse(result, file)
            {
                this.isUploading = false;

                (result.success && result.path)
                    ? this.insertText(`![${file.name}](${result.path})`)
                    : this.showErrorMessage(result.message || '{{ trans('livewire-markdown-editor::markdown-editor.uploads.exceptions.store') }}');
            },
            processFileUpload(file)
            {
                const reader = new FileReader();

                reader.readAsDataURL(file);

                reader.onload = () =>
                {
                    this.$wire.uploadBase64Image(reader.result, file.name, file.type).then(result => {
                       this.processFileUploadResponse(result, file);
                   }).catch(error => {
                       this.fileUploadFailed(error.message);
                   });
                };

                reader.onerror = () =>
                {
                    this.fileUploadFailed(
                        '{{ trans('livewire-markdown-editor::markdown-editor.uploads.errors.unreadable') }}'
                    );
                };
            },
            handleFileUpload(event)
            {
                const file = event.target.files[0];

                if (!file) return;

                this.fileInput.value = '';

                const validation = this.validateImageFile(file);

                if (!validation.valid)
                {
                    this.showErrorMessage(validation.message);

                    return;
                }

                this.isUploading = true;

                this.processFileUpload(file);
            },

            droppingFile(event)
            {
                const files = event.dataTransfer.files;

                if (!files || files.length === 0) return;

                const file = files[0];

                const validation = this.validateImageFile(file);

                if (!validation.valid)
                {
                    this.showErrorMessage(validation.message);
                    return;
                }

                this.isUploading = true;
                this.processFileUpload(file);
            }
        }));
    });
</script>
