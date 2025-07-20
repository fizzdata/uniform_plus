<script setup>
import { ref } from "vue";
import { Dialog, DialogPanel } from "@headlessui/vue";
import IconXMark from "../icons/IconXMark.vue";
import IconBars3 from "../icons/IconBars3.vue";
const shopName = ref(localStorage.getItem("shop_name") || null);

const mobileMenuOpen = ref(false);
const navigation = [
  { name: "Dashboard", href: "/dashboard" },
  { name: "Purchase Orders", href: "/purchase-orders" },
  { name: "Orders", href: "/orders" },
  { name: "Statuses", href: "/status" },
  { name: "Inventory", href: "/inventory" },
  { name: "Exchange", href: "/exchange" },
];
</script>

<template>
  <header class="bg-white">
    <nav
      class="flex items-center justify-between py-4 lg:py-6"
      aria-label="Global"
    >
      <div class="flex lg:flex-1">
        <div class="flex">
          <router-link
            v-if="shopName"
            :to="`/?shop=${shopName}`"
            class="flex-shrink-0 flex items-center"
          >
            <span class="text-xl font-bold text-gray-800">Uniform Plus</span>
          </router-link>
          <router-link
            to="/install"
            v-else
            class="flex-shrink-0 flex items-center"
          >
            <span class="text-xl font-bold text-gray-800">Uniform Plus</span>
          </router-link>
        </div>
      </div>
      <div class="flex lg:hidden">
        <button
          type="button"
          class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
          @click="mobileMenuOpen = true"
        >
          <span class="sr-only">Open main menu</span>
          <IconBars3 class="size-6" aria-hidden="true" />
        </button>
      </div>
      <div class="hidden lg:flex lg:gap-x-10">
        <router-link
          v-for="item in navigation"
          :key="item.name"
          :to="item.href"
          class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
          active-class="border-indigo-500 text-gray-900"
          >{{ item.name }}
        </router-link>
      </div>
    </nav>
    <Dialog
      class="lg:hidden"
      @close="mobileMenuOpen = false"
      :open="mobileMenuOpen"
    >
      <div class="fixed inset-0 z-50" />
      <DialogPanel
        class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white p-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10"
      >
        <div class="flex items-center justify-between">
          <div class="flex">
            <router-link
              v-if="shopName"
              :to="`/?shop=${shopName}`"
              class="flex-shrink-0 flex items-center"
            >
              <span class="text-xl font-bold text-gray-800">Uniform Plus</span>
            </router-link>
            <router-link
              v-else
              to="/install"
              class="flex-shrink-0 flex items-center"
            >
              <span class="text-xl font-bold text-gray-800">Uniform Plus</span>
            </router-link>
          </div>
          <button
            type="button"
            class="-m-2.5 rounded-md p-2.5 text-gray-700"
            @click="mobileMenuOpen = false"
          >
            <span class="sr-only">Close menu</span>
            <IconXMark class="size-6" aria-hidden="true" />
          </button>
        </div>
        <div class="mt-6 flow-root">
          <div class="-my-6 divide-y divide-gray-500/10">
            <div class="space-y-2 py-6">
              <router-link
                v-for="item in navigation"
                :key="item.name"
                :to="item.href"
                @click="mobileMenuOpen = false"
                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50"
                >{{ item.name }}
              </router-link>
            </div>
          </div>
        </div>
      </DialogPanel>
    </Dialog>
  </header>
</template>
