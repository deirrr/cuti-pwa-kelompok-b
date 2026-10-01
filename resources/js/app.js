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

document.querySelectorAll('[data-status-scroller]').forEach((scroller) => {
    const track = scroller.querySelector('[data-status-scroll-track]');
    const previousButton = scroller.querySelector('[data-status-scroll-previous]');
    const nextButton = scroller.querySelector('[data-status-scroll-next]');

    if (! track || ! previousButton || ! nextButton) {
        return;
    }

    const updateButtons = () => {
        const hasOverflow = track.scrollWidth > track.clientWidth + 1;
        const reachedStart = track.scrollLeft <= 1;
        const reachedEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;

        previousButton.classList.toggle('hidden', ! hasOverflow);
        previousButton.classList.toggle('flex', hasOverflow);
        nextButton.classList.toggle('hidden', ! hasOverflow);
        nextButton.classList.toggle('flex', hasOverflow);
        previousButton.disabled = reachedStart;
        nextButton.disabled = reachedEnd;
    };

    previousButton.addEventListener('click', () => {
        track.scrollBy({ left: -Math.max(160, track.clientWidth * 0.7), behavior: 'smooth' });
    });
    nextButton.addEventListener('click', () => {
        track.scrollBy({ left: Math.max(160, track.clientWidth * 0.7), behavior: 'smooth' });
    });
    track.addEventListener('scroll', updateButtons, { passive: true });
    window.addEventListener('resize', updateButtons);
    requestAnimationFrame(updateButtons);
});

document.querySelectorAll('[data-dialog-pegawai-open]').forEach((button) => {
    const dialog = document.getElementById(button.dataset.dialogPegawaiOpen);

    if (! (dialog instanceof HTMLDialogElement)) {
        return;
    }

    button.addEventListener('click', () => dialog.showModal());

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });
});

document.querySelectorAll('[data-cuti-calendar]').forEach((calendar) => {
    const title = calendar.querySelector('[data-calendar-title]');
    const daysContainer = calendar.querySelector('[data-calendar-days]');
    const previousButton = calendar.querySelector('[data-calendar-previous]');
    const nextButton = calendar.querySelector('[data-calendar-next]');
    const inputContainer = calendar.parentElement?.querySelector('[data-calendar-inputs]');
    const countLabel = calendar.parentElement?.querySelector('[data-calendar-count]');

    if (! title || ! daysContainer || ! previousButton || ! nextButton || ! inputContainer || ! countLabel) {
        return;
    }

    const selectedDates = new Set(JSON.parse(calendar.dataset.selected || '[]'));
    const disabledDates = new Set(JSON.parse(calendar.dataset.disabled || '[]'));
    const maximumSelections = Number.parseInt(calendar.dataset.maxSelections || '31', 10);
    const minimumDate = calendar.dataset.minDate;
    const initialDate = selectedDates.size > 0 ? [...selectedDates][0] : minimumDate;
    const parseDate = (date) => {
        const [year, month, day] = date.split('-').map(Number);

        return new Date(year, month - 1, day);
    };
    const formatDate = (date) => [
        date.getFullYear(),
        String(date.getMonth() + 1).padStart(2, '0'),
        String(date.getDate()).padStart(2, '0'),
    ].join('-');
    let visibleMonth = new Date(parseDate(initialDate).getFullYear(), parseDate(initialDate).getMonth(), 1);

    const syncInputs = () => {
        inputContainer.replaceChildren();

        [...selectedDates].sort().forEach((date) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tanggal_cuti[]';
            input.value = date;
            inputContainer.append(input);
        });

        countLabel.textContent = `${selectedDates.size} tanggal`;
    };

    const renderCalendar = () => {
        const year = visibleMonth.getFullYear();
        const month = visibleMonth.getMonth();
        const firstDay = new Date(year, month, 1).getDay();
        const totalDays = new Date(year, month + 1, 0).getDate();
        const minimumMonth = new Date(parseDate(minimumDate).getFullYear(), parseDate(minimumDate).getMonth(), 1);

        title.textContent = new Intl.DateTimeFormat('id-ID', {
            month: 'long',
            year: 'numeric',
        }).format(visibleMonth);
        previousButton.disabled = visibleMonth <= minimumMonth;
        daysContainer.replaceChildren();

        for (let blank = 0; blank < firstDay; blank += 1) {
            daysContainer.append(document.createElement('span'));
        }

        for (let day = 1; day <= totalDays; day += 1) {
            const currentDate = new Date(year, month, day);
            const date = formatDate(currentDate);
            const isSunday = currentDate.getDay() === 0;
            const isHoliday = disabledDates.has(date);
            const isPast = date < minimumDate;
            const isDisabled = isSunday || isHoliday || isPast;
            const isSelected = selectedDates.has(date);
            const button = document.createElement('button');

            button.type = 'button';
            button.textContent = String(day);
            button.disabled = isDisabled;
            button.setAttribute('aria-label', new Intl.DateTimeFormat('id-ID', { dateStyle: 'full' }).format(currentDate));
            button.className = 'aspect-square rounded-xl text-sm font-semibold transition focus:outline-none focus:ring-4 focus:ring-emerald-500/15';

            if (isSelected) {
                button.classList.add('bg-emerald-600', 'text-white', 'shadow-sm');
            } else if (isDisabled) {
                button.classList.add('cursor-not-allowed', 'bg-red-50', 'text-red-300', 'line-through', 'dark:bg-red-950/40', 'dark:text-red-700');
                button.title = isSunday ? 'Klinik tutup setiap hari Minggu' : (isHoliday ? 'Hari libur nasional' : 'Tanggal sudah lewat');
            } else {
                button.classList.add('text-slate-700', 'hover:bg-emerald-50', 'hover:text-emerald-700', 'dark:text-slate-200', 'dark:hover:bg-emerald-950');
            }

            button.addEventListener('click', () => {
                if (selectedDates.has(date)) {
                    selectedDates.delete(date);
                } else {
                    if (maximumSelections === 1) {
                        selectedDates.clear();
                    }

                    if (selectedDates.size < maximumSelections) {
                        selectedDates.add(date);
                    }
                }

                syncInputs();
                renderCalendar();
            });

            daysContainer.append(button);
        }
    };

    previousButton.addEventListener('click', () => {
        visibleMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() - 1, 1);
        renderCalendar();
    });
    nextButton.addEventListener('click', () => {
        visibleMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() + 1, 1);
        renderCalendar();
    });

    syncInputs();
    renderCalendar();
});

document.querySelectorAll('[data-pegawai-form]').forEach((form) => {
    const peranSelect = form.querySelector('[data-peran]');
    const atasanGroup = form.querySelector('[data-atasan-group]');
    const atasanSelect = form.querySelector('[data-atasan]');
    const jatahGroup = form.querySelector('[data-jatah-group]');
    const jatahInput = form.querySelector('[data-jatah]');

    if (! peranSelect || ! atasanGroup || ! atasanSelect || ! jatahGroup || ! jatahInput) {
        return;
    }

    const sesuaikanForm = () => {
        const staffDipilih = peranSelect.value === 'karyawan';
        const adminHrDipilih = peranSelect.value === 'admin_hr';

        atasanGroup.classList.toggle('hidden', ! staffDipilih);
        atasanSelect.disabled = ! staffDipilih;
        atasanSelect.required = staffDipilih;

        jatahGroup.classList.toggle('hidden', adminHrDipilih);
        jatahInput.disabled = adminHrDipilih;
        jatahInput.required = ! adminHrDipilih;
    };

    peranSelect.addEventListener('change', sesuaikanForm);
    sesuaikanForm();
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
