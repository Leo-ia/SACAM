# Cómo funciona el login de administrador sin crear un usuario en la BD

Documento de apoyo para explicar el Sprint 4 ante el docente. Responde la
pregunta: **¿cómo es posible que haya login si no se crea ningún usuario en la
base de datos?**

---

## 1. La respuesta corta

Las credenciales del administrador (`admin` / `1234`) **no viven en la base de
datos sino en el código PHP**. El login solo compara lo que el usuario escribe
en el formulario contra dos variables que ya están fijas en el archivo:

```php
// app/administrador/autenticar_admin.php
$usuario_correcto = 'admin';
$clave_correcta    = '1234';
```

Si coinciden, PHP "marca" al navegador con una **sesión**; si no, lo regresa al
formulario. A partir de ese momento, cada página del panel consulta en memoria
si esa marca existe: sí → entra, no → lo manda al login.

---

## 2. Cómo se ve el flujo completo

```
navegador                          servidor PHP
──────────                          ────────────
1. Abre login.php        ──────►   muestra el formulario
2. Escribe admin / 1234  ────►  con POST llega a autenticar_admin.php
   y presiona "Entrar"              │
                                    ▼
                         ¿admin == 'admin' AND 1234 == '1234'?
                                    │
                    ┌───────────────┴───────────────┐
                    │ sí (coinciden)                │ no
                    ▼                               ▼
          $_SESSION['admin_autenticado'] = true    guarda un mensaje de error
          crea la cookie PHPSESSID                 y regresa a login.php
          redirige a usuarios.php
                    │
                    ▼
3. usuarios.php pide la sesión       ──►  require_login.php
   (manda la cookie PHPSESSID)             ¿existe admin_autenticado?
                    │                          │
                    │ sí                       │ no
                    ▼                          ▼
              Muestra la página         Redirige a login.php
```

La clave del "truco" es que **la credencial real nunca viaja ni se guarda**:
solo se compara al momento del POST.

---

## 3. Pieza por pieza

### 3.1 El formulario — `public/administrador/login.php`

Una página HTML normal. Por seguridad envía los datos con `POST`
(no aparecen en la URL) hacia `autenticar_admin.php`:

```html
<form method="post" action="autenticar_admin.php">
    <input type="text"     name="usuario" required>
    <input type="password" name="clave"   required>
    <button type="submit">Entrar</button>
</form>
```

Si el backend falló la validación, este mismo archivo muestra el mensaje
`Usuario o contraseña incorrectos` que guardó en la sesión.

### 3.2 La validación — `app/administrador/autenticar_admin.php`

Es donde las credenciales están fijas y donde se decide si entrar o no:

```php
<?php
session_start();

// Credenciales fijas del avance mínimo (no hay tabla en BD).
$usuario_correcto = 'admin';
$clave_correcta    = '1234';

$usuario = $_POST['usuario'] ?? '';
$clave   = $_POST['clave']   ?? '';

if (hash_equals($usuario_correcto, $usuario) && hash_equals($clave_correcta, $clave)) {
    $_SESSION['admin_autenticado'] = true;   // <-- la "marca" de que entró
    header('Location: usuarios.php');
    exit;
}

$_SESSION['login_error'] = 'Usuario o contraseña incorrectos.';
header('Location: login.php');
exit;
```

Detalles importantes:

- **`hash_equals()`** compara en tiempo constante (evita ataques de "timing").
  Es la forma correcta de comparar credenciales, en vez de `==`.
- **`$_SESSION['admin_autenticado'] = true`** es todo lo que se guarda. No se
  guarda el usuario, ni la contraseña, ni nada más. Si la comparación falla,
  esa línea nunca se ejecuta.

### 3.3 ¿Qué es la sesión de PHP?

PHP tiene un mecanismo de sesiones por defecto:

1. Cuando un script llama `session_start()`, el servidor le asigna al
   navegador una **cookie** con un identificador aleatorio (generalmente
   `PHPSESSID=abc123...`).
2. Toda variable `$_SESSION[...]` que se asigne **se guarda en un archivo del
   servidor**, no en la cookie. La cookie solo es una "llave" que identifica a
   ese visitante.
3. Así, en el siguiente clic, el navegador manda la cookie y PHP vuelve a abrir
   el archivo de sesión correspondiente: `$_SESSION` "recuerda" si venía
   autenticado.

Comparación para entenderlo:

| Qué es | Dónde vive | Contenido |
|---|---|---|
| Cookie `PHPSESSID` | Navegador | Solo un identificador aleatorio (una "ficha" sin datos) |
| Archivo de sesión | Servidor (p. ej. `/tmp/sess_...`) | `admin_autenticado = true` cuando entró |

Ningún dato sensible va en la cookie: si la interceptaran no sabrían si hay
sesión ni con qué credenciales se entró.

### 3.4 El portero — `app/administrador/requiere_login.php`

Cada página y cada procesador del panel lo incluye en la primera línea, como
`require __DIR__ . '/../../app/administrador/requiere_login.php';`:

```php
<?php
session_start();

if (empty($_SESSION['admin_autenticado'])) {
    header('Location: login.php');
    exit;
}
```

Es el "portero": si alguien teclea directamente la URL de `usuarios.php` sin
haber pasado por el login, este archivo lo redirige al formulario y la página
**nunca llega a mostrarse** (por eso `exit;`). Lo mismo aplica a los `POST` de
los procesadores: no sirve de nada mandar un POST directo a
`procesar_eliminar_usuario.php`, porque también pasa por el portero.

### 3.5 El cierre de sesión — `app/administrador/cerrar_sesion.php`

```php
<?php
session_start();

unset($_SESSION['admin_autenticado']);
session_destroy();

header('Location: login.php');
```

Borra la marca y destruye la sesión: la "ficha" deja de valer, y el navegador
vuelve a quedar bloqueado.

---

## 4. ¿Por qué no se usó una tabla en la BD ni JavaScript?

Son dos decisiones distintas:

### 4.1 Por qué no en la BD

Es el **requisito explícito de este avance**. Además, el esquema ya tiene
comentada la tabla `cuentas_acceso` (con `password_hash`) para el Sprint 2;
cuando exista, el código se actualiza para preguntarle a la BD en lugar de
comparar contra variables fijas. Mientras tanto, comparar contra variables
fijas es la forma más simple de "simular" un usuario único sin montar
infraestructura.

### 4.2 Por qué no en JavaScript

Se sopesó hacer la validación "por coincidencias" con JS dentro de la página:
para el ojo sería lo mismo y aún más simple. Pero tiene un problema grave: el
navegador recibe el código JS, así que la contraseña `1234` quedaría **visible
a simple vista** (clic derecho → "Ver código fuente" y ahí se lee). Eso no
resiste una revisión de seguridad. En cambio con PHP la comparación ocurre en
el servidor y el navegador nunca ve las credenciales: solo ve el formulario y
si fue aceptado o rechazado.

---

## 5. Cómo se cambiarían las credenciales

En `app/administrador/autenticar_admin.php`:

```php
$usuario_correcto = 'otro_admin';
$clave_correcta    = 'otra_clave';
```

No hay que tocar la base de datos ni ningún otro archivo.

---

## 6. Limitaciones (y por qué está bien para este sprint)

| Limitación | Por qué es aceptable ahora | Cómo se resuelve (Sprint 2) |
|---|---|---|
| Credenciales en el código, no en BD | Avance mínimo; la tabla aún no existe | Tabla `cuentas_acceso` con `password_hash` / `password_verify` |
| Un solo administrador fijo | El solicitante pidió un acceso simple | Varias cuentas por usuario en BD |
| Cambiar la clave requiere editar código | — | Panel de administración para gestionar cuentas |

---

Documento de apoyo del Sprint 4 (`docs/sprint4/`).