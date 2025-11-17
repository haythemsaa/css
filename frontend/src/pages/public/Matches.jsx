import React, { useState, useEffect } from 'react';
import { matchesService } from '../../services/api';
import { Card, Badge, Button } from '../../components/common';

const Matches = () => {
  const [matches, setMatches] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [activeTab, setActiveTab] = useState('upcoming');

  useEffect(() => {
    loadMatches();
  }, [activeTab]);

  const loadMatches = async () => {
    setLoading(true);
    setError(null);

    try {
      let response;
      if (activeTab === 'upcoming') {
        response = await matchesService.getUpcoming();
      } else {
        response = await matchesService.getResults();
      }

      if (response.success) {
        setMatches(response.data);
      }
    } catch (err) {
      setError(err.message || 'Erreur lors du chargement des matchs');
    } finally {
      setLoading(false);
    }
  };

  const getCompetitionBadge = (competition) => {
    const badges = {
      'ligue_1': { label: 'Ligue 1', variant: 'primary' },
      'coupe_tunisie': { label: 'Coupe de Tunisie', variant: 'secondary' },
      'champions_league': { label: 'Champions League CAF', variant: 'success' },
      'coupe_caf': { label: 'Coupe CAF', variant: 'info' },
      'super_coupe': { label: 'Super Coupe', variant: 'warning' },
    };
    return badges[competition] || { label: competition, variant: 'default' };
  };

  const getResultBadge = (result) => {
    if (result === 'win') return { label: 'Victoire', variant: 'success' };
    if (result === 'draw') return { label: 'Nul', variant: 'warning' };
    if (result === 'loss') return { label: 'Défaite', variant: 'error' };
    return { label: '-', variant: 'default' };
  };

  const formatMatchTime = (date) => {
    return new Date(date).toLocaleTimeString('fr-FR', {
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const formatMatchDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  };

  const isHomeMatch = (match) => {
    return match.home_team?.toLowerCase().includes('css') ||
           match.home_team?.toLowerCase().includes('sfaxien');
  };

  return (
    <div className="bg-gray-50 min-h-screen py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-2">
            Calendrier <span className="text-gradient-gold">CSS</span>
          </h1>
          <p className="text-gray-600">
            Saison 2024/2025
          </p>
        </div>

        {/* Tabs */}
        <div className="mb-8">
          <div className="border-b border-gray-200">
            <div className="flex space-x-8">
              <button
                onClick={() => setActiveTab('upcoming')}
                className={`py-4 px-2 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === 'upcoming'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                }`}
              >
                ⏭️ Prochains matchs
              </button>
              <button
                onClick={() => setActiveTab('results')}
                className={`py-4 px-2 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === 'results'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                }`}
              >
                📊 Résultats
              </button>
            </div>
          </div>
        </div>

        {/* Liste des matchs */}
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-black"></div>
            <p className="text-gray-600 mt-4">Chargement...</p>
          </div>
        ) : error ? (
          <div className="text-center py-12">
            <p className="text-red-600 mb-4">{error}</p>
            <Button variant="outline" onClick={loadMatches}>
              Réessayer
            </Button>
          </div>
        ) : matches.length === 0 ? (
          <div className="text-center py-12">
            <div className="text-6xl mb-4">⚽</div>
            <h3 className="text-2xl font-bold text-gray-900 mb-2">
              Aucun match {activeTab === 'upcoming' ? 'programmé' : 'récent'}
            </h3>
            <p className="text-gray-600">
              {activeTab === 'upcoming'
                ? 'Le calendrier sera bientôt disponible'
                : 'Les résultats seront affichés ici'}
            </p>
          </div>
        ) : (
          <div className="space-y-4">
            {matches.map((match) => {
              const compBadge = getCompetitionBadge(match.competition);
              const isHome = isHomeMatch(match);

              return (
                <Card key={match.id} padding="none" className="overflow-hidden">
                  <div className="flex flex-col md:flex-row">
                    {/* Left side - Date & Competition */}
                    <div className="bg-black text-white p-6 md:w-1/4 flex flex-col justify-center items-center">
                      <div className="text-center mb-3">
                        <div className="text-sm opacity-80 mb-1">
                          {formatMatchDate(match.match_date)}
                        </div>
                        <div className="text-2xl font-bold">
                          {formatMatchTime(match.match_date)}
                        </div>
                      </div>
                      <Badge variant={compBadge.variant} size="sm">
                        {compBadge.label}
                      </Badge>
                    </div>

                    {/* Center - Match details */}
                    <div className="flex-1 p-6">
                      <div className="flex items-center justify-between">
                        {/* Home Team */}
                        <div className={`flex-1 text-center ${isHome ? 'order-1' : 'order-3'}`}>
                          <div className="mb-2">
                            <div className="w-16 h-16 mx-auto mb-2 bg-gradient-gold rounded-full flex items-center justify-center text-2xl">
                              {isHome ? '🏠' : '✈️'}
                            </div>
                          </div>
                          <div className="font-bold text-lg">
                            {isHome ? 'CSS' : match.opponent}
                          </div>
                          {match.home_score !== null && match.home_score !== undefined && (
                            <div className="text-3xl font-bold text-gray-900 mt-2">
                              {isHome ? match.home_score : match.away_score}
                            </div>
                          )}
                        </div>

                        {/* VS or Score */}
                        <div className="order-2 px-8 flex flex-col items-center">
                          {activeTab === 'upcoming' ? (
                            <div className="text-2xl font-bold text-gray-400">VS</div>
                          ) : (
                            match.home_score !== null && match.away_score !== null && (
                              <div className="flex flex-col items-center">
                                <div className="text-4xl font-bold text-gray-900 mb-2">
                                  {match.home_score} - {match.away_score}
                                </div>
                                {match.result && (
                                  <Badge variant={getResultBadge(match.result).variant}>
                                    {getResultBadge(match.result).label}
                                  </Badge>
                                )}
                              </div>
                            )
                          )}
                        </div>

                        {/* Away Team */}
                        <div className={`flex-1 text-center ${isHome ? 'order-3' : 'order-1'}`}>
                          <div className="mb-2">
                            <div className="w-16 h-16 mx-auto mb-2 bg-gray-200 rounded-full flex items-center justify-center text-2xl">
                              {!isHome ? '🏠' : '✈️'}
                            </div>
                          </div>
                          <div className="font-bold text-lg">
                            {!isHome ? 'CSS' : match.opponent}
                          </div>
                          {match.away_score !== null && match.away_score !== undefined && (
                            <div className="text-3xl font-bold text-gray-900 mt-2">
                              {!isHome ? match.home_score : match.away_score}
                            </div>
                          )}
                        </div>
                      </div>

                      {/* Stadium */}
                      {match.stadium && (
                        <div className="mt-4 pt-4 border-t border-gray-200 text-center text-sm text-gray-600">
                          <span className="flex items-center justify-center">
                            <svg className="w-4 h-4 mr-2" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                              <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                              <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {match.stadium}
                          </span>
                        </div>
                      )}

                      {/* Attendance */}
                      {match.attendance && (
                        <div className="mt-2 text-center text-sm text-gray-600">
                          <span className="flex items-center justify-center">
                            <svg className="w-4 h-4 mr-2" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                              <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {match.attendance.toLocaleString()} spectateurs
                          </span>
                        </div>
                      )}
                    </div>
                  </div>
                </Card>
              );
            })}
          </div>
        )}
      </div>
    </div>
  );
};

export default Matches;
