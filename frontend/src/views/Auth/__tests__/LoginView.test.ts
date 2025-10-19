import { describe, it, expect, vi, beforeEach } from "vitest";
import { mount } from "@vue/test-utils";
import { createPinia, setActivePinia } from "pinia";
import LoginView from "../LoginView.vue";

// Mock router
const mockPush = vi.fn();
vi.mock("vue-router", () => ({
  useRouter: () => ({
    push: mockPush,
  }),
}));

// Mock API services
vi.mock("@/services/api", () => ({
  login: vi.fn(),
}));

describe("LoginView", () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    mockPush.mockClear();
  });

  it("should render login form", () => {
    const wrapper = mount(LoginView);

    expect(wrapper.find("form").exists()).toBe(true);
    expect(wrapper.find('input[type="email"]').exists()).toBe(true);
    expect(wrapper.find('input[type="password"]').exists()).toBe(true);
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
  });

  it("should have email and password fields", () => {
    const wrapper = mount(LoginView);

    const emailInput = wrapper.find('input[type="email"]');
    const passwordInput = wrapper.find('input[type="password"]');

    expect(emailInput.exists()).toBe(true);
    expect(passwordInput.exists()).toBe(true);
  });

  it("should update form data when typing", async () => {
    const wrapper = mount(LoginView);

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
    const wrapper = mount(LoginView);

    const form = wrapper.find("form");
    await form.trigger("submit.prevent");

    // Aguarda próximo tick para validações processarem
    await wrapper.vm.$nextTick();

    expect(wrapper.text()).toContain("obrigatório");
  });

  it("should show validation error for invalid email", async () => {
    const wrapper = mount(LoginView);

    const emailInput = wrapper.find('input[type="email"]');
    await emailInput.setValue("invalid-email");

    const form = wrapper.find("form");
    await form.trigger("submit.prevent");
    await wrapper.vm.$nextTick();

    expect(wrapper.text()).toContain("válido");
  });

  it("should disable submit button while loading", async () => {
    // Mock login to delay response
    const { login } = await import("@/services/api");
    vi.mocked(login).mockImplementation(
      () => new Promise((resolve) => setTimeout(resolve, 100))
    );

    const wrapper = mount(LoginView);

    const emailInput = wrapper.find('input[type="email"]');
    const passwordInput = wrapper.find('input[type="password"]');

    await emailInput.setValue("test@example.com");
    await passwordInput.setValue("password123");

    const form = wrapper.find("form");
    await form.trigger("submit.prevent");

    // Aguarda próximo tick para processar submit
    await wrapper.vm.$nextTick();

    const submitButton = wrapper.find('button[type="submit"]');
    expect((submitButton.element as HTMLButtonElement).disabled).toBe(true);
  });
});
