# 🧪 Frontend Testing Guide

Comprehensive testing documentation for the CSS Platform frontend React application.

## 📊 Test Coverage Summary

- **Total Tests**: 127 tests
- **Test Files**: 6
- **Components**: 86 tests (4 components)
- **Services**: 32 tests (7 services)
- **Stores**: 9 tests (Auth store)
- **Status**: ✅ All tests passing

## 🛠️ Testing Stack

### Core Testing Tools
- **Vitest 1.0.4** - Fast, Vite-native test runner
- **@testing-library/react 14.1.2** - React component testing utilities
- **@testing-library/jest-dom 6.1.5** - Custom DOM matchers
- **@testing-library/user-event 14.5.1** - User interaction simulation
- **jsdom 23.0.1** - DOM implementation for Node.js

### Coverage
- **@vitest/coverage-v8** - V8 coverage provider
- **Reports**: text, json, html, lcov

## 🚀 Quick Start

### Run Tests
```bash
cd frontend

# Run all tests
npm test

# Run tests with UI
npm run test:ui

# Run tests with coverage
npm run test:coverage

# Run tests in watch mode (development)
npm test -- --watch
```

### Test Structure
```
frontend/
├── src/
│   ├── components/
│   │   └── common/
│   │       ├── __tests__/
│   │       │   ├── Button.test.jsx        (22 tests)
│   │       │   ├── Input.test.jsx         (25 tests)
│   │       │   ├── Card.test.jsx          (19 tests)
│   │       │   └── Badge.test.jsx         (20 tests)
│   │       ├── Button.jsx
│   │       ├── Input.jsx
│   │       ├── Card.jsx
│   │       └── Badge.jsx
│   ├── services/
│   │   ├── __tests__/
│   │   │   └── api.test.js                (32 tests)
│   │   └── api.js
│   ├── stores/
│   │   ├── __tests__/
│   │   │   └── authStore.test.js          (9 tests)
│   │   └── authStore.js
│   └── test/
│       ├── setup.js                       # Global test setup
│       └── utils.jsx                      # Test utilities
├── vitest.config.js                       # Vitest configuration
└── package.json                           # Test scripts
```

## 📝 Component Tests

### Button Component (22 tests)
**File**: `src/components/common/__tests__/Button.test.jsx`

Tests all button functionality:
- ✅ Rendering with text
- ✅ Click handlers
- ✅ Disabled states
- ✅ Loading states
- ✅ All variants (primary, secondary, outline, ghost, danger)
- ✅ All sizes (sm, md, lg, xl)
- ✅ Full width mode
- ✅ Icons
- ✅ Custom className
- ✅ Type attributes

**Example:**
```jsx
it('renders with primary variant by default', () => {
  render(<Button>Primary</Button>);
  const button = screen.getByRole('button');
  expect(button).toHaveClass('bg-black', 'text-white');
});
```

### Input Component (25 tests)
**File**: `src/components/common/__tests__/Input.test.jsx`

Tests form input functionality:
- ✅ Label rendering
- ✅ Required fields with asterisk
- ✅ onChange handlers
- ✅ Value management
- ✅ Error states and messages
- ✅ Helper text
- ✅ Input types (text, password, email, number)
- ✅ Disabled states
- ✅ Icons
- ✅ Full width mode
- ✅ Custom className
- ✅ Accessibility (labels, required, aria attributes)

**Example:**
```jsx
it('shows error message when error prop is provided', () => {
  render(<Input name="email" error="Email is required" />);
  expect(screen.getByText('Email is required')).toBeInTheDocument();
});
```

### Card Component (19 tests)
**File**: `src/components/common/__tests__/Card.test.jsx`

Tests card container functionality:
- ✅ Children rendering
- ✅ All variants (default, elevated, outline, gold)
- ✅ All padding sizes (none, sm, md, lg, xl)
- ✅ Hover effects
- ✅ Click handlers
- ✅ Cursor pointer on clickable cards
- ✅ Custom className
- ✅ Base styles

**Example:**
```jsx
it('calls onClick when clicked', () => {
  const handleClick = vi.fn();
  const { container } = render(<Card onClick={handleClick}>Click me</Card>);

  fireEvent.click(container.firstChild);
  expect(handleClick).toHaveBeenCalledTimes(1);
});
```

### Badge Component (20 tests)
**File**: `src/components/common/__tests__/Badge.test.jsx`

Tests badge/label functionality:
- ✅ Text rendering
- ✅ All variants (default, primary, secondary, success, warning, error, info)
- ✅ All sizes (sm, md, lg)
- ✅ Icons
- ✅ Custom className
- ✅ Span element rendering

**Example:**
```jsx
it('renders with success variant', () => {
  render(<Badge variant="success">Success</Badge>);
  const badge = screen.getByText('Success');
  expect(badge).toHaveClass('bg-green-100', 'text-green-800');
});
```

## 🔌 Service Tests

### API Services (32 tests)
**File**: `src/services/__tests__/api.test.js`

Tests all 7 API service modules:
1. **Auth Service** (7 methods)
   - register, login, logout
   - getProfile, updateProfile
   - changePassword, verifySocios

2. **Partners Service** (5 methods)
   - getCategories, getPartners
   - getPartner, getFeatured
   - getNearby

3. **Offers Service** (3 methods)
   - getPartnerOffers, getOffer
   - getActiveOffers

4. **Codes Service** (4 methods)
   - generateCode, getMyCodes
   - validateCode, useCode

5. **Content Service** (5 methods)
   - getContent, getFeatured
   - getContentDetail
   - likeContent, unlikeContent

6. **Players Service** (3 methods)
   - getPlayers, getPlayer
   - getActivePlayers

7. **Matches Service** (4 methods)
   - getMatches, getUpcoming
   - getResults, getMatch

**Example:**
```js
it('has login method', () => {
  expect(authService.login).toBeDefined();
  expect(typeof authService.login).toBe('function');
});
```

## 🗄️ Store Tests

### Auth Store (9 tests)
**File**: `src/stores/__tests__/authStore.test.js`

Tests Zustand authentication store:
- ✅ Initial state verification
- ✅ Login (success & error)
- ✅ Register (success & error)
- ✅ Logout
- ✅ Helper methods (isPremium, isSocios, getDiscountLevel)
- ✅ Clear error

**Example:**
```js
it('successfully logs in a user', async () => {
  const mockUser = {
    id: 1,
    name: 'John Doe',
    email: 'john@example.com',
    user_type: 'premium',
  };
  const mockToken = 'mock-token-123';

  authService.login.mockResolvedValue({
    success: true,
    data: { user: mockUser, token: mockToken },
  });

  const { result } = renderHook(() => useAuthStore());

  await act(async () => {
    await result.current.login({
      email: 'john@example.com',
      password: 'password123',
    });
  });

  expect(result.current.user).toEqual(mockUser);
  expect(result.current.isAuthenticated).toBe(true);
});
```

## 🔧 Test Configuration

### vitest.config.js
```javascript
import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
  plugins: [react()],
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: './src/test/setup.js',
    css: true,
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html', 'lcov'],
      exclude: [
        'node_modules/',
        'src/test/',
        '**/*.spec.jsx',
        '**/*.test.jsx',
        'src/main.jsx',
        'dist/',
      ],
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
});
```

### Test Setup (src/test/setup.js)
```javascript
import { expect, afterEach } from 'vitest';
import { cleanup } from '@testing-library/react';
import '@testing-library/jest-dom/vitest';

// Cleanup after each test
afterEach(() => {
  cleanup();
});

// Mock window.matchMedia
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: vi.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(),
    removeListener: vi.fn(),
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  })),
});

// Mock IntersectionObserver
global.IntersectionObserver = class IntersectionObserver {
  constructor() {}
  disconnect() {}
  observe() {}
  takeRecords() { return []; }
  unobserve() {}
};

// Mock scrollTo
window.scrollTo = vi.fn();
```

## 📚 Test Utilities

### Custom Render (src/test/utils.jsx)
```jsx
import { render } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';

export function renderWithRouter(ui, { route = '/' } = {}) {
  window.history.pushState({}, 'Test page', route);
  return render(ui, { wrapper: BrowserRouter });
}
```

## 🎯 Testing Best Practices

### 1. Test User Behavior, Not Implementation
```jsx
// ❌ Bad - Testing implementation
expect(component.state.isOpen).toBe(true);

// ✅ Good - Testing behavior
expect(screen.getByRole('dialog')).toBeVisible();
```

### 2. Use Accessible Queries
```jsx
// ❌ Bad
screen.getByTestId('submit-button');

// ✅ Good
screen.getByRole('button', { name: /submit/i });
screen.getByLabelText(/email/i);
```

### 3. Test Error States
```jsx
it('shows error when email is invalid', async () => {
  const user = userEvent.setup();
  render(<LoginForm />);

  await user.type(screen.getByLabelText(/email/i), 'invalid');
  await user.click(screen.getByRole('button', { name: /submit/i }));

  expect(screen.getByText('Email invalide')).toBeInTheDocument();
});
```

### 4. Use act() for Async Operations
```jsx
await act(async () => {
  await result.current.login(credentials);
});
```

### 5. Clean Up Properly
```jsx
afterEach(() => {
  cleanup();
  vi.clearAllMocks();
  localStorage.clear();
});
```

## 🔄 Continuous Integration

### GitHub Actions
The frontend tests run automatically on:
- Push to `main`, `develop`, or `claude/**` branches
- Pull requests to `main` or `develop`

**Workflow**: `.github/workflows/frontend.yml`

```yaml
- name: Run Vitest tests
  run: npm test -- --run --coverage
  env:
    CI: true
```

## 📈 Coverage Goals

Current coverage targets:
- **Statements**: > 80%
- **Branches**: > 75%
- **Functions**: > 80%
- **Lines**: > 80%

Coverage reports are generated in:
- `coverage/index.html` - HTML report
- `coverage/coverage-final.json` - JSON report
- `coverage/lcov.info` - LCOV report

## 🐛 Debugging Tests

### 1. Run Single Test File
```bash
npm test -- Button.test.jsx
```

### 2. Run Single Test
```bash
npm test -- -t "renders with children text"
```

### 3. Use Vitest UI
```bash
npm run test:ui
```

### 4. Debug in Browser
```bash
npm test -- --inspect-brk --no-coverage
```

Then open `chrome://inspect` in Chrome.

### 5. Print Debug Info
```jsx
import { screen } from '@testing-library/react';
screen.debug(); // Print entire DOM
screen.debug(screen.getByRole('button')); // Print specific element
```

## 🔍 Common Test Patterns

### Testing Forms
```jsx
it('submits form with valid data', async () => {
  const user = userEvent.setup();
  const handleSubmit = vi.fn();

  render(<Form onSubmit={handleSubmit} />);

  await user.type(screen.getByLabelText(/name/i), 'John');
  await user.type(screen.getByLabelText(/email/i), 'john@example.com');
  await user.click(screen.getByRole('button', { name: /submit/i }));

  expect(handleSubmit).toHaveBeenCalledWith({
    name: 'John',
    email: 'john@example.com',
  });
});
```

### Testing Async State
```jsx
it('loads and displays data', async () => {
  render(<UserList />);

  expect(screen.getByText(/loading/i)).toBeInTheDocument();

  const users = await screen.findByRole('list');
  expect(users).toBeInTheDocument();

  expect(screen.queryByText(/loading/i)).not.toBeInTheDocument();
});
```

### Testing Zustand Stores
```jsx
it('updates state correctly', async () => {
  const { result } = renderHook(() => useStore());

  await act(async () => {
    await result.current.updateUser({ name: 'John' });
  });

  expect(result.current.user.name).toBe('John');
});
```

## 📦 Dependencies

```json
{
  "devDependencies": {
    "@testing-library/jest-dom": "^6.1.5",
    "@testing-library/react": "^14.1.2",
    "@testing-library/user-event": "^14.5.1",
    "@vitest/coverage-v8": "^1.0.4",
    "@vitest/ui": "^1.0.4",
    "jsdom": "^23.0.1",
    "vitest": "^1.0.4"
  }
}
```

## 🎓 Learning Resources

- [Vitest Documentation](https://vitest.dev/)
- [Testing Library Docs](https://testing-library.com/docs/react-testing-library/intro/)
- [Jest DOM Matchers](https://github.com/testing-library/jest-dom)
- [Testing Zustand Stores](https://github.com/pmndrs/zustand#testing)

## 📞 Support

For questions or issues with frontend tests:
1. Check this documentation
2. Review existing test files for examples
3. Open an issue on GitHub
4. Contact the development team

---

**Last Updated**: 2025-11-17
**Test Suite Version**: 1.0.0
**Maintainer**: CSS Platform Development Team
