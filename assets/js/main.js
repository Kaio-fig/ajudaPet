document.addEventListener("DOMContentLoaded", function () {

    // 1. LÓGICA DE CÁLCULO DE IDADE
    const todosOsCardsDeIdade = document.querySelectorAll(".info-idade-animal");

    todosOsCardsDeIdade.forEach((card) => {
        const dataString = card.dataset.nascimento;
        const spanResultado = card.querySelector(".idade-calculada");

        if (!dataString) {
            spanResultado.textContent = "Não informada";
            return;
        }
        const idade = calcularIdadeAnosMeses(dataString);

        // (O resto do código que formata o texto...)
        let textoIdade = "";
        if (idade.anos > 0) {
            textoIdade = idade.anos + (idade.anos === 1 ? " ano" : " anos");
            if (idade.meses > 0) {
                textoIdade += " e " + idade.meses + (idade.meses === 1 ? " mês" : " meses");
            }
        } else if (idade.meses > 0) {
            textoIdade = idade.meses + (idade.meses === 1 ? " mês" : " meses");
        } else {
            textoIdade = "Menos de 1 mês";
        }
        spanResultado.textContent = textoIdade;
    });

    // 2. LÓGICA DO CARROSSEL
    const heroSection = document.querySelector('.hero');
    if (heroSection && typeof listaImagensCarrossel !== 'undefined' && listaImagensCarrossel.length > 0) {

        let indiceAtual = 0;

        // Função que troca a imagem de fundo
        function trocarImagemHero() {
            heroSection.style.backgroundImage = `url('${listaImagensCarrossel[indiceAtual]}')`;
            indiceAtual++;
            if (indiceAtual >= listaImagensCarrossel.length) {
                indiceAtual = 0;
            }
        }
        trocarImagemHero();
        setInterval(trocarImagemHero, 5000);
    }
});


//--- Lógica do Modal de Adoção ---
document.addEventListener('DOMContentLoaded', function () {

    const btnAbrir = document.getElementById('btn-abrir-modal-adocao');
    const btnFechar = document.getElementById('btn-fechar-modal-adocao');
    const btnCancelar = document.getElementById('btn-cancelar-modal-adocao');
    const modal = document.getElementById('modal-adocao');

    // Só roda se os elementos existirem nesta página
    if (btnAbrir && modal) {

        // Abrir o modal
        btnAbrir.addEventListener('click', () => {
            modal.classList.add('ativo');
        });

        // Fechar no (X)
        btnFechar.addEventListener('click', () => {
            modal.classList.remove('ativo');
        });

        // Fechar no (Cancelar)
        btnCancelar.addEventListener('click', () => {
            modal.classList.remove('ativo');
        });

        // Fechar se clicar fora da caixa
        modal.addEventListener('click', (e) => {
            // Se o alvo do clique for o overlay (fundo)
            if (e.target === modal) {
                modal.classList.remove('ativo');
            }
        });
    }
});

// --- Lógica das Abas de Doação (no index.php) ---
document.addEventListener('DOMContentLoaded', function() {
    
    const tabLinks = document.querySelectorAll('.doacao-tab-link');
    const tabContents = document.querySelectorAll('.doacao-content');

    // Verifica se os elementos das abas existem nesta página
    if (tabLinks.length > 0) {
        
        tabLinks.forEach(link => {
            link.addEventListener('click', () => {
                const tabId = link.dataset.tab; // ex: "tab-monetaria"

                // 1. Remove 'active' de todos
                tabLinks.forEach(l => l.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                // 2. Adiciona 'active' ao link clicado e ao conteúdo
                link.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    }
});