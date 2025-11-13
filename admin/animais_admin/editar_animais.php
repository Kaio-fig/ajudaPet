<?php
// 1. INICIA A SESSÃO
session_start();

// 2. O Bouncer (Segurança)
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../../login.php?erro=acesso_negado");
    exit();
}

// 3. PEGAR O ID DA URL E VERIFICAR
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Erro: ID do animal não fornecido ou inválido.");
}
$animal_id = $_GET['id'];

// 4. Inclui a conexão
require_once '../../config/conexao.php';

// 5. Preparar e Executar a Query SQL (Read) - TUDO NO TOPO
try {
    // Query 1: Pega o animal
    $sql = "SELECT * FROM Animal WHERE id = ?"; // Tabela 'Animal' (maiúsculo)
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$animal_id]); 
    $animal = $stmt->fetch();

    if (!$animal) {
        die("Animal com o ID $animal_id não encontrado.");
    }

    // Query 2: Pega as vacinas deste animal
    $sql_vacinas = "SELECT * FROM vacinas WHERE id_animal = ? ORDER BY data_aplicacao DESC";
    $stmt_vacinas = $pdo->prepare($sql_vacinas);
    $stmt_vacinas->execute([$animal['id']]);
    $vacinas = $stmt_vacinas->fetchAll();

} catch (PDOException $e) {
    echo "Erro ao buscar dados: " . $e->getMessage();
    $animal = null; 
    $vacinas = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Animal - Ajudapet</title>
    
    <!-- [MUDANÇA] Carregando os 3 CSS (note o ../../) -->
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/admin_global.css">
    <link rel="stylesheet" href="../../assets/css/admin_animais.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    
    <header class="navbar">
        <div class="container">
            <a href="../index.php" class="logo" style="color: var(--cor-principal);">Ajudapet (Admin)</a>
            <a href="../../backend/logout.php" class="btn-login" style="background-color: var(--cor-principal);">Sair</a>
        </div>
    </header>
    
    <main class="container admin-dashboard">

        <!-- [MUDANÇA] Header e Abas do Admin -->
        <div class="admin-header">
            <h1>Painel Administrativo</h1>
        </div>
        <nav class="admin-tabs">
            <a href="../index.php" class="tab-link">
                <i class="fas fa-chart-bar"></i> DashBoard
            </a>
            <a href="../avaliar_solicitacoes.php" class="tab-link">
                <i class="fas fa-tasks"></i> Solicitações 
            </a>
            <a href="consultar_animal.php" class="tab-link active">
                <i class="fas fa-paw"></i> Gerenciar Animais
            </a>
            <a href="../relatorio.php" class="tab-link">
                <i class="fas fa-file-alt"></i> Solicitantes
            </a>
            <a href="../ver_doacoes.php" class="tab-link">
                <i class="fas fa-box-open"></i> Doações Físicas
            </a>
        </nav>

        <!-- [CONTEÚDO REATORADO] -->
        <div id="gerenciar-animais-edit" class="tab-content active" style="display:block;">
            
            <h3>Editando: <?php echo htmlspecialchars($animal['nome']); ?></h3>
            <p>Altere os dados necessários e salve as modificações.</p>
            
            <!-- Card 1: Formulário Principal do Animal -->
            <div class="form-card">
                <form action="../../backend/animais_backend/processa_edicao_animais.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">
                    
                    <div class="form-grid-2-cols">
                        <div class="form-group">
                            <label for="nome">Nome do Animal:</label>
                            <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($animal['nome']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status" required>
                                <option value="Disponível" <?php if ($animal['status'] == 'Disponível') echo 'selected'; ?>>Disponível</option>
                                <option value="Em processo" <?php if ($animal['status'] == 'Em processo') echo 'selected'; ?>>Em processo</option>
                                <option value="Adotado" <?php if ($animal['status'] == 'Adotado') echo 'selected'; ?>>Adotado</option>
                                <option value="Indisponível" <?php if ($animal['status'] == 'Indisponível') echo 'selected'; ?>>Indisponível</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2-cols">
                        <div class="form-group">
                            <label for="sexo">Sexo:</label>
                            <select id="sexo" name="sexo" required>
                                <option value="Macho" <?php if ($animal['sexo'] == 'Macho') echo 'selected'; ?>>Macho</option>
                                <option value="Fêmea" <?php if ($animal['sexo'] == 'Fêmea') echo 'selected'; ?>>Fêmea</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="raca">Raça (ex: SRD, Poodle):</label>
                            <input type="text" id="raca" name="raca" required value="<?php echo htmlspecialchars($animal['raca']); ?>">
                        </div>
                    </div>

                    <div class="form-grid-2-cols">
                        <div class="form-group">
                            <label for="porte">Porte:</label>
                            <select id="porte" name="porte" required>
                                <option value="Pequeno" <?php if ($animal['porte'] == 'Pequeno') echo 'selected'; ?>>Pequeno</option>
                                <option value="Médio" <?php if ($animal['porte'] == 'Médio') echo 'selected'; ?>>Médio</option>
                                <option value="Grande" <?php if ($animal['porte'] == 'Grande') echo 'selected'; ?>>Grande</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="data_nascimento">Data de Nascimento (Aproximada):</label>
                            <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($animal['data_nascimento']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-grid-2-cols">
                        <div class="form-group">
                            <label for="peso">Peso (kg) (ex: 12.5):</label>
                            <input type="number" step="0.1" id="peso" name="peso" value="<?php echo htmlspecialchars($animal['peso']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="cor_pelagem">Cor da Pelagem:</label>
                            <input type="text" id="cor_pelagem" name="cor_pelagem" value="<?php echo htmlspecialchars($animal['cor_pelagem']); ?>">
                        </div>
                    </div>

                    <div class="form-grid-2-cols">
                        <div class="form-group">
                            <label for="castrado">É castrado?</label>
                            <select id="castrado" name="castrado" required>
                                <option value="1" <?php if ($animal['castrado'] == '1') echo 'selected'; ?>>Sim</option>
                                <option value="0" <?php if ($animal['castrado'] == '0') echo 'selected'; ?>>Não</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="microchip">Microchip (se houver):</label>
                            <input type="text" id="microchip" name="microchip" value="<?php echo htmlspecialchars($animal['microchip']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="personalidade">Personalidade (ex: Dócil, Agitado, Tímido):</label>
                        <textarea id="personalidade" name="personalidade" rows="3"><?php echo htmlspecialchars($animal['personalidade']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="descricao_historia">História / Descrição:</label>
                        <textarea id="descricao_historia" name="descricao_historia" rows="5"><?php echo htmlspecialchars($animal['descricao_historia']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="observacoes">Observações (Saúde, etc):</label>
                        <textarea id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($animal['observacoes']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Foto Atual:</label>
                        <img src="../../uploads/<?php echo htmlspecialchars($animal['imagem_url']); ?>" alt="Foto Atual" class="current-photo">
                        
                        <label for="imagem" style="display:block; margin-top:10px;">Trocar Foto (Opcional):</label>
                        <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg">
                        
                        <input type="hidden" name="imagem_antiga" value="<?php echo htmlspecialchars($animal['imagem_url']); ?>">
                    </div>

                    <button type="submit" class="btn-submit">Salvar Alterações</button>
                </form>
            </div> <!-- fim .form-card -->

            <!-- Card 2: Seção de Vacinas -->
            <div class="vacina-section">
                <h2><i class="fas fa-syringe"></i> Carteira de Vacinação: <?php echo htmlspecialchars($animal['nome']); ?></h2>

                <!-- Formulário de Adicionar Vacina -->
                <div class="form-card" style="background-color: #f9f9f9; margin-top: 1rem;">
                    <h3>Adicionar Nova Vacina</h3>
                    <form action="../../backend/vacinas_backend/processa_cadastro_vacinas.php" method="POST" class="form-grid-2-cols">
                        <input type="hidden" name="id_animal" value="<?php echo $animal['id']; ?>">
                        <div class="form-group">
                            <label for="tipo_vacina">Vacina (ex: V10, Raiva):</label>
                            <input type="text" id="tipo_vacina" name="tipo_vacina" required>
                        </div>
                        <div class="form-group">
                            <label for="data_aplicacao">Data da Aplicação:</label>
                            <input type="date" id="data_aplicacao" name="data_aplicacao" required>
                        </div>
                        <div class="form-group">
                            <label for="proxima_dose">Próxima Dose (Opcional):</label>
                            <input type="date" id="proxima_dose" name="proxima_dose">
                        </div>
                        <div class="form-group">
                            <label for="veterinario">Veterinário (Opcional):</label>
                            <input type="text" id="veterinario" name="veterinario">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="obs_vacina">Observações:</label>
                            <input type="text" id="obs_vacina" name="observacoes" placeholder="Marca da vacina, lote, etc.">
                        </div>
                        <button type="submit" class="btn-submit" style="grid-column: 1 / -1; width: auto; justify-self: start; background-color: #27ae60;">
                            <i class="fas fa-plus"></i> Adicionar Vacina
                        </button>
                    </form>
                </div>
                
                <!-- Tabela de Histórico de Vacinas -->
                <h3 style="margin-top: 2rem;">Histórico Registrado</h3>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Vacina</th>
                                <th>Data Aplicação</th>
                                <th>Próxima Dose</th>
                                <th>Status</th>
                                <th>Veterinário</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($vacinas) > 0): ?>
                                <?php foreach ($vacinas as $vacina): ?>
                                    <?php
                                    // Lógica do Status (movida para o PHP)
                                    $status_texto = "-";
                                    $status_classe = "padrao";
                                    if (!empty($vacina['proxima_dose'])) {
                                        $hoje = new DateTime();
                                        $prox = new DateTime($vacina['proxima_dose']);
                                        if ($prox < $hoje) {
                                            $status_texto = "Atrasada"; $status_classe = "atrasada";
                                        } elseif ($prox->format('Y-m-d') == $hoje->format('Y-m-d')) {
                                            $status_texto = "Vence Hoje"; $status_classe = "vence-hoje";
                                        } else {
                                            $status_texto = "Em dia"; $status_classe = "em-dia";
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($vacina['tipo_vacina']); ?></strong>
                                            <?php if (!empty($vacina['observacoes'])): ?>
                                                <br><small style="color: #7f8c8d; font-style: italic;"><?php echo htmlspecialchars($vacina['observacoes']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;"><?php echo date('d/m/Y', strtotime($vacina['data_aplicacao'])); ?></td>
                                        <td style="text-align: center;"><?php echo $vacina['proxima_dose'] ? date('d/m/Y', strtotime($vacina['proxima_dose'])) : '-'; ?></td>
                                        <td style="text-align: center;">
                                            <span class="status-tag <?php echo $status_classe; ?>">
                                                <?php echo $status_texto; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($vacina['veterinario'] ?? '-'); ?></td>
                                        <td class="action-buttons" style="text-align: center;">
                                            <a href="../vacinas_admin/editar_vacina.php?id=<?php echo $vacina['id']; ?>" class="action-btn view" title="Editar Vacina">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a href="../../backend/animais_backend/processa_exclusao_vacina.php?id_vacina=<?php echo $vacina['id']; ?>&id_animal=<?php echo $animal['id']; ?>" class="action-btn reject" onclick="return confirm('Remover esta vacina?');" title="Remover Vacina">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2rem; color: #777;">
                                        Nenhuma vacina registrada para este animal.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div> <!-- fim .vacina-section -->

        </div> <!-- Fim #gerenciar-animais-edit -->
    </main>
</body>
</html>