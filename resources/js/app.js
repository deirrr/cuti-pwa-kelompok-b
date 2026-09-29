const root = document.documentElement;
const storedTheme = localStorage.getItem('tema');
const prefersDarkTheme = window.matchMedia('(prefers-color-scheme: dark)').matches;

root.classList.toggle('dark', storedTheme === 'gelap' || (storedTheme === null && prefersDarkTheme));

document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const darkThemeEnabled = root.classList.toggle('dark');

        localStorage.setItem('tema', darkThemeEnabled ? 'gelap' : 'terang');
    });
});

const sidebar = document.querySelector('[data-sidebar]');
const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');

const openSidebar = () => {
    if (! sidebar || ! sidebarOverlay) {
        return;
    }

    sidebar.classList.remove('-translate-x-full');
    sidebar.classList.add('translate-x-0');
    sidebarOverlay.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
};

const closeSidebar = () => {
    if (! sidebar || ! sidebarOverlay) {
        return;
    }

    sidebar.classList.remove('translate-x-0');
    sidebar.classList.add('-translate-x-full');
    sidebarOverlay.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
};

document.querySelectorAll('[data-sidebar-open]').forEach((button) => button.addEventListener('click', openSidebar));
document.querySelectorAll('[data-sidebar-close]').forEach((button) => button.addEventListener('click', closeSidebar));

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeSidebar();
    }
});

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = button.parentElement?.querySelector('input');

        if (! input) {
            return;
        }

        const shouldShowPassword = input.type === 'password';
        input.type = shouldShowPassword ? 'text' : 'password';
        button.setAttribute('aria-label', shouldShowPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        button.querySelector('[data-password-show-icon]')?.classList.toggle('hidden', shouldShowPassword);
        button.querySelector('[data-password-hide-icon]')?.classList.toggle('hidden', ! shouldShowPassword);
    });
});

document.querySelectorAll('[data-bagian-organisasi-form]').forEach((form) => {
    const unitSelect = form.querySelector('[data-unit-bisnis]');
    const jenisSelect = form.querySelector('[data-jenis-bagian]');
    const indukSelect = form.querySelector('[data-induk-bagian]');

    if (! unitSelect || ! jenisSelect || ! indukSelect) {
        return;
    }

    const sesuaikanPilihan = () => {
        const selectedUnit = unitSelect.selectedOptions[0];
        const selectedUnitId = unitSelect.value;
        const kategoriUnit = selectedUnit?.dataset.kategori;

        Array.from(indukSelect.options).forEach((option) => {
            const sesuaiUnit = option.value === '' || option.dataset.unitBisnisId === selectedUnitId;
            option.hidden = ! sesuaiUnit;
            option.disabled = ! sesuaiUnit;
        });

        if (indukSelect.selectedOptions[0]?.disabled) {
            indukSelect.value = '';
        }

        Array.from(jenisSelect.options).forEach((option) => {
            if (option.value === '') {
                return;
            }

            const sesuaiKategori = kategoriUnit === 'operasional'
                ? option.value === 'bagian'
                : option.value !== 'bagian';

            option.hidden = ! sesuaiKategori;
            option.disabled = ! sesuaiKategori;
        });

        if (jenisSelect.selectedOptions[0]?.disabled) {
            jenisSelect.value = '';
        }
    };

    unitSelect.addEventListener('change', sesuaikanPilihan);
    sesuaikanPilihan();
});
