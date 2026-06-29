<?php
require_once __DIR__ . '/../backend/continentes/funcoes.php';
require_once __DIR__ . '/../backend/paises/funcoes.php';
require_once __DIR__ . '/../backend/cidades/funcoes.php';
require_once __DIR__ . '/../backend/governantes/funcoes.php';

$paginaAtual  = 'index';
$tituloPagina = 'Início';

$continentes        = listarContinentes();
$paises             = listarPaises();
$cidades            = listarCidades();
$governantes        = listarGovernantes();
$ranking            = cidadeMaisPopulosaPorPais();
$cidadesPorContinente = cidadesPorContinente();

require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Painel Geral</h1>
        <p>Resumo dos dados cadastrados no banco bd_mundo.</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="num"><?= count($continentes) ?></div>
        <div class="label">Continentes</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= count($paises) ?></div>
        <div class="label">Países</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= count($cidades) ?></div>
        <div class="label">Cidades</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= count($governantes) ?></div>
        <div class="label">Governantes</div>
    </div>
</div>

<div class="card">
    <h2>Cidades cadastradas por continente</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Continente</th>
                    <th>Países cadastrados</th>
                    <th>Cidades cadastradas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($continentes)): ?>
                    <tr><td colspan="3" class="vazio">Nenhum continente cadastrado ainda.</td></tr>
                <?php else: ?>
                    <?php foreach ($cidadesPorContinente as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nm_continente']) ?></td>
                            <td>
                                <?php
                                    $qtdPaises = 0;
                                    foreach ($continentes as $cont) {
                                        if ($cont['id_continente'] == $c['id_continente']) {
                                            $qtdPaises = (int)$cont['qtd_paises_cadastrados'];
                                        }
                                    }
                                    echo $qtdPaises;
                                ?>
                            </td>
                            <td><?= (int)$c['qtd_cidades'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2>Cidade mais populosa de cada país</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>País</th>
                    <th>Cidade mais populosa</th>
                    <th>População</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ranking)): ?>
                    <tr><td colspan="3" class="vazio">Cadastre países e cidades para ver essa estatística.</td></tr>
                <?php else: ?>
                    <?php foreach ($ranking as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['nm_pais']) ?></td>
                            <td><?= htmlspecialchars($r['cidade']) ?></td>
                            <td><?= $r['populacao'] !== null ? number_format((float)$r['populacao'], 0, ',', '.') : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
