<?php
require_once __DIR__ . '/../backend/cidades/funcoes.php';
require_once __DIR__ . '/../backend/paises/funcoes.php';
require_once __DIR__ . '/../backend/governantes/funcoes.php';

$paginaAtual  = 'cidades';
$tituloPagina = 'Cidades';
$mensagem     = null;
$erro         = null;
$registroEditando = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $dados = [
        'nome'          => trim($_POST['nome'] ?? ''),
        'pais_id'       => trim($_POST['pais_id'] ?? ''),
        'populacao'     => trim($_POST['populacao'] ?? ''),
        'area_km2'      => trim($_POST['area_km2'] ?? ''),
        'clima'         => trim($_POST['clima'] ?? ''),
        'governante_id' => trim($_POST['governante_id'] ?? ''),
        'dt_fundacao'   => trim($_POST['dt_fundacao'] ?? ''),
    ];

    try {
        if ($dados['nome'] === '') {
            throw new InvalidArgumentException('O nome da cidade é obrigatório.');
        }
        if ($dados['pais_id'] === '') {
            throw new InvalidArgumentException('Selecione o país desta cidade.');
        }

        if ($_POST['acao'] === 'inserir') {
            inserirCidade($dados);
            $mensagem = 'Cidade cadastrada com sucesso.';
        } elseif ($_POST['acao'] === 'atualizar' && isset($_POST['id_cidade'])) {
            atualizarCidade((int)$_POST['id_cidade'], $dados);
            $mensagem = 'Cidade atualizada com sucesso.';
        }
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    } catch (PDOException $e) {
        $erro = 'Erro ao salvar cidade: ' . $e->getMessage();
    }
}

if (isset($_GET['excluir'])) {
    try {
        excluirCidade((int)$_GET['excluir']);
        $mensagem = 'Cidade excluída com sucesso.';
    } catch (PDOException $e) {
        $erro = 'Erro ao excluir cidade: ' . $e->getMessage();
    }
}

if (isset($_GET['editar'])) {
    $registroEditando = buscarCidade((int)$_GET['editar']);
}

$cidades     = listarCidades();
$paises      = listarPaises();
$governantes = listarGovernantes();

require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Cidades</h1>
        <p>Cadastre cidades vinculadas a um país existente e seus governantes.</p>
    </div>
</div>

<?php if ($mensagem): ?>
    <div class="alerta sucesso"><?= htmlspecialchars($mensagem) ?></div>
<?php endif; ?>
<?php if ($erro): ?>
    <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<?php if (empty($paises)): ?>
    <div class="alerta erro">
        Cadastre pelo menos um <a href="paises.php">país</a> antes de adicionar cidades.
    </div>
<?php endif; ?>

<div class="card" id="form">
    <h2><?= $registroEditando ? 'Editar cidade' : 'Nova cidade' ?></h2>
    <form method="post" data-validar action="cidades.php<?= $registroEditando ? '#form' : '' ?>">
        <input type="hidden" name="acao" value="<?= $registroEditando ? 'atualizar' : 'inserir' ?>">
        <?php if ($registroEditando): ?>
            <input type="hidden" name="id_cidade" value="<?= (int)$registroEditando['id_cidade'] ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="field full">
                <label>Nome da cidade <span class="req">*</span></label>
                <input type="text" name="nome" required maxlength="100"
                       value="<?= htmlspecialchars($registroEditando['nome'] ?? '') ?>">
            </div>

            <div class="field">
                <label>País <span class="req">*</span></label>
                <select name="pais_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($paises as $p): ?>
                        <option value="<?= (int)$p['id_pais'] ?>"
                            <?= (isset($registroEditando['pais_id']) && $registroEditando['pais_id'] == $p['id_pais']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nm_pais']) ?>
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
                <label>Clima</label>
                <input type="text" name="clima" maxlength="100"
                       value="<?= htmlspecialchars($registroEditando['clima'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Data de fundação</label>
                <input type="date" name="dt_fundacao"
                       value="<?= htmlspecialchars($registroEditando['dt_fundacao'] ?? '') ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn"><?= $registroEditando ? 'Salvar alterações' : 'Cadastrar cidade' ?></button>
                <?php if ($registroEditando): ?>
                    <a href="cidades.php" class="btn secundario">Cancelar</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h2>Cidades cadastradas</h2>
    <div class="busca">
        <input type="text" placeholder="Buscar por nome, país..." data-busca-tabela="tabela-cidades">
        <small class="busca-aviso text-muted"></small>
    </div>
    <div class="table-wrap">
        <table id="tabela-cidades">
            <thead>
                <tr>
                    <th>Cidade</th>
                    <th>País</th>
                    <th>Governante</th>
                    <th>População</th>
                    <th>Fundação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cidades)): ?>
                    <tr><td colspan="6" class="vazio">Nenhuma cidade cadastrada ainda.</td></tr>
                <?php else: ?>
                    <?php foreach ($cidades as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nome']) ?></td>
                            <td><?= htmlspecialchars($c['nm_pais'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($c['nm_governante'] ?? '—') ?></td>
                            <td><?= $c['populacao'] !== null ? number_format((float)$c['populacao'], 0, ',', '.') : '—' ?></td>
                            <td><?= $c['dt_fundacao'] ? date('d/m/Y', strtotime($c['dt_fundacao'])) : '—' ?></td>
                            <td class="acoes">
                                <a class="btn secundario btn-small" href="cidades.php?editar=<?= (int)$c['id_cidade'] ?>#form">Editar</a>
                                <a class="btn perigo btn-small btn-excluir"
                                   data-nome="<?= htmlspecialchars($c['nome']) ?>"
                                   href="cidades.php?excluir=<?= (int)$c['id_cidade'] ?>">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
