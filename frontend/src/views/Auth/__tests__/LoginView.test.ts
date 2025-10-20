import { describe, it, expect, vi, beforeEach } from "vitest";
import { mount } from "@vue/test-utils";
import { createPinia, setActivePinia } from "pinia";
import PrimeVue from 'primevue/config';
import LoginView from "../LoginView.vue";

// Mock router
const mockPush = vi.fn();
vi.mock("vue-router", () => ({
  useRouter: () => ({
    push: mockPush,
  }),
  RouterLink: {
    template: '<a href="#"><slot></slot></a>',
  },
}));

describe("LoginView", () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    mockPush.mockClear();
  });

  const mountWithPrimeVue = (options = {}) => {
    return mount(LoginView, {
      global: {
        plugins: [PrimeVue],
      },
      ...options,
    });
  };

  it("should render login form", () => {
    const wrapper = mountWithPrimeVue();

    expect(wrapper.find("form").exists()).toBe(true);
    expect(wrapper.find('input[type="email"]').exists()).toBe(true);
    expect(wrapper.find('input[type="password"]').exists()).toBe(true);
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
  });

  it("should have email and password fields", () => {
    const wrapper = mountWithPrimeVue();

    const emailInput = wrapper.find('input[type="email"]');
    const passwordInput = wrapper.find('input[type="password"]');

    expect(emailInput.exists()).toBe(true);
    expect(passwordInput.exists()).toBe(true);
  });

  it("should update form data when typing", async () => {
    const wrapper = mountWithPrimeVue();

    const emailInput = wrapper.find('input[type="email"]');
    const passwordInput = wrapper.find('input[type="password"]');

    await emailInput.setValue("test@example.com");
    await passwordInput.setValue("password123");

    expect((emailInput.element as HTMLInputElement).value).toBe(
      "test@example.com"
    );
    expect((passwordInput.element as HTMLInputElement).value).toBe(
      "password123"
    );
  });

  it("should show validation errors for empty fields", async () => {
    const wrapper = mountWithPrimeVue();

    const form = wrapper.find("form");
    await form.trigger("submit.prevent");

    // Aguarda próximo tick para validações processarem
    await wrapper.vm.$nextTick();

    expect(wrapper.text()).toContain("obrigatório");
  });

  it("should show validation error for invalid email", async () => {
    const wrapper = mountWithPrimeVue();

    const emailInput = wrapper.find('input[type="email"]');
    await emailInput.setValue("invalid-email");

    const form = wrapper.find("form");
    await form.trigger("submit.prevent");
    await wrapper.vm.$nextTick();

    expect(wrapper.text()).toContain("válido");
  });

  it("should disable submit button while loading", async () => {
    const wrapper = mountWithPrimeVue();

    // Access component data using wrapper methods
    await wrapper.setData({ loading: true });
    await wrapper.vm.$nextTick();

    const submitButton = wrapper.find('button[type="submit"]');
    expect((submitButton.element as HTMLButtonElement).disabled).toBe(true);
  });
});
