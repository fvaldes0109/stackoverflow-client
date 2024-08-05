# Factorenergía - Prueba Técnica

## Cliente de StackOverflow (PHP - Laravel)

### Descripción

Implementación de un endpoint que consuma la API de StackOverflow, concretamente el endpoint de búsqueda de preguntas:

```plaintext
https://api.stackexchange.com/docs/questions
```

El acceso al cliente implementado se realiza a través de una petición GET a la siguiente URL:

```plaintext
http://localhost/api/v1/questions
```

Los parámetros de la petición GET son los siguientes:

- `tagged` **(requerido)**: una lista de tags separados por punto y coma (`;`). Ejemplo: `php;laravel`. Esto filtrará las preguntas que contengan todos los tags especificados.

- `fromdate` **(opcional)**: fecha de inicio en formato UNIX timestamp. Ejemplo: `1614556800`. Esto filtrará las preguntas que hayan sido creadas a partir de la fecha especificada.

- `todate` **(opcional)**: fecha de fin en formato UNIX timestamp. Ejemplo: `1614556800`. Esto filtrará las preguntas que hayan sido creadas hasta la fecha especificada. La fecha de fin no puede ser mayor a la fecha actual, de lo contrario el sistema devolverá un error de validación.


### Requisitos

Solo se necesita tener instalado Docker y Docker Compose en el sistema.

### Instalación

1. Clonar el repositorio:

    ```bash
    git clone https://github.com/fvaldes0109/stackoverflow-client.git
    ```

2. Ingresar al directorio del proyecto:

    ```bash
    cd stackoverflow-client
    ```

3. Ejecutar el script de instalación:

    ```bash
    ./setup.sh
    ```

4. Ejecutar las migraciones

    ```bash
    ./vendor/bin/sail artisan migrate
    ```

Es recomendable esperar unos segundos antes de ejecutar las migraciones, ya que dentro del contenedor de MySQL, la base de datos puede tardar unos segundos en levantarse.

Luego de esto se podrán realizar peticiones al endpoint mencionado anteriormente.

### Ejecución

Luego de haber realizado la instalación, el sistema estará funcionando en la dirección `http://localhost`.

Para detenerlo, se puede ejecutar el siguiente comando:

```bash
./vendor/bin/sail down
```

Para volver a levantar el sistema, se puede ejecutar el siguiente comando:

```bash
./vendor/bin/sail up -d
```

### Estructura

La petición es manejada por el controlador `App\Http\Controllers\QuestionController`.

Primeramente se valida la petición para asegurarse de que los parámetros `tagged`, `fromdate` y `todate` sean correctos.

Luego se revisa si la petición ya ha sido realizada anteriormente en la tabla `inquires`. Esta tabla almacena las tuplas de query params realizadas, y por medio de una relación ManyToMany con la tabla `questions` se obtienen las preguntas asociadas a la petición que se obtuvieron en la primera llamada a la API de StackOverflow. Si la petición ya ha sido realizada anteriormente, se devuelven las preguntas almacenadas en la tabla `questions`, evitando realizar una nueva llamada a la API.

En caso de que la petición no haya sido realizada anteriormente, se realiza una llamada a la API de StackOverflow y se almacenan las preguntas obtenidas en la tabla `questions`, su autor en la tabla `owners`, y sus tags en la tabla `tags`. Luego se almacena la petición en la tabla `inquires` para futuras consultas, y se le asocian las preguntas obtenidas.

### Manejo de errores

En caso de que la petición no cumpla con las validaciones, se devolverá un error de validación con los mensajes correspondientes.

En caso de que la petición a la API de StackOverflow falle, se devolverá la respuesta de error de la API de StackOverflow.

Además, ocurre que en ocasiones el `owner` de una pregunta solo trae el `user_type` como `does_not_exist`, y el `display_name`. En estos casos, se almacena el `display_name` en la tabla `owners` y se asocia a la pregunta correspondiente.
