// カスタムセレクトの初期化
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.custom-select').forEach(select => {
        const trigger = select.querySelector('.custom-select__trigger');
        const valueEl = select.querySelector('.custom-select__value');
        const options = select.querySelectorAll('.custom-select__option');
        const hidden = select.querySelector('input[type="hidden"]');
        const placeholder = select.dataset.placeholder || '選択してください';
        const autoSubmit = select.dataset.autoSubmit === 'true';

        // 選択中の表示テキストを更新する
        const applyValue = (label) => {
            const text = (label || '').trim();

            if (text) {
                valueEl.textContent = text;
                select.classList.add('has-value');
            } else {
                valueEl.textContent = placeholder;
                select.classList.remove('has-value');
            }
        };

        // セレクトの開閉状態を切り替える
        const setOpen = (open) => {
            select.classList.toggle('is-open', open);
            trigger.setAttribute('aria-expanded', open ? 'true' : 'false');

            // 未選択のまま開いたときは表示を空にする
            if (open && !hidden.value) {
                valueEl.textContent = '';
            }

            // 未選択のまま閉じたときはプレースホルダーに戻す
            if (!open && !hidden.value) {
                applyValue('');
            }
        };

        // 初期値がある場合はその内容を表示する
        if (hidden.value) {
            const selected = [...options].find(option => option.dataset.value === hidden.value);

            if (selected) {
                applyValue(selected.textContent);
                selected.classList.add('is-selected');
            } else {
                applyValue('');
            }
        } else {
            applyValue('');
        }

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            setOpen(!select.classList.contains('is-open'));
        });

        options.forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const value = option.dataset.value;
                const label = option.textContent.trim();

                options.forEach(o => o.classList.remove('is-selected'));
                option.classList.add('is-selected');

                hidden.value = value;
                applyValue(label);
                setOpen(false);

                // 自動送信設定がある場合はフォーム送信する
                if (autoSubmit) {
                    const form = hidden.closest('form');

                    if (form) {
                        form.submit();
                    }
                }
            });
        });

        document.addEventListener('click', (e) => {
            if (!select.contains(e.target)) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                setOpen(false);
            }
        });
    });
});