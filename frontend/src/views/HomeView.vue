<script setup>
import WeatherDetailsGrid from '../components/WeatherDetailsGrid.vue'
import Sidebar from '../components/Sidebar.vue'
import WeekdaysTab from '../components/WeekdaysTab.vue';
</script>

<template>
    <main class="grid h-screen grid-cols-1">
        <!-- routerView -->
        <section class="grid gap-4 mx-8 grid-rows-[auto_auto_auto_1fr]">
            <div class="header">
                <form class="header__search">
                    <label for="search" class="header__label">Rechercher une ville</label>
                    <input id="search" type="text" class="header__input" placeholder="Rechercher une ville...">
                    <button class="header__search-btn">
                    <img src="" alt="Rechercher">
                    </button>
                </form>

                <button class="header__profile-btn">
                    <img class="header__profile-icon" src="" alt="Profil">
                </button>
            </div>

            <section class="filters">
                <!-- Component -->
                <h2 class="filters__title">Paris</h2>
                <div class="filters__options">
                    <input type="checkbox" class="filters__checkbox">
                    <label for="" class="filters__label">UV</label>

                    <input type="checkbox" class="filters__checkbox">
                    <label for="" class="filters__label">Humidité</label>

                    <input type="checkbox" class="filters__checkbox">
                    <label for="" class="filters__label">Vent</label>

                    <input type="checkbox" class="filters__checkbox">
                    <label for="" class="filters__label">Visibilité</label>

                    <input type="checkbox" class="filters__checkbox">
                    <label for="" class="filters__label">Lever/soleil</label>
                </div>
                <button class="filters__save">Enregistrer</button>
                <button class="filters__toggle">Filtre</button>
            </section>

            <WeekdaysTab />

            <section class="weather-info">
                <!-- Component WeatherDetailsGrid.vue -->
                <div>
                    <div class="weather-info__summary">
                        <h2 class="weather-info__temp">28°C</h2>
                        <p class="weather-info__feels">Ressenti: 23°C</p>
                        <img class="weather-info__icon" src="" alt="">
                        <p class="weather-info__update">Dernière mise à jour: 19h</p>
                    </div>
                    <div class="weather-info__periods">
                        <div class="weather-info__period">
                            <h3 class="weather-info__label">Matin 14°C</h3>
                            <img src="" alt="">
                        </div>
                        <div class="weather-info__period">
                            <h3 class="weather-info__label">Midi 14°C</h3>
                            <img src="" alt="">
                        </div>
                        <div class="weather-info__period">
                            <h3 class="weather-info__label">Soir 14°C</h3>
                            <img src="" alt="">
                        </div>
                    </div>
                </div>
                <div class="weather-info__details">
                    <div class="weather-info__param">
                        <h3 class="weather-info__title">Coucher du soleil 21h50</h3>
                        <p class="weather-info__text">Lever : 6h20</p>
                    </div>
                    <div class="weather-info__param">
                        <h3 class="weather-info__title">5km/h</h3>
                    </div>
                    <div>

                    </div>
                    <div class="weather-info__param flex-1 w-full">
                        <WeatherDetailsGrid />
                    </div>
                </div>
            </section>
        </section>
    </main>
</template>

