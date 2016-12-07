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

                <app-menu></app-menu>
        
                <div class="right-menu">
                    <div class="menu-item"  v-if="$store.state.activeTeam.id">
                        <router-link to="/my-account" class="fa-user-circle fa icon"></router-link>
                    </div>
                    <div class="menu-item"  v-if="$store.state.activeTeam.id && $store.state.user.roles.length > 1">
                        <router-link to="/select-team" class="fa-users fa icon"></router-link>
                    </div>
                    <div class="menu-item">
                        <form id="logout-form" action="/api/logout" method="POST" @submit.prevent="logout">
                            <button class="logout">Logout</button>
                        </form>
                    </div>
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
