<template>
  <DialogRoot :open="isOpen" @close="onClose">
    <div class="fixed inset-0 bg-black/30" aria-hidden="true" />
    <div class="fixed inset-0 flex items-center justify-center p-4">
      <DialogPanel class="bg-white rounded-lg p-6 max-w-md w-full">
        <div class="inline-flex justify-between w-full">
          <DialogTitle class="!text-xl font-bold text-gray-900">
            {{ title }}
          </DialogTitle>
          <IconClose class="cursor-pointer" @click="onClose" />
        </div>
        <div v-if="description" class="mt-2">
          <DialogDescription>
            {{ description }}
          </DialogDescription>
        </div>

        <div class="border-t border-gray-200 my-3 w-full" />
        <div class="mt-4">
          <slot>
            <p class="text-sm text-gray-500">
              {{ body }}
            </p>
          </slot>
        </div>

        <div class="border-t border-gray-200 my-3 w-full" />
        <div class="flex justify-end space-x-3">
          <slot name="actions" :close="onClose">
            <button
              type="button"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              @click="onClose"
            >
              Cancel
            </button>
            <button
              type="button"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              @click="onConfirm"
            >
              Confirm
            </button>
          </slot>
        </div>
      </DialogPanel>
    </div>
  </DialogRoot>
</template>

<script setup>
import { ref, watch } from "vue";
import {
  Dialog as DialogRoot,
  DialogPanel,
  DialogTitle,
  DialogDescription,
} from "@headlessui/vue";
import IconClose from "@/components/icons/IconClose.vue";

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    default: "Dialog Title",
  },
  description: {
    type: String,
    default: "",
  },
  body: {
    type: String,
    default: "",
  },
});

const emit = defineEmits(["update:modelValue", "confirm", "close"]);

const isOpen = ref(props.modelValue);

const onClose = () => {
  isOpen.value = false;
  emit("update:modelValue", false);
  emit("close");
};

const onConfirm = () => {
  emit("confirm");
  onClose();
};

// Sync with v-model
watch(
  () => props.modelValue,
  (newValue) => {
    isOpen.value = newValue;
  }
);
</script>
