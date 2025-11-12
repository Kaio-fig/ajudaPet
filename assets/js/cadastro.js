// assets/js/cadastro.js

// --- Função de Máscara de CPF (###.###.###-##) ---
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


// --- Lógica de Validação e Máscara (no DOMContentLoaded) ---
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Encontra os elementos
    const form = document.getElementById('form-cadastro');
    const inputCPF = document.getElementById('cpf');
    const inputEmail = document.getElementById('email');
    const inputTelefone = document.getElementById('telefone');
    const errorBox = document.getElementById('js-error-box');

    // 2. [NOVO] Adiciona os "escutadores" de máscara
    if (inputCPF) {
        inputCPF.addEventListener('input', (e) => maskCPF(e.target));
    }
    if (inputTelefone) {
        inputTelefone.addEventListener('input', (e) => maskTelefone(e.target));
    }

    // 3. Adiciona o "escutador" de validação ao SUBMIT
    if (form) {
        form.addEventListener('submit', function(event) {
            
            // Limpa erros antigos
            errorBox.innerHTML = '';
            errorBox.style.display = 'none';

            let erros = [];

            // 4. Executa as validações
            
            // Valida CPF (a função validarCPF já remove a máscara)
            if (!validarCPF(inputCPF.value)) {
                erros.push('O CPF informado parece ser inválido.');
            }
            
            // Valida E-mail
            if (!validarEmail(inputEmail.value)) {
                erros.push('O E-mail informado parece ser inválido.');
            }

            // Valida Telefone (a função validarTelefone já remove a máscara)
            if (inputTelefone.value && !validarTelefone(inputTelefone.value)) {
                 erros.push('O Telefone informado parece ser inválido (use 10 ou 11 dígitos).');
            }

            // 5. Se houver erros, impede o envio do formulário
            if (erros.length > 0) {
                event.preventDefault(); 
                
                // Mostra os erros
                errorBox.style.display = 'block';
                errorBox.innerHTML = '<strong>Por favor, corrija os seguintes erros:</strong><br>' + erros.join('<br>');
            }
        });
    }
});