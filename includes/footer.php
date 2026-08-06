<footer class="bg-dark text-center py-4 mt-5">
            <div class="container">
                <p class="mb-0 text-white-50">&copy; 2026 DiloFlow. Розроблено SerhiiKolobovDeveloper 💻</p>
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // 1. Реєструємо Service Worker (обов'язкова умова для PWA)
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('Service Worker зареєстровано успішно:', reg))
                        .catch(err => console.error('Помилка реєстрації Service Worker:', err));
                });
            }

            // 2. Логіка роботи кнопки "Встановити на телефон"
            let deferredPrompt;
            const installBtn = document.getElementById('installAppBtn');

            // Перехоплюємо подію від браузера
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                
                // Показуємо кнопку
                if (installBtn) {
                    installBtn.style.display = 'inline-block';
                }
            });

            // Клік по кнопці
            if (installBtn) {
                installBtn.addEventListener('click', async () => {
                    if (!deferredPrompt) return;
                    
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`Вибір користувача: ${outcome}`);
                    
                    deferredPrompt = null;
                    installBtn.style.display = 'none';
                });
            }

            // Якщо додаток уже встановлено — ховаємо кнопку
            window.addEventListener('appinstalled', () => {
                if (installBtn) {
                    installBtn.style.display = 'none';
                }
                console.log('DiloFlow успішно встановлено!');
            });
        </script>
    </body>
</html>