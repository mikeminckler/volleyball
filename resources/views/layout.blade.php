<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Volleyball</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>

<body>

    <div id="app">

        <transition name="slide-down">
            <div class="header" v-if="$store.state.user.authenticated">

                <div class="home-link">
                    <router-link to="/home" class="button">Home</router-link>
                </div>
                
                <app-menu></app-menu>
        
                <div class="right-menu">
                    <div class="menu-item"><router-link to="/my-account">@{{ $store.getters.user_name }}</div>
                    <form id="logout-form" action="/api/logout" method="POST" @submit.prevent="logout">
                        <button class="logout">Logout</button>
                    </form>
                </div>
            </div>
        </transition>
        
        <feedback></feedback>

        <div class="container">
            <transition name="fade" mode="out-in">
                <router-view :key="$route.fullPath"></router-view>
            </transition>
        </div>

        <div class="footer">
            &copy; Mike Minckler <a href="http://minckler.ca">minckler.ca</a>
        </div>
    </div>

    <div id="loading" class="spinner"></div>

    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="/js/app.js"></script>

</body>

</html>
