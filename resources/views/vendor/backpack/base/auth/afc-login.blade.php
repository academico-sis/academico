<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AF Cuenca - plataformas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tailwindcss/ui@latest/dist/tailwind-ui.min.css">
</head>
<body class="bg-gray-100">

    <div class="container bg-gray-100 mx-auto">
        <div class="grid grid-cols-1 gap-16 md:grid-cols-2 min-h-screen ">

            <div class="flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 mt-8 mb-8 rounded overflow-hidden shadow-lg">
                <div class="pt-6 max-w-md w-full">
                    <div>
                        <img class="mx-auto h-12 w-auto" src="{{ asset('afcuenca/af.png') }}" alt="Plataforma AFC" />
                        <h2 class="mt-6 text-center text-3xl leading-9 font-extrabold text-gray-900">
                            Estudiantes de la Alianza&nbsp;Francesa
                        </h2>
                        <div class="text-sm text-center mt-3 leading-5">
                            <p class="mt-3 mb-4">Si tomas clases dentro de la Alianza Francesa, usa esta sección</p>
                            <a href="/register" class="font-medium text-red-500 hover:text-red-300 focus:outline-none focus:underline transition ease-in-out duration-150">
                                Nuevo estudiante? Create una cuenta
                            </a>
                        </div>
                    </div>
                    <form class="mt-8" action="/login" name="internalstudent" onsubmit="DoSubmit();" method="POST">
                        @csrf
                        <div class="rounded-md shadow-sm">
                            <div>
                                <input aria-label="Correo electrónico" name="username" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Correo electrónico" />

                                <input type="hidden" name="email" />

                            </div>
                            <div class="-mt-px">
                                <input aria-label="Constraseña" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Constraseña" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember" type="checkbox" class="form-checkbox h-4 w-4 text-red-600 transition duration-150 ease-in-out" />
                                <label for="remember" name="remember" class="ml-2 block text-sm leading-5 text-gray-900">
                                    Recordar mis datos
                                </label>
                            </div>

                            <div class="text-sm leading-5">
                                <a href="/password/reset" class="font-medium text-red-600 hover:text-red-500 focus:outline-none focus:underline transition ease-in-out duration-150">
                                    Olvidasté tu constraseña?
                                </a>
                            </div>
                        </div>

                        <p class="mt-20 mb-8 block text-md leading-5 text-gray-900 text-center font-medium">Acceder a:</p>
                        <div class="mt-6 flex">
                            <div class="w-1/2 pr-2">
                                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-700 transition duration-150 ease-in-out" formaction="">
                                    Plataforma academica
                                </button>
                                <p class="ml-2 block text-sm leading-5 text-gray-900 text-center">para ver tus notas y matriculas</p>
                            </div>

                            <div class="w-1/2 pl-2">
                                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-700 transition duration-150 ease-in-out" formaction="https://moodle.afcuenca.org.ec/login/index.php">
                                    Plataforma SPHERE
                                </button>
                                <p class="ml-2 block text-sm leading-5 text-gray-900 text-center">para acceder a tus cursos en linea</p>
                            </div>

                        </div>



                    </form>
                </div>


            </div>

            <div class="flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 mt-8 mb-8 rounded overflow-hidden shadow-lg">
                <div class="max-w-md w-full">
                    <div>
                        <img class="mx-auto h-12 w-auto" src="{{ asset('afcuenca/sphere.png') }}" alt="Plataforma SPHERE" />
                        <h2 class="mt-6 text-center text-3xl leading-9 font-extrabold text-gray-900">
                            Estudiantes externos
                        </h2>
                        <div class="text-sm text-center mt-3 leading-5">
                            <p class="mt-3 mb-8">Si tomas clases dentro de tu universidad/institución educativa, usa esta sección.</p>
                            <a href="https://moodle.afcuenca.org.ec/login/signup.php" class="font-medium text-indigo-500 hover:text-indigo-300 focus:outline-none focus:underline transition ease-in-out duration-150">
                                Crear una cuenta
                            </a>
                        </div>
                    </div>
                    <form class="mt-12" action="https://moodle.afcuenca.org.ec/login/index.php" method="POST">
                        <div class="rounded-md shadow-sm">
                            <div>
                                <input aria-label="Nombre de usuario / correo electrónico" name="username" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Nombre de usuario / correo electrónico" />
                            </div>
                            <div class="-mt-px">
                                <input aria-label="Constraseña" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Constraseña" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="rememberusername" name="rememberusername" type="checkbox" class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out" />
                                <label for="rememberusername" class="ml-2 block text-sm leading-5 text-gray-900">
                                    Recordar mis datos
                                </label>
                            </div>

                            <div class="text-sm leading-5">
                                <a href="https://moodle.afcuenca.org.ec/login/forgot_password.php" class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">
                                    Olvidasté tu constraseña?
                                </a>
                            </div>
                        </div>

                        <div class="mt-16">
                            <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-red active:bg-indigo-700 transition duration-150 ease-in-out">
                                Acceder a la plataforma SPHERE
                            </button>
                            <p class="ml-2 block text-sm leading-5 text-gray-900 text-center">para ver tus cursos en linea</p>
                        </div>
                    </form>
                </div>


            </div>
        </div>
    </div>
</body>

<script>
function DoSubmit(){
  document.internalstudent.email.value = document.internalstudent.username.value;
  return true;
}

</script>
</html>
