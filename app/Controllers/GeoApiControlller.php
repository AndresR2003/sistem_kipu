<?php
namespace App\Controllers;
use App\Models\ModeloApi2;
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

        private function sendFCM($token, $title, $mensaje) {
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



} //FIN
