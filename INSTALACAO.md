# 📖 Guia Completo de Instalação na GoDaddy

## 🎯 Pré-requisitos

- ✅ Domínio registrado/apontado para GoDaddy
- ✅ Plano hospedagem compartilhada GoDaddy (com cPanel)
- ✅ PHP 7.4+
- ✅ MySQL 5.7+ (opcional, recomendado)

---

## PASSO 1: Acessar cPanel

1. Vá para `https://seu-dominio.com:2083` ou painel da GoDaddy
2. Faça login com suas credenciais

---

## PASSO 2: Criar banco de dados (Opcional)

1. No cPanel, clique **MySQL Databases**
2. Crie novo banco:
   - **Database Name:** `87servicos`
3. Em **MySQL Users**, crie usuário:
   - **Username:** `87_user`
   - **Password:** (gere senha forte)
4. Atribua usuário ao banco com todas permissões

**Salve essas informações:**
```
DB_HOST: localhost
DB_USER: 87_user
DB_PASS: sua_senha
DB_NAME: 87servicos
```

---

## PASSO 3: Upload dos Arquivos

1. cPanel → **File Manager**
2. Navegue para `public_html`
3. Crie pastas:
   ```
   api/
   data/
   data/logs/
   ```
4. Faça upload de:
   - `.htaccess` → raiz
   - `api/config.php`
   - `api/chat.php`
   - `api/leads.php`
   - `api/admin-save.php`
   - `api/db-config.php`
   - `index.html` (seu site)
   - `content.json`
   - pasta `assets/` (logo + imagem)

---

## PASSO 4: Configurar Variáveis de Ambiente

### Opção A: Via cPanel (recomendado)

1. cPanel → **Environment Variables**
2. Add Variable para cada uma:

```env
APP_ENV = production
ANTHROPIC_API_KEY = sk-ant-seu-token-aqui
ADMIN_TOKEN = sua-senha-super-secreta
DB_HOST = localhost
DB_USER = seu_usuario
DB_PASS = sua_senha
DB_NAME = 87servicos
ADMIN_EMAIL = comercial87ss@gmail.com
```

### Opção B: Via `.user.ini` (fallback)

1. File Manager em `public_html`
2. Crie arquivo `.user.ini`:

```ini
auto_prepend_file = /home/seu-usuario/87-servicos/api/config.php
```

---

## PASSO 5: Obter Chave Anthropic (IA)

1. Acesse https://console.anthropic.com
2. Settings → **API Keys**
3. **Create Key**
4. Copie chave (começa com `sk-ant-`)
5. Cole em `ANTHROPIC_API_KEY`

---

## PASSO 6: Criar Tabelas do Banco

1. Acesse no navegador:
   ```
   https://seu-dominio.com/api/db-config.php?init=true&token=sua-senha-super-secreta
   ```

2. Você verá:
   ```json
   {"success": true, "message": "Tabelas criadas com sucesso!"}
   ```

3. **Depois delete este arquivo** (`api/db-config.php`)

---

## PASSO 7: Atualizar `index.html`

Abra seu `index.html` com editor e mude:

**ENCONTRE:**
```javascript
const response = await fetch('/.netlify/functions/chat', {
```

**MUDE PARA:**
```javascript
const response = await fetch('/api/chat.php', {
```

E no formulário:

**ANTES:**
```javascript
fetch('/', { method: 'POST', body: data })
```

**DEPOIS:**
```javascript
fetch('/api/leads.php', { method: 'POST', body: data })
```

---

## PASSO 8: Testar

### Teste 1: Chat
```bash
curl -X POST https://seu-dominio.com/api/chat.php \
  -H "Content-Type: application/json" \
  -d '{"messages":[{"role":"user","content":"Olá!"}]}'
```

**Esperado:**
```json
{"reply": "Olá! Sou a Ana..."}
```

### Teste 2: Formulário
Preencha o formulário "Peça uma Proposta" e verifique:
- E-mail em `comercial87ss@gmail.com`
- Arquivo `data/leads.csv` (via FTP)

---

## 🔍 Verificar Logs

### No cPanel
1. **Error Log** (em Logs)
2. Veja erros PHP em tempo real

### Arquivo local
```
public_html/data/logs/error.log
```

---

## ⚠️ Problemas Comuns

### "500 Internal Server Error"
- ✅ Verificar `error.log` no cPanel
- ✅ Permissões: 644 para PHP, 755 para pastas
- ✅ PHP 7.4+ habilitado

### "Cannot connect to database"
- ✅ Confirmar credenciais MySQL
- ✅ Usuário tem permissões no banco?
- ✅ localhost é host correto?

### "E-mail não é enviado"
- ✅ `mail()` habilitado?
- ✅ `ADMIN_EMAIL` correto em `config.php`?
- ✅ Teste com outro e-mail da GoDaddy?

---

## 🚀 Próximos Passos

1. ✅ **Domínio próprio:** Site configuration → Domain management
2. ✅ **SSL/HTTPS:** Gratuito (Let's Encrypt)
3. ✅ **E-mail corporativo:** Crie `comercial@seu-dominio.com`
4. ✅ **Backups:** Configure Backup Wizard

---

**Sucesso! 🎉 Seu site está no ar!**
