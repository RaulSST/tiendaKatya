// Script para manejar el navbar al hacer scroll
const navDesktop = document.getElementById('navContainer');

window.addEventListener('scroll', () => {
    if (window.scrollY > 10) {
        navContainer.classList.add('scrolled');
    }
    else {
        navContainer.classList.remove('scrolled');
    }
});

const navMobile = document.getElementById('navMobile');

window.addEventListener('scroll', () => {
    if (window.scrollY > 10) {
        navMobile.classList.add('scrolled');
    }
    else {
        navMobile.classList.remove('scrolled');
    }
});