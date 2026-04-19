# Stage 1: Build the frontend
FROM node:20-alpine AS builder
WORKDIR /app

# Copy only what's needed for install
COPY frontend/package*.json ./
RUN npm install --ignore-scripts --legacy-peer-deps

# Copy the rest and build
COPY frontend/ ./
RUN npm run build

# Stage 2: Serve with Nginx
FROM nginx:alpine

# Copy the built assets to a safe, unmasked path
COPY --from=builder /app/dist /usr/share/nginx/html

# Copy the production config
COPY docker/nginx.prod.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
