<?php
/**
 * Script para limpar conexões órfãs do banco de dados
 * 
 * USO: Acesse via navegador: http://seudominio.com/limpar-conexoes-banco.php
 * 
 * IMPORTANTE: Remova este arquivo após o uso por segurança!
 */

// Verificar se está rodando via WordPress ou direto
$mysqli = null;

if (file_exists('wp-load.php')) {
    require_once('wp-load.php');
    
    // Verificar se é admin (segurança adicional)
    if (!current_user_can('manage_options')) {
        die('Acesso negado. Apenas administradores podem executar este script.');
    }
    
    // Usar conexão do WordPress
    if (isset($wpdb) && $wpdb->dbh) {
        if ($wpdb->dbh instanceof mysqli) {
            $mysqli = $wpdb->dbh;
        } else {
            // Se não for mysqli, criar nova conexão usando dados do wp-config
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            if ($mysqli->connect_error) {
                die('Erro de conexão: ' . $mysqli->connect_error);
            }
        }
    } else {
        // Criar conexão usando constantes do wp-config
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($mysqli->connect_error) {
            die('Erro de conexão: ' . $mysqli->connect_error);
        }
    }
} else {
    // Configuração manual se não estiver no WordPress
    // IMPORTANTE: Configure essas variáveis com seus dados do banco
    $db_host = 'localhost';
    $db_user = 'u696538442_sgjuridico'; // Substitua pelo seu usuário
    $db_pass = ''; // Substitua pela sua senha
    $db_name = 'u696538442_sgjuridico'; // Substitua pelo seu banco
    
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($mysqli->connect_error) {
        die('Erro de conexão: ' . $mysqli->connect_error);
    }
}

if (!$mysqli) {
    die('Erro: Não foi possível estabelecer conexão com o banco de dados.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Limpar Conexões do Banco de Dados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #5CE1E6;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #5CE1E6;
            color: #000;
            font-weight: bold;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .sleep {
            background: #fff3cd;
            color: #856404;
        }
        .query {
            background: #d1ecf1;
            color: #0c5460;
        }
        .button {
            background: #5CE1E6;
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .button:hover {
            background: #4BC4C8;
        }
        .button.danger {
            background: #dc3545;
            color: white;
        }
        .button.danger:hover {
            background: #c82333;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            border: 1px solid #28a745;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Limpar Conexões do Banco de Dados</h1>
        
        <?php
        // Processar ações
        if (isset($_GET['action']) && $_GET['action'] === 'kill' && isset($_GET['id'])) {
            $kill_id = intval($_GET['id']);
            $result = $mysqli->query("KILL $kill_id");
            
            if ($result) {
                echo '<div class="success">✅ Conexão #' . $kill_id . ' foi finalizada com sucesso!</div>';
            } else {
                echo '<div class="warning">⚠️ Erro ao finalizar conexão #' . $kill_id . ': ' . $mysqli->error . '</div>';
            }
        }
        
        if (isset($_GET['action']) && $_GET['action'] === 'kill_sleep') {
            $killed = 0;
            $result = $mysqli->query("SHOW PROCESSLIST");
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Matar apenas conexões Sleep que estão dormindo há mais de 60 segundos
                    // E que não são do usuário atual (para não matar a própria conexão)
                    if ($row['Command'] === 'Sleep' && 
                        $row['Time'] > 60 && 
                        $row['Id'] != $mysqli->thread_id) {
                        $mysqli->query("KILL " . $row['Id']);
                        $killed++;
                    }
                }
                echo '<div class="success">✅ ' . $killed . ' conexões órfãs foram finalizadas!</div>';
            }
        }
        ?>
        
        <div class="info">
            <strong>ℹ️ Informações:</strong><br>
            • Este script mostra todas as conexões ativas no banco de dados<br>
            • Conexões "Sleep" são conexões que estão aguardando (podem estar órfãs)<br>
            • <strong>NÃO</strong> mate conexões que estão executando queries (Command != 'Sleep')<br>
            • <strong>NÃO</strong> mate a própria conexão (ID = <?php echo $mysqli->thread_id; ?>)<br>
            • Use o botão "Finalizar Conexões Órfãs" para limpeza automática segura
        </div>
        
        <a href="?action=kill_sleep" class="button danger" onclick="return confirm('Tem certeza que deseja finalizar todas as conexões órfãs (Sleep > 60s)?');">
            🔄 Finalizar Conexões Órfãs (Automático)
        </a>
        <a href="?" class="button">🔄 Atualizar Lista</a>
        
        <h2>Conexões Ativas</h2>
        <?php
        $result = $mysqli->query("SHOW PROCESSLIST");
        
        if ($result) {
            echo '<table>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Usuário</th>';
            echo '<th>Host</th>';
            echo '<th>Banco</th>';
            echo '<th>Comando</th>';
            echo '<th>Tempo (s)</th>';
            echo '<th>Estado</th>';
            echo '<th>Info</th>';
            echo '<th>Ação</th>';
            echo '</tr>';
            
            $current_thread_id = $mysqli->thread_id;
            $sleep_count = 0;
            $total_count = 0;
            
            while ($row = $result->fetch_assoc()) {
                $total_count++;
                $is_sleep = ($row['Command'] === 'Sleep');
                $is_current = ($row['Id'] == $current_thread_id);
                
                if ($is_sleep) {
                    $sleep_count++;
                }
                
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['Id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['User']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Host']) . '</td>';
                echo '<td>' . htmlspecialchars($row['db'] ?? '') . '</td>';
                echo '<td><span class="status ' . ($is_sleep ? 'sleep' : 'query') . '">' . htmlspecialchars($row['Command']) . '</span></td>';
                echo '<td>' . htmlspecialchars($row['Time']) . '</td>';
                echo '<td>' . htmlspecialchars($row['State'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars(substr($row['Info'] ?? '', 0, 50)) . '</td>';
                
                // Mostrar botão de kill apenas para conexões Sleep que não são a atual
                if ($is_sleep && !$is_current && $row['Time'] > 60) {
                    echo '<td><a href="?action=kill&id=' . $row['Id'] . '" class="button danger" onclick="return confirm(\'Tem certeza que deseja finalizar a conexão #' . $row['Id'] . '?\');">Finalizar</a></td>';
                } else if ($is_current) {
                    echo '<td><span style="color: #28a745;">✓ Atual</span></td>';
                } else {
                    echo '<td>-</td>';
                }
                
                echo '</tr>';
            }
            
            echo '</table>';
            
            echo '<div class="info">';
            echo '<strong>📊 Estatísticas:</strong><br>';
            echo '• Total de conexões: <strong>' . $total_count . '</strong><br>';
            echo '• Conexões Sleep: <strong>' . $sleep_count . '</strong><br>';
            echo '• Conexão atual: <strong>#' . $current_thread_id . '</strong>';
            echo '</div>';
        } else {
            echo '<div class="warning">⚠️ Erro ao consultar processos: ' . $mysqli->error . '</div>';
        }
        ?>
        
        <div class="warning">
            <strong>⚠️ IMPORTANTE:</strong><br>
            • Após usar este script, <strong>DELETE este arquivo</strong> por segurança!<br>
            • Este script deve ser usado apenas para diagnóstico e limpeza inicial<br>
            • As otimizações já implementadas no código devem prevenir o problema no futuro
        </div>
    </div>
</body>
</html>

