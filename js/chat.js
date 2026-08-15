(function() {
    var panel, messagesBox, input, fileInput, preview, attachmentName, badge, conversationsBox;
    var mode = 'grupo';
    var targetId = null;
    var lastId = 0;
    var unread = 0;
    var firstLoad = true;
    var messagesRequest = false;
    var conversationsRequest = false;

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

    function showChatMessage(html) {
        messagesBox.html('<div class="chat-empty">' + html + '</div>');
    }

    function avatarHtml(item, group) {
        if (!group && item.foto) {
            return '<img src="' + escapeHtml(window.BASE_URL + item.foto) + '" alt="">';
        }
        return '<i class="bi ' + (group ? 'bi-people-fill' : 'bi-person-fill') + '"></i>';
    }

    function renderConversation(item) {
        var active = (mode === item.tipo && Number(targetId || 0) === Number(item.usuario_id || 0));
        var previewText = item.ultimo_mensaje || 'Iniciar conversación';
        return '<button type="button" class="chat-conversation' + (active ? ' active' : '') + '" data-chat-type="' + item.tipo + '" data-chat-user="' + (item.usuario_id || '') + '">' +
            '<span class="chat-conversation-avatar">' + avatarHtml(item, item.tipo === 'grupo') + '</span>' +
            '<span class="chat-conversation-body"><span class="chat-conversation-name">' + escapeHtml(item.nombre) + '</span>' +
            '<span class="chat-conversation-preview">' + escapeHtml(previewText) + '</span></span>' +
            '<span class="chat-conversation-time">' + formatTime(item.ultimo_en) + '</span></button>';
    }

    function loadConversations() {
        if (conversationsRequest) return;
        conversationsRequest = true;
        $.getJSON(window.BASE_URL + 'chat/conversaciones')
            .done(function(response) {
                if (!response.success || !Array.isArray(response.data)) return;
                conversationsBox.html(response.data.map(renderConversation).join(''));
                conversationsBox.find('.chat-conversation').on('click', function() {
                    mode = $(this).data('chat-type');
                    targetId = mode === 'individual' ? Number($(this).data('chat-user')) : null;
                    panel.addClass('room-open');
                    updateHeader();
                    resetConversation();
                    conversationsBox.find('.chat-conversation').removeClass('active');
                    $(this).addClass('active');
                    showBadge(0);
                    input.trigger('focus');
                });
            })
            .always(function() { conversationsRequest = false; });
    }

    function updateHeader() {
        var selected = conversationsBox.find('.chat-conversation.active');
        var name = selected.find('.chat-conversation-name').text();
        var group = mode === 'grupo';
        $('#chatHeaderIcon').attr('class', 'bi ' + (group ? 'bi-people-fill' : 'bi-person-fill'));
        $('#chatHeaderName').text(name || (group ? 'Chat grupal' : 'Chat individual'));
        $('#chatHeaderStatus').text(group ? 'Comunicación del equipo' : 'Conversación privada');
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
            '<div class="chat-avatar">' + avatar + '</div><div class="chat-bubble">' +
            (mine ? '' : '<div class="chat-author">' + escapeHtml(nombre) + '</div>') +
            (message.mensaje ? '<div class="chat-text">' + escapeHtml(message.mensaje) + '</div>' : '') + attachment +
            '<div class="chat-meta">' + formatTime(message.creado_en) + '</div></div></div>';
    }

    function scrollToBottom() { messagesBox.scrollTop(messagesBox[0].scrollHeight); }

    function showBadge(count) {
        unread = count;
        if (unread > 0) badge.text(unread > 99 ? '99+' : unread).css('display', 'flex');
        else badge.hide();
    }

    function loadMessages() {
        if (messagesRequest || (mode === 'individual' && !targetId)) return;
        messagesRequest = true;
        $.getJSON(window.BASE_URL + 'chat/listar', {
            desde: lastId > 0 ? lastId : 0,
            destinatario_id: mode === 'individual' ? targetId : ''
        }).done(function(response) {
            if (!response.success || !Array.isArray(response.data)) return;
            var messages = response.data;
            if (firstLoad) {
                messagesBox.empty();
                if (messages.length) messagesBox.html(messages.map(renderMessage).join(''));
                else messagesBox.html('<div class="chat-empty"><i class="bi bi-chat-square-text"></i>Aún no hay mensajes. ¡Inicia la conversación!</div>');
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
        }).fail(function(xhr) {
            if (firstLoad) {
                var text = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'No se pudo cargar el chat.';
                showChatMessage('<i class="bi bi-exclamation-circle"></i>' + escapeHtml(text));
            }
        }).always(function() { messagesRequest = false; });
    }

    function resetConversation() {
        lastId = 0;
        firstLoad = true;
        showChatMessage('<i class="bi bi-hourglass-split"></i>Cargando mensajes...');
        loadMessages();
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
        conversationsBox = $('#chatConversations');

        $('#chatToggle').on('click', function() {
            panel.toggleClass('is-open');
            if (panel.hasClass('is-open')) {
                loadConversations();
                loadMessages();
                input.trigger('focus');
            }
        });
        $('#chatClose').on('click', function() { panel.removeClass('is-open room-open'); });
        $('#chatBack').on('click', function() { panel.removeClass('room-open'); });
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
            var element = input[0], emoji = $(this).data('emoji');
            var start = element.selectionStart, end = element.selectionEnd;
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
            if (!$.trim(input.val()) && !fileInput[0].files.length) return;
            var formData = new FormData(this);
            if (mode === 'individual') formData.append('destinatario_id', targetId);
            $('#chatSend').prop('disabled', true);
            $.ajax({
                url: window.BASE_URL + 'chat/enviar', type: 'POST', data: formData,
                processData: false, contentType: false, dataType: 'json'
            }).done(function(response) {
                if (!response.success) {
                    if (window.Swal) Swal.fire({ icon: 'error', title: 'Chat', text: response.message || 'No se pudo enviar.' });
                    return;
                }
                input.val('');
                clearAttachment();
                loadMessages();
                loadConversations();
            }).fail(function(xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'No se pudo enviar el mensaje.';
                if (window.Swal) Swal.fire({ icon: 'error', title: 'Chat', text: message });
            }).always(function() { $('#chatSend').prop('disabled', false); });
        });

        updateHeader();
        loadConversations();
        loadMessages();
        window.setInterval(function() {
            loadConversations();
            loadMessages();
        }, 2000);
    });
})();
