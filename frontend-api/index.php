<?php
$apiUrl = "http://localhost:8080/alunos";

// =========================================================================
// LÓGICA DE PROCESSAMENTO (POST, PUT, DELETE)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    
    $acao = $_POST['acao'];

    // 1. CADASTRAR (POST)
    if ($acao === 'cadastrar') {
        $novoAluno = [
            'nome' => $_POST['nome'],
            'email' => $_POST['email'],
            'matricula' => $_POST['matricula']
        ];
        $opcoes = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($novoAluno)
            ]
        ];
        $contexto  = stream_context_create($opcoes);
        @file_get_contents($apiUrl, false, $contexto);
    } 
    
    // 2. ATUALIZAR (PUT)
    elseif ($acao === 'editar') {
        $id = $_POST['id'];
        $alunoEditado = [
            'nome' => $_POST['nome'],
            'email' => $_POST['email'],
            'matricula' => $_POST['matricula']
        ];
        $opcoes = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'PUT',
                'content' => json_encode($alunoEditado)
            ]
        ];
        $contexto  = stream_context_create($opcoes);
        @file_get_contents($apiUrl . '/' . $id, false, $contexto);
    } 
    
    // 3. EXCLUIR (DELETE)
    elseif ($acao === 'excluir') {
        $id = $_POST['id'];
        $opcoes = [
            'http' => [
                'method'  => 'DELETE'
            ]
        ];
        $contexto  = stream_context_create($opcoes);
        @file_get_contents($apiUrl . '/' . $id, false, $contexto);
    }

    // Recarrega a página para limpar o formulário e atualizar a tabela
    header("Location: index.php");
    exit;
}

// =========================================================================
// LÓGICA DE LISTAGEM (GET)
// =========================================================================
$response = @file_get_contents($apiUrl);
$alunos = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão Acadêmica</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold;}
        .btn-salvar { background-color: #28a745; }
        .btn-salvar:hover { background-color: #218838; }
        .btn-editar { background-color: #ffc107; color: black; }
        .btn-editar:hover { background-color: #e0a800; }
        .btn-excluir { background-color: #dc3545; }
        .btn-excluir:hover { background-color: #c82333; }
        .btn-cancelar { background-color: #6c757d; display: none; }
        hr { margin: 30px 0; border: 0; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Painel de Alunos</h2>
        <p>Dados consumidos diretamente da API Spring Boot.</p>

        <div style="background-color: #f1f8ff; padding: 15px; border-left: 4px solid #0056b3; margin-bottom: 20px;">
            <h3 id="formTitulo">Cadastrar Novo Aluno</h3>
            <form method="POST" action="index.php">
                <input type="hidden" name="acao" id="formAcao" value="cadastrar">
                <input type="hidden" name="id" id="formId" value="">
                
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" name="nome" id="formNome" required placeholder="Digite o nome completo">
                </div>
                
                <div class="form-group">
                    <label>E-mail:</label>
                    <input type="email" name="email" id="formEmail" required placeholder="Digite um e-mail válido">
                </div>

                <div class="form-group">
                    <label>Matrícula:</label>
                    <input type="text" name="matricula" id="formMatricula" required placeholder="Ex: 202610123">
                </div>

                <button type="submit" class="btn-salvar" id="btnSalvar">Salvar Aluno</button>
                <button type="button" class="btn-cancelar" id="btnCancelar" onclick="cancelarEdicao()">Cancelar Edição</button>
            </form>
        </div>

        <hr>

        <h3>Alunos Matriculados</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Matrícula</th>
                    <th style="width: 150px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($alunos)): ?>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td><?= htmlspecialchars($aluno['id']) ?></td>
                            <td><?= htmlspecialchars($aluno['nome']) ?></td>
                            <td><?= htmlspecialchars($aluno['email']) ?></td>
                            <td><?= htmlspecialchars($aluno['matricula']) ?></td>
                            <td>
                                <button class="btn-editar" onclick="prepararEdicao(<?= $aluno['id'] ?>, '<?= htmlspecialchars(addslashes($aluno['nome'])) ?>', '<?= htmlspecialchars(addslashes($aluno['email'])) ?>', '<?= htmlspecialchars(addslashes($aluno['matricula'])) ?>')">Editar</button>
                                
                                <form method="POST" action="index.php" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir o aluno <?= htmlspecialchars(addslashes($aluno['nome'])) ?>?');">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?= $aluno['id'] ?>">
                                    <button type="submit" class="btn-excluir">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Nenhum aluno cadastrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function prepararEdicao(id, nome, email, matricula) {
            document.getElementById('formId').value = id;
            document.getElementById('formNome').value = nome;
            document.getElementById('formEmail').value = email;
            document.getElementById('formMatricula').value = matricula;
            
            document.getElementById('formAcao').value = 'editar';
            document.getElementById('formTitulo').innerText = 'Editar Aluno (ID: ' + id + ')';
            document.getElementById('btnSalvar').innerText = 'Atualizar Aluno';
            document.getElementById('btnCancelar').style.display = 'inline-block';
            
            // Rola a tela para o topo
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function cancelarEdicao() {
            document.getElementById('formId').value = '';
            document.getElementById('formNome').value = '';
            document.getElementById('formEmail').value = '';
            document.getElementById('formMatricula').value = '';
            
            document.getElementById('formAcao').value = 'cadastrar';
            document.getElementById('formTitulo').innerText = 'Cadastrar Novo Aluno';
            document.getElementById('btnSalvar').innerText = 'Salvar Aluno';
            document.getElementById('btnCancelar').style.display = 'none';
        }
    </script>
</body>
</html>