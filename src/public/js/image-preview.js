// 画像選択時にその場でプレビュー表示する
document.addEventListener('DOMContentLoaded', () => {
    setupImagePreview('profile_image', 'profilePreview', 'profilePreviewText');
    setupImagePreview('image', 'itemPreview', 'itemPreviewText');
});

// 画像プレビューを設定する
function setupImagePreview(inputId, previewId, previewTextId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const previewText = document.getElementById(previewTextId);

    if (!input || !preview || !previewText) {
        return;
    }

    input.addEventListener('change', (event) => {
        const file = event.target.files && event.target.files[0];

        if (!file) {
            return;
        }

        // 画像ファイル以外は無視
        if (!file.type.startsWith('image/')) {
            return;
        }

        const imageUrl = URL.createObjectURL(file);

        preview.src = imageUrl;
        preview.classList.remove('is-hidden');
        previewText.classList.add('is-hidden');

        preview.onload = () => {
            URL.revokeObjectURL(imageUrl);
        };
    });
}