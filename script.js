// Function to reinitialize hover styles
function reinitializeHoverStyles() {
    // Force a reflow to ensure CSS is applied correctly
    void document.body.offsetWidth; // This triggers a reflow
}

// Function to handle page transition
function navigateToPage(newPage) {
    // Fade out the current page
    document.body.classList.add("fade-out");
    setTimeout(() => {
        // Load the new page content (e.g., update the DOM or fetch new content)
        loadPageContent(newPage);

        // Fade in the new page
        document.body.classList.remove("fade-out");

        // Reinitialize hover styles after the transition
        reinitializeHoverStyles();
    }, 500); // Match the duration of your fade-out transition
}

// Function to load new page content
function loadPageContent(page) {
    const content = document.getElementById("content");

    // Example: Load content based on the page
    switch (page) {
        case "home":
            content.innerHTML = `
                <h1>Home</h1>
                <button class="button">Click Me</button>
                <div class="card">Card Content</div>
            `;
            break;
        case "about":
            content.innerHTML = `
                <h1>About</h1>
                <p>Learn more about us.</p>
            `;
            break;
        case "contact":
            content.innerHTML = `
                <h1>Contact</h1>
                <p>Get in touch with us.</p>
            `;
            break;
        default:
            content.innerHTML = `<h1>Page Not Found</h1>`;
    }
}

// Attach event listeners to navigation links
document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".nav-link");
    links.forEach(link => {
        link.addEventListener("click", (e) => {
            e.preventDefault(); // Prevent default link behavior
            const page = link.getAttribute("data-page"); // Get the page name from the data attribute
            navigateToPage(page);
        });
    });

    // Handle dropdown menus on hover
    const dropdowns = document.querySelectorAll(".nav-item.dropdown");
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener("mouseenter", () => {
            dropdown.querySelector(".dropdown-menu").classList.add("show");
        });
        dropdown.addEventListener("mouseleave", () => {
            dropdown.querySelector(".dropdown-menu").classList.remove("show");
        });
    });
});

// Reinitialize hover styles on initial load
reinitializeHoverStyles();