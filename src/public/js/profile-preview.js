// プロフィール画像を選択したら、その場でプレビュー表示する
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('profile_image');
    const preview = document.getElementById('profilePreview');
    const previewText = document.getElementById('profilePreviewText');

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
});