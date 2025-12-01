# Prueba Técnica: Plataforma de Anuncios Clasificados - Sounds Market

## Descripción del Proyecto

Este proyecto es una aplicación web desarrollada con **Laravel 12** y **Livewire 3** que simula una plataforma de anuncios clasificados para instrumentos musicales y equipos de DJ. El objetivo de esta prueba técnica es implementar funcionalidades de creación y visualización de anuncios.

## Objetivo de la Prueba Técnica

Deberás implementar las siguientes funcionalidades:

### 1. Creación de Anuncios
- Crear un formulario para publicar nuevos anuncios que incluya:
  - **Título** del anuncio
  - **Descripción** detallada
  - **Categoría** (selección jerárquica)
  - **Precio** (en euros)
  - **Fotos** (mínimo 1, máximo 6 imágenes)
- Implementar validación completa de los datos:
  - Todos los campos son obligatorios
  - El precio debe ser un número positivo
  - Al menos una imagen es requerida, máximo 6
- La vista debe estar enfocada para usuarios finales con un diseño limpio y profesional
- Esta ruta debe estar **protegida** y solo accesible para usuarios autenticados

### 2. Vista del Anuncio
- Crear una página de detalle que muestre:
  - Toda la información del anuncio y la imagen principal
  - **(Opcional)** Galería de fotos navegable
  - Información del usuario que publicó el anuncio
  - Fecha de publicación o última actualización
- Esta ruta debe ser **pública** y accesible para todos los usuarios
- **(Opcional)** Añadir opciones de editar/eliminar anuncio, visibles solo para el creador

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- **Docker** y **Docker Compose**
- **(Opcional)** Composer con Laravel Sail instalado globalmente

## Instalación y Configuración

Sigue estos pasos para configurar el proyecto:

### 1. Clonar el repositorio

```bash
git clone git@github.com:soundsmarket/tech-test-soundsmarket.git
cd tech-test-soundsmarket
```

### 2. Copiar el archivo de entorno

```bash
cp .env.example .env
```

Edita el archivo `.env` si necesitas ajustar las credenciales de la base de datos u otros parámetros.

### 3. Levantar el entorno con Docker (Laravel Sail)

```bash
./vendor/bin/sail up -d
```

### 4. Instalar dependencias (si es necesario)

```bash
./vendor/bin/sail composer install
./vendor/bin/sail npm install
```

### 5. Generar la clave de la aplicación

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Ejecutar las migraciones

```bash
./vendor/bin/sail artisan migrate
```

### 7. Poblar la base de datos con los seeders

```bash
./vendor/bin/sail artisan db:seed
```

Esto creará:
- Un usuario de prueba: `test@example.com` / `password`
- Categorías jerárquicas de instrumentos musicales y equipos de DJ

### 8. Iniciar el servidor de assets (Vite)

```bash
./vendor/bin/sail npm run dev
```

## Estructura del Proyecto

### Modelos Disponibles

#### `Listing` (Anuncio)
```php
- id
- title (string)
- description (text)
- price (integer)
- user_id (foreignId)
- category_id (foreignId)
- timestamps
```

**Relaciones:**
- `user()`: Pertenece a un usuario
- `category()`: Pertenece a una categoría
- Ya implementa `HasMedia` de Spatie Media Library para las imágenes

#### `Category` (Categoría)
```php
- id
- name (string)
- parent_id (foreignId, nullable)
- timestamps
```

**Relaciones:**
- `parent()`: Pertenece a una categoría padre
- `children()`: Tiene muchas categorías hijas
- `listings()`: Tiene muchos anuncios

#### `User` (Usuario)
```php
- listings()`: Tiene muchos anuncios
```

### Estructura de Categorías

El proyecto incluye las siguientes categorías precargadas:

- **Instrumentos Musicales**
  - Guitarras
    - Guitarras Eléctricas
    - Guitarras Acústicas
  - Baterías
  - Teclados
- **Equipos de DJ**
  - Controladoras
  - Mezcladores
  - Altavoces

## Reglas y Requisitos

### Tecnologías a Utilizar

- **Componentes Livewire** para toda la lógica de formularios y vistas
- **Laravel Media Library** (Spatie) para la gestión de imágenes
- **Flux UI** (ya integrado en el proyecto) para los componentes de interfaz

### Buenas Prácticas Requeridas

1. **Validación de datos**: Implementar validación tanto en el cliente como en el servidor
2. **Autorización**: Proteger rutas y acciones según los permisos del usuario
3. **Código limpio**: Seguir las convenciones de Laravel y PSR-12
4. **Componentes reutilizables**: Crear componentes Livewire modulares
5. **Manejo de imágenes**: Utilizar Media Library para subir y gestionar fotos

### Restricciones

- No modificar la estructura base del proyecto innecesariamente
- Solo añadir una nueva entrada en el panel lateral de navegación
- Utilizar los modelos Eloquent existentes

## Gestión de Imágenes

El proyecto utiliza **Laravel Media Library de Spatie** para la gestión de archivos multimedia.

### Ejemplo de uso:

```php
// Añadir una imagen a un anuncio
$listing->addMedia($file)->toMediaCollection('images');

// Obtener todas las imágenes
$listing->getMedia('images');

// Obtener la primera imagen
$listing->getFirstMediaUrl('images');
```

**Documentación oficial**: https://spatie.be/docs/laravel-medialibrary

## Criterios de Evaluación

Se evaluará:
- ✅ Funcionalidad completa y correcta
- ✅ Calidad del código (limpieza, organización, PSR-12)
- ✅ Uso apropiado de Livewire y Laravel
- ✅ Validación y manejo de errores
- ✅ Experiencia de usuario (UX/UI)
- ✅ Commits descriptivos y estructura Git
- 🌟 Bonus: Implementación de funcionalidades opcionales

## Tiempo Estimado

Esta prueba está diseñada para completarse en **3-5 horas** aproximadamente, dependiendo de tu experiencia con Laravel y Livewire.

## Recursos Útiles

- [Documentación de Laravel 12](https://laravel.com/docs/12.x)
- [Documentación de Livewire 3](https://livewire.laravel.com/docs)
- [Laravel Media Library](https://spatie.be/docs/laravel-medialibrary)
- [Flux UI Components](https://flux.laravel.com)

## Usuario de Prueba

Para facilitar las pruebas, el proyecto incluye un usuario precargado:

- **Email**: `test@example.com`
- **Password**: `password`

## Entrega

Una vez completada la prueba, asegúrate de:

1. Realizar commits frecuentes con mensajes descriptivos
2. Incluir instrucciones adicionales si modificaste la configuración
3. Documentar cualquier decisión técnica importante
4. Verificar que todas las migraciones y seeders funcionen correctamente
5. Subir el proyecto a un repositorio privado en GitHub y proporcionar acceso al usuario `soundsmarket-dev` para su revisión
6. (Opcional) Si se prefiere, puedes enviar una Pull Request pública al repositorio original con las modificaciones realizadas, aunque se recomienda mantener el repositorio privado

## Soporte

Si tienes dudas sobre el enunciado de la prueba o encuentras problemas con la configuración inicial del proyecto, contacta a info@soundsmarket.com.

---

**¡Buena suerte con la prueba técnica!** 🚀
