import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { updateFavoriteConfig } from '@/services/services'

export function useWeatherLayout(route, auth) {
  const allBlocks = ref([
    { i: '1', name: 'Vent', active: true },
    { i: '2', name: 'Cycle Soleil', active: true },
    { i: '3', name: 'Humidité', active: true },
    { i: '4', name: 'Visibilité', active: true },
    { i: '5', name: 'Nuages', active: true },
    { i: '6', name: 'Pression', active: true }
  ])

  const predefinedLayouts = {
    1: [{ i: '1', x: 0, y: 0, w: 3, h: 1 }],
    2: [
      { i: '1', x: 0, y: 0, w: 3, h: 1 }, 
      { i: '2', x: 0, y: 1, w: 3, h: 1 }
    ],
    3: [
      { i: '1', x: 0, y: 0, w: 1, h: 1 }, 
      { i: '2', x: 1, y: 0, w: 1, h: 1 }, 
      { i: '3', x: 2, y: 0, w: 1, h: 1 }
    ],
    4: [
      { i: '1', x: 0, y: 0, w: 1.5, h: 1 }, 
      { i: '2', x: 1.5, y: 0, w: 1.5, h: 1 }, 
      { i: '3', x: 0, y: 1, w: 1.5, h: 1 }, 
      { i: '4', x: 1.5, y: 1, w: 1.5, h: 1 }
    ],
    5: [
      { i: '1', x: 0, y: 0, w: 1, h: 1 }, 
      { i: '2', x: 1, y: 0, w: 1, h: 1 }, 
      { i: '3', x: 2, y: 0, w: 1, h: 1 }, 
      { i: '4', x: 0, y: 1, w: 1, h: 1 }, 
      { i: '5', x: 1, y: 1, w: 2, h: 1 }
    ],
    6: [
      { i: '1', x: 0, y: 0, w: 1, h: 1 }, 
      { i: '2', x: 1, y: 0, w: 1, h: 1 }, 
      { i: '3', x: 2, y: 0, w: 1, h: 1 }, 
      { i: '4', x: 0, y: 1, w: 1, h: 1 }, 
      { i: '5', x: 1, y: 1, w: 1, h: 1 }, 
      { i: '6', x: 2, y: 1, w: 1, h: 1 }
    ]
  }

  const layout = ref([])
  const colNum = ref(3)
  const containerWidth = ref(window.innerWidth || 1200)
  const currentFavorite = ref(null)
  const isLoadingConfig = ref(false)

  let isUpdating = false
  let updateTimeout
  let saveTimeout

  function getDefaultVille() {
    if (auth.isConnected && auth.favorites.length > 0) {
      return auth.favorites[0].city
    }
    return 'Paris'
  }

  function updateColNum() {
    const width = window.innerWidth
    colNum.value = width < 640 ? 1 : 3
  }

  function updateContainerWidth() {
    containerWidth.value = window.innerWidth
  }

  function generateDefaultLayout() {
    const activeBlocks = allBlocks.value.filter((b) => b.active)
    const isMobile = window.innerWidth < 640

    if (activeBlocks.length === 0) {
      layout.value = []
      return
    }

    if (isMobile) {
      layout.value = activeBlocks.map((block, index) => ({
        i: block.i,
        x: 0,
        y: index,
        w: 1,
        h: 1,
        name: block.name,
      }))
    } else {
      const config = predefinedLayouts[activeBlocks.length] || []
      layout.value = config.map((l, index) => ({
        ...l,
        name: activeBlocks[index]?.name || 'Bloc',
      }))
    }
  }

  function updateLayout(newLayout) {
    if (isUpdating || isLoadingConfig.value) return
    isUpdating = true
    clearTimeout(updateTimeout)

    updateTimeout = setTimeout(() => {
      const isMobile = window.innerWidth < 640

      if (isMobile) {
        const sortedLayout = newLayout.slice().sort((a, b) => a.y - b.y)
        layout.value = sortedLayout.map((item, index) => ({
          ...item,
          x: 0,
          y: index,
          w: 1,
          h: 1
        }))
      } else {
        layout.value = newLayout
      }

      if (!isLoadingConfig.value && currentFavorite.value) {
        saveFavoriteConfig()
      }

      isUpdating = false
    }, 100)
  }

  function loadFavoriteConfig() {
    const ville = route.query.ville || getDefaultVille()
    
    if (auth.isConnected) {
      isLoadingConfig.value = true
      
      currentFavorite.value = auth.favorites.find(fav => 
        fav.city.toLowerCase() === ville.toLowerCase()
      )
      
      if (currentFavorite.value) {
        // Ville dans les favoris : charger la configuration sauvegardée
        allBlocks.value.forEach(block => {
          switch(block.name) {
            case 'Vent': block.active = currentFavorite.value.showWind; break
            case 'Cycle Soleil': block.active = currentFavorite.value.showSunCycle; break
            case 'Humidité': block.active = currentFavorite.value.showHumidity; break
            case 'Visibilité': block.active = currentFavorite.value.showVisibility; break
            case 'Nuages': block.active = currentFavorite.value.showUV; break
            case 'Pression': block.active = currentFavorite.value.showPressure; break
          }
        })
        
        if (currentFavorite.value.gridLayout && currentFavorite.value.gridLayout.length > 0) {
          layout.value = [...currentFavorite.value.gridLayout]
        } else {
          generateDefaultLayout()
        }
      } else {
        // Ville PAS dans les favoris : tous les blocs décochés par défaut
        allBlocks.value.forEach(block => {
          block.active = false
        })
        layout.value = [] // Grille vide
      }
      
      nextTick(() => {
        setTimeout(() => {
          isLoadingConfig.value = false
        }, 200)
      })
    } else {
      // Utilisateur non connecté : tous les blocs actifs
      allBlocks.value.forEach(block => {
        block.active = true
      })
      layout.value = []
    }
  }

  async function saveFavoriteConfig() {
    if (!currentFavorite.value || isLoadingConfig.value) return
    
    clearTimeout(saveTimeout)
    saveTimeout = setTimeout(async () => {
      const config = {
        showWind: allBlocks.value.find(b => b.name === 'Vent')?.active || false,
        showSunCycle: allBlocks.value.find(b => b.name === 'Cycle Soleil')?.active || false,
        showHumidity: allBlocks.value.find(b => b.name === 'Humidité')?.active || false,
        showVisibility: allBlocks.value.find(b => b.name === 'Visibilité')?.active || false,
        showPressure: allBlocks.value.find(b => b.name === 'Pression')?.active || false,
        showUV: allBlocks.value.find(b => b.name === 'Nuages')?.active || false,
        gridLayout: layout.value
      }
      
      try {
        const result = await updateFavoriteConfig(currentFavorite.value.id, config)
        if (result.success) {
          const currentFavId = currentFavorite.value.id
          await auth.fetchFavorites()
          currentFavorite.value = auth.favorites.find(fav => fav.id === currentFavId)
        }
      } catch (error) {
        console.error('Erreur lors de la sauvegarde:', error)
      }
    }, 500)
  }

  // Watchers
  watch(() => allBlocks.value.map((b) => b.active), () => {
    if (!isLoadingConfig.value) {
      generateDefaultLayout()
    }
  }, { immediate: true })

  watch(() => allBlocks.value.map(b => b.active), () => {
    if (!isLoadingConfig.value && currentFavorite.value) {
      generateDefaultLayout()
      saveFavoriteConfig()
    }
  }, { deep: true })

  watch(() => route.query.ville, () => {
    loadFavoriteConfig()
  }, { immediate: true })

  watch(() => auth.favorites, (newFavorites, oldFavorites) => {
    if (newFavorites.length > 0 && (!oldFavorites || oldFavorites.length === 0)) {
      loadFavoriteConfig()
    }
  }, { immediate: true })

  // Lifecycle
  onMounted(() => {
    updateColNum()
    updateContainerWidth()
    window.addEventListener('resize', updateColNum)
    window.addEventListener('resize', updateContainerWidth)
  })

  onUnmounted(() => {
    window.removeEventListener('resize', updateColNum)
    window.removeEventListener('resize', updateContainerWidth)
  })

  return {
    allBlocks,
    layout,
    colNum,
    containerWidth,
    updateLayout,
    loadFavoriteConfig
  }
}