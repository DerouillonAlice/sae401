<script setup>
import WeatherDetailsGrid from '../components/WeatherDetailsGrid.vue'

</script>

<template>
    <main>
        <section>
            <div>
                <h1>Paris</h1>
            </div>
            <div>
                <h1>Troyes</h1>
            </div>
            <div>
                <h1>Reims</h1>
            </div>
            <div>
                <h1>Connectez-vous</h1>
            </div>
        </section>

        <section>
            <nav>
                <form action="">
                    <label for="">Rechercher une ville</label>
                    <input type="text" placeholder="Rechercher une ville...">
                    <button><img src="" alt=""></button>
                </form>
                <button>
                    <img src="" alt="">
                </button>
            </nav>
            <section>
                <h1>Paris</h1>
                <div>
                    <input type="checkbox">
                    <label for="">UV</label>

                    <input type="checkbox">
                    <label for="">Humidité</label>

                    <input type="checkbox">
                    <label for="">Vent</label>

                    <input type="checkbox">
                    <label for="">Visibilité</label>

                    <input type="checkbox">
                    <label for="">Lever/soleil</label>
                </div>
                <button>Enregistrer</button>
                <button>Filtre</button>
            </section>

            <section>
                <button>Lun</button>
                <button>Mar</button>
                <button>Mer</button>
                <button>Jeu</button>
                <button>Ven</button>
                <button>Sam</button>
                <button>Dim</button>
            </section>

            <section>
                <div>
                    <div>
                        <h2>28°C</h2>
                        <p>Ressenti: 23°C</p>
                        <img src="" alt="">
                        <p>Dernière mise à jour: 19h</p>
                    </div>
                    <div>
                        <div>
                            <h3>Matin 14°C</h3>
                            <img src="" alt="">
                        </div>
                        <div>
                            <h3>Midi 14°C</h3>
                            <img src="" alt="">
                        </div>
                        <div>
                            <h3>Soir 14°C</h3>
                            <img src="" alt="">
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <h3>Coucher du soleil 21h50</h3>
                    </div>
                    <div>
                        <h3>5km/h</h3>
                    </div>
                    <div>

                    </div>
                    <div class="flex-1 lg:w-2/4">
                        <WeatherDetailsGrid />
                    </div>
                </div>
            </section>
        </section>
    </main>
</template>

<style scoped>
main {
  display: grid;
  grid-template-columns: 250px 1fr;
  height: 100vh;
}

/* Sidebar */
main > section:first-of-type {
  background-color: #f4f4f4;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Contenu principal */
main > section:last-of-type {
  padding: 1rem;
  display: grid;
  grid-template-rows: auto auto auto 1fr;
  gap: 1rem;
}

@media (max-width: 768px) {
  main {
    grid-template-columns: 1fr;
  }

  main > section:first-of-type {
    flex-direction: row;
    overflow-x: auto;
  }
}

</style>