# Frontend Dockerfile - React 19 + Vite
FROM node:20-alpine

# Définir le répertoire de travail
WORKDIR /app

# Copier package.json et package-lock.json
COPY package*.json ./

# Installer les dépendances
RUN npm ci

# Copier le reste de l'application
COPY . .

# Exposer le port Vite
EXPOSE 5173

# Démarrer le serveur de développement Vite
CMD ["npm", "run", "dev", "--", "--host"]
