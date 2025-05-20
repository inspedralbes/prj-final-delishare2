FROM node:18-alpine

WORKDIR /app

# Instala nodemon globalmente
RUN npm install -g nodemon

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 3000

# Usa el script start como fallback si dev no existe
CMD ["sh", "-c", "npm run dev || npm run start"]