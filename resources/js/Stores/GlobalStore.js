import { defineStore } from 'pinia';
import { ref } from 'vue'

export const useGlobalStore = defineStore('global', () => {
    const showUndo = ref(false);
    const selectedPlayers = ref([]);

    return {
        showUndo,
        selectedPlayers,
    }
})
