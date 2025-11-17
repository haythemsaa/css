import React, { useState, useEffect } from 'react';
import { playersService } from '../../services/api';
import { Card, Badge, Button } from '../../components/common';

const Players = () => {
  const [players, setPlayers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [selectedPosition, setSelectedPosition] = useState('');

  useEffect(() => {
    loadPlayers();
  }, [selectedPosition]);

  const loadPlayers = async () => {
    setLoading(true);
    setError(null);

    try {
      const params = {};
      if (selectedPosition) params.position = selectedPosition;

      const response = await playersService.getPlayers(params);

      if (response.success) {
        setPlayers(response.data);
      }
    } catch (err) {
      setError(err.message || 'Erreur lors du chargement des joueurs');
    } finally {
      setLoading(false);
    }
  };

  const positions = [
    { value: '', label: 'Tous', icon: '⚽' },
    { value: 'goalkeeper', label: 'Gardiens', icon: '🧤' },
    { value: 'defender', label: 'Défenseurs', icon: '🛡️' },
    { value: 'midfielder', label: 'Milieux', icon: '⚡' },
    { value: 'forward', label: 'Attaquants', icon: '🎯' },
  ];

  const getPositionLabel = (position) => {
    const labels = {
      goalkeeper: 'Gardien',
      defender: 'Défenseur',
      midfielder: 'Milieu',
      forward: 'Attaquant',
    };
    return labels[position] || position;
  };

  const getPositionColor = (position) => {
    const colors = {
      goalkeeper: 'bg-yellow-100 text-yellow-800',
      defender: 'bg-blue-100 text-blue-800',
      midfielder: 'bg-green-100 text-green-800',
      forward: 'bg-red-100 text-red-800',
    };
    return colors[position] || 'bg-gray-100 text-gray-800';
  };

  const calculateAge = (birthDate) => {
    if (!birthDate) return null;
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
      age--;
    }
    return age;
  };

  return (
    <div className="bg-gray-50 min-h-screen py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-2">
            Effectif <span className="text-gradient-gold">CSS</span>
          </h1>
          <p className="text-gray-600">
            {players.length} joueur{players.length > 1 ? 's' : ''} • Saison 2024/2025
          </p>
        </div>

        {/* Position filters */}
        <div className="mb-8">
          <div className="flex flex-wrap gap-3">
            {positions.map((position) => (
              <button
                key={position.value}
                onClick={() => setSelectedPosition(position.value)}
                className={`px-6 py-3 rounded-lg font-medium transition-all ${
                  selectedPosition === position.value
                    ? 'bg-black text-white'
                    : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
                }`}
              >
                {position.icon} {position.label}
              </button>
            ))}
          </div>
        </div>

        {/* Liste des joueurs */}
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-black"></div>
            <p className="text-gray-600 mt-4">Chargement...</p>
          </div>
        ) : error ? (
          <div className="text-center py-12">
            <p className="text-red-600 mb-4">{error}</p>
            <Button variant="outline" onClick={loadPlayers}>
              Réessayer
            </Button>
          </div>
        ) : players.length === 0 ? (
          <div className="text-center py-12">
            <div className="text-6xl mb-4">👥</div>
            <h3 className="text-2xl font-bold text-gray-900 mb-2">
              Aucun joueur trouvé
            </h3>
            <p className="text-gray-600">
              Aucun joueur dans cette catégorie
            </p>
          </div>
        ) : (
          <div className="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {players.map((player) => (
              <Card key={player.id} padding="none" className="overflow-hidden hover:shadow-xl transition-shadow">
                {/* Photo */}
                <div className="relative h-64 bg-gradient-to-b from-black to-gray-800 flex items-end justify-center">
                  {player.photo ? (
                    <img
                      src={player.photo}
                      alt={player.name}
                      className="h-full w-full object-cover object-top"
                    />
                  ) : (
                    <div className="text-8xl mb-4">👤</div>
                  )}

                  {/* Jersey number */}
                  {player.jersey_number && (
                    <div className="absolute top-4 right-4">
                      <div className="bg-gradient-gold text-black w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl">
                        {player.jersey_number}
                      </div>
                    </div>
                  )}
                </div>

                {/* Info */}
                <div className="p-4">
                  {/* Position */}
                  <div className="mb-2">
                    <span className={`inline-block px-2 py-1 rounded-full text-xs font-medium ${getPositionColor(player.position)}`}>
                      {getPositionLabel(player.position)}
                    </span>
                  </div>

                  {/* Name */}
                  <h3 className="text-xl font-bold text-gray-900 mb-1">
                    {player.name}
                  </h3>

                  {/* Details */}
                  <div className="space-y-1 text-sm text-gray-600 mb-4">
                    {player.nationality && (
                      <div className="flex items-center">
                        <span className="mr-2">🌍</span>
                        <span>{player.nationality}</span>
                      </div>
                    )}
                    {player.birth_date && (
                      <div className="flex items-center">
                        <span className="mr-2">🎂</span>
                        <span>{calculateAge(player.birth_date)} ans</span>
                      </div>
                    )}
                    {player.height && (
                      <div className="flex items-center">
                        <span className="mr-2">📏</span>
                        <span>{player.height} cm</span>
                      </div>
                    )}
                  </div>

                  {/* Stats */}
                  {player.matches_played > 0 && (
                    <div className="grid grid-cols-3 gap-2 pt-3 border-t border-gray-200">
                      <div className="text-center">
                        <div className="text-lg font-bold text-gray-900">{player.matches_played}</div>
                        <div className="text-xs text-gray-500">Matchs</div>
                      </div>
                      {player.position !== 'goalkeeper' && (
                        <>
                          <div className="text-center">
                            <div className="text-lg font-bold text-gray-900">{player.goals || 0}</div>
                            <div className="text-xs text-gray-500">Buts</div>
                          </div>
                          <div className="text-center">
                            <div className="text-lg font-bold text-gray-900">{player.assists || 0}</div>
                            <div className="text-xs text-gray-500">Passes D.</div>
                          </div>
                        </>
                      )}
                      {player.position === 'goalkeeper' && (
                        <div className="text-center col-span-2">
                          <div className="text-lg font-bold text-gray-900">{player.clean_sheets || 0}</div>
                          <div className="text-xs text-gray-500">Clean Sheets</div>
                        </div>
                      )}
                    </div>
                  )}

                  {/* Cards */}
                  {(player.yellow_cards > 0 || player.red_cards > 0) && (
                    <div className="flex items-center gap-2 mt-3 pt-3 border-t border-gray-200">
                      {player.yellow_cards > 0 && (
                        <Badge variant="warning" size="sm">
                          🟨 {player.yellow_cards}
                        </Badge>
                      )}
                      {player.red_cards > 0 && (
                        <Badge variant="error" size="sm">
                          🟥 {player.red_cards}
                        </Badge>
                      )}
                    </div>
                  )}
                </div>
              </Card>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default Players;
