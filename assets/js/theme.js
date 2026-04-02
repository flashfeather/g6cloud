// Script para alternância de tema
const themeToggle = document.getElementById("theme-toggle");
const htmlElement = document.documentElement;

if (themeToggle) {
  themeToggle.addEventListener("click", () => {
    htmlElement.classList.toggle("dark");
    localStorage.theme = htmlElement.classList.contains("dark") ? "dark" : "light";
  });
}