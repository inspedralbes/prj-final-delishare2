# Imagen base usando Node.js 18 con Alpine Linux para un tamaño reducido
FROM node:18-alpine

# Establecer el directorio de trabajo para la aplicación
WORKDIR /app

# Copiar package.json y package-lock.json (si existe) para instalar dependencias
COPY package*.json ./

# Instalar dependencias del proyecto
RUN npm install

# Copiar el resto del código de la aplicación
COPY . .

# Exponer el puerto 5173 para el servidor de desarrollo de Vite
EXPOSE 5173

# Iniciar el servidor de desarrollo
CMD ["npm", "run", "dev"]
