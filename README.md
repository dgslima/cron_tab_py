# Consolare Crontab - Python Version

Sistema de integração entre bancos de dados SQL Server e MySQL para gerenciamento de guias de recolhimento de falecidos.

## Descrição

Este projeto foi convertido de PHP para Python e realiza a sincronização de dados entre:
- **SQL Server** (Consolare) - banco de dados de origem
- **MySQL** (consolare_homolog) - banco de dados de destino

O sistema busca novos óbitos no SQL Server e os registra no MySQL, incluindo:
- Dados do falecido
- Endereços (hospital, laboratório, velório, cemitério)
- Geocodificação automática de endereços
- Informações de contratante e responsável
- Itens e valores da guia

## Arquivos do Projeto

- **address.py** - Classe para manipulação de endereços e geocodificação
- **consolare.py** - Classe principal com modelo de dados do Consolare
- **consolare_ms.py** - Conexão e queries para SQL Server
- **consolare_mysql.py** - Conexão e queries para MySQL
- **main.py** - Script principal de execução
- **requirements.txt** - Dependências do projeto

## Requisitos

### Python
- Python 3.8 ou superior

### Dependências
```bash
pip install -r requirements.txt
```

### Drivers de Banco de Dados

#### SQL Server (ODBC Driver)
**Windows:**
- Baixe e instale o [Microsoft ODBC Driver 17 for SQL Server](https://docs.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server)

**Linux:**
```bash
# Ubuntu/Debian
curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
curl https://packages.microsoft.com/config/ubuntu/$(lsb_release -rs)/prod.list > /etc/apt/sources.list.d/mssql-release.list
apt-get update
ACCEPT_EULA=Y apt-get install -y msodbcsql17
```

**macOS:**
```bash
brew tap microsoft/mssql-release https://github.com/Microsoft/homebrew-mssql-release
brew update
HOMEBREW_NO_ENV_FILTERING=1 ACCEPT_EULA=Y brew install msodbcsql17
```

## Instalação

1. Clone o repositório:
```bash
git clone <repository-url>
cd consolare_crontab
```

2. Crie um ambiente virtual (recomendado):
```bash
python -m venv venv

# Windows
venv\Scripts\activate

# Linux/Mac
source venv/bin/activate
```

3. Instale as dependências:
```bash
pip install -r requirements.txt
```

## Configuração

### Credenciais de Banco de Dados

As credenciais estão hardcoded nos arquivos. Para uso em produção, considere usar variáveis de ambiente:

**SQL Server** ([consolare_ms.py](consolare_ms.py)):
- Server: consolaredb.consolare.com.br
- Database: Consolare
- Username: Integracao_Rastreamento
- Password: f3R9K1pQ7B

**MySQL** ([consolare_mysql.py](consolare_mysql.py)):
- Host: 35.199.115.108
- Database: consolare_homolog
- Username: root
- Password: abc123**

### API de Geocodificação

O projeto usa a API HERE Maps para geocodificação. A chave está em [address.py](address.py):
```python
apiKey=aJOrzbvwdAB3Rc2SbC8ilup4wxJgtf3voDBuln2y598
```

## Uso

Execute o script principal:
```bash
python main.py
```

### Como Agendar (Crontab)

**Linux/Mac:**
```bash
# Editar crontab
crontab -e

# Executar a cada 5 minutos
*/5 * * * * cd /caminho/para/projeto && /caminho/para/venv/bin/python main.py >> /var/log/consolare_cron.log 2>&1
```

**Windows (Task Scheduler):**
1. Abra o Agendador de Tarefas
2. Criar Tarefa Básica
3. Configurar gatilho (ex: repetir a cada 5 minutos)
4. Ação: Iniciar programa
   - Programa: `C:\caminho\para\venv\Scripts\python.exe`
   - Argumentos: `main.py`
   - Iniciar em: `C:\caminho\para\projeto`

## Estrutura do Sistema

### Fluxo de Dados

1. **Busca de Dados** - Conecta ao SQL Server e busca óbitos atualizados nas últimas 400 minutos
2. **Verificação** - Checa no MySQL quais registros já foram inseridos
3. **Geocodificação** - Para cada endereço novo, faz geocodificação via API HERE Maps
4. **Inserção** - Insere ou atualiza os dados no MySQL
5. **Log** - Registra todas as operações com timestamp

### Classes Principais

- **Address**: Gerencia endereços e geocodificação
- **Consolare**: Modelo de dados completo do óbito
- **Item**: Representa itens da guia
- **ConsolareMS**: Gerencia conexão com SQL Server
- **ConsolareMysql**: Gerencia conexão com MySQL e operações de inserção

## Logs

O sistema gera logs detalhados no formato:
```
[INFO][2024:12:17 14:30:45123][main.py][0]: ROUTINE STARTED
[INFO][2024:12:17 14:30:47456][main.py][25]: ORDERS FETCHED - [12345, 12346]
[INFO][2024:12:17 14:30:48789][main.py][31]: NEW ORDERS - [12345]
[INFO][2024:12:17 14:30:52012][main.py][35]: SUCCESSFUL ORDER INSERT - [12345]
[INFO][2024:12:17 14:30:52100][main.py][40]: END ROUTINE
```

## Melhorias Sugeridas

1. **Segurança**:
   - Mover credenciais para variáveis de ambiente
   - Usar arquivo de configuração separado (.env)

2. **Robustez**:
   - Implementar retry em caso de falha de conexão
   - Adicionar validação de dados antes de inserir

3. **Performance**:
   - Implementar pool de conexões
   - Fazer inserções em lote quando possível

4. **Monitoramento**:
   - Salvar logs em arquivo
   - Implementar alertas em caso de erro
   - Métricas de performance

## Diferenças do PHP

- Uso de pyodbc ao invés de PDO para SQL Server
- mysql-connector-python ao invés de PDO para MySQL
- requests ao invés de curl para HTTP
- Tipagem dinâmica do Python ao invés de tipagem fraca do PHP
- List comprehensions e métodos Python modernos

## Licença

[Adicione aqui a licença do projeto]

## Contato

[Adicione informações de contato]
