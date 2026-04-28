FROM node:20-alpine

WORKDIR /app

COPY package.json package-lock.json* /app/
RUN npm install --legacy-peer-deps

COPY . /app

ENV NITRO_HOST=0.0.0.0
ENV NITRO_PORT=3000
EXPOSE 3000

# Production build + запуск SSR-сервера
CMD ["/bin/sh", "-c", "npm run build && node .output/server/index.mjs"]
