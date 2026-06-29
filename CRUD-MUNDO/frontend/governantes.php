<?php
require_once __DIR__ . '/../backend/governantes/funcoes.php';

$paginaAtual  = 'governantes';
$tituloPagina = 'Governantes';
$mensagem     = null;
$erro         = null;
$registroEditando = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $dados = [
        'nm_governante'     => trim($_POST['nm_governante'] ?? ''),
        'partido_politico'  => trim($_POST['partido_politico'] ?? ''),
        'dt_nascimento'     => trim($_POST['dt_nascimento'] ?? ''),
        'idade'             => trim($_POST['idade'] ?? ''),
        'dt_inicio_mandato' => trim($_POST['dt_inicio_mandato'] ?? ''),
        'dt_final_mandato'  => trim($_POST['dt_final_mandato'] ?? ''),
    ];

    try {
        if ($dados['nm_governante'] === '') {
            throw new InvalidArgumentException('O nome do governante é obrigatório.');
        }
        if ($dados['dt_inicio_mandato'] !== '' && $dados['dt_final_mandato'] !== ''
            && $dados['dt_inicio_mandato'] > $dados['dt_final_mandato']) {
            throw new InvalidArgumentException('A data de início do mandato não pode ser depois da data final.');
        }

        if ($_POST['acao'] === 'inserir') {
            inserirGovernante($dados);
            $mensagem = 'Governante cadastrado com sucesso.';
        } elseif ($_POST['acao'] === 'atualizar' && isset($_POST['id_governante'])) {
            atualizarGovernante((int)$_POST['id_governante'], $dados);
            $mensagem = 'Governante atualizado com sucesso.';
        }
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    } catch (PDOException $e) {
        $erro = 'Erro ao salvar governante: ' . $e->getMessage();
    }
}

if (isset($_GET['excluir'])) {
    try {
        excluirGovernante((int)$_GET['excluir']);
        $mensagem = 'Governante excluído com sucesso.';
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            $erro = 'Não é possível excluir: este governante está vinculado a um país ou cidade.';
        } else {
            $erro = 'Erro ao excluir governante: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['editar'])) {
    $registroEditando = buscarGovernante((int)$_GET['editar']);
}

$governantes = listarGovernantes();

require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Governantes</h1>
        <p>Cadastre, edite e exclua governantes que podem ser vinculados a países e cidades.</p>
    </div>
</div>

<?php if ($mensagem): ?>
    <div class="alerta sucesso"><?= htmlspecialchars($mensagem) ?></div>
<?php endif; ?>
<?php if ($erro): ?>
    <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="card" id="form">
    <h2><?= $registroEditando ? 'Editar governante' : 'Novo governante' ?></h2>
    <form method="post" data-validar action="governantes.php<?= $registroEditando ? '#form' : '' ?>">
        <input type="hidden" name="acao" value="<?= $registroEditando ? 'atualizar' : 'inserir' ?>">
        <?php if ($registroEditando): ?>
            <input type="hidden" name="id_governante" value="<?= (int)$registroEditando['id_governante'] ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="field full">
                <label>Nome <span class="req">*</span></label>
                <input type="text" name="nm_governante" required maxlength="150"
                       value="<?= htmlspecialchars($registroEditando['nm_governante'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Partido político</label>
                <input type="text" name="partido_politico" maxlength="150"
                       value="<?= htmlspecialchars($registroEditando['partido_politico'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Data de nascimento</label>
                <input type="date" name="dt_nascimento"
                       value="<?= htmlspecialchars($registroEditando['dt_nascimento'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Idade</label>
                <input type="number" name="idade" min="0" max="130"
                       value="<?= htmlspecialchars($registroEditando['idade'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Início do mandato</label>
                <input type="date" name="dt_inicio_mandato"
                       value="<?= htmlspecialchars($registroEditando['dt_inicio_mandato'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Final do mandato</label>
                <input type="date" name="dt_final_mandato"
                       value="<?= htmlspecialchars($registroEditando['dt_final_mandato'] ?? '') ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn"><?= $registroEditando ? 'Salvar alterações' : 'Cadastrar governante' ?></button>
                <?php if ($registroEditando): ?>
                    <a href="governantes.php" class="btn secundario">Cancelar</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h2>Governantes cadastrados</h2>
    <div class="busca">
        <input type="text" placeholder="Buscar por nome..." data-busca-tabela="tabela-governantes">
        <small class="busca-aviso text-muted"></small>
    </div>
    <div class="table-wrap">
        <table id="tabela-governantes">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Partido</th>
                    <th>Nascimento</th>
                    <th>Idade</th>
                    <th>Mandato</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($governantes)): ?>
                    <tr><td colspan="6" class="vazio">Nenhum governante cadastrado ainda.</td></tr>
                <?php else: ?>
                    <?php foreach ($governantes as $g): ?>
                        <tr>
                            <td><?= htmlspecialchars($g['nm_governante']) ?></td>
                            <td><?= htmlspecialchars($g['partido_politico'] ?? '—') ?></td>
                            <td><?= $g['dt_nascimento'] ? date('d/m/Y', strtotime($g['dt_nascimento'])) : '—' ?></td>
                            <td><?= $g['idade'] ?? '—' ?></td>
                            <td>
                                <?= $g['dt_inicio_mandato'] ? date('d/m/Y', strtotime($g['dt_inicio_mandato'])) : '—' ?>
                                &rarr;
                                <?= $g['dt_final_mandato'] ? date('d/m/Y', strtotime($g['dt_final_mandato'])) : 'atual' ?>
                            </td>
                            <td class="acoes">
                                <a class="btn secundario btn-small" href="governantes.php?editar=<?= (int)$g['id_governante'] ?>#form">Editar</a>
                                <a class="btn perigo btn-small btn-excluir"
                                   data-nome="<?= htmlspecialchars($g['nm_governante']) ?>"
                                   href="governantes.php?excluir=<?= (int)$g['id_governante'] ?>">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
