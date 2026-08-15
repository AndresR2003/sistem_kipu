(function() {
    var panel;
    var messagesBox;
    var input;
    var fileInput;
    var preview;
    var attachmentName;
    var badge;
    var lastId = 0;
    var unread = 0;
    var firstLoad = true;

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    }

    function formatSize(bytes) {
        if (!bytes) return '';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function formatTime(value) {
        return value && value.length >= 16 ? value.substring(11, 16) : '';
    }

    function renderMessage(message) {
        var mine = Number(message.usuario_id) === Number(window.USUARIO_ID);
        var nombre = message.usuario_nombre || 'Usuario';
        var inicial = escapeHtml(nombre.trim().charAt(0).toUpperCase() || 'U');
        var avatar = message.usuario_foto
            ? '<img src="' + escapeHtml(window.BASE_URL + message.usuario_foto) + '" alt="">'
            : inicial;
        var attachment = '';

        if (message.archivo_nombre) {
            var icon = (message.archivo_mime || '').indexOf('image/') === 0 ? 'bi-image' : 'bi-file-earmark-arrow-down';
            attachment = '<a class="chat-attachment" href="' + window.BASE_URL + 'chat/archivo/' + Number(message.id) + '" target="_blank" rel="noopener">' +
                '<i class="bi ' + icon + '"></i><span>' + escapeHtml(message.archivo_nombre) + '</span>' +
                '<small>' + formatSize(Number(message.archivo_tamano)) + '</small></a>';
        }

        return '<div class="chat-message' + (mine ? ' mine' : '') + '" data-message-id="' + Number(message.id) + '">' +
            '<div class="chat-avatar">' + avatar + '</div>' +
            '<div class="chat-bubble">' +
            (mine ? '' : '<div class="chat-author">' + escapeHtml(nombre) + '</div>') +
            (message.mensaje ? '<div class="chat-text">' + escapeHtml(message.mensaje) + '</div>' : '') +
            attachment +
            '<div class="chat-meta">' + formatTime(message.creado_en) + '</div>' +
            '</div></div>';
    }

    function scrollToBottom() {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function showBadge(count) {
        unread = count;
        if (unread > 0) {
            badge.text(unread > 99 ? '99+' : unread).css('display', 'flex');
        } else {
            badge.hide();
        }
    }

    function loadMessages() {
        var since = lastId > 0 ? lastId : 0;
        $.getJSON(window.BASE_URL + 'chat/listar', { desde: since })
            .done(function(response) {
                if (!response.success || !Array.isArray(response.data)) return;
                var messages = response.data;
                if (firstLoad) {
                    messagesBox.empty();
                    if (!messages.length) {
                        messagesBox.html('<div class="chat-empty"><i class="bi bi-chat-square-text"></i>Aún no hay mensajes. ¡Inicia la conversación!</div>');
                    } else {
                        messagesBox.html(messages.map(renderMessage).join(''));
                    }
                    firstLoad = false;
                    if (messages.length) scrollToBottom();
                } else if (messages.length) {
                    if (messagesBox.find('.chat-empty').length) messagesBox.empty();
                    messages.forEach(function(message) { messagesBox.append(renderMessage(message)); });
                    if (!panel.hasClass('is-open')) showBadge(unread + messages.filter(function(message) {
                        return Number(message.usuario_id) !== Number(window.USUARIO_ID);
                    }).length);
                    scrollToBottom();
                }
                messages.forEach(function(message) { lastId = Math.max(lastId, Number(message.id)); });
            })
            .fail(function() {
                if (firstLoad) {
                    messagesBox.html('<div class="chat-empty"><i class="bi bi-exclamation-circle"></i>No se pudo cargar el chat.</div>');
                }
            });
    }

    function clearAttachment() {
        fileInput.val('');
        preview.removeClass('show');
        attachmentName.text('');
    }

    $(function() {
        panel = $('#chatPanel');
        messagesBox = $('#chatMessages');
        input = $('#chatInput');
        fileInput = $('#chatFile');
        preview = $('#chatAttachmentPreview');
        attachmentName = $('#chatAttachmentName');
        badge = $('#chatBadge');

        $('#chatToggle').on('click', function() {
            panel.toggleClass('is-open');
            if (panel.hasClass('is-open')) {
                showBadge(0);
                input.trigger('focus');
                loadMessages();
            }
        });
        $('#chatClose').on('click', function() { panel.removeClass('is-open'); });
        $('#chatFileButton').on('click', function() { fileInput.trigger('click'); });
        $('#chatAttachmentRemove').on('click', clearAttachment);
        fileInput.on('change', function() {
            var file = this.files[0];
            if (!file) return clearAttachment();
            attachmentName.text(file.name + ' (' + formatSize(file.size) + ')');
            preview.addClass('show');
        });
        $('#chatEmojiButton').on('click', function() { $('#chatEmojiPicker').toggleClass('show'); });
        $('.chat-emoji').on('click', function() {
            var emoji = $(this).data('emoji');
            var element = input[0];
            var start = element.selectionStart;
            var end = element.selectionEnd;
            element.value = element.value.substring(0, start) + emoji + element.value.substring(end);
            element.selectionStart = element.selectionEnd = start + emoji.length;
            element.focus();
            $('#chatEmojiPicker').removeClass('show');
        });
        input.on('keydown', function(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                $('#chatForm').trigger('submit');
            }
        });
        $('#chatForm').on('submit', function(event) {
            event.preventDefault();
            var form = this;
            var sendButton = $('#chatSend');
            if (!$.trim(input.val()) && !fileInput[0].files.length) return;
            sendButton.prop('disabled', true);
            $.ajax({
                url: window.BASE_URL + 'chat/enviar',
                type: 'POST',
                data: new FormData(form),
                processData: false,
                contentType: false,
                dataType: 'json'
            }).done(function(response) {
                if (!response.success) {
                    if (window.Swal) Swal.fire({ icon: 'error', title: 'Chat', text: response.message || 'No se pudo enviar.' });
                    return;
                }
                input.val('');
                clearAttachment();
                loadMessages();
            }).fail(function(xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'No se pudo enviar el mensaje.';
                if (window.Swal) Swal.fire({ icon: 'error', title: 'Chat', text: message });
            }).always(function() { sendButton.prop('disabled', false); });
        });

        loadMessages();
        window.setInterval(loadMessages, 12000);
    });
})();
