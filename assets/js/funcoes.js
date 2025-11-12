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
// Valida um e-mail usando uma expressão regular (Regex).

function validarEmail(email) {
    const re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return re.test(String(email).toLowerCase());
}

// Valida um telefone (aceita de 10 a 11 dígitos).
function validarTelefone(telefone) {
    // Remove tudo que não for dígito
    const digitos = telefone.replace(/\D/g, '');
    // Verifica se tem 10 ou 11 dígitos
    return digitos.length >= 10 && digitos.length <= 11;
}


// Valida um CPF (algoritmo completo com dígitos verificadores).
// Impede CPFs com todos os números iguais (ex: 111.111.111-11).

function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, ''); // Remove formatação
    if (cpf.length !== 11) return false;

    // Impede sequências óbvias
    if (/^(\d)\1{10}$/.test(cpf)) return false;

    let soma = 0;
    let resto;

    // Valida primeiro dígito verificador
    for (let i = 1; i <= 9; i++) {
        soma = soma + parseInt(cpf.substring(i - 1, i)) * (11 - i);
    }
    resto = (soma * 10) % 11;
    if ((resto === 10) || (resto === 11)) resto = 0;
    if (resto !== parseInt(cpf.substring(9, 10))) return false;

    // Valida segundo dígito verificador
    soma = 0;
    for (let i = 1; i <= 10; i++) {
        soma = soma + parseInt(cpf.substring(i - 1, i)) * (12 - i);
    }
    resto = (soma * 10) % 11;
    if ((resto === 10) || (resto === 11)) resto = 0;
    if (resto !== parseInt(cpf.substring(10, 11))) return false;

    return true;
}

function maskCPF(cpf) {
    let v = cpf.value.replace(/\D/g, ''); // Remove tudo que não for dígito
    v = v.replace(/(\d{3})(\d)/, '$1.$2'); // Coloca ponto após 3 digitos
    v = v.replace(/(\d{3})(\d)/, '$1.$2'); // Coloca ponto após 3 digitos
    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2'); // Coloca hífen antes dos últimos 2 digitos
    cpf.value = v.substring(0, 14); // Limita ao tamanho máximo
}

// --- Função de Máscara de Telefone ((##) #####-####) ---
function maskTelefone(tel) {
    let v = tel.value.replace(/\D/g, ''); 
    v = v.replace(/^(\d{2})(\d)/g, '($1) $2'); 
    v = v.replace(/(\d{5})(\d)/, '$1-$2'); 
    tel.value = v.substring(0, 15); 
}

