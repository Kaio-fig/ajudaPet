/**
 * Função robusta que calcula a idade (evita bugs de fuso horário).
 * Recebe a data como string "YYYY-MM-DD".
 */
function calcularIdadeAnosMeses(dataNascimentoStr) {
    const partes = dataNascimentoStr.split("-");
    const dataNascimento = new Date(partes[0], partes[1] - 1, partes[2]);
    const hoje = new Date();
    
    let anos = hoje.getFullYear() - dataNascimento.getFullYear();
    let meses = hoje.getMonth() - dataNascimento.getMonth();

    if (hoje.getDate() < dataNascimento.getDate()) {
        meses--;
    }
    if (meses < 0) {
        anos--;
        meses += 12;
    }
    return { anos: anos, meses: meses };
}

/**
 * No futuro, você pode adicionar outras funções aqui:
 * function validarCPF(cpf) { ... }
 * function formatarTelefone(tel) { ... }
 */