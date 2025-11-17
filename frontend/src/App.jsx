import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import MainLayout from './components/layout/MainLayout';
import useAuthStore from './stores/authStore';

// Public Pages
import Home from './pages/public/Home';
import Partners from './pages/public/Partners';
import PartnerDetail from './pages/public/PartnerDetail';
import Content from './pages/public/Content';
import ContentDetail from './pages/public/ContentDetail';
import Players from './pages/public/Players';
import Matches from './pages/public/Matches';
import Login from './pages/auth/Login';
import Register from './pages/auth/Register';

// Dashboard Pages
import Dashboard from './pages/dashboard/Dashboard';

// Protected Route Component
const ProtectedRoute = ({ children }) => {
  const { isAuthenticated } = useAuthStore();

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  return children;
};

// Premium Route Component (requires Premium or Socios)
const PremiumRoute = ({ children }) => {
  const { isAuthenticated, isPremium } = useAuthStore();

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  if (!isPremium()) {
    return <Navigate to="/upgrade" replace />;
  }

  return children;
};

function App() {
  return (
    <BrowserRouter>
      <Routes>
        {/* Public Routes with Layout */}
        <Route element={<MainLayout />}>
          {/* Home */}
          <Route index element={<Home />} />

          {/* Auth Routes */}
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />

          {/* Partners Routes */}
          <Route path="/partners" element={<Partners />} />
          <Route path="/partners/:slug" element={<PartnerDetail />} />

          {/* Content Routes */}
          <Route path="/content" element={<Content />} />
          <Route path="/content/:slug" element={<ContentDetail />} />

          {/* Players & Matches */}
          <Route path="/players" element={<Players />} />
          <Route path="/matches" element={<Matches />} />

          {/* Protected Routes */}
          <Route
            path="/dashboard"
            element={
              <ProtectedRoute>
                <Dashboard />
              </ProtectedRoute>
            }
          />

          {/* 404 */}
          <Route path="*" element={
            <div className="container mx-auto px-4 py-16 text-center">
              <h1 className="text-6xl font-bold text-gray-900 mb-4">404</h1>
              <p className="text-xl text-gray-600 mb-8">Page non trouvée</p>
              <a href="/" className="text-black hover:underline">Retour à l'accueil</a>
            </div>
          } />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
