# API de Facturación Electrónica (CFDI) 🧾

API REST que simula el flujo completo de facturación electrónica mexicana (CFDI), construida con Laravel 11. Desarrollada como proyecto de portafolio basado en experiencia real trabajando con sistemas de facturación en producción.

## 🚀 Tecnologías

- **PHP 8.2+** / **Laravel 11**
- **MySQL** — base de datos relacional
- **Laravel Sanctum** — autenticación por tokens
- **L5-Swagger / OpenAPI 3.0** — documentación interactiva
- **PHPUnit** — pruebas automatizadas (15 tests)

## ✨ Funcionalidades

- Autenticación segura con tokens Bearer (Sanctum)
- CRUD completo de Clientes con validación de RFC mexicano
- CRUD completo de Productos con clave SAT y unidad de medida
- Emisión de facturas con cálculo automático de IVA (16%)
- Generación de folio único UUID por factura
- Flujo de estados: `borrador` → `emitida` → `cancelada`
- Cancelación de facturas con motivo registrado
- Soft deletes para conservar historial
- Auditoría completa de todas las acciones
- Documentación interactiva con Swagger UI

## 📋 Instalación

### Requisitos previos

- PHP 8.2+
- Composer
- MySQL

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/xCJRA/api-cfdi.git
cd api-cfdi

# 2. Instalar dependencias
composer install

# 3. Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar tu base de datos en .env
# DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed

# 6. Generar documentación Swagger
php artisan l5-swagger:generate

# 7. Levantar el servidor
php artisan serve
```

## 📖 Documentación

Con el servidor corriendo, accede a la documentación interactiva:
http://localhost:8000/api/documentation

Desde ahí puedes autenticarte y probar todos los endpoints directamente en el navegador.

## 🔐 Autenticación

Todos los endpoints requieren un token Bearer. Para obtenerlo:

```http
POST /api/login
Content-Type: application/json

{
    "email": "admin@apicfdi.com",
    "password": "password123"
}
```

Usa el token recibido en el header de todas las peticiones:

```http
Authorization: Bearer {token}
```

## 📌 Endpoints principales

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/login` | Obtener token de acceso |
| POST | `/api/logout` | Cerrar sesión |
| GET | `/api/clientes` | Listar clientes |
| POST | `/api/clientes` | Crear cliente |
| GET | `/api/clientes/{id}` | Ver cliente |
| PUT | `/api/clientes/{id}` | Actualizar cliente |
| DELETE | `/api/clientes/{id}` | Eliminar cliente |
| GET | `/api/productos` | Listar productos |
| POST | `/api/productos` | Crear producto |
| GET | `/api/productos/{id}` | Ver producto |
| PUT | `/api/productos/{id}` | Actualizar producto |
| DELETE | `/api/productos/{id}` | Eliminar producto |
| GET | `/api/facturas` | Listar facturas |
| POST | `/api/facturas` | Emitir factura |
| GET | `/api/facturas/{id}` | Ver factura |
| POST | `/api/facturas/{id}/cancelar` | Cancelar factura |

## 💡 Ejemplo de emisión de factura

```http
POST /api/facturas
Authorization: Bearer {token}
Content-Type: application/json

{
    "cliente_id": 1,
    "conceptos": [
        { "producto_id": 1, "cantidad": 2 },
        { "producto_id": 2, "cantidad": 5 }
    ]
}
```

Respuesta:

```json
{
    "id": 1,
    "folio": "550e8400-e29b-41d4-a716-446655440000",
    "cliente_id": 1,
    "subtotal": "36000.00",
    "iva": "5760.00",
    "total": "41760.00",
    "estado": "emitida",
    "fecha_emision": "2026-05-21T10:00:00"
}
```

## 🧪 Tests

El proyecto incluye tests automatizados con PHPUnit cubriendo los flujos principales de la API.

### Configurar ambiente de testing

```bash
cp .env.testing.example .env.testing
php artisan key:generate --env=testing
```

> Los tests usan SQLite en memoria — no necesitas configurar una base de datos separada.

### Ejecutar todos los tests

```bash
php artisan test
```

### Ejecutar un módulo específico

```bash
php artisan test --filter AuthTest
php artisan test --filter ClienteTest
php artisan test --filter ProductoTest
php artisan test --filter FacturaTest
```

### Cobertura de tests

| Módulo | Tests | Qué se verifica |
|--------|-------|-----------------|
| **Auth** | 2 | Login correcto, credenciales incorrectas |
| **Clientes** | 4 | Listar, crear, RFC inválido, acceso sin token |
| **Productos** | 3 | Listar, crear, validación de campos |
| **Facturas** | 5 | Listar, crear, cálculo de IVA, cancelar, validaciones |

## 🗄️ Estructura de la base de datos

| Tabla | Descripción |
|-------|-------------|
| `users` | Usuarios del sistema |
| `clientes` | Clientes con validación de RFC |
| `productos` | Catálogo con clave SAT |
| `facturas` | Facturas con folio UUID y estados |
| `conceptos_factura` | Líneas de detalle por factura |
| `auditoria` | Registro de todas las acciones |

## 👨‍💻 Autor

**César José Reyes Alonso** — Backend Developer  
[LinkedIn](https://linkedin.com/in/xcjra) · [GitHub](https://github.com/xCJRA)
