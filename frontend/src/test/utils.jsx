import { render } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';
import { vi } from 'vitest';

// Custom render with Router wrapper
export function renderWithRouter(ui, { route = '/' } = {}) {
  window.history.pushState({}, 'Test page', route);

  return render(ui, { wrapper: BrowserRouter });
}

// Mock useNavigate hook
export const mockNavigate = vi.fn();

// Mock react-router-dom
export const mockReactRouterDom = {
  useNavigate: () => mockNavigate,
  useLocation: () => ({ pathname: '/', search: '', hash: '', state: null }),
  useParams: () => ({}),
  Link: ({ children, to, ...props }) => <a href={to} {...props}>{children}</a>,
  BrowserRouter: ({ children }) => <div>{children}</div>,
};

// Mock axios
export const mockAxios = {
  get: vi.fn(() => Promise.resolve({ data: {} })),
  post: vi.fn(() => Promise.resolve({ data: {} })),
  put: vi.fn(() => Promise.resolve({ data: {} })),
  delete: vi.fn(() => Promise.resolve({ data: {} })),
  create: vi.fn(() => mockAxios),
  interceptors: {
    request: { use: vi.fn(), eject: vi.fn() },
    response: { use: vi.fn(), eject: vi.fn() },
  },
};

// Create a mock store
export function createMockStore(initialState = {}) {
  return {
    ...initialState,
    subscribe: vi.fn(),
    getState: () => initialState,
    setState: vi.fn(),
  };
}

// Wait for async operations
export const waitFor = (callback, options) => {
  return new Promise((resolve) => {
    const checkCondition = () => {
      try {
        callback();
        resolve();
      } catch (error) {
        if (options?.timeout && Date.now() > options.timeout) {
          throw error;
        }
        setTimeout(checkCondition, 50);
      }
    };
    checkCondition();
  });
};
