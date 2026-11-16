<?php
/** @var array $datePeriod */
// Antes do include: $datePeriod = ['start' => DateTime|null, 'end' => DateTime|null, 'background' => 'bg-white-100']
// Opcional: 'required' => true. Os campos se chamam start_date e end_date, como no banco.
// As datas chegam já validadas por quem usa (filtro ou página)
$periodRequired = !empty($datePeriod['required']);
$periodMonths = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
// Anos do seletor: de 15 anos para a frente até 2000, do mais novo para o mais antigo (como no Figma)
$periodYears = range((int) date('Y') + 15, 2000);

$periodNavClass = 'cursor-pointer rounded-full p-1 hover:bg-secondary-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary disabled:cursor-not-allowed disabled:opacity-30';
$periodToggleClass = 'flex cursor-pointer items-center gap-1.5 rounded-lg border border-gray-100 px-3 py-1 text-base font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary';
$periodListClass = 'absolute left-0 top-full z-10 mt-1 max-h-44 overflow-y-auto rounded-lg bg-white-100 py-1 shadow-card';
$periodOptionClass = 'group flex w-full cursor-pointer items-center justify-between gap-2 px-3 py-0.5 text-left text-sm hover:bg-secondary-100 focus:outline-none focus-visible:bg-secondary-100 aria-pressed:bg-secondary-100';
?>
<div class="relative" data-date-period>
    <div class="flex gap-2.5">
        <?php foreach (['start' => 'Início', 'end' => 'Fim'] as $periodField => $periodBoxLabel): ?>
            <?php $periodDate = $datePeriod[$periodField]; ?>
            <input type="hidden" name="<?= $periodField ?>_date" value="<?= $periodDate?->format('Y-m-d') ?>" data-period-input="<?= $periodField ?>">
            <button type="button" aria-haspopup="dialog" aria-expanded="false" data-period-box="<?= $periodField ?>"
                class="flex min-w-0 flex-1 cursor-pointer flex-col gap-2 rounded-2xl p-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary aria-[invalid=true]:ring-2 aria-[invalid=true]:ring-red-400 <?= $datePeriod['background'] ?>">
                <span class="text-base font-medium"><?= $periodBoxLabel ?></span>
                <span class="flex items-center gap-2.5">
                    <img src="../assets/icons/calendar-range.svg" alt="" width="24" height="24" class="size-6">
                    <span class="text-lg font-medium <?= $periodDate ? '' : 'text-black-200' ?>" data-period-text><?= $periodDate ? $periodDate->format('d/m/Y') : 'dd/mm/aaaa' ?></span>
                </span>
            </button>
        <?php endforeach; ?>
    </div>

    <?php if ($periodRequired): ?>
        <p hidden class="mt-1 text-sm text-red-400" data-period-error>Escolha a data de início e a de fim.</p>
    <?php endif; ?>

    <!-- Um calendário só para as duas caixas: abre em cima delas (embaixo cortava no fim da tela) -->
    <div hidden role="dialog" aria-label="Escolher data" data-period-popup
        class="absolute bottom-full left-0 z-30 mb-2 flex w-[300px] flex-col gap-3 rounded-2xl bg-white-100 p-4 text-primary shadow-card">
        <div class="flex items-center justify-between">
            <button type="button" aria-label="Mês anterior" title="Mês anterior" data-period-prev class="<?= $periodNavClass ?>">
                <img src="../assets/icons/chevron-down.svg" alt="" width="20" height="20" class="size-5 rotate-90">
            </button>

            <div class="flex gap-2">
                <div class="relative">
                    <button type="button" aria-expanded="false" data-period-month-toggle class="<?= $periodToggleClass ?>">
                        <span data-period-month-label></span>
                        <img src="../assets/icons/chevron-down.svg" alt="" width="12" height="12" class="size-3">
                    </button>
                    <div hidden class="<?= $periodListClass ?> w-32" data-period-month-list>
                        <?php foreach ($periodMonths as $monthIndex => $monthName): ?>
                            <button type="button" aria-pressed="false" data-month="<?= $monthIndex ?>" class="<?= $periodOptionClass ?>">
                                <span><?= $monthName ?></span>
                                <span aria-hidden="true" class="invisible text-secondary group-aria-pressed:visible">✓</span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="relative">
                    <button type="button" aria-expanded="false" data-period-year-toggle class="<?= $periodToggleClass ?>">
                        <span data-period-year-label></span>
                        <img src="../assets/icons/chevron-down.svg" alt="" width="12" height="12" class="size-3">
                    </button>
                    <div hidden class="<?= $periodListClass ?> w-24" data-period-year-list>
                        <?php foreach ($periodYears as $periodYear): ?>
                            <button type="button" aria-pressed="false" data-year="<?= $periodYear ?>" class="<?= $periodOptionClass ?>">
                                <span><?= $periodYear ?></span>
                                <span aria-hidden="true" class="invisible text-secondary group-aria-pressed:visible">✓</span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <button type="button" aria-label="Próximo mês" title="Próximo mês" data-period-next class="<?= $periodNavClass ?>">
                <img src="../assets/icons/chevron-down.svg" alt="" width="20" height="20" class="size-5 -rotate-90">
            </button>
        </div>

        <div class="grid grid-cols-7 text-center">
            <?php foreach (['D', 'S', 'T', 'Q', 'Q', 'S', 'S'] as $weekDay): ?>
                <span class="text-sm text-blue-300"><?= $weekDay ?></span>
            <?php endforeach; ?>
        </div>
        <!-- Os dias entram pelo JS, porque mudam a cada mês -->
        <div class="grid grid-cols-7 gap-y-1" data-period-days></div>

        <hr class="border-secondary-100">

        <div class="flex items-center justify-between">
            <span class="text-sm text-black-300" data-period-picked></span>
            <div class="flex items-center gap-2">
                <button type="button" data-period-done
                    class="h-9 cursor-pointer rounded-[48px] bg-sky-100 px-4 text-base font-medium text-sky-400 transition hover:bg-sky-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                    Concluir
                </button>
                <button type="button" aria-label="Cancelar" title="Cancelar" data-period-cancel
                    class="flex size-9 cursor-pointer items-center justify-center rounded-full bg-secondary-200 transition hover:bg-secondary-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                    <img src="../assets/icons/x.svg" alt="" width="16" height="16" class="size-4">
                </button>
            </div>
        </div>
    </div>

    <script>
        {
            const period = document.currentScript.closest('[data-date-period]');
            const popup = period.querySelector('[data-period-popup]');
            const days = period.querySelector('[data-period-days]');
            const prev = period.querySelector('[data-period-prev]');
            const next = period.querySelector('[data-period-next]');
            const monthToggle = period.querySelector('[data-period-month-toggle]');
            const monthList = period.querySelector('[data-period-month-list]');
            const monthLabel = period.querySelector('[data-period-month-label]');
            const yearToggle = period.querySelector('[data-period-year-toggle]');
            const yearList = period.querySelector('[data-period-year-list]');
            const yearLabel = period.querySelector('[data-period-year-label]');
            const pickedText = period.querySelector('[data-period-picked]');
            const error = period.querySelector('[data-period-error]');
            const monthOptions = [...monthList.querySelectorAll('[data-month]')];
            const yearOptions = [...yearList.querySelectorAll('[data-year]')];
            const maxYear = Number(yearOptions[0].dataset.year);
            const minYear = Number(yearOptions[yearOptions.length - 1].dataset.year);
            const dayClass = 'mx-auto flex size-8 cursor-pointer items-center justify-center rounded-full text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary disabled:cursor-not-allowed disabled:text-gray-100';

            // Estado do popup: qual caixa abriu, o mês que aparece e o dia marcado (só salva no Concluir)
            let field = null;
            let view = null;
            let picked = null;

            function getInput(name) {
                return period.querySelector(`[data-period-input="${name}"]`);
            }

            function getBox(name) {
                return period.querySelector(`[data-period-box="${name}"]`);
            }

            function parseDate(value) {
                if (!value) {
                    return null;
                }
                const [year, month, day] = value.split('-').map(Number);
                return new Date(year, month - 1, day);
            }

            function pad(number) {
                return String(number).padStart(2, '0');
            }

            function toValue(date) {
                return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
            }

            function toText(date) {
                return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()}`;
            }

            // Fim não pode ser antes do Início, nem o Início depois do Fim
            function isBlocked(date) {
                if (date.getFullYear() < minYear || date.getFullYear() > maxYear) {
                    return true;
                }
                if (field === 'end') {
                    const start = parseDate(getInput('start').value);
                    return start !== null && date < start;
                }
                const end = parseDate(getInput('end').value);
                return end !== null && date > end;
            }

            function getDayColor(date, month) {
                if (picked !== null && date.getTime() === picked.getTime()) {
                    return 'bg-secondary text-white-100';
                }
                if (date.getMonth() !== month) {
                    return 'text-gray-200 hover:bg-secondary-100';
                }
                return 'hover:bg-secondary-100';
            }

            function renderDays() {
                const year = view.getFullYear();
                const month = view.getMonth();
                // Começa no domingo da semana do dia 1 e completa as semanas com os meses vizinhos
                const offset = new Date(year, month, 1).getDay();
                const total = Math.ceil((offset + new Date(year, month + 1, 0).getDate()) / 7) * 7;
                const cells = [];

                for (let index = 0; index < total; index++) {
                    const date = new Date(year, month, index - offset + 1);
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = date.getDate();
                    button.disabled = isBlocked(date);
                    button.setAttribute('aria-label', toText(date));
                    button.className = `${dayClass} ${getDayColor(date, month)}`;
                    button.addEventListener('click', () => {
                        picked = date;
                        view = new Date(date.getFullYear(), date.getMonth(), 1);
                        render();
                    });
                    cells.push(button);
                }
                days.replaceChildren(...cells);
            }

            function render() {
                const year = view.getFullYear();
                const month = view.getMonth();

                monthLabel.textContent = monthOptions[month].querySelector('span').textContent;
                yearLabel.textContent = year;
                monthOptions.forEach((option, index) => {
                    option.setAttribute('aria-pressed', index === month);
                });
                yearOptions.forEach((option) => {
                    option.setAttribute('aria-pressed', Number(option.dataset.year) === year);
                });
                prev.disabled = year === minYear && month === 0;
                next.disabled = year === maxYear && month === 11;
                pickedText.textContent = picked ? toText(picked) : 'Nenhuma data';
                renderDays();
            }

            function closeLists() {
                monthList.hidden = true;
                yearList.hidden = true;
                monthToggle.setAttribute('aria-expanded', 'false');
                yearToggle.setAttribute('aria-expanded', 'false');
            }

            // Abre a lista com a opção marcada na 3ª linha
            function openList(list, toggle, options, selectedIndex) {
                const opening = list.hidden;
                closeLists();
                if (opening) {
                    list.hidden = false;
                    toggle.setAttribute('aria-expanded', 'true');
                    list.scrollTop = (selectedIndex - 2) * options[selectedIndex].offsetHeight;
                }
            }

            function open(name) {
                field = name;
                picked = parseDate(getInput(name).value);
                const base = picked ?? new Date();
                const year = Math.min(Math.max(base.getFullYear(), minYear), maxYear);
                view = new Date(year, base.getMonth(), 1);

                closeLists();
                period.querySelectorAll('[data-period-box]').forEach((box) => {
                    box.setAttribute('aria-expanded', box.dataset.periodBox === name);
                });
                popup.hidden = false;
                render();
            }

            function close(returnFocus) {
                if (popup.hidden) {
                    return;
                }
                popup.hidden = true;
                closeLists();
                getBox(field).setAttribute('aria-expanded', 'false');
                if (returnFocus) {
                    getBox(field).focus();
                }
            }

            function done() {
                if (picked) {
                    const input = getInput(field);
                    const box = getBox(field);
                    const text = box.querySelector('[data-period-text]');

                    input.value = toValue(picked);
                    text.textContent = toText(picked);
                    text.classList.remove('text-black-200');
                    box.removeAttribute('aria-invalid');
                    if (error && !period.querySelector('[aria-invalid]')) {
                        error.hidden = true;
                    }
                    // Avisa quem usa: o filtro aplica na hora, o item-form só guarda
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
                close(true);
            }

            period.querySelectorAll('[data-period-box]').forEach((box) => {
                box.addEventListener('click', () => {
                    if (!popup.hidden && field === box.dataset.periodBox) {
                        close(true);
                    } else {
                        open(box.dataset.periodBox);
                    }
                });
            });

            prev.addEventListener('click', () => {
                view = new Date(view.getFullYear(), view.getMonth() - 1, 1);
                render();
            });

            next.addEventListener('click', () => {
                view = new Date(view.getFullYear(), view.getMonth() + 1, 1);
                render();
            });

            monthToggle.addEventListener('click', () => {
                openList(monthList, monthToggle, monthOptions, view.getMonth());
            });

            yearToggle.addEventListener('click', () => {
                const selectedIndex = yearOptions.findIndex((option) => Number(option.dataset.year) === view.getFullYear());
                openList(yearList, yearToggle, yearOptions, selectedIndex);
            });

            monthOptions.forEach((option) => {
                option.addEventListener('click', () => {
                    view = new Date(view.getFullYear(), Number(option.dataset.month), 1);
                    closeLists();
                    render();
                    monthToggle.focus();
                });
            });

            yearOptions.forEach((option) => {
                option.addEventListener('click', () => {
                    view = new Date(Number(option.dataset.year), view.getMonth(), 1);
                    closeLists();
                    render();
                    yearToggle.focus();
                });
            });

            period.querySelector('[data-period-done]').addEventListener('click', done);
            period.querySelector('[data-period-cancel]').addEventListener('click', () => {
                close(true);
            });

            // Clique fora fecha sem mudar nada. O composedPath guarda o caminho do clique mesmo
            // quando o dia clicado já saiu da tela (o render troca os botões dos dias)
            document.addEventListener('click', (event) => {
                if (!popup.hidden && !event.composedPath().includes(period)) {
                    close(false);
                }
            });

            period.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape' || popup.hidden) {
                    return;
                }
                // Sem isso o mesmo Esc também fecharia o drawer em volta
                event.preventDefault();
                if (!monthList.hidden || !yearList.hidden) {
                    closeLists();
                } else {
                    close(true);
                }
            });

            // Campo obrigatório (item-form): o input escondido não tem validação nativa, então confere no envio
            if (error) {
                period.closest('form').addEventListener('submit', (event) => {
                    const empty = [...period.querySelectorAll('[data-period-input]')].filter((input) => input.value === '');
                    if (empty.length === 0) {
                        return;
                    }
                    event.preventDefault();
                    empty.forEach((input) => {
                        getBox(input.dataset.periodInput).setAttribute('aria-invalid', 'true');
                    });
                    error.hidden = false;
                    getBox(empty[0].dataset.periodInput).focus();
                });
            }
        }
    </script>
</div>
