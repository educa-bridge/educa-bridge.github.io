
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
        "what": "EduBridge is a fintech platform that helps students access affordable education financing options. We connect students with low-interest loans, financial guidance, and academic support to help them achieve their educational goals. Learn more in our <a href='whoweare.html'>About page</a>.",
        "apply": "Any student enrolled or planning to enroll in a partner institution in Latin America (currently Mexico, and soon expanding to Peru and Brazil) is elegible to apply, subject to eligibility cirteria.",
        "difference": "Unlike traditional banks, EduBridge focuses specifically on students. We offer more flexible terms, lower interest rates, and additional support such as financial literacy resources and academic success tools.",
        "documents": "You'll typically need: \n 1. A valid ID \n 2. Proof of enrollment or acceptance \n 3. Financial information (e.g., guardian income, expenses) \n 4. Academic records \n I'll be happy to walk you through the steps! Please type 'begin' to get started.",
        "files": "Now that you are ready to take a next step into your future, let me walk you through the required documents needed to apply for our flexible, accessible, and student-focused loans! \n 1. Take a clear photo of your national ID or passport. Make sure all details are easily visible—avoid blurry images and ensure good lighting without glare or shadows. \n 2. For proof of enrollment or acceptance you can provide the acceptance letter of the institution you have been admitted into, a screenshot of the university's portal showing that you are a current student, or an academic transcript of the institution. \n 3. No sé ups \n 4. For academic records please submit your official High School transcript or, if you are currently attending a higher-education institution, your official transcript of the institution. \n If you are not sure about a document, do not worry! We will let you know so you can re-submit any missing information.",
        "long": "Once all required documents are submitted, the approval process usually takes just a few minutes. You'll receive a decision instantly or shortly after -- and we'll keep you updated every step of the way!",
        "secure": "Your information is secure with us. We use industry-standard encryption and comply with data protection laws to keep your personal and financial information safe.",
        "repay": "Yes, you can repay your loan early! There are no penalties for early repayment, and we encourage students to do so if possible to reduce interest.",
        "can't": "If you are unable to make a payment, do not worry, we understand that life happens. If you're facing difficulties, contact us right away. We offer flexible repayment plans and support options to help you stay on track!",
        "advice": "We offer financial advice! Our platform includes financial literacy resources, budget tips, and access to mentors who can help you make smart money decisions while studying.",
        "started": "To get started, just click on the 'Log In' button or chat more with me to guide you through the steps!",
        "loan": "EduBridge offers flexible student loans for international students. You can learn more on our <a href='students.html'>Students page</a>.",
        "university": "We partner with universities worldwide. Check our <a href='universities.html'>Universities page</a> for more information.",
        "invest": "Interested in investing? Visit our <a href='investors.html'>Investors page</a> to learn about opportunities.",
        "contact": "You can look for specific answers to certain questions at our <a href='FAQ.html'>FAQ page</a>.",
        "default": "I'm sorry, I didn't understand that. Could you try asking about loans, universities, or investments?",
        "bye": "Thanks for chatting with me! If you need anything else, you know where to find me. Good luck!"
    };

    // Keywords for responses
    const keywords = {
        //What is EduBridge answers
        "what is edubridge": "what",
        "What is edubridge": "what",
        "what is Edubridge": "what",
        "what is EduBridge": "what",
        "what is": "what",
        "What is EduBridge": "what",
        "what is edubridge?": "what",
        "what is Edubridge?": "what",
        "What is edubridge?": "what",
        "What is Edubridge?": "what",
        "what is EduBridge?": "what",
        "What is EduBridge?": "what",
        "is edubridge?": "what",
        "is Edubridge?": "what",
        "is EduBridge?": "what",
        "is eduBridge?": "what",

        //Apply to a loan answers
        "documents": "documents",
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