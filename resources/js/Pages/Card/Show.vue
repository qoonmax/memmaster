<script setup lang="ts">
import {ref, onMounted, onBeforeUnmount, onUpdated, computed, onBeforeMount, watch} from "vue";
import axios from "axios";
import {useCardsStore} from "@/Stores/CardsStore";
import {useToast} from "vue-toastification";
import Editor from "primevue/editor";
import {Inertia} from "@inertiajs/inertia";
import {Head, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {PageProps} from "@/types";

const props = defineProps({
    slug: String as any,
});

const currentCard = ref({
    slug: props.slug,
    stage: 0,
    content: '',
    next_repeat_at: '',
    tags: '',
});
const cardIsLoading = ref(false);
const cardCounterText = computed(function () {
    const cardCounter = (usePage().props as unknown as PageProps).card_counter as number;

    if (cardCounter > 0) {
        return '(' + cardCounter + ') • ';
    }
    return '';
});

// Загружаем карточку при монтировании компонента
let onload: any = null;
onBeforeMount(async () => {
    if (currentCard.value.slug !== 'new') {
        await useCardsStore().fetchCard(currentCard.value.slug);
    }

    onload = ({instance}: any) => {
        instance.setContents(instance.clipboard.convert({
            html: currentCard.value.content
        }))
    };

    if (currentCard.value.slug !== 'new') {
        currentCard.value = useCardsStore().currentCard;
    }
    cardIsLoading.value = true;

    // Загружаем слушатели на клавиатуру
    window.addEventListener("keydown", handleKeydown);
    window.addEventListener('beforeunload', async function (event) {
        await saveCard();
    });
});

// Сохранение контента карточки при изменении
let timeout: any = null;
let isInitialized = false;

watch(
    () => currentCard.value.content,
    async () => {
        // Чтобы при открытии карточки не отправлялся запрос на сохранение
        if (!isInitialized) {
            isInitialized = true;
            return;
        }

        if (timeout) {
            clearTimeout(timeout);
        }

        timeout = setTimeout(async () => {
            await saveCard();
        }, 1500);
    }
);

// Уйти со страницы
const leave = async () => {
    if (currentCard.value.content !== '') {
        await saveCard();
    }

    Inertia.visit(route('pages.cards.index'));
};

// Закрытие модального окна по нажатию клавиши "Esc"
const handleKeydown = async (event: KeyboardEvent) => {
    if ((event.metaKey && event.key === 's')) {
        await saveCard();
    }
    if (event.key === "Escape" || (event.metaKey && event.key === 'k')) {
        await leave();
    }
};

onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleKeydown);
    window.removeEventListener('beforeunload', async function (event) {
        await saveCard();
    });
});

const saveCard = async () => {
    // Если карточка пустая, то не отправляем запрос на сохранение
    if (currentCard.value.content === '') {
        return;
    }

    // Если карточка новая, то отправляем запрос на создание
    let slug = currentCard.value.slug !== '' ? currentCard.value.slug : 'new';
    try {
        const response = await axios.post(route('api.cards.update', slug), {
            content: currentCard.value.content,
            tags: currentCard.value.tags,
        });

        currentCard.value = response.data.card;
        useToast().info(response.data.message);
    } catch (error : any) {
        useToast().error(error?.response?.data?.message || 'An error occurred while saving the card');
    }
};

const isLoadingGenerationTags = ref(false);
const tagsInputPlaceholder = computed(() => {
    return isLoadingGenerationTags.value ? 'generating tags...' : 'enter tags...';
});

const generateTags = async () => {
    try {
        // Проверка будет выполнена только после того, как editor.save() завершится
        if (currentCard.value.tags !== '' || currentCard.value.content === '') {
            return;
        }

        isLoadingGenerationTags.value = true;
        // await editor.readOnly.toggle(true);

        const response = await axios.post(route('api.ai-helper.tags.generate'), {
            content: currentCard.value.content,
        });

        if (currentCard.value.tags !== '') {
            return;
        }
        currentCard.value.tags = response.data.tags;

        // await editor.readOnly.toggle(false);
        isLoadingGenerationTags.value = false;
        useToast().info(response.data.message);
    } catch (error: any) {
        // await editor.readOnly.toggle(false);
        isLoadingGenerationTags.value = false;

        if (currentCard.value.tags !== '') {
            return;
        }

        useToast().error(error?.response?.data?.message || 'An error occurred while generating tags');
    }
};

// Обработка ввода тегов
let typingTagsTimer: any = null;
const handleInputTags = () => {
    if (typingTagsTimer) {
        clearTimeout(typingTagsTimer);
    }

    typingTagsTimer = window.setTimeout(() => {
        saveCard();
    }, 1500);
};

// Показывать кнопку "сразу в повторение"
const showRepeatImmediatelyButton = computed(() => {
    if (currentCard.value.slug === '' || currentCard.value.next_repeat_at === '') {
        return false;
    }

    const today = new Date();
    const nextRepeatDate = new Date(currentCard.value.next_repeat_at);

    // Проверяем, если текущая дата больше, чем дата следующего повторения
    return today < nextRepeatDate;
});

// Отправить карточку на повторение сразу
const repeatImmediately = () => {
    if (currentCard.value.slug === '') {
        useToast().error('The card hasn\'t been saved yet');
        return;
    }

    axios.post(route('api.cards.repeat-immediately', currentCard.value.slug))
        .then(response => {
            currentCard.value = response.data.card;
            useToast().info(response.data.message);
        })
        .catch(error => {
            useToast().error(error?.response?.data?.message || 'An error occurred when changing the card status');
        });
};

const deleteCard = async () => {
    if (currentCard.value.slug === 'new') {
        useToast().error('Card not saved');
        return;
    }

    if (!confirm('Are you sure you want to delete the card?')) {
        return;
    }

    try {
        const response = await axios.delete(route('api.cards.delete', currentCard.value.slug));

        if (response.status === 200) {
            useToast().info('Card deleted');
            currentCard.value = {
                slug: '',
                stage: 0,
                content: '',
                next_repeat_at: '',
                tags: '',
            };
            Inertia.visit(route('pages.cards.index')); // Переход после успешного удаления
        }
    } catch (error : any) {
        useToast().error(error?.response?.data?.message || 'Error occurred while deleting the card.');
    }
};

const countSymbols = computed(() => {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = currentCard.value.content;
    const textContent = tempDiv.textContent || tempDiv.innerText || "";
    return textContent.trim().length;
});

const isLimitCountSymbolsExceeded = computed(() => {
    return countSymbols.value > 768;
});

</script>

<template>
    <Head :title="cardCounterText + 'Edit card'" />

    <AuthenticatedLayout>
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Затемненный фон, по клику на который также закрывается модальное окно -->
            <div class="fixed inset-0 bg-points transition-opacity" aria-hidden="true"></div>

            <div v-if="cardIsLoading" class="fixed inset-0 z-50 w-screen overflow-y-auto">
                <div class="md:flex md:justify-center min-h-full sm:items-center px-4 sm:px-6 lg:px-8 py-6">
                    <div class="sm:my-8 sm:w-full sm:max-w-2xl">
                        <span @click="leave" class="text-accent cursor-pointer"><span class="mr-2">&#10229;</span>go back</span>
                        <div class="mt-2 bg-dark text-white-gray border border-gray rounded-3xl">
                            <Editor v-model="currentCard.content" @load="onload" editorStyle="height: 300px" class="p-7" autofocus>
                                <template v-slot:toolbar>
                                <span>
                                    <select class="ql-header">
                                        <option value="1">Heading</option>
                                        <option value="2">Subheading</option>
                                        <option value="0">Normal</option>
                                    </select>
                                    <button v-tooltip.bottom="'Bold'" class="ql-bold"></button>
                                    <button v-tooltip.bottom="'Italic'" class="ql-italic"></button>
                                    <button v-tooltip.bottom="'Underline'" class="ql-underline"></button>
                                    <button v-tooltip.bottom="'Link'" class="ql-link"></button>
                                    <button class="ql-blockquote"></button>
                                    <button class="ql-code-block"></button>
                                    <select class="ql-background">
                                        <option value="#ec0606"></option>
                                        <option value="#e79206"></option>
                                        <option value="#008a00"></option>
                                    </select>
                                    <span :class="isLimitCountSymbolsExceeded ? 'text-red' : 'text-gray'" class="float-right text-sm">{{ countSymbols }}</span>
                                </span>
                                </template>
                            </Editor>
                        </div>
                        <div class="relative w-full mt-5">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-7 text-accent">#</span>
                            <input v-model="currentCard.tags" @focus="generateTags" @input="handleInputTags" :disabled="isLoadingGenerationTags" :placeholder="tagsInputPlaceholder" type="text" class="w-full rounded-2xl bg-dark border border-gray pl-12 text-white-gray placeholder-gray py-3 focus:outline-none">
                        </div>

                        <div class="bg-dark flex justify-between gap-4 border border-gray rounded-2xl mt-5 px-6 py-4">
<!--                            <button class="cursor-pointer w-fit text-left text-accent hover:underline hover:underline-offset-4 hover:decoration-wavy transition ease-in-out duration-150">-->
<!--                                generate test-->
<!--                            </button>-->
                            <button v-if="showRepeatImmediatelyButton" @click="repeatImmediately" class="cursor-pointer w-fit text-left text-gray hover:underline hover:underline-offset-4 hover:decoration-wavy">
                                repeat immediately
                            </button>
                            <button @click="deleteCard" :class="showRepeatImmediatelyButton ? 'text-right' : 'text-left'" class="justify-end cursor-pointer w-fit text-dark-red hover:underline hover:underline-offset-4 hover:decoration-wavy">
                                delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
