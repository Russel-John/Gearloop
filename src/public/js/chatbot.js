// src/public/js/chatbot.js
document.addEventListener('DOMContentLoaded', function() {
    const chatWindow = document.getElementById('chat-window');
    const chatToggle = document.getElementById('chatbot-toggle');
    const closeChat = document.getElementById('close-chat');
    const sendBtn = document.getElementById('send-chat');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    if (!chatToggle || !chatWindow) return;

    chatToggle.addEventListener('click', () => {
        chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
        if (chatWindow.style.display === 'flex') {
            chatInput.focus();
        }
    });

    closeChat.addEventListener('click', () => {
        chatWindow.style.display = 'none';
    });

    async function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        addMessage(message, 'user');
        chatInput.value = '';

        const typingIndicator = '<div class="typing"><span></span><span></span><span></span></div>';
        const loadingMsg = addMessage(typingIndicator, 'ai', null, true);

        try {
            const response = await fetch('process-chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            });
            const data = await response.json();

            loadingMsg.innerText = data.response;
        } catch (error) {
            loadingMsg.innerText = "Sorry, I'm having trouble connecting right now.";
        }
    }

    function addMessage(text, sender, id = null, isHTML = false) {
        const div = document.createElement('div');
        div.className = 'message ' + sender;
        if (id) div.id = id;
        
        if (isHTML) {
            div.innerHTML = text;
        } else {
            div.innerText = text;
        }
        
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return div;
    }

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});
