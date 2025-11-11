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

    // 2. LÓGICA DO CARROSSEL (futuro)
    // initCarousel('.hero-slider');

    // 3. OUTRAS FUNÇÕES DO INDEX
    // ...
});