<?php
namespace App\Controllers;
use App\Models\ModeloApi2;
use App\Models\ChatModel;
use App\Models\AdminModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class GeoApiControlller extends ResourceController
{
    protected $format = 'json';
    private $secret_key;

    public function __construct()
    {
        $this->model = new ModeloApi2();
        $this->secret_key = getenv('JWT_SECRET');
        if (!$this->secret_key) {
            throw new \RuntimeException('Configura JWT_SECRET en el archivo .env');
        }
        date_default_timezone_set('America/Lima');
    }

    // 🔐 Generar token
    private function generateToken($user_data)
    {
        $issuedAt = time();
        $expire = $issuedAt + (60 * 60 * 1);

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => $user_data
        ];

        return JWT::encode($payload, $this->secret_key, 'HS256');
    }

    private function validateToken()
    {
        $header = $this->request->getHeaderLine('Authorization');

        if (!$header) {
            return false;
        }

        try {
            $token = str_replace('Bearer ', '', $header);
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            return $decoded->data;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function login() //✅✅
    {

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $usuario = trim((string) ($input['usuario'] ?? $input['username'] ?? ''));
        $clave   = (string) ($input['clave'] ?? $input['password'] ?? '');

        if (!$usuario || !$clave) {
            return $this->fail([
                'status' => false,
                'message' => 'Todos los campos son obligatorios'
            ]);
        }

        $user = $this->model->login($usuario, $clave);

        if ($user) {

            $token = $this->generateToken([
                'usuario' => $usuario,
                'coduser' => $user['id']
            ]);

            return $this->respond([
                'status' => true,
                'data'   => $user,
                'token'  => $token
            ]);
        }

        return $this->fail([
            'status' => false,
            'message' => 'Credenciales inválidas'
        ]);
    }

    public function get_gestores_campo() //✅
    {

        $user_data = $this->validateToken();
        if (!$user_data) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Token inválido'
            ]);
        }

        $usuarios = $this->model->get_gestores_campo();

        if ($usuarios) {
            return $this->response->setJSON([
                'status' => true,
                'data' => $usuarios
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No se encontraron usuarios activos.'
            ]);
        }
    }

    public function get_data_usuario() //✅
    {

        $user = $this->validateToken();
        if (!$user) {
            return $this->failUnauthorized('Token inválido');
        }

        $coduser = $this->request->getVar('coduser') ?: ($user->coduser ?? null);
        if (!$coduser || (int) $coduser !== (int) ($user->coduser ?? 0)) {
            return $this->failForbidden('No puedes consultar otro usuario');
        }
        $usuario = $this->model->get_data_usuario($coduser);

        if ($usuario) {
            return $this->response->setJSON([
                'status' => true,
                'data' => $usuario
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No se encontraron usuarios activos.'
            ]);
        }
    }

    public function update_datos_personales() //✅✅
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->failUnauthorized('Token inválido');
        }

        // 📥 Obtener datos (CI4)
        $coduser    = trim((string) $this->request->getVar('coduser'));
        $telefono   = trim($this->request->getVar('telefono'));
        $correo     = trim((string) $this->request->getVar('correo'));
        $direccion  = trim($this->request->getVar('direccion'));
        $observacion= trim((string) $this->request->getVar('observacion'));

        // 🛑 Validación básica
        if (!$coduser || (int) $coduser !== (int) ($user->coduser ?? 0)) {
            return $this->respond([
                'status'  => false,
                'message' => 'coduser es requerido'
            ]);
        }

        // 📦 Data
        $data = [
            'telefono'     => $telefono,
            'email'        => $correo,
            'direccion'    => $direccion,
            'observacion'  => $observacion,
        ];

        // 🔄 Update
        $resultado = $this->model->updateData('admin_usuarios', $data, 'id', $coduser);

        if ($resultado) {
            return $this->respond([
                'status'  => true,
                'message' => 'Usuario actualizado correctamente'
            ]);
        } else {
            return $this->respond([
                'status'  => false,
                'message' => 'No se pudo actualizar el usuario'
            ]);
        }
    }


    // Métodos privados del controlador
    // ============================
        private function base64url($data) {
            return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        }

        private function getFirebaseConfig() {
            $path = getenv('FCM_CREDENTIALS_PATH');
            if (!$path) {
                throw new \RuntimeException('Configura FCM_CREDENTIALS_PATH para enviar notificaciones');
            }
            if (!file_exists($path)) {
                throw new \RuntimeException('Archivo de credenciales no encontrado: '.$path);
            }
            return json_decode(file_get_contents($path), true);
        }

        private function getAccessToken() {
            $keyFile = $this->getFirebaseConfig();
            $now = time();
            $jwtHeader = $this->base64url(json_encode(['alg'=>'RS256','typ'=>'JWT']));
            $jwtClaim = $this->base64url(json_encode([
                'iss' => $keyFile['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]));
            $unsignedToken = "$jwtHeader.$jwtClaim";

            $success = openssl_sign($unsignedToken, $signature, $keyFile['private_key'], 'sha256');
            if (!$success) {
                throw new \RuntimeException('Fallo en openssl_sign, revisa tu clave privada');
            }

            $jwt = $unsignedToken . '.' . $this->base64url($signature);

            $ch = curl_init('https://oauth2.googleapis.com/token');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt
                ])
            ]);

            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            if ($curlError) {
                throw new \RuntimeException('Curl error: ' . $curlError);
            }

            $result = json_decode($response, true);
            if (!isset($result['access_token'])) {
                throw new \RuntimeException('No se obtuvo access_token: ' . $response);
            }

            return $result['access_token'];
        }

        private function sendFCM($token, $title, $mensaje, array $data = []) {
            $config = $this->getFirebaseConfig();
            $accessToken = $this->getAccessToken();
            $projectId = $config['project_id'];

            $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

            $payload = [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "title" => $title,
                        "body" => $mensaje
                    ]
                ]
            ];

            if (!empty($data)) {
                $payload["message"]["data"] = array_map('strval', $data);
            }

            $headers = [
                "Authorization: Bearer $accessToken",
                "Content-Type: application/json"
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => json_encode($payload)
            ]);

            $response = curl_exec($ch);
            $error    = curl_error($ch);

            if ($error) {
                return json_encode(['status' => false, 'curl_error' => $error]);
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode != 200) {
                return json_encode([
                    'status' => false,
                    'http_code' => $httpCode,
                    'response' => json_decode($response, true)
                ]);
            }

            return $response;
        }

    // ======================================
    // ===========SIN MODELO=================


    public function api_registrar_token_fcm3()
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->failUnauthorized('Token inválido');
        }

        $fcm_token = $this->request->getVar('fcm_token');
        $user_id   = (int) ($user->coduser ?? 0);
        $user_name = $this->request->getVar('user_name');

        if (!$fcm_token || !$user_id) {

            return $this->response->setJSON([
                'status' => false,
                'message' => 'Datos incompletos'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('fcm_tokens');

        $token = $builder->where('token', $fcm_token)->get()->getRow();

        $now = date('Y-m-d H:i:s');

        if ($token) {

            $builder->where('id', $token->id)->update([
                'user_id' => $user_id,
                'user_name' => $user_name,
                'is_active' => 1,
                'updated_at' => $now
            ]);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Token actualizado'
            ]);
        }

        $builder->insert([
            'token' => $fcm_token,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'is_active' => 1,
            'created_at' => $now
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Token registrado'
        ]);
    }


    public function enviar_notificacion_fcm_v1()
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->failUnauthorized('Token inválido');
        }

        $token   = $this->request->getVar('token');
        $titulo  = $this->request->getVar('titulo');
        $mensaje = $this->request->getVar('mensaje');

        if (!$token || !$titulo || !$mensaje) {
            return $this->fail('Faltan datos');
        }

        $resultado = $this->sendFCM($token, $titulo, $mensaje);

        return $this->respond(json_decode($resultado, true));
    }


    // ======================================
    // ============ CHAT (app) ==============
    // ======================================

    private const CHAT_MAX_FILE_SIZE = 10485760;

    private const CHAT_ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'text/plain', 'text/csv',
        'application/zip', 'application/x-zip-compressed',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'audio/mpeg', 'audio/ogg', 'audio/wav', 'audio/x-wav', 'audio/webm', 'audio/mp4', 'audio/m4a', 'video/mp4', 'video/webm',
        'application/x-rar-compressed', 'application/vnd.rar', 'application/octet-stream',
    ];

    private const CHAT_ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'csv',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar',
        'mp3', 'ogg', 'wav', 'webm', 'm4a', 'mp4',
    ];

    public function chat_usuarios()
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->failUnauthorized('Token inválido');
        }

        $usuarioId = (int) ($user->coduser ?? 0);

        return $this->response->setJSON([
            'status' => true,
            'data'   => (new ChatModel())->obtenerUsuarios($usuarioId),
        ]);
    }

    public function chat_conversaciones()
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->failUnauthorized('Token inválido');
        }

        $usuarioId = (int) ($user->coduser ?? 0);

        return $this->response->setJSON([
            'status' => true,
            'data'   => (new ChatModel())->obtenerConversaciones($usuarioId),
        ]);
    }

    public function chat_listar()
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->failUnauthorized('Token inválido');
        }

        $usuarioId = (int) ($user->coduser ?? 0);
        $desde = max(0, (int) $this->request->getGet('desde'));
        $destinatarioIdRaw = $this->request->getGet('destinatario_id');
        $destinatarioId = ($destinatarioIdRaw === null || $destinatarioIdRaw === '') ? null : (int) $destinatarioIdRaw;

        try {
            $mensajes = (new ChatModel())->obtenerRecientes($usuarioId, $destinatarioId, $desde);
        } catch (\Throwable $e) {
            log_message('error', 'chat_listar: {message}', ['message' => $e->getMessage()]);
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No se pudo cargar el chat.',
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $mensajes,
        ]);
    }

    public function chat_marcar_leidos()
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->failUnauthorized('Token inválido');
        }

        $usuarioId = (int) ($user->coduser ?? 0);
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $destinatarioIdRaw = $input['destinatario_id'] ?? null;
        $destinatarioId = ($destinatarioIdRaw === null || $destinatarioIdRaw === '') ? null : (int) $destinatarioIdRaw;

        (new ChatModel())->marcarComoLeidos($usuarioId, $destinatarioId);

        return $this->response->setJSON(['status' => true]);
    }

    public function chat_enviar()
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->failUnauthorized('Token inválido');
        }

        $usuarioId = (int) ($user->coduser ?? 0);
        $destinatarioIdRaw = $this->request->getPost('destinatario_id');
        $destinatarioId = ($destinatarioIdRaw === null || $destinatarioIdRaw === '') ? null : (int) $destinatarioIdRaw;
        $mensaje = trim((string) $this->request->getPost('mensaje'));
        $archivo = $this->request->getFile('archivo');

        if ($destinatarioId === $usuarioId) {
            return $this->response->setJSON(['status' => false, 'message' => 'No puedes iniciar una conversación contigo mismo.']);
        }

        if ($destinatarioId !== null) {
            $destinatario = (new AdminModel())->where('id', $destinatarioId)->where('activo', 1)->first();
            if (!$destinatario) {
                return $this->response->setJSON(['status' => false, 'message' => 'El usuario seleccionado no está disponible.']);
            }
        }

        if ($mensaje === '' && (!$archivo || $archivo->getError() === UPLOAD_ERR_NO_FILE)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Escribe un mensaje o selecciona un archivo.']);
        }

        $datos = [
            'usuario_id'      => $usuarioId,
            'destinatario_id' => $destinatarioId,
            'mensaje'         => mb_substr($mensaje, 0, 2000),
            'creado_en'       => date('Y-m-d H:i:s'),
        ];

        if ($archivo && $archivo->getError() !== UPLOAD_ERR_NO_FILE) {
            if (!$archivo->isValid()) {
                return $this->response->setJSON(['status' => false, 'message' => 'El archivo no se pudo cargar.']);
            }
            if ($archivo->getSize() > self::CHAT_MAX_FILE_SIZE) {
                return $this->response->setJSON(['status' => false, 'message' => 'El archivo debe pesar como máximo 10 MB.']);
            }
            $extension = strtolower(pathinfo($archivo->getClientName(), PATHINFO_EXTENSION) ?: $archivo->getExtension());
            $mime = strtolower((string) $archivo->getMimeType());
            $mimeBase = trim(explode(';', $mime)[0]);
            $esImagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
            $imagenValida = $esImagen && @getimagesize($archivo->getTempName()) !== false;
            if (!in_array($extension, self::CHAT_ALLOWED_EXTENSIONS, true) || (!$imagenValida && !in_array($mimeBase, self::CHAT_ALLOWED_MIMES, true))) {
                return $this->response->setJSON(['status' => false, 'message' => 'Este tipo de archivo no está permitido.']);
            }
            if (in_array($extension, ['mp3', 'ogg', 'wav', 'webm', 'm4a'], true) && $mimeBase === 'video/webm') {
                $mimeBase = 'audio/webm';
            }

            $directorio = WRITEPATH . 'uploads/chat';
            if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
                return $this->response->setJSON(['status' => false, 'message' => 'No se pudo preparar el almacenamiento del archivo.']);
            }

            $nombreGuardado = bin2hex(random_bytes(16)) . '.' . $extension;
            $archivo->move($directorio, $nombreGuardado);
            $datos['archivo_nombre'] = $archivo->getClientName();
            $datos['archivo_ruta'] = $nombreGuardado;
            $datos['archivo_mime'] = $mimeBase;
            $datos['archivo_tamano'] = $archivo->getSize();
        }

        $model = new ChatModel();
        try {
            $id = $model->insert($datos, true);
        } catch (\Throwable $e) {
            log_message('error', 'chat_enviar: {message}', ['message' => $e->getMessage()]);
            return $this->response->setJSON(['status' => false, 'message' => 'No se pudo enviar el mensaje.']);
        }
        if (!$id) {
            log_message('error', 'chat_enviar: ' . json_encode($model->errors()));
            return $this->response->setJSON(['status' => false, 'message' => 'No se pudo guardar el mensaje.']);
        }

        try {
            $this->notificarNuevoMensajeChat($usuarioId, $destinatarioId, $mensaje !== '' ? $mensaje : '📎 Archivo adjunto');
        } catch (\Throwable $e) {
            log_message('error', 'chat_enviar push: {message}', ['message' => $e->getMessage()]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => ['id' => (int) $id],
        ]);
    }

    public function chat_archivo($id)
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->failUnauthorized('Token inválido');
        }

        $usuarioId = (int) ($user->coduser ?? 0);
        $mensaje = (new ChatModel())->obtenerPorId((int) $id, $usuarioId);
        if (!$mensaje || empty($mensaje['archivo_ruta'])) {
            return $this->response->setStatusCode(404)->setBody('Archivo no encontrado.');
        }

        $ruta = realpath(WRITEPATH . 'uploads/chat' . DIRECTORY_SEPARATOR . basename($mensaje['archivo_ruta']));
        $directorio = realpath(WRITEPATH . 'uploads/chat');
        if (!$ruta || !$directorio || dirname($ruta) !== $directorio || !is_file($ruta)) {
            return $this->response->setStatusCode(404)->setBody('Archivo no encontrado.');
        }

        $nombre = str_replace(['"', "\r", "\n"], '', $mensaje['archivo_nombre'] ?: basename($ruta));

        return $this->response
            ->setContentType($mensaje['archivo_mime'] ?: 'application/octet-stream')
            ->setHeader('Content-Disposition', 'inline; filename="' . $nombre . '"')
            ->setBody((string) file_get_contents($ruta));
    }

    private function notificarNuevoMensajeChat(int $emisorId, ?int $destinatarioId, string $preview): void
    {
        $db = \Config\Database::connect();

        $emisor = $db->table('admin_usuarios')->select('nombre')->where('id', $emisorId)->get()->getRow();
        $nombreEmisor = $emisor->nombre ?? 'Alguien';
        $titulo = $destinatarioId === null ? "Chat grupal - $nombreEmisor" : $nombreEmisor;

        $builder = $db->table('fcm_tokens')->where('is_active', 1);
        if ($destinatarioId === null) {
            $builder->where('user_id !=', $emisorId);
        } else {
            $builder->where('user_id', $destinatarioId);
        }

        foreach ($builder->get()->getResultArray() as $t) {
            $this->sendFCM($t['token'], $titulo, $preview, [
                'type'       => 'chat',
                'es_grupal'  => $destinatarioId === null ? '1' : '0',
                'emisor_id'  => $emisorId,
            ]);
        }
    }



} //FIN
