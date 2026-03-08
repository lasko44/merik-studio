<script setup lang="ts">
import { ref, computed } from 'vue';

interface Review {
    id: number;
    author: string;
    rating: number;
    content: string;
    date: string;
    verified?: boolean;
}

interface Props {
    title?: string;
    allowSubmissions?: boolean;
    showRatingBreakdown?: boolean;
    reviewsPerPage?: number;
    reviews?: Review[];
    product?: {
        id: number;
        name: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Customer Reviews',
    allowSubmissions: true,
    showRatingBreakdown: true,
    reviewsPerPage: 5,
    reviews: () => [],
});

const currentPage = ref(1);
const showReviewForm = ref(false);
const newReview = ref({
    author: '',
    email: '',
    rating: 5,
    content: '',
});
const isSubmitting = ref(false);
const submitMessage = ref<{ type: 'success' | 'error'; text: string } | null>(null);

const averageRating = computed(() => {
    if (props.reviews.length === 0) return 0;
    const sum = props.reviews.reduce((acc, review) => acc + review.rating, 0);
    return (sum / props.reviews.length).toFixed(1);
});

const ratingBreakdown = computed(() => {
    const breakdown = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
    props.reviews.forEach(review => {
        if (review.rating >= 1 && review.rating <= 5) {
            breakdown[review.rating as keyof typeof breakdown]++;
        }
    });
    return breakdown;
});

const paginatedReviews = computed(() => {
    const start = (currentPage.value - 1) * props.reviewsPerPage;
    const end = start + props.reviewsPerPage;
    return props.reviews.slice(start, end);
});

const totalPages = computed(() => Math.ceil(props.reviews.length / props.reviewsPerPage));

const getRatingPercentage = (rating: number): number => {
    if (props.reviews.length === 0) return 0;
    return (ratingBreakdown.value[rating as keyof typeof ratingBreakdown.value] / props.reviews.length) * 100;
};

const setRating = (rating: number) => {
    newReview.value.rating = rating;
};

const submitReview = async () => {
    if (!newReview.value.author || !newReview.value.content) {
        submitMessage.value = { type: 'error', text: 'Please fill in all required fields.' };
        return;
    }

    isSubmitting.value = true;
    submitMessage.value = null;

    // Simulate API call - in production, this would be a real endpoint
    await new Promise(resolve => setTimeout(resolve, 1000));

    submitMessage.value = { type: 'success', text: 'Thank you for your review! It will be visible after moderation.' };
    newReview.value = { author: '', email: '', rating: 5, content: '' };
    showReviewForm.value = false;
    isSubmitting.value = false;
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <section class="product-reviews">
        <h2 v-if="title" class="text-2xl font-bold text-gray-900 mb-6">
            {{ title }}
        </h2>

        <!-- Summary -->
        <div v-if="reviews.length > 0" class="flex flex-col lg:flex-row gap-8 mb-8">
            <!-- Average Rating -->
            <div class="text-center lg:text-left">
                <div class="text-5xl font-bold text-gray-900">{{ averageRating }}</div>
                <div class="flex items-center justify-center lg:justify-start gap-1 mt-2">
                    <svg
                        v-for="i in 5"
                        :key="i"
                        :class="[
                            'w-5 h-5',
                            i <= Math.round(Number(averageRating)) ? 'text-yellow-400' : 'text-gray-300'
                        ]"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 mt-1">Based on {{ reviews.length }} reviews</p>
            </div>

            <!-- Rating Breakdown -->
            <div v-if="showRatingBreakdown" class="flex-1 space-y-2">
                <div v-for="rating in [5, 4, 3, 2, 1]" :key="rating" class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 w-8">{{ rating }} star</span>
                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-yellow-400 rounded-full transition-all duration-300"
                            :style="{ width: `${getRatingPercentage(rating)}%` }"
                        />
                    </div>
                    <span class="text-sm text-gray-500 w-8">{{ ratingBreakdown[rating as keyof typeof ratingBreakdown] }}</span>
                </div>
            </div>
        </div>

        <!-- Write Review Button -->
        <div v-if="allowSubmissions && !showReviewForm" class="mb-8">
            <button
                @click="showReviewForm = true"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Write a Review
            </button>
        </div>

        <!-- Review Form -->
        <div v-if="showReviewForm && allowSubmissions" class="bg-gray-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Write Your Review</h3>
            <form @submit.prevent="submitReview" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="review-author" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input
                            id="review-author"
                            v-model="newReview.author"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        />
                    </div>
                    <div>
                        <label for="review-email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            id="review-email"
                            v-model="newReview.email"
                            type="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating *</label>
                    <div class="flex gap-1">
                        <button
                            v-for="i in 5"
                            :key="i"
                            type="button"
                            @click="setRating(i)"
                            class="p-1 focus:outline-none"
                        >
                            <svg
                                :class="[
                                    'w-8 h-8 transition-colors',
                                    i <= newReview.rating ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-200'
                                ]"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="review-content" class="block text-sm font-medium text-gray-700">Your Review *</label>
                    <textarea
                        id="review-content"
                        v-model="newReview.content"
                        rows="4"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        placeholder="Share your experience with this product..."
                    ></textarea>
                </div>

                <div class="flex gap-3">
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        {{ isSubmitting ? 'Submitting...' : 'Submit Review' }}
                    </button>
                    <button
                        type="button"
                        @click="showReviewForm = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Submit Message -->
        <div
            v-if="submitMessage"
            :class="[
                'p-4 rounded-lg mb-6',
                submitMessage.type === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
            ]"
        >
            {{ submitMessage.text }}
        </div>

        <!-- Reviews List -->
        <div v-if="paginatedReviews.length > 0" class="space-y-6">
            <div
                v-for="review in paginatedReviews"
                :key="review.id"
                class="border-b border-gray-200 pb-6 last:border-0"
            >
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-900">{{ review.author }}</span>
                            <span
                                v-if="review.verified"
                                class="inline-flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full"
                            >
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Verified
                            </span>
                        </div>
                        <div class="flex items-center gap-1 mt-1">
                            <svg
                                v-for="i in 5"
                                :key="i"
                                :class="['w-4 h-4', i <= review.rating ? 'text-yellow-400' : 'text-gray-300']"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ formatDate(review.date) }}</span>
                </div>
                <p class="text-gray-700">{{ review.content }}</p>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">No reviews yet. Be the first to review!</p>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center gap-2 mt-8">
            <button
                v-for="page in totalPages"
                :key="page"
                @click="currentPage = page"
                :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                    currentPage === page
                        ? 'bg-gray-900 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
            >
                {{ page }}
            </button>
        </div>
    </section>
</template>
