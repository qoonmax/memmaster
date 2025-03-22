import { defineStore } from 'pinia'
import axios from "axios";
import {useToast} from "vue-toastification";
import {useTagsStore} from "@/Stores/TagsStore";
import {Inertia} from "@inertiajs/inertia";

export const useCardsStore = defineStore('cards', {
    state: () => ({
        currentCard: {} as any,
        cards: [] as any[],
        search: '',
        filters: {
            all: false,
        } as Record<string, boolean>,
        isLoading: false,
    }),
    actions: {
        async getAllCard() {
            try {
                this.isLoading = true;
                const response = await axios.get(route('api.cards.index', {
                    tags: this.selectedTagIds(),
                    filters: {
                        all: true,
                    },
                }));
                this.cards = response.data;
                this.isLoading = false;
            } catch (error: any) {
                useToast().error(error?.response?.data?.message ||  'Error fetching cards');
            }
        },

        async getCardForRepetition() {
            try {
                this.isLoading = true;
                const response = await axios.get(route('api.cards.index', {
                    tags: this.selectedTagIds(),
                    filters: {
                        all: false,
                    },
                }));
                this.cards = response.data;
                this.isLoading = false;
            } catch (error: any) {
                useToast().error(error?.response?.data?.message ||  'Error fetching cards');
            }
        },



        async fetchCard(slug: string) {
            this.isLoading = true;

            try {
                const response = await axios.get(route('api.cards.show', { slug }));
                this.currentCard = response.data;
                return this.currentCard;
            } catch (error: any) {
                useToast().error(error?.response?.data?.message || 'Error fetching card');
                return null;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Работа с состоянием карточек
         */
        updateCardBySlug(slug: string, card: any) {
            const index = this.cards.findIndex(card => card.slug === slug);
            this.cards[index] = card;
        },
        addCard(card: any) {
            this.cards.unshift(card);
        },
        deleteCardBySlug(slug: string) {
            const index = this.cards.findIndex(card => card.slug === slug);
            this.cards.splice(index, 1);
        },

        /**
         * Работа с фильтрами
         */
        async filterToggle(filter: string) {
            this.filters[filter] = !this.filters[filter];

            if (route().current('pages.cards.repetition')) {
                await this.getCardForRepetition()
            } else {
                await this.getAllCard()
            }
        },

        /**
         * Работа с карточками
         */
        async repeatCard(slug: string) {
            try {
                const response = await axios.post(`/api/cards/${slug}/repeat`);

                if (this.getFilter('all')) {
                    this.updateCardBySlug(slug, response.data.card);
                } else {
                    useCardsStore().deleteCardBySlug(slug);
                }
                useToast().info(response.data.message);
                Inertia.reload({
                    only: ['card_counter'],
                    preserveScroll: true,
                    preserveState: true,
                });
            } catch (error: any) {
                useToast().error(error?.response?.data?.message || 'Failed to replicate the card');
            }
        },
        async skipCard(slug: string) {
            try {
                const response = await axios.post(`/api/cards/${slug}/skip`);

                if (this.getFilter('all') === false) {
                    useCardsStore().deleteCardBySlug(slug);
                }
                useToast().info(response.data.message);
                Inertia.reload({
                    only: ['card_counter'],
                    preserveScroll: true,
                    preserveState: true,
                });
            } catch (error: any) {
                useToast().error(error?.response?.data?.message || 'Failed to swipe the card');
            }
        }
    },
    getters: {
        getCurrentCard: (state) => () => {
            return state.currentCard;
        },
        selectedTagIds: (state) => () => {
            return useTagsStore().selectedTags.map((tag: any) => tag.id).join(',')
        },
        getFilter: (state) => (filter: string) => {
            return state.filters[filter];
        },
        existsCard: (state) => (slug: string) => {
            return state.cards.some(card => card.slug === slug);
        },
    }
})
