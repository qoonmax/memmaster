import { defineStore } from 'pinia'
import axios from "axios";
import {useToast} from "vue-toastification";
import {useCardsStore} from "@/Stores/CardsStore";

export const useTagsStore = defineStore('tags', {
    state: () => ({
        tags: [] as any[],
        selectedTags: [] as any[],
        isLoading: false
    }),
    actions: {
        async getTags() {
            try {
                this.isLoading = true;
                const response = await axios.get('/api/tags');
                this.tags = response.data;
                this.isLoading = false;
            } catch (error: any) {
                useToast().error(error?.response?.data?.message || 'Error fetching tags');
            }
        },
        async selectTag(tag: any) {
            if (this.selectedTags.includes(tag)) {
                this.selectedTags = this.selectedTags.filter((t) => t.id !== tag.id)
            } else {
                this.selectedTags.push(tag)
            }

            if (route().current('pages.cards.repetition')) {
                await useCardsStore().getCardForRepetition()
            } else {
                await useCardsStore().getAllCard()
            }
        }
    },
    getters: {
        tagIsSelected: (state) => (tag: any) => {
            return state.selectedTags.includes(tag)
        }
    }
})
