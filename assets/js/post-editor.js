/**
 * Prepara o HTML editorial em Base64 UTF-8 e valida imagens antes do envio.
 * Imagens por URL continuam permitidas; arquivos devem usar Imagem Destaque.
 */
(function () {
    'use strict';

    const form = document.getElementById('postForm');
    const editor = document.getElementById('quillEditor');
    const rawInput = document.getElementById('conteudoInput');
    const encodedInput = document.getElementById('conteudoB64Input');
    if (!form || !editor || !rawInput || !encodedInput) return;

    const warning = document.createElement('div');
    warning.className = 'alert alert-error';
    warning.setAttribute('role', 'alert');
    warning.setAttribute('tabindex', '-1');
    warning.style.display = 'none';
    form.before(warning);

    function stopSubmission(event, message) {
        event.preventDefault();
        event.stopImmediatePropagation();
        warning.textContent = message;
        warning.style.display = '';
        warning.focus();
    }

    // Capture executa antes da serialização do conteúdo e do aviso de saída.
    form.addEventListener('submit', function (event) {
        const hasEmbeddedImage = Array.from(editor.querySelectorAll('img')).some(image => {
            const source = (image.getAttribute('src') || '').replace(/[\u0000-\u0020]/g, '');
            return /^(data|blob):/i.test(source);
        });

        if (hasEmbeddedImage) {
            stopSubmission(event, 'O conteúdo contém uma imagem colada no texto. Remova essa imagem do editor e envie o arquivo pelo campo Imagem Destaque antes de salvar.');
            return;
        }

        try {
            const content = editor.querySelector('.ql-editor');
            if (!content) throw new Error('Editor indisponível.');

            const bytes = new TextEncoder().encode(content.innerHTML);
            let binary = '';
            for (let i = 0; i < bytes.length; i += 0x8000) {
                binary += String.fromCharCode.apply(null, bytes.subarray(i, i + 0x8000));
            }
            encodedInput.value = btoa(binary);
            rawInput.value = '';
            rawInput.disabled = true;
            warning.style.display = 'none';
        } catch (error) {
            stopSubmission(event, 'Não foi possível preparar o conteúdo do post. Recarregue a página e tente novamente.');
        }
    }, true);
})();
