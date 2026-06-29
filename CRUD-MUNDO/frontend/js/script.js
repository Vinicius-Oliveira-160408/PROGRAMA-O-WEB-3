// ---------------------------------------------------------------
// CRUD Mundo - interações de front-end
// ---------------------------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
    confirmarExclusoes();
    validarFormularios();
    ativarBuscaDinamica();
    calcularIdadeAutomatica();
});

/**
 * Pede confirmação antes de qualquer exclusão (links/botões com a classe .btn-excluir)
 */
function confirmarExclusoes() {
    document.querySelectorAll('.btn-excluir').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const nome = el.getAttribute('data-nome') || 'este registro';
            const ok = confirm('Tem certeza que deseja excluir "' + nome + '"? Essa ação não pode ser desfeita.');
            if (!ok) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Validação simples de campos obrigatórios antes do envio do formulário
 */
function validarFormularios() {
    document.querySelectorAll('form[data-validar]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            let valido = true;
            let primeiroInvalido = null;

            form.querySelectorAll('[required]').forEach(function (campo) {
                limparErro(campo);
                if (!campo.value || !campo.value.trim()) {
                    valido = false;
                    marcarErro(campo, 'Campo obrigatório.');
                    if (!primeiroInvalido) primeiroInvalido = campo;
                }
            });

            // Validação extra: datas de mandato (início não pode ser depois do fim)
            const inicio = form.querySelector('[name="dt_inicio_mandato"]');
            const fim = form.querySelector('[name="dt_final_mandato"]');
            if (inicio && fim && inicio.value && fim.value && inicio.value > fim.value) {
                valido = false;
                marcarErro(fim, 'A data de início do mandato não pode ser depois da data final.');
                if (!primeiroInvalido) primeiroInvalido = fim;
            }

            if (!valido) {
                e.preventDefault();
                if (primeiroInvalido) primeiroInvalido.focus();
            }
        });
    });
}

function marcarErro(campo, mensagem) {
    campo.style.borderColor = '#c0392b';
    let aviso = campo.parentElement.querySelector('.erro-campo');
    if (!aviso) {
        aviso = document.createElement('small');
        aviso.className = 'erro-campo';
        aviso.style.color = '#c0392b';
        campo.parentElement.appendChild(aviso);
    }
    aviso.textContent = mensagem;
}

function limparErro(campo) {
    campo.style.borderColor = '';
    const aviso = campo.parentElement.querySelector('.erro-campo');
    if (aviso) aviso.remove();
}

/**
 * Busca dinâmica: filtra as linhas de uma tabela conforme o usuário digita
 * Requer um input com [data-busca-tabela="ID_DA_TABELA"]
 */
function ativarBuscaDinamica() {
    document.querySelectorAll('[data-busca-tabela]').forEach(function (input) {
        const tabela = document.getElementById(input.getAttribute('data-busca-tabela'));
        if (!tabela) return;

        input.addEventListener('input', function () {
            const termo = input.value.trim().toLowerCase();
            const linhas = tabela.querySelectorAll('tbody tr');
            let visiveis = 0;

            linhas.forEach(function (linha) {
                const texto = linha.textContent.toLowerCase();
                const corresponde = texto.includes(termo);
                linha.style.display = corresponde ? '' : 'none';
                if (corresponde) visiveis++;
            });

            const aviso = tabela.parentElement.querySelector('.busca-aviso');
            if (aviso) {
                aviso.textContent = termo
                    ? visiveis + ' resultado(s) para "' + termo + '"'
                    : '';
            }
        });
    });
}

/**
 * Calcula a idade automaticamente a partir da data de nascimento (formulário de governantes)
 */
function calcularIdadeAutomatica() {
    const dtNascimento = document.querySelector('[name="dt_nascimento"]');
    const idadeCampo = document.querySelector('[name="idade"]');
    if (!dtNascimento || !idadeCampo) return;

    dtNascimento.addEventListener('change', function () {
        if (!dtNascimento.value) return;
        const nascimento = new Date(dtNascimento.value);
        const hoje = new Date();
        let idade = hoje.getFullYear() - nascimento.getFullYear();
        const aindaNaoFezAniversario =
            hoje.getMonth() < nascimento.getMonth() ||
            (hoje.getMonth() === nascimento.getMonth() && hoje.getDate() < nascimento.getDate());
        if (aindaNaoFezAniversario) idade--;
        if (idade >= 0) idadeCampo.value = idade;
    });
}
