FROM nginx:alpine

# Copy custom nginx config
COPY nginx.conf /etc/nginx/nginx.conf
COPY sites /etc/nginx/sites-enabled

# Expose ports
EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
