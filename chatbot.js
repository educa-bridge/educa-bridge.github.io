
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const chatbotToggle = document.querySelector('.chatbot-toggle');
    const chatNavIcon = document.querySelector('.chat-nav');
    const chatbotContainer = document.querySelector('.chatbot-container');
    const closeChatbot = document.querySelector('.close-chatbot');
    const chatbotMessages = document.querySelector('.chatbot-messages');
    const chatbotInput = document.querySelector('.chatbot-text-input');
    const chatbotSendBtn = document.querySelector('.chatbot-send-btn');
            
    // Predefined responses
    const responses = {
        "hello": "Hello! I'm EduBridge Assistant. How can I help you today?",
        "loan": "EduBridge offers flexible student loans for international students. You can learn more on our <a href='students.html'>Students page</a>.",
        "university": "We partner with universities worldwide. Check our <a href='universities.html'>Universities page</a> for more information.",
        "invest": "Interested in investing? Visit our <a href='investors.html'>Investors page</a> to learn about opportunities.",
        "contact": "You can reach our support team through our <a href='support.html'>Support page</a>.",
        "default": "I'm sorry, I didn't understand that. Could you try asking about loans, universities, or investments?"
    };

    // Keywords for responses
    const keywords = {
        "hi": "hello",
        "hey": "hello",
        "loan": "loan",
        "finance": "loan",
        "university": "university",
        "school": "university",
        "invest": "invest",
        "investment": "invest",
        "contact": "contact",
        "support": "contact",
        "help": "contact"
    };

    // Toggle chatbot visibility
    function toggleChatbot() {
        chatbotContainer.classList.toggle('active');
        if (chatbotContainer.classList.contains('active')) {
            chatbotInput.focus();
        }
    }

    // Add message to chat
    function addMessage(text, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('chatbot-message');
        messageDiv.classList.add(isUser ? 'chatbot-user' : 'chatbot-response');
        messageDiv.innerHTML = `<p>${text}</p>`;
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Process user input
    function processInput() {
        const userInput = chatbotInput.value.trim().toLowerCase();
        if (userInput === '') return;
                
        addMessage(userInput, true);
        chatbotInput.value = '';
                
        // Simulate "typing" delay
        setTimeout(() => {
            let responseKey = 'default';
                    
            // Check for keywords
            for (const [word, key] of Object.entries(keywords)) {
                if (userInput.includes(word)) {
                    responseKey = key;
                    break;
                }
            }
                    
            addMessage(responses[responseKey]);
        }, 800);
    }

    // Event listeners
    chatbotToggle.addEventListener('click', toggleChatbot);
    closeChatbot.addEventListener('click', toggleChatbot);
            
    // Make nav chat icon also open the popup
    chatNavIcon.addEventListener('click', function(e) {
        e.preventDefault();
        toggleChatbot();
    });

    // Send message on button click
    chatbotSendBtn.addEventListener('click', processInput);
            
    // Send message on Enter key
    chatbotInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            processInput();
        }
    });

    // Initial greeting
    setTimeout(() => {
        addMessage(responses['hello']);
    }, 1000);
});