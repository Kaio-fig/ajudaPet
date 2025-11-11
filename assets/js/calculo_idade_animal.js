/**
 * Roda automaticamente quando o HTML da página carregar.
 */
document.addEventListener("DOMContentLoaded", function () {
  // 1. Encontra TODOS os <li> com a classe '.info-idade-animal'
  const todosOsCardsDeIdade = document.querySelectorAll(".info-idade-animal");

  // 2. Faz um loop (forEach) em cada <li> que ele encontrou
  todosOsCardsDeIdade.forEach((card) => {
    // 3. Pega a data de nascimento que o PHP escreveu no 'data-nascimento'
    const dataString = card.dataset.nascimento; // "data-nascimento" vira "dataset.nascimento"

    // 4. Encontra o <span> onde vamos escrever o resultado
    const spanResultado = card.querySelector(".idade-calculada");

    if (!dataString) {
      spanResultado.textContent = "Não informada";
      return; // Pula para o próximo animal se não tiver data
    }

    // 5. Calcula a idade usando a função robusta
    const idade = calcularIdadeAnosMeses(dataString);

    // 6. Formata o texto final
    let textoIdade = "";
    if (idade.anos > 0) {
      textoIdade = idade.anos + (idade.anos === 1 ? " ano" : " anos");
      if (idade.meses > 0) {
        textoIdade +=
          " e " + idade.meses + (idade.meses === 1 ? " mês" : " meses");
      }
    } else if (idade.meses > 0) {
      textoIdade = idade.meses + (idade.meses === 1 ? " mês" : " meses");
    } else {
      textoIdade = "Menos de 1 mês"; // Ex: Recém-nascido
    }

    // 7. Finalmente, escreve o resultado no <span>!
    spanResultado.textContent = textoIdade;
  });
});

/**
 * Função robusta que calcula a idade (evita bugs de fuso horário).
 * Recebe a data como string "YYYY-MM-DD".
 */
function calcularIdadeAnosMeses(dataNascimentoStr) {
  // 1. Quebra a string "YYYY-MM-DD" em ["YYYY", "MM", "DD"]
  const partes = dataNascimentoStr.split("-");

  // 2. Cria a data (Mês em JS é 0-11, por isso "partes[1] - 1")
  const dataNascimento = new Date(partes[0], partes[1] - 1, partes[2]);
  const hoje = new Date();

  // 3. Calcula a diferença bruta
  let anos = hoje.getFullYear() - dataNascimento.getFullYear();
  let meses = hoje.getMonth() - dataNascimento.getMonth();

  // 4. Ajuste fino (se o dia do aniversário ainda não chegou no mês)
  if (hoje.getDate() < dataNascimento.getDate()) {
    meses--;
  }

  // 5. Ajuste fino (se o mês do aniversário ainda não chegou no ano)
  if (meses < 0) {
    anos--;
    meses += 12; // Adiciona os 12 meses do ano "descontado"
  }

  return {
    anos: anos,
    meses: meses,
  };
}
