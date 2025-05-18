# DeliShare - Red Social de Recetas

## 📋 Información del Proyecto
* **Integrantes:** Simran y Ishaa
* **Nombre del Proyecto:** DeliShare
* **Descripción:** DeliShare es una red social centrada en compartir y descubrir recetas. Permite a los usuarios crear perfiles, compartir recetas, interactuar con otros chefs, y participar en transmisiones en vivo de cocina.

## 🚀 Características Principales
* Sistema de autenticación de usuarios
* Perfiles de usuario personalizables
* Compartir y descubrir recetas
* Sistema de chat en tiempo real
* Transmisiones en vivo de cocina
* Sistema de notificaciones
* Búsqueda avanzada de recetas
* Interacción social (likes, comentarios)

## 🛠️ Tecnologías Utilizadas
* **Frontend:** 
  * Vue.js 3 con Vite
  * Tailwind CSS para estilos
  * Socket.IO Client para tiempo real
* **Backend Principal:** Laravel (PHP)
* **API en Tiempo Real:** Node.js con Socket.IO
* **Base de Datos:** MySQL 8.2
* **Gestión de Base de Datos:** Adminer
* **Contenedorización:** Docker y Docker Compose

## 📦 Requisitos Previos
* Docker y Docker Compose instalados
* Git
* Node.js (opcional, para desarrollo local)
* Composer (opcional, para desarrollo local)

## 🚀 Instalación y Ejecución

### 1. Clonar el Repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd delishare
```

### 2. Configuración del Entorno
El proyecto utiliza Docker Compose para gestionar todos los servicios. La configuración está definida en `docker-compose.yml` e incluye:

* **Base de Datos MySQL**
  * Puerto: 3306
  * Base de datos: delidelishare
  * Usuario: root
  * Contraseña: root

* **Frontend (Vue.js)**
  * Puerto: 5173
  * URL: http://localhost:5173

* **Backend (Laravel)**
  * Puerto: 8000
  * URL: http://localhost:8000

* **API Node.js (Tiempo Real)**
  * Puerto: 4000
  * URL: http://localhost:4000

* **Adminer (Gestión BD)**
  * Puerto: 9094
  * URL: http://localhost:9094

### 3. Iniciar el Proyecto
```bash
docker-compose up
```

Este comando:
* Construirá las imágenes necesarias
* Iniciará todos los contenedores
* Instalará las dependencias automáticamente
* Inicializará la base de datos
* Configurará las variables de entorno

### 4. Acceso a los Servicios
Una vez iniciado, podrás acceder a:
* Frontend: http://localhost:5173
* Backend API: http://localhost:8000
* API Tiempo Real: http://localhost:4000
* Adminer: http://localhost:9094

## 📊 Gestión del Proyecto
* **Prototipo Gráfico:** [https://design.penpot.app/#/view/96c4bd8e-df43-800f-8005-9d539a93bab0?page-id=96c4bd8e-df43-800f-8005-9d539a93bab1&section=interactions&index=0&share-id=790b4dba-cade-8121-8005-9d6d348cdc72]
* **URL de Producción:** [https://delishare.cat/]

## 📈 Estado Actual del Proyecto
El proyecto se encuentra en fase de desarrollo activo. Las funcionalidades implementadas incluyen:
* ✅ Sistema de autenticación
* ✅ Perfiles de usuario
* ✅ Compartir recetas
* ✅ Chat en tiempo real
* ✅ Transmisiones en vivo
* ✅ Sistema de notificaciones
* ✅ Documentación 

## 🤝 Contribución
Para contribuir al proyecto:
1. Haz fork del repositorio
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia
Este proyecto está bajo la Licencia.

## 📧 Contacto
* Simran - [k94simran@gmail.com]
* Ishaa - [ishaaamin22@gmail.com]