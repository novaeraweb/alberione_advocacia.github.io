<?php
/**
 * Alberione Advogados — Partial: Modal de Confirmação
 * admin/partials/modal-confirm.php
 * Uso: inclua este arquivo e chame confirmModal(url, texto) via JS
 */
?>
<!-- Modal de confirmação genérico -->
<div id="modalConfirm" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalConfirmTitle" style="display:none">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-icon-wrap danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 id="modalConfirmTitle">Confirmar ação</h3>
        </div>
        <p id="modalConfirmMsg" class="modal-msg">Tem certeza que deseja continuar?</p>
        <div class="modal-footer">
            <button type="button" id="modalCancelBtn" class="btn btn-outline">
                <i class="fas fa-times"></i> Cancelar
            </button>
            <button type="button" id="modalConfirmBtn" class="btn btn-danger">
                <i class="fas fa-check"></i> Confirmar
            </button>
        </div>
    </div>
</div>

<script>
/**
 * Abre o modal de confirmação genérico.
 * @param {string}   confirmText  - Texto da mensagem de confirmação
 * @param {Function} onConfirm    - Callback executado ao confirmar
 * @param {string}   [title]      - Título do modal (opcional)
 */
function confirmModal(confirmText, onConfirm, title) {
    const overlay = document.getElementById('modalConfirm');
    const msgEl   = document.getElementById('modalConfirmMsg');
    const titleEl = document.getElementById('modalConfirmTitle');
    const confirmBtn = document.getElementById('modalConfirmBtn');
    const cancelBtn  = document.getElementById('modalCancelBtn');

    if (!overlay) return;

    msgEl.textContent   = confirmText || 'Tem certeza que deseja continuar?';
    titleEl.textContent = title || 'Confirmar ação';
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Limpa listeners anteriores clonando os botões
    const newConfirmBtn = confirmBtn.cloneNode(true);
    const newCancelBtn  = cancelBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

    const close = () => {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    };

    newConfirmBtn.addEventListener('click', () => {
        close();
        if (typeof onConfirm === 'function') onConfirm();
    });

    newCancelBtn.addEventListener('click', close);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) close();
    });

    // Fechar com ESC
    const escHandler = (e) => {
        if (e.key === 'Escape') { close(); document.removeEventListener('keydown', escHandler); }
    };
    document.addEventListener('keydown', escHandler);
}

/**
 * Confirmação para formulários de exclusão.
 * @param {HTMLFormElement} form
 * @param {string} msg
 */
function confirmDelete(form, msg) {
    confirmModal(
        msg || 'Esta ação não pode ser desfeita. Deseja realmente excluir?',
        () => form.submit(),
        'Confirmar exclusão'
    );
}
</script>
