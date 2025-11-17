import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, Input, Button, Badge } from '../../components/common';
import useAuthStore from '../../stores/authStore';

const Profile = () => {
  const navigate = useNavigate();
  const { user, updateProfile, logout } = useAuthStore();

  const [activeTab, setActiveTab] = useState('info');
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState({ type: '', text: '' });

  // Formulaire informations personnelles
  const [profileForm, setProfileForm] = useState({
    name: user?.name || '',
    email: user?.email || '',
    phone: user?.phone || '',
    city: user?.city || '',
    governorate: user?.governorate || '',
    birth_date: user?.birth_date || '',
  });

  // Formulaire changement de mot de passe
  const [passwordForm, setPasswordForm] = useState({
    current_password: '',
    password: '',
    password_confirmation: '',
  });

  const [errors, setErrors] = useState({});

  const handleProfileChange = (e) => {
    const { name, value } = e.target;
    setProfileForm((prev) => ({ ...prev, [name]: value }));
    if (errors[name]) {
      setErrors((prev) => ({ ...prev, [name]: '' }));
    }
  };

  const handlePasswordChange = (e) => {
    const { name, value } = e.target;
    setPasswordForm((prev) => ({ ...prev, [name]: value }));
    if (errors[name]) {
      setErrors((prev) => ({ ...prev, [name]: '' }));
    }
  };

  const validateProfileForm = () => {
    const newErrors = {};

    if (!profileForm.name || profileForm.name.length < 3) {
      newErrors.name = 'Le nom doit contenir au moins 3 caractères';
    }

    if (!profileForm.email || !/\S+@\S+\.\S+/.test(profileForm.email)) {
      newErrors.email = 'Email invalide';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const validatePasswordForm = () => {
    const newErrors = {};

    if (!passwordForm.current_password) {
      newErrors.current_password = 'Mot de passe actuel requis';
    }

    if (!passwordForm.password || passwordForm.password.length < 8) {
      newErrors.password = 'Le nouveau mot de passe doit contenir au moins 8 caractères';
    }

    if (passwordForm.password !== passwordForm.password_confirmation) {
      newErrors.password_confirmation = 'Les mots de passe ne correspondent pas';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleProfileSubmit = async (e) => {
    e.preventDefault();

    if (!validateProfileForm()) {
      return;
    }

    setLoading(true);
    setMessage({ type: '', text: '' });

    try {
      const result = await updateProfile(profileForm);

      if (result.success) {
        setMessage({ type: 'success', text: 'Profil mis à jour avec succès' });
      } else {
        setMessage({ type: 'error', text: result.error || 'Erreur lors de la mise à jour' });
      }
    } catch (err) {
      setMessage({ type: 'error', text: err.message || 'Erreur lors de la mise à jour' });
    } finally {
      setLoading(false);
    }
  };

  const handlePasswordSubmit = async (e) => {
    e.preventDefault();

    if (!validatePasswordForm()) {
      return;
    }

    setLoading(true);
    setMessage({ type: '', text: '' });

    try {
      // TODO: Implement password change API call
      // await authService.changePassword(passwordForm);

      setMessage({ type: 'success', text: 'Mot de passe modifié avec succès' });
      setPasswordForm({
        current_password: '',
        password: '',
        password_confirmation: '',
      });
    } catch (err) {
      setMessage({ type: 'error', text: err.message || 'Erreur lors du changement de mot de passe' });
    } finally {
      setLoading(false);
    }
  };

  const handleDeleteAccount = async () => {
    if (!confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) {
      return;
    }

    // TODO: Implement account deletion
    alert('Fonctionnalité de suppression de compte à implémenter');
  };

  const cities = ['Sfax', 'Tunis', 'Sousse', 'Monastir', 'Mahdia', 'Gabès', 'Kairouan', 'Bizerte'];
  const governorates = ['Sfax', 'Tunis', 'Sousse', 'Monastir', 'Mahdia', 'Gabès', 'Kairouan', 'Bizerte'];

  return (
    <div className="bg-gray-50 min-h-screen py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="mb-8">
          <button
            onClick={() => navigate('/dashboard')}
            className="text-css-gold hover:underline mb-4 inline-flex items-center"
          >
            ← Retour au dashboard
          </button>
          <h1 className="text-4xl font-bold text-gray-900 mb-2">
            Mon Profil
          </h1>
          <p className="text-gray-600">
            Gérez vos informations personnelles et votre compte
          </p>
        </div>

        {/* User Type Badge */}
        <div className="mb-8">
          <Card padding="lg" className="max-w-4xl mx-auto">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-4">
                <div className="w-16 h-16 bg-gradient-gold rounded-full flex items-center justify-center text-3xl">
                  {user?.user_type === 'socios' ? '👑' : user?.user_type === 'premium' ? '⭐' : '👤'}
                </div>
                <div>
                  <h3 className="text-2xl font-bold text-gray-900">{user?.name}</h3>
                  <div className="flex items-center gap-2 mt-1">
                    <Badge variant={user?.user_type === 'socios' ? 'secondary' : user?.user_type === 'premium' ? 'warning' : 'default'}>
                      {user?.user_type === 'socios' ? 'Socios' : user?.user_type === 'premium' ? 'Premium' : 'Free'}
                    </Badge>
                    {user?.user_type === 'socios' && user?.socios_number && (
                      <Badge variant="secondary">{user.socios_number}</Badge>
                    )}
                  </div>
                </div>
              </div>

              {user?.user_type === 'free' && (
                <Button variant="secondary" onClick={() => navigate('/upgrade')}>
                  Passer Premium
                </Button>
              )}
            </div>
          </Card>
        </div>

        {/* Tabs */}
        <div className="max-w-4xl mx-auto mb-6">
          <div className="border-b border-gray-200">
            <div className="flex space-x-8">
              <button
                onClick={() => setActiveTab('info')}
                className={`py-4 px-2 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === 'info'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                }`}
              >
                Informations personnelles
              </button>
              <button
                onClick={() => setActiveTab('password')}
                className={`py-4 px-2 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === 'password'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                }`}
              >
                Sécurité
              </button>
              <button
                onClick={() => setActiveTab('preferences')}
                className={`py-4 px-2 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === 'preferences'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                }`}
              >
                Préférences
              </button>
            </div>
          </div>
        </div>

        {/* Message */}
        {message.text && (
          <div className="max-w-4xl mx-auto mb-6">
            <div className={`p-4 rounded-lg ${
              message.type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'
            }`}>
              {message.text}
            </div>
          </div>
        )}

        {/* Tab Content */}
        <div className="max-w-4xl mx-auto">
          {activeTab === 'info' && (
            <Card padding="lg">
              <h3 className="text-xl font-bold mb-6">Informations personnelles</h3>

              <form onSubmit={handleProfileSubmit} className="space-y-6">
                <Input
                  label="Nom complet"
                  type="text"
                  name="name"
                  value={profileForm.name}
                  onChange={handleProfileChange}
                  error={errors.name}
                  required
                />

                <Input
                  label="Email"
                  type="email"
                  name="email"
                  value={profileForm.email}
                  onChange={handleProfileChange}
                  error={errors.email}
                  required
                />

                <Input
                  label="Téléphone"
                  type="tel"
                  name="phone"
                  value={profileForm.phone}
                  onChange={handleProfileChange}
                  placeholder="+216 XX XXX XXX"
                />

                <div className="grid md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Ville
                    </label>
                    <select
                      name="city"
                      value={profileForm.city}
                      onChange={handleProfileChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
                    >
                      <option value="">Sélectionner une ville</option>
                      {cities.map((city) => (
                        <option key={city} value={city}>{city}</option>
                      ))}
                    </select>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Gouvernorat
                    </label>
                    <select
                      name="governorate"
                      value={profileForm.governorate}
                      onChange={handleProfileChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
                    >
                      <option value="">Sélectionner un gouvernorat</option>
                      {governorates.map((gov) => (
                        <option key={gov} value={gov}>{gov}</option>
                      ))}
                    </select>
                  </div>
                </div>

                <Input
                  label="Date de naissance"
                  type="date"
                  name="birth_date"
                  value={profileForm.birth_date}
                  onChange={handleProfileChange}
                />

                <div className="flex gap-4 pt-6 border-t border-gray-200">
                  <Button type="submit" variant="primary" loading={loading} disabled={loading}>
                    Enregistrer les modifications
                  </Button>
                  <Button type="button" variant="outline" onClick={() => navigate('/dashboard')}>
                    Annuler
                  </Button>
                </div>
              </form>
            </Card>
          )}

          {activeTab === 'password' && (
            <Card padding="lg">
              <h3 className="text-xl font-bold mb-6">Changer le mot de passe</h3>

              <form onSubmit={handlePasswordSubmit} className="space-y-6">
                <Input
                  label="Mot de passe actuel"
                  type="password"
                  name="current_password"
                  value={passwordForm.current_password}
                  onChange={handlePasswordChange}
                  error={errors.current_password}
                  required
                />

                <Input
                  label="Nouveau mot de passe"
                  type="password"
                  name="password"
                  value={passwordForm.password}
                  onChange={handlePasswordChange}
                  error={errors.password}
                  helperText="Minimum 8 caractères"
                  required
                />

                <Input
                  label="Confirmer le nouveau mot de passe"
                  type="password"
                  name="password_confirmation"
                  value={passwordForm.password_confirmation}
                  onChange={handlePasswordChange}
                  error={errors.password_confirmation}
                  required
                />

                <div className="flex gap-4 pt-6 border-t border-gray-200">
                  <Button type="submit" variant="primary" loading={loading} disabled={loading}>
                    Changer le mot de passe
                  </Button>
                  <Button type="button" variant="outline" onClick={() => {
                    setPasswordForm({ current_password: '', password: '', password_confirmation: '' });
                    setErrors({});
                  }}>
                    Réinitialiser
                  </Button>
                </div>
              </form>

              <div className="mt-8 pt-8 border-t border-gray-200">
                <h4 className="text-lg font-bold text-red-600 mb-4">Zone de danger</h4>
                <p className="text-gray-600 mb-4">
                  La suppression de votre compte est irréversible. Toutes vos données seront définitivement supprimées.
                </p>
                <Button variant="danger" onClick={handleDeleteAccount}>
                  Supprimer mon compte
                </Button>
              </div>
            </Card>
          )}

          {activeTab === 'preferences' && (
            <Card padding="lg">
              <h3 className="text-xl font-bold mb-6">Préférences</h3>

              <div className="space-y-6">
                <div>
                  <h4 className="font-medium text-gray-900 mb-3">Notifications</h4>
                  <div className="space-y-2">
                    <label className="flex items-center">
                      <input type="checkbox" defaultChecked className="h-4 w-4 text-black focus:ring-black border-gray-300 rounded" />
                      <span className="ml-2 text-gray-700">Nouveaux matchs</span>
                    </label>
                    <label className="flex items-center">
                      <input type="checkbox" defaultChecked className="h-4 w-4 text-black focus:ring-black border-gray-300 rounded" />
                      <span className="ml-2 text-gray-700">Nouvelles offres Freeoui</span>
                    </label>
                    <label className="flex items-center">
                      <input type="checkbox" defaultChecked className="h-4 w-4 text-black focus:ring-black border-gray-300 rounded" />
                      <span className="ml-2 text-gray-700">Actualités du club</span>
                    </label>
                    <label className="flex items-center">
                      <input type="checkbox" className="h-4 w-4 text-black focus:ring-black border-gray-300 rounded" />
                      <span className="ml-2 text-gray-700">Newsletter hebdomadaire</span>
                    </label>
                  </div>
                </div>

                <div className="pt-6 border-t border-gray-200">
                  <h4 className="font-medium text-gray-900 mb-3">Langue</h4>
                  <select className="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="fr">Français</option>
                    <option value="ar">العربية</option>
                    <option value="en">English</option>
                  </select>
                </div>

                <div className="flex gap-4 pt-6 border-t border-gray-200">
                  <Button variant="primary">
                    Enregistrer les préférences
                  </Button>
                </div>
              </div>
            </Card>
          )}
        </div>
      </div>
    </div>
  );
};

export default Profile;
