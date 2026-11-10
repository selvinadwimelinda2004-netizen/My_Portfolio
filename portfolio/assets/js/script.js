
const typingText = document.getElementById("typing-text");

const professions = [
    "Web Developer",
    "Programmer",
    "IoT Enthusiast"
];

let professionIndex = 0;

function changeProfession() {
    if (!typingText) return;

    typingText.style.opacity = 0;

    setTimeout(() => {
        professionIndex = (professionIndex + 1) % professions.length;
        typingText.textContent = professions[professionIndex];
        typingText.style.opacity = 1;
    }, 300);
}

if (typingText) {
    setInterval(changeProfession, 2500);
}
