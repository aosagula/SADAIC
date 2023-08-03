# SADAIC - Registro de Obras

## Entorno de desarrollo
Se compone de 5 servicios:
- app: NGINX 
- php: PHP-FPM 7.2
- database: MaríaDB
- mailhog
- adminer

La integración de estos servicios se hace mediante Docker Compose y, más allá de la copia del código, no requiere configuraciones adicionales para funcionar.

### Uso
```
docker-compose up
```

Una vez iniciado la aplicación se podrá acceder a través del puerto 8000, Adminer el puerto 8306 y MailHog 8025.