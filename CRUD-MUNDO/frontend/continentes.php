<?php
require_once __DIR__ . '/../backend/continentes/funcoes.php';

$paginaAtual  = 'continentes';
$tituloPagina = 'Continentes';
$mensagem     = null;
$erro         = null;
$registroEditando = null;

// ----- Processa o formulário (inserir ou atualizar) -----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $dados = [
        'nm_continente' => trim($_POST['nm_continente'] ?? ''),
        'populacao'     => trim($_POST['populacao'] ?? ''),
        'area_km2'      => trim($_POST['area_km2'] ?? ''),
        'total_paises'  => trim($_POST['total_paises'] ?? ''),
    ];

    try {
        if ($dados['nm_continente'] === '') {
            throw new InvalidArgumentException('O nome do continente é obrigatório.');
        }

        if ($_POST['acao'] === 'inserir') {
            inserirContinente($dados);
            $mensagem = 'Continente cadastrado com sucesso.';
        } elseif ($_POST['acao'] === 'atualizar' && isset($_POST['id_continente'])) {
            atualizarContinente((int)$_POST['id_continente'], $dados);
            $mensagem = 'Continente atualizado com sucesso.';
        }
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    } catch (PDOException $e) {
        $erro = 'Erro ao salvar continente: ' . $e->getMessage();
    }
}

// ----- Exclusão -----
if (isset($_GET['excluir'])) {
    try {
        excluirContinente((int)$_GET['excluir']);
        $mensagem = 'Continente excluído com sucesso.';
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            $erro = 'Não é possível excluir: existem países vinculados a este continente.';
        } else {
            $erro = 'Erro ao excluir continente: ' . $e->getMessage();
        }
    }
}

// ----- Carrega registro para edição -----
if (isset($_GET['editar'])) {
    $registroEditando = buscarContinente((int)$_GET['editar']);
}

$continentes = listarContinentes();

require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Continentes</h1>
        <p>Cadastre, edite e exclua continentes do banco bd_mundo.</p>
    </div>
</div>

<?php if ($mensagem): ?>
    <div class="alerta sucesso"><?= htmlspecialchars($mensagem) ?></div>
<?php endif; ?>
<?php if ($erro): ?>
    <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="card">
    <h2><?= $registroEditando ? 'Editar continente' : 'Novo continente' ?></h2>
    <form method="post" data-validar action="continentes.php<?= $registroEditando ? '#form' : '' ?>">
        <input type="hidden" name="acao" value="<?= $registroEditando ? 'atualizar' : 'inserir' ?>">
        <?php if ($registroEditando): ?>
            <input type="hidden" name="id_continente" value="<?= (int)$registroEditando['id_continente'] ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="field full">
                <label>Nome do continente <span class="req">*</span></label>
                <input type="text" name="nm_continente" required maxlength="100"
                       value="<?= htmlspecialchars($registroEditando['nm_continente'] ?? '') ?>">
            </div>
            <div class="field">
                <label>População</label>
                <input type="number" name="populacao" min="0"
                       value="<?= htmlspecialchars($registroEditando['populacao'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Área (km²)</label>
                <input type="number" step="0.01" name="area_km2" min="0"
                       value="<?= htmlspecialchars($registroEditando['area_km2'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Total de países (oficial)</label>
                <input type="number" name="total_paises" min="0"
                       value="<?= htmlspecialchars($registroEditando['total_paises'] ?? '') ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn"><?= $registroEditando ? 'Salvar alterações' : 'Cadastrar continente' ?></button>
                <?php if ($registroEditando): ?>
                    <a href="continentes.php" class="btn secundario">Cancelar</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h2>Continentes cadastrados</h2>
    <div class="busca">
        <input type="text" placeholder="Buscar por nome..." data-busca-tabela="tabela-continentes">
        <small class="busca-aviso text-muted"></small>
    </div>
    <div class="table-wrap">
        <table id="tabela-continentes">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>População</th>
                    <th>Área (km²)</th>
                    <th>Total de países (oficial)</th>
                    <th>Países cadastrados</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($continentes)): ?>
                    <tr><td colspan="6" class="vazio">Nenhum continente cadastrado ainda.</td></tr>
                <?php else: ?>
                    <?php foreach ($continentes as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nm_continente']) ?></td>
                            <td><?= $c['populacao'] !== null ? number_format((float)$c['populacao'], 0, ',', '.') : '—' ?></td>
                            <td><?= $c['area_km2'] !== null ? number_format((float)$c['area_km2'], 2, ',', '.') : '—' ?></td>
                            <td><?= $c['total_paises'] ?? '—' ?></td>
                            <td><span class="badge"><?= (int)$c['qtd_paises_cadastrados'] ?></span></td>
                            <td class="acoes">
                                <a class="btn secundario btn-small" href="continentes.php?editar=<?= (int)$c['id_continente'] ?>#form">Editar</a>
                                <a class="btn perigo btn-small btn-excluir"
                                   data-nome="<?= htmlspecialchars($c['nm_continente']) ?>"
                                   href="continentes.php?excluir=<?= (int)$c['id_continente'] ?>">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
