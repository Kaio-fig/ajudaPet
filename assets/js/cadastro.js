
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