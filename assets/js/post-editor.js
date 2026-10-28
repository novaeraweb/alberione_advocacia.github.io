/**
 * Impede o envio de imagens incorporadas no HTML antes do bloqueio da hospedagem.
 * Imagens por URL continuam permitidas; arquivos devem usar Imagem Destaque.
 */
(function () {
    'use strict';

    const form = document.getElementById('postForm');
    const editor = document.getElementById('quillEditor');
    if (!form || !editor) return;

    const warning = document.createElement('div');
    warning.className = 'alert alert-error';
    warning.setAttribute('role', 'alert');
    warning.setAttribute('tabindex', '-1');
    warning.style.display = 'none';
    warning.textContent = 'O conteúdo contém uma imagem colada no texto. Remova essa imagem do editor e envie o arquivo pelo campo Imagem Destaque antes de salvar.';
    form.before(warning);

    // Capture executa antes da serialização do conteúdo e do aviso de saída.
    form.addEventListener('submit', function (event) {
        const hasEmbeddedImage = Array.from(editor.querySelectorAll('img')).some(image => {
            const source = (image.getAttribute('src') || '').replace(/[\u0000-\u0020]/g, '');
            return /^(data|blob):/i.test(source);
        });

        warning.style.display = hasEmbeddedImage ? '' : 'none';
        if (hasEmbeddedImage) {
            event.preventDefault();
            event.stopImmediatePropagation();
            warning.focus();
        }
    }, true);
})();
