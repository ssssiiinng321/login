<?php
require 'session.php'; // Protect this page

$username = $_SESSION['username'] ?? 'User';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot - YourPurpose</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Chat Container Styling */
        .chat-container {
            max-width: 900px;
            margin: 50px auto;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            height: 80vh; /* Fixed height for the chat window */
            overflow: hidden;
        }

        .chat-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.2);
        }

        .chat-title {
            font-size: 1.5rem;
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        .chat-messages {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            scroll-behavior: smooth;
        }

        .message {
            max-width: 80%;
            padding: 1rem;
            border-radius: 1rem;
            line-height: 1.5;
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.user {
            align-self: flex-end;
            background: #4f46e5; /* Indigo */
            color: #fff;
            border-bottom-right-radius: 0.2rem;
        }

        .message.ai {
            align-self: flex-start;
            background: rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            border-bottom-left-radius: 0.2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .chat-input-area {
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            gap: 1rem;
        }

        .chat-input {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 0.5rem;
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .chat-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #a5b4fc;
        }

        .btn-send {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border: none;
            padding: 0 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1.2rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .typing-indicator {
            display: none;
            align-self: flex-start;
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 1rem;
            margin-bottom: 1rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }
        
        /* Markdown-like styling for AI response */
        .message.ai code {
            background: rgba(0,0,0,0.3);
            padding: 2px 4px;
            border-radius: 4px;
            font-family: monospace;
        }
        .message.ai pre {
            background: rgba(0,0,0,0.3);
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin: 0.5rem 0;
        }

    </style>
</head>
<body>

    <nav>
        <div class="logo">
            <i class="fas fa-robot"></i> AI Chat
        </div>
        <div>
            <a href="dashboard.php" class="btn-purchase" style="background: transparent; border: 1px solid rgba(255,255,255,0.2); margin-right: 10px;">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <a href="logout.php" class="btn-purchase"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <main>
        <div class="chat-container">
            <div class="chat-header">
                <span class="chat-title">Gemini Assistant</span>
                <span style="color: rgba(255,255,255,0.5); font-size: 0.9rem;">Powered by Google</span>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <!-- Welcome Message -->
                <div class="message ai">
                    Hello, <?php echo htmlspecialchars($username); ?>! I am your AI assistant. How can I help you today?
                </div>
                
                <!-- Typing Indicator -->
                <div class="typing-indicator" id="typingIndicator">
                    <i class="fas fa-circle-notch fa-spin"></i> AI is thinking...
                </div>
            </div>

            <div class="chat-input-area">
                <input type="text" class="chat-input" id="messageInput" placeholder="Type your message..." autocomplete="off">
                <button class="btn-send" id="sendBtn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </main>

    <script>
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const chatMessages = document.getElementById('chatMessages');
        const typingIndicator = document.getElementById('typingIndicator');

        function appendMessage(text, sender) {
            const div = document.createElement('div');
            div.classList.add('message', sender);
            
            // Simple text content for user, basic formatting for AI could be added here
            // For now, we use innerText for safety for user, and simple HTML for AI if trusted
            // But let's stick to textContent for safety first or simple parsing
            
            if (sender === 'ai') {
                 // Basic markdown-ish parsing for bold and code
                 // VERY basic implementation
                 let formatted = text
                    .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')
                    .replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
                    .replace(/\n/g, '<br>');
                 div.innerHTML = formatted;
            } else {
                div.textContent = text;
            }

            // Insert before typing indicator
            chatMessages.insertBefore(div, typingIndicator);
            scrollToBottom();
        }

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        async function sendMessage() {
            const message = messageInput.value.trim();
            if (!message) return;

            // UI Updates
            appendMessage(message, 'user');
            messageInput.value = '';
            typingIndicator.style.display = 'block';
            scrollToBottom();
            messageInput.disabled = true;
            sendBtn.disabled = true;

            try {
                const response = await fetch('gemini_chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();

                if (response.ok) {
                    appendMessage(data.reply, 'ai');
                } else {
                    appendMessage('Error: ' + (data.error || 'Something went wrong'), 'ai');
                }

            } catch (error) {
                appendMessage('Error: Could not connect to server.', 'ai');
                console.error(error);
            } finally {
                typingIndicator.style.display = 'none';
                messageInput.disabled = false;
                sendBtn.disabled = false;
                messageInput.focus();
                scrollToBottom();
            }
        }

        sendBtn.addEventListener('click', sendMessage);

        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html>
