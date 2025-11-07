# WEB2025TPE-PT3
Repositorio para el trabajo final de WEB2 parte 3

Se adjunta documentacion de los enpoints generados. Para entender como consumir nuestra API, realizamos una breve explicacion y damos algunos ejemplos.

# Requerimientos Funcionales Mínimos:

1. La  API debe ser RESTful
Diseñamos los endpoints partiendo del recurso para mantener una interfaz unificada. Para ejemplifica adjuntamos una tabla de ruteo en la que se muestra:
    - Recurso.
    - Verbo.
    - Controlador.
    - Metodo.

'movies',     'GET',    'FilmApiController',    'getFilms' 
'movie/:id',  'GET',    'FilmApiController',    'getFilmById'
'movie',      'POST',   'FilmApiController',    'addFilm' (TODO)
'movie/:id',  'PUT',    'FilmApiController',    'updateFilm'
'movie/:id',  'DELETE', 'FilmApiController',    'deleteFilm'

2. Debe tener al menos un servicio que liste (GET) una coleccion entera de entidades.

GET  ej:  http://localhost/tpe3-web2-2025/api/movies

Rebira un JSON: 

[
    {"id_pelicula":3,"titulo":"El Padrino","anio":1972,"rating":10,"id_genero":3,"poster":""},{"id_pelicula":5,"titulo":"Guerra Mundial Z","anio":2013,"rating":7,"id_genero":1,"poster":""},{"id_pelicula":4,"titulo":"Interestelar","anio":2014,"rating":9,"id_genero":4,"poster":""},{"id_pelicula":8,"titulo":"John Wick 4","anio":2023,"rating":8,"id_genero":1,"poster":""},{"id_pelicula":11,"titulo":"Me before you","anio":2016,"rating":7,"id_genero":8,"poster":""},{"id_pelicula":10,"titulo":"Son como niños","anio":2010,"rating":6,"id_genero":2,"poster":""},{"id_pelicula":1,"titulo":"Terminator: El destino oculto","anio":2019,"rating":5,"id_genero":1,"poster":""}
]

3. El servicio que lista una colección entera debe poder ordenarse opcionalmente por al menos un campo de la tabla, de manera ascendente o descendente.
Para ordenar una busqueda hay que elegir un campo y criterio de orden.
Los campos disponibles para ordenar son:
    - 'titulo'
    - 'anio'
    - 'rating'
Y los criterios posibles son:
    - 'asc (ascendente)'
    - 'desc (descendente)'

Si no se especifica parametros, el orden por defecto sera "titulo ascendente"

GET http://localhost/tpe3-web2-2025/api/movies ---> ordenado ASC por defecto
GET http://localhost/tpe3-web2-2025/api/movies?orderBy=anio&sort=ASC ---> orden por año ascendente
GET http://localhost/tpe3-web2-2025/api/movies?orderBy=rating&sort=DESC ---> orden por rating ascendete

4. Debe tener al menos un servicio que obtenga (GET) una entidad determinada por su ID.

GET ej: http://localhost/tpe3-web2-2025/api/movie/1

Recibira un JSON:

{
    "id_pelicula":1,
    "titulo":"Terminator: El destino oculto",
    "anio":2019,
    "rating":5,
    "id_genero":1,
    "poster":"",
    "genero":"Acción"
}


Si ingresa un id inexistente ejemplo:
GET ej: http://localhost/tpe3-web2-2025/api/movie/100000

Recibira: Status 400 Not Found y un mensaje com "La pelicula con el id=100000 no existe".

5. Debe tener al menos un servicio para agregar y otro para modificar datos (POST y PUT).

Modificacion (PUT)
En cuanto a la modificacion, no es necesario completar todos los atributos, ya que solo serán modificados los presentes en la petición. El atributo "id_pelicula" no podrá modificarse por ser el que indica el item a editar. Para modificar un item se debe utilizar el verbo PUT a travéz del siguente endpoint:

PUT ej: http://localhost/tpe3-web2-2025/api/movies/1

{
    "id_pelicula": 1,
    "titulo": "Terminator: El destino oculto",
    "anio": 2019,
    "rating": 5,
    "id_genero": 1,
    "poster": "",
    "genero": "Acción"
}

6. La API Rest debe manejar de manera adecuada al menos los siguientes códigos de error (200, 201, 400 y 404)

200 OK: La solicitud se ha completado con éxito y se devuelve una lista de elementos o un elemento individual.
201 Created: Se ha creado o modificado un elemento correctamente.
400 Bad Request: La solicitud contiene datos inválidos o incompletos.
404 Not Found: No se ha encontrado la lista o el elemento solicitado.
500 Internal Server Error: Ha ocurrido un error interno en la inserción o modificación de un item. (TODO)

