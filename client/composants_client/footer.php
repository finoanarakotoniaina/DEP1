<footer class="py-10 text-center border-t border-white/5 text-gray-500 text-xs tracking-widest font-title">
    &copy; 2024 SUPERCAR MAURITIUS - TOUS DROITS RÉSERVÉS
</footer>

<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = entry.target.getAttribute('data-delay') || 0;
                setTimeout(() => {
                    entry.target.classList.add('reveal-active');
                }, delay);
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('.reveal-right, .reveal-left').forEach(el => observer.observe(el));

    document.querySelectorAll('.brand-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const textElement = this.querySelector('.typewriter-text');
            if (textElement && !textElement.dataset.hasTyped) {
                textElement.dataset.hasTyped = "true";
                textElement.classList.remove('opacity-0');
                textElement.classList.add('opacity-100');
                
                const text = textElement.getAttribute('data-text');
                let i = 0;
                function typing() {
                    if (i < text.length) {
                        textElement.textContent += text.charAt(i);
                        i++;
                        setTimeout(typing, 15);
                    }
                }
                typing();
            }
        });
    });
</script>
</body>
</html>