<?php
// 1. INICIA A SESSÃO E VERIFICA O ACESSO
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

// 2. INCLUI A CONEXÃO
require_once '../config/conexao.php';

// 3. BUSCAR DADOS PARA O RELATÓRIO
try {
    // Query para listar todas as doações físicas
    $sql_doacoes = "SELECT * FROM DoacoesFisicas 
                    ORDER BY 
                        CASE 
                            WHEN status = 'Pendente' THEN 1
                            WHEN status = 'Contatado' THEN 2
                            WHEN status = 'Concluído' THEN 3
                        END ASC, 
                        data_oferta DESC";
    
    $stmt_doacoes = $pdo->query($sql_doacoes);
    $doacoes = $stmt_doacoes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar dados das doações: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Doações - Ajudapet</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="logo" style="color: var(--cor-principal);">Ajudapet (Admin)</a>
            <a href="../backend/logout.php" class="btn-login" style="background-color: var(--cor-principal);">Sair</a>
        </div>
    </header>

    <main class="container admin-dashboard">

        <div class="admin-header">
            <h1>Painel Administrativo</h1>
            <p>Gerencie animais, solicitações de adoção e acompanhe estatísticas</p>
        </div>

        <nav class="admin-tabs">
            <a href="index.php" class="tab-link">
                <i class="fas fa-chart-bar"></i> DashBoard
            </a>
            <a href="avaliar_solicitacoes.php" class="tab-link">
                <i class="fas fa-tasks"></i> Solicitações 
            </a>
            <a href="./animais_admin/consultar_animal.php" class="tab-link">
                <i class="fas fa-paw"></i> Gerenciar Animais
            </a>
            <a href="relatorio.php" class="tab-link">
                <i class="fas fa-file-alt"></i> Relatório
            </a>
            <a href="ver_doacoes.php" class="tab-link active">
                <i class="fas fa-box-open"></i> Doações Físicas
            </a>
        </nav>

        <div id="doacoes" class="tab-content active" style="display:block;">
            
            <h3>Relatório de Ofertas de Doação (Itens)</h3>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Doador</th>
                            <th>Contato</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doacoes as $doacao): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($doacao['nome_doador']); ?></strong><br>
                                </td>
                                <td>
                                    <small><?php echo htmlspecialchars($doacao['email']); ?></small><br>
                                    <small><?php echo htmlspecialchars($doacao['telefone']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($doacao['tipo_item']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($doacao['descricao'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($doacao['data_oferta'])); ?></td>
                                <td>
                                    <span class="status-<?php echo strtolower($doacao['status']); ?>">
                                        <?php echo htmlspecialchars($doacao['status']); ?>
                                    </span>
                                </td>
                                <td class="action-buttons" style="flex-direction: column;">
                                    <?php if ($doacao['status'] == 'Pendente'): ?>
                                        <a href="../backend/processa_status_doacao.php?id=<?php echo $doacao['id']; ?>&status=Contatado" 
                                           class="action-btn view" style="margin-bottom: 5px;">
                                            Marcar como Contatado
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($doacao['status'] != 'Concluído'): ?>
                                        <a href="../backend/processa_status_doacao.php?id=<?php echo $doacao['id']; ?>&status=Concluído" 
                                           class="action-btn approve">
                                            Marcar como Concluído
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($doacoes)): ?>
                            <tr>
                                <td colspan="7">Nenhuma oferta de doação física foi recebida.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div> </main>
</body>
</html>