<?php
// 1. INICIA A SESSÃO E VERIFICA O ACESSO
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['nivel_acesso'] != 0) {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

// 2. INCLUI A CONEXÃO
require_once '../config/conexao.php';

// 3. BUSCAR DADOS PARA OS CARDS DE STATUS
try {
    // Query 1: Contagem de Solicitações (para os cards)
    $sql_counts = "SELECT
                        SUM(CASE WHEN status = 'Pendente' THEN 1 ELSE 0 END) AS pendentes,
                        SUM(CASE WHEN status = 'Aprovada' THEN 1 ELSE 0 END) AS aprovadas,
                        SUM(CASE WHEN status = 'Rejeitada' THEN 1 ELSE 0 END) AS rejeitadas
                    FROM SolicitacaoAdoção";
    $stmt_counts = $pdo->query($sql_counts);
    $counts = $stmt_counts->fetch(PDO::FETCH_ASSOC);

    $total_pendentes = $counts['pendentes'] ?? 0;
    $total_aprovadas = $counts['aprovadas'] ?? 0;
    $total_rejeitadas = $counts['rejeitadas'] ?? 0;
    $total_finalizadas = $total_aprovadas + $total_rejeitadas;


    // Query 2: Lista de Solicitações (para a tabela)
    // Usamos JOIN para pegar os nomes do animal e do solicitante
    $sql_list = "SELECT 
                    s.id, 
                    s.data_solicitacao, 
                    s.data_visita_sugerida, 
                    s.status,
                    sol.nome AS solicitante_nome,
                    sol.email AS solicitante_email,
                    a.nome AS animal_nome
                FROM SolicitacaoAdoção AS s
                JOIN Solicitante AS sol ON s.id_solicitante = sol.id
                JOIN Animal AS a ON s.id_animal = a.id
                ORDER BY 
                    CASE 
                        WHEN s.status = 'Pendente' THEN 1
                        WHEN s.status = 'Aprovada' THEN 2
                        WHEN s.status = 'Rejeitada' THEN 3
                    END ASC, 
                    s.data_solicitacao DESC"; // Pendentes primeiro, depois por data
    
    $stmt_list = $pdo->query($sql_list);
    $solicitacoes = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar dados das solicitações: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Solicitações - Ajudapet</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin_global.css">
    <link rel="stylesheet" href="../assets/css/admin_solicitacoes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.php" class="logo" style="color: var(--cor-principal);">Ajudapet (Admin)</a>
            <nav>
                </nav>
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
            <a href="avaliar_solicitacoes.php" class="tab-link active">
                <i class="fas fa-tasks"></i> Solicitações 
                <?php if ($total_pendentes > 0): ?>
                    <span class="notification-badge"><?php echo $total_pendentes; ?></span>
                <?php endif; ?>
            </a>
            <a href="./animais_admin/consultar_animal.php" class="tab-link">
                <i class="fas fa-paw"></i> Gerenciar Animais
            </a>
            <a href="relatorio.php" class="tab-link">
                <i class="fas fa-file-alt"></i> Solicitantes
            </a>
            <a href="../ver_doacoes.php" class="tab-link">
                <i class="fas fa-box-open"></i> Doações Físicas
            </a>
        </nav>

        <div id="solicitacoes" class="tab-content active" style="display:block;"> <h3>Gerenciar Solicitações</h3>
            
            <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr);">
                <div class="stat-card" style="background: #fffbe6; border-color: #ffe58f;">
                    <div class="stat-info">
                        <h2><?php echo $total_pendentes; ?></h2>
                        <p>Pendente</p>
                    </div>
                </div>
                <div class="stat-card" style="background: #e6f7ff; border-color: #91d5ff;">
                    <div class="stat-info">
                        <h2><?php echo $total_aprovadas; ?></h2>
                        <p>Aprovada</o>
                    </div>
                </div>
                <div class="stat-card" style="background: #fff1f0; border-color: #ffa39e;">
                    <div class="stat-info">
                        <h2><?php echo $total_rejeitadas; ?></h2>
                        <p>Rejeitada</p>
                    </div>
                </div>
                <div class="stat-card" style="background: #f0f0f0; border-color: #d9d9d9;">
                    <div class="stat-info">
                        <h2><?php echo $total_finalizadas; ?></h2>
                        <p>Finalizada</p>
                    </div>
                </div>
            </div>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th>Animal</th>
                            <th>Data Solicitada</th>
                            <th>Visita Sugerida</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($solicitacoes) > 0): ?>
                            <?php foreach ($solicitacoes as $s): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($s['solicitante_nome']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($s['solicitante_email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($s['animal_nome']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($s['data_solicitacao'])); ?></td>
                                    <td>
                                        <?php echo $s['data_visita_sugerida'] ? date('d/m/Y H:i', strtotime($s['data_visita_sugerida'])) : 'Não sugerida'; ?>
                                    </td>
                                    <td>
                                        <span class="status-<?php echo strtolower($s['status']); ?>">
                                            <?php echo htmlspecialchars($s['status']); ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <button class="action-btn view" onclick="abrirModalView(<?php echo $s['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if ($s['status'] == 'Pendente'): ?>
                                            <button class="action-btn approve" onclick="abrirModalAprovar(<?php echo $s['id']; ?>)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="action-btn reject" onclick="abrirModalRejeitar(<?php echo $s['id']; ?>)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">Nenhuma solicitação encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div> </main>
    
    <div class="modal-overlay" id="modal-view">
        <div class="modal-box modal-lg">
            <button class="modal-close" onclick="fecharModal('modal-view')">&times;</button>
            <h2>Detalhes da Solicitação</h2>
            
            <div class="detalhes-grid">
                <div>
                    <strong>Solicitante:</strong>
                    <span id="view-sol-nome">...</span>
                    <span id="view-sol-email">...</span>
                </div>
                <div>
                    <strong>Animal:</strong>
                    <span id="view-animal-nome">...</span>
                </div>
                <div>
                    <strong>Data Solicitada:</strong>
                    <span id="view-data-solicitacao">...</span>
                </div>
                <div>
                    <strong>Telefone:</strong>
                    <span id="view-sol-telefone">...</span>
                </div>
            </div>
            
            <div class="detalhes-bloco" id="view-bloco-mensagem">
                <strong>Mensagem:</strong>
                <p id="view-mensagem">...</p>
            </div>
            
            <div class="detalhes-bloco" id="view-bloco-perfil">
                <strong>Informações do Perfil</strong>
                <ul>
                    <li><strong>Endereço:</strong> <span id="view-sol-endereco">...</span></li>
                    <li><strong>Possui Quintal?</strong> <span id="view-sol-quintal">...</span></li>
                    <li><strong>Experiência:</strong> <span id="view-sol-exp">...</span></li>
                    <li><strong>Disponibilidade:</strong> <span id="view-sol-disp">...</span></li>
                </ul>
            </div>
            
            <div class="form-actions" style="justify-content: flex-end;">
                <button type="button" class="btn-cancelar" onclick="fecharModal('modal-view')">Fechar</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modal-aprovar">
        <div class="modal-box">
            <button class="modal-close" onclick="fecharModal('modal-aprovar')">&times;</button>
            <h2>Aprovar Solicitação</h2>
            <form action="../backend/processa_status_solicitacao.php" method="POST">
                <input type="hidden" name="acao" value="aprovar">
                <input type="hidden" name="id_solicitacao" id="aprovar-id" value="">
                
                <div class="form-group">
                    <label for="data_visita_aprovada">Data Aprovada para Visita:</label>
                    <input type="datetime-local" id="data_visita_aprovada" name="data_visita_aprovada" required>
                </div>
                <div class="form-group">
                    <label for="notas_admin">Notas do Administrador:</label>
                    <textarea id="notas_admin" name="notas_admin" rows="3" placeholder="Adicione observações aqui..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancelar" onclick="fecharModal('modal-aprovar')">Cancelar</button>
                    <button type="submit" class="btn-submit" style="background-color: #28a745;">Aprovar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modal-rejeitar">
        <div class="modal-box">
            <button class="modal-close" onclick="fecharModal('modal-rejeitar')">&times;</button>
            <h2>Rejeitar Solicitação</h2>
            <form action="../backend/processa_status_solicitacao.php" method="POST">
                <input type="hidden" name="acao" value="rejeitar">
                <input type="hidden" name="id_solicitacao" id="rejeitar-id" value="">
                
                <div class="form-group">
                    <label for="motivo_rejeicao">Motivo da Rejeição:</label>
                    <textarea id="motivo_rejeicao" name="motivo_rejeicao" rows="4" placeholder="Adicione o motivo da rejeição..." required></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancelar" onclick="fecharModal('modal-rejeitar')">Cancelar</button>
                    <button type="submit" class="btn-submit" style="background-color: #dc3545;">Rejeitar</button>
                </div>
            </form>
        </div>
    </div>


    <script>
    // --- Funções para controlar os Modais ---

    function fecharModal(modalId) {
        document.getElementById(modalId).classList.remove('ativo');
    }

    function abrirModalAprovar(id) {
        // 1. Preenche o ID escondido no formulário
        document.getElementById('aprovar-id').value = id;
        // 2. Mostra o modal
        document.getElementById('modal-aprovar').classList.add('ativo');
    }

    function abrirModalRejeitar(id) {
        // 1. Preenche o ID escondido no formulário
        document.getElementById('rejeitar-id').value = id;
        // 2. Mostra o modal
        document.getElementById('modal-rejeitar').classList.add('ativo');
    }

    // --- Função AJAX para o Modal "VIEW" ---
    async function abrirModalView(id) {
        const modal = document.getElementById('modal-view');
        modal.classList.add('ativo');

        // Reseta o modal para "Carregando..."
        document.getElementById('view-sol-nome').textContent = 'Carregando...';
        document.getElementById('view-sol-email').textContent = '...';
        document.getElementById('view-animal-nome').textContent = '...';
        document.getElementById('view-data-solicitacao').textContent = '...';
        document.getElementById('view-sol-telefone').textContent = '...';
        document.getElementById('view-mensagem').textContent = '...';
        document.getElementById('view-sol-endereco').textContent = '...';
        document.getElementById('view-sol-quintal').textContent = '...';
        document.getElementById('view-sol-exp').textContent = '...';
        document.getElementById('view-sol-disp').textContent = '...';
        
        try {
            // 1. Chama o script PHP que busca os dados
            // Usamos '../admin/' porque o script está na pasta admin, não no backend
            const response = await fetch(`get_solicitacao_detalhes.php?id=${id}`);
            
            if (!response.ok) {
                throw new Error('Falha ao buscar dados da solicitação. Status: ' + response.status);
            }
            
            const dados = await response.json();

            // 2. Popula o modal com os dados recebidos
            document.getElementById('view-sol-nome').textContent = dados.sol_nome || 'Não informado';
            document.getElementById('view-sol-email').textContent = dados.sol_email || 'Não informado';
            document.getElementById('view-animal-nome').textContent = dados.animal_nome || 'Não informado';
            document.getElementById('view-data-solicitacao').textContent = new Date(dados.data_solicitacao).toLocaleString('pt-BR');
            document.getElementById('view-sol-telefone').textContent = dados.sol_telefone || 'Não informado';
            
            document.getElementById('view-mensagem').textContent = dados.observacoes || '(Nenhuma mensagem)';
            
            const endereco = `${dados.endereco_completo || ''}, ${dados.cidade || ''} - ${dados.estado || ''}, CEP: ${dados.cep || ''}`;
            document.getElementById('view-sol-endereco').textContent = endereco === ',  - , CEP: ' ? 'Não informado' : endereco;
            
            document.getElementById('view-sol-quintal').textContent = dados.possui_quintal || 'Não informado';
            document.getElementById('view-sol-exp').textContent = dados.experiencia_animais || 'Não informado';
            document.getElementById('view-sol-disp').textContent = dados.disponibilidade_tempo || 'Não informado';

        } catch (error) {
            console.error('Erro no fetch:', error);
            document.getElementById('view-sol-nome').textContent = 'Erro ao carregar dados.';
        }
    }
    </script>
</body>
</html>