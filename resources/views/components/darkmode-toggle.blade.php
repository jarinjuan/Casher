<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Nastavení Tailwindu pro přepínání pomocí třídy
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
</head>
<body class="transition-colors duration-500 bg-white dark:bg-black">

    <div>  
        <button id="theme-toggle" class="group relative inline-flex h-9 w-12 items-center rounded-full border-2 border-black bg-white p-1 transition-colors duration-500 dark:border-white dark:bg-black">
            <span id="toggle-circle" class="flex h-6 w-6 transform items-center justify-center rounded-full bg-black text-white transition-transform duration-500 ease-in-out dark:translate-x-12 dark:bg-white dark:text-black">
                <svg id="sun-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                
                <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </span>
        </button>

    </div>

    <script>
        const toggleBtn = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;

        // 1. Kontrola při načtení: Pokud není v localStorage nic, necháme Light Mode
        if (localStorage.getItem('theme') === 'dark') {
            htmlElement.classList.add('dark');
        } else {
            htmlElement.classList.remove('dark');
        }

        // 2. Funkce přepínání
        toggleBtn.addEventListener('click', () => {
            if (htmlElement.classList.contains('dark')) {
                htmlElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                htmlElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    </script>
</body>
</html>