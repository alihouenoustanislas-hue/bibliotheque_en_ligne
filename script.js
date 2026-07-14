/**
 * BIBLIOTHÈQUE EN LIGNE - JavaScript
 * Gestion des interactions utilisateur
 */

document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // Menu mobile responsive
    // ==========================================
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            const isOpen = navLinks.classList.contains('active');
            menuToggle.innerHTML = isOpen ? '✕' : '☰';
        });

        // Fermer le menu quand on clique sur un lien
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                menuToggle.innerHTML = '☰';
            });
        });
    }

    // ==========================================
    // Confirmation de suppression
    // ==========================================
    document.querySelectorAll('.confirm-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ==========================================
    // Animation des cartes au scroll
    // ==========================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const fadeObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                fadeObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.book-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.animationDelay = `${index * 0.1}s`;
        fadeObserver.observe(card);
    });

    // ==========================================
    // Validation des formulaires
    // ==========================================
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                // Reset previous errors
                field.style.borderColor = '';

                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';

                    // Shake animation
                    field.style.animation = 'none';
                    field.offsetHeight; // Trigger reflow
                    field.style.animation = 'shake 0.4s ease';
                }
            });

            // Validation email
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    isValid = false;
                    emailField.style.borderColor = '#e74c3c';
                    alert('Veuillez entrer une adresse email valide.');
                }
            }

            // Validation nombre
            const numberFields = form.querySelectorAll('input[type="number"]');
            numberFields.forEach(field => {
                if (field.value && parseInt(field.value) < 0) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                    alert('Le nombre d'exemplaires ne peut pas être négatif.');
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // ==========================================
    // Auto-focus sur le champ de recherche
    // ==========================================
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        searchInput.focus();
    }

    // ==========================================
    // Compteur de caractères pour textarea
    // ==========================================
    document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.cssText = 'text-align: right; font-size: 0.8rem; color: #7f8c8d; margin-top: 0.25rem;';
        textarea.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${textarea.value.length} / ${maxLength} caractères`;
            counter.style.color = remaining < 20 ? '#e74c3c' : '#7f8c8d';
        }

        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });

    // ==========================================
    // Fermeture automatique des alertes
    // ==========================================
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(100%)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // ==========================================
    // Recherche en temps réel (page résultats)
    // ==========================================
    const liveSearch = document.getElementById('live-search');
    if (liveSearch) {
        let debounceTimer;
        liveSearch.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const term = this.value.toLowerCase();
                document.querySelectorAll('.book-card').forEach(card => {
                    const title = card.querySelector('.book-title').textContent.toLowerCase();
                    const author = card.querySelector('.book-author').textContent.toLowerCase();
                    if (title.includes(term) || author.includes(term)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }, 300);
        });
    }

    // ==========================================
    // Ajout au panier / liste de lecture (AJAX)
    // ==========================================
    document.querySelectorAll('.add-to-wishlist').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const bookId = this.dataset.bookId;
            const readerId = this.dataset.readerId || 1; // Lecteur par défaut

            try {
                const response = await fetch('api_add_wishlist.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id_livre=${bookId}&id_lecteur=${readerId}`
                });

                const result = await response.json();

                if (result.success) {
                    // Feedback visuel
                    this.innerHTML = '✓ Ajouté';
                    this.classList.remove('btn-secondary');
                    this.classList.add('btn-success');
                    this.disabled = true;

                    // Notification
                    showNotification('Livre ajouté à votre liste de lecture !', 'success');
                } else {
                    showNotification(result.message || 'Erreur lors de l'ajout', 'error');
                }
            } catch (err) {
                // Fallback : redirection normale si AJAX échoue
                window.location.href = this.getAttribute('href');
            }
        });
    });

    // ==========================================
    // Notification toast
    // ==========================================
    function showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type}`;
        toast.style.cssText = `
            position: fixed;
            top: 90px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease;
        `;
        toast.innerHTML = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ==========================================
    // CSS animations dynamiques
    // ==========================================
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    console.log('📚 Bibliothèque en ligne - JavaScript chargé avec succès !');
});
