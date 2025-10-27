let message = [
  "Bienvenue sur Class Connect, votre portail universitaire intelligent !",
  "Gérez vos salles, étudiants et notes en toute simplicité.",
  "Découvrez une nouvelle façon d'apprendre et d'enseigner.",
  "Class Connect : l'avenir de l'éducation à portée de main !",
];

const container = document.getElementById("welcomeText");
let msgIndex = 0;
let charIndex = 0;
let typing = true;

function typeWriter() {
  const currentMessage = message[msgIndex];

  if (typing) {
    container.textContent = currentMessage.substring(0, charIndex);
    charIndex++;
    if (charIndex === currentMessage.length) {
      typing = false;
      setTimeout(typeWriter, 2000); // Pause avant suppression
      
    } else {
      setTimeout(typeWriter, 50); // Vitesse de frappe
    }
  } else {
    container.textContent = currentMessage.substring(0, charIndex - 1);
    charIndex--;
    if (charIndex === 0) {
      typing = true;
      msgIndex = (msgIndex + 1) % message.length; // CORRECTION ICI : message.length au lieu de messages.length
      setTimeout(typeWriter, 500); // Pause avant le message suivant
    } else {
      setTimeout(typeWriter, 40); // Vitesse d'effacement
    }
  }
}

typeWriter();