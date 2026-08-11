<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion - Kipucloud</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
    <style>
        :root {
            --primary: #4669FA;
            --primary-dark: #3651d4;
            --bg-body: #0f0f1a;
            --bg-card: #1a1a2e;
            --text: #e2e8f0;
            --text-muted: rgba(255,255,255,0.52);
            --input-bg: rgba(255,255,255,0.04);
            --border: rgba(255,255,255,0.06);
        }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: var(--bg-body);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            background-image: radial-gradient(ellipse at 30% 20%, rgba(70,105,250,0.06) 0%, transparent 60%),
                              radial-gradient(ellipse at 70% 80%, rgba(70,105,250,0.04) 0%, transparent 60%);
        }
        .login-container { width: 100%; max-width: 420px; }
        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px 32px;
        }
        .login-brand {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-brand .brand-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4669FA 0%, #3651d4 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.3rem;
            margin-bottom: 12px;
        }
        .login-brand h2 {
            font-weight: 800;
            font-size: 1.5rem;
            margin: 0 0 2px;
            color: var(--text);
            letter-spacing: -0.5px;
        }
        .login-brand small {
            color: var(--text-muted);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .form-control {
            background: var(--input-bg);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 10px;
            padding: 11px 16px 11px 42px;
            font-size: 0.9rem;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.06);
            border-color: var(--primary);
            color: var(--text);
            box-shadow: 0 0 0 3px rgba(70,105,250,0.12);
        }
        .form-control::placeholder { color: rgba(255,255,255,0.2); }
        .form-label {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.82rem;
            margin-bottom: 6px;
        }
        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.25s;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(70,105,250,0.35);
            color: white;
        }
        .alert { border-radius: 10px; font-size: 0.85rem; padding: 10px 14px; }
        .input-icon { position: relative; }
        .input-icon i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.2);
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-brand">
                <div class="brand-icon"><i class="bi bi-lightning-fill"></i></div>
                <h2>Kipucloud</h2>
                <small>Control de Pagos</small>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('login/autenticar') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <div class="input-icon">
                        <i class="bi bi-person-fill"></i>
                        <input type="text" name="username" class="form-control" placeholder="Ingresa tu usuario" value="<?= old('username') ?>" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Contrasena</label>
                    <div class="input-icon">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" name="password" class="form-control" placeholder="Ingresa tu contrasena" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesion
                </button>
            </form>
        </div>
    </div>
</body>
</html>
