<?php
require_once __DIR__ . '/../backend/paises/funcoes.php';
require_once __DIR__ . '/../backend/continentes/funcoes.php';
require_once __DIR__ . '/../backend/governantes/funcoes.php';

$paginaAtual  = 'paises';
$tituloPagina = 'Países';
$mensagem     = null;
$erro         = null;
$registroEditando = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $dados = [
        'nm_pais'         => trim($_POST['nm_pais'] ?? ''),
        'continente_id'   => trim($_POST['continente_id'] ?? ''),
        'populacao'       => trim($_POST['populacao'] ?? ''),
        'area_km2'        => trim($_POST['area_km2'] ?? ''),
        'idioma'          => trim($_POST['idioma'] ?? ''),
        'governante_id'   => trim($_POST['governante_id'] ?? ''),
        'clima'           => trim($_POST['clima'] ?? ''),
        'regime_politico' => trim($_POST['regime_politico'] ?? ''),
        'moeda'           => trim($_POST['moeda'] ?? ''),
    ];

    try {
        if ($dados['nm_pais'] === '') {
            throw new InvalidArgumentException('O nome do país é obrigatório.');
        }
        if ($dados['continente_id'] === '') {
            throw new InvalidArgumentException('Selecione o continente do país.');
        }

        if ($_POST['acao'] === 'inserir') {
            inserirPais($dados);
            $mensagem = 'País cadastrado com sucesso.';
        } elseif ($_POST['acao'] === 'atualizar' && isset($_POST['id_pais'])) {
            atualizarPais((int)$_POST['id_pais'], $dados);
            $mensagem = 'País atualizado com sucesso.';
        }
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    } catch (PDOException $e) {
        $erro = 'Erro ao salvar país: ' . $e->getMessage();
    }
}

if (isset($_GET['excluir'])) {
    try {
        excluirPais((int)$_GET['excluir']);
        $mensagem = 'País excluído com sucesso.';
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            $erro = 'Não é possível excluir: existem cidades vinculadas a este país.';
        } else {
            $erro = 'Erro ao excluir país: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['editar'])) {
    $registroEditando = buscarPais((int)$_GET['editar']);
}

$paises      = listarPaises();
$continentes = listarContinentes();
$governantes = listarGovernantes();

require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Países</h1>
        <p>Cadastre países e associe continente, governante, clima, regime e moeda.</p>
    </div>
</div>

<?php if ($mensagem): ?>
    <div class="alerta sucesso"><?= htmlspecialchars($mensagem) ?></div>
<?php endif; ?>
<?php if ($erro): ?>
    <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<?php if (empty($continentes)): ?>
    <div class="alerta erro">
        Cadastre pelo menos um <a href="continentes.php">continente</a> antes de adicionar países.
    </div>
<?php endif; ?>

<div class="card" id="form">
    <h2><?= $registroEditando ? 'Editar país' : 'Novo país' ?></h2>
    <form method="post" data-validar action="paises.php<?= $registroEditando ? '#form' : '' ?>">
        <input type="hidden" name="acao" value="<?= $registroEditando ? 'atualizar' : 'inserir' ?>">
        <?php if ($registroEditando): ?>
            <input type="hidden" name="id_pais" value="<?= (int)$registroEditando['id_pais'] ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="field full">
                <label>Nome do país <span class="req">*</span></label>
                <input type="text" name="nm_pais" required maxlength="100"
                       value="<?= htmlspecialchars($registroEditando['nm_pais'] ?? '') ?>">
            </div>

            <div class="field">
                <label>Continente <span class="req">*</span></label>
                <select name="continente_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($continentes as $c): ?>
                        <option value="<?= (int)$c['id_continente'] ?>"
                            <?= (isset($registroEditando['continente_id']) && $registroEditando['continente_id'] == $c['id_continente']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nm_continente']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label>Governante</label>
                <select name="governante_id">
                    <option value="">Nenhum / não informado</option>
                    <?php foreach ($governantes as $g): ?>
                        <option value="<?= (int)$g['id_governante'] ?>"
                            <?= (isset($registroEditando['governante_id']) && $registroEditando['governante_id'] == $g['id_governante']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['nm_governante']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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
                <label>Idioma</label>
                <input type="text" name="idioma" maxlength="100"
                       value="<?= htmlspecialchars($registroEditando['idioma'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Clima</label>
                <input type="text" name="clima" maxlength="100"
                       value="<?= htmlspecialchars($registroEditando['clima'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Regime político</label>
                <input type="text" name="regime_politico" maxlength="100"
                       value="<?= htmlspecialchars($registroEditando['regime_politico'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Moeda</label>
                <input type="text" name="moeda" maxlength="100"
                       value="<?= htmlspecialchars($registroEditando['moeda'] ?? '') ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn"><?= $registroEditando ? 'Salvar alterações' : 'Cadastrar país' ?></button>
                <?php if ($registroEditando): ?>
                    <a href="paises.php" class="btn secundario">Cancelar</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h2>Países cadastrados</h2>
    <div class="busca">
        <input type="text" placeholder="Buscar por nome, continente, idioma..." data-busca-tabela="tabela-paises">
        <small class="busca-aviso text-muted"></small>
    </div>
    <div class="table-wrap">
        <table id="tabela-paises">
            <thead>
                <tr>
                    <th>País</th>
                    <th>Continente</th>
                    <th>Governante</th>
                    <th>População</th>
                    <th>Idioma</th>
                    <th>Cidades</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($paises)): ?>
                    <tr><td colspan="7" class="vazio">Nenhum país cadastrado ainda.</td></tr>
                <?php else: ?>
                    <?php foreach ($paises as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nm_pais']) ?></td>
                            <td><?= htmlspecialchars($p['nm_continente'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($p['nm_governante'] ?? '—') ?></td>
                            <td><?= $p['populacao'] !== null ? number_format((float)$p['populacao'], 0, ',', '.') : '—' ?></td>
                            <td><?= htmlspecialchars($p['idioma'] ?? '—') ?></td>
                            <td><span class="badge"><?= (int)$p['qtd_cidades'] ?></span></td>
                            <td class="acoes">
                                <a class="btn secundario btn-small" href="paises.php?editar=<?= (int)$p['id_pais'] ?>#form">Editar</a>
                                <a class="btn perigo btn-small btn-excluir"
                                   data-nome="<?= htmlspecialchars($p['nm_pais']) ?>"
                                   href="paises.php?excluir=<?= (int)$p['id_pais'] ?>">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
