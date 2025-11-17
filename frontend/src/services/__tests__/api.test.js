import { describe, it, expect, beforeEach } from 'vitest';
import {
  authService,
  partnersService,
  offersService,
  codesService,
  contentService,
  playersService,
  matchesService,
} from '../api';

describe('API Services', () => {
  beforeEach(() => {
    localStorage.clear();
  });

  describe('Auth Service', () => {
    it('has register method', () => {
      expect(authService.register).toBeDefined();
      expect(typeof authService.register).toBe('function');
    });

    it('has login method', () => {
      expect(authService.login).toBeDefined();
      expect(typeof authService.login).toBe('function');
    });

    it('has logout method', () => {
      expect(authService.logout).toBeDefined();
      expect(typeof authService.logout).toBe('function');
    });

    it('has getProfile method', () => {
      expect(authService.getProfile).toBeDefined();
      expect(typeof authService.getProfile).toBe('function');
    });

    it('has updateProfile method', () => {
      expect(authService.updateProfile).toBeDefined();
      expect(typeof authService.updateProfile).toBe('function');
    });

    it('has changePassword method', () => {
      expect(authService.changePassword).toBeDefined();
      expect(typeof authService.changePassword).toBe('function');
    });

    it('has verifySocios method', () => {
      expect(authService.verifySocios).toBeDefined();
      expect(typeof authService.verifySocios).toBe('function');
    });
  });

  describe('Partners Service', () => {
    it('has getCategories method', () => {
      expect(partnersService.getCategories).toBeDefined();
      expect(typeof partnersService.getCategories).toBe('function');
    });

    it('has getPartners method', () => {
      expect(partnersService.getPartners).toBeDefined();
      expect(typeof partnersService.getPartners).toBe('function');
    });

    it('has getPartner method', () => {
      expect(partnersService.getPartner).toBeDefined();
      expect(typeof partnersService.getPartner).toBe('function');
    });

    it('has getFeatured method', () => {
      expect(partnersService.getFeatured).toBeDefined();
      expect(typeof partnersService.getFeatured).toBe('function');
    });

    it('has getNearby method', () => {
      expect(partnersService.getNearby).toBeDefined();
      expect(typeof partnersService.getNearby).toBe('function');
    });
  });

  describe('Offers Service', () => {
    it('has getPartnerOffers method', () => {
      expect(offersService.getPartnerOffers).toBeDefined();
      expect(typeof offersService.getPartnerOffers).toBe('function');
    });

    it('has getOffer method', () => {
      expect(offersService.getOffer).toBeDefined();
      expect(typeof offersService.getOffer).toBe('function');
    });

    it('has getActiveOffers method', () => {
      expect(offersService.getActiveOffers).toBeDefined();
      expect(typeof offersService.getActiveOffers).toBe('function');
    });
  });

  describe('Codes Service', () => {
    it('has generateCode method', () => {
      expect(codesService.generateCode).toBeDefined();
      expect(typeof codesService.generateCode).toBe('function');
    });

    it('has getMyCodes method', () => {
      expect(codesService.getMyCodes).toBeDefined();
      expect(typeof codesService.getMyCodes).toBe('function');
    });

    it('has validateCode method', () => {
      expect(codesService.validateCode).toBeDefined();
      expect(typeof codesService.validateCode).toBe('function');
    });

    it('has useCode method', () => {
      expect(codesService.useCode).toBeDefined();
      expect(typeof codesService.useCode).toBe('function');
    });
  });

  describe('Content Service', () => {
    it('has getContent method', () => {
      expect(contentService.getContent).toBeDefined();
      expect(typeof contentService.getContent).toBe('function');
    });

    it('has getFeatured method', () => {
      expect(contentService.getFeatured).toBeDefined();
      expect(typeof contentService.getFeatured).toBe('function');
    });

    it('has getContentDetail method', () => {
      expect(contentService.getContentDetail).toBeDefined();
      expect(typeof contentService.getContentDetail).toBe('function');
    });

    it('has likeContent method', () => {
      expect(contentService.likeContent).toBeDefined();
      expect(typeof contentService.likeContent).toBe('function');
    });

    it('has unlikeContent method', () => {
      expect(contentService.unlikeContent).toBeDefined();
      expect(typeof contentService.unlikeContent).toBe('function');
    });
  });

  describe('Players Service', () => {
    it('has getPlayers method', () => {
      expect(playersService.getPlayers).toBeDefined();
      expect(typeof playersService.getPlayers).toBe('function');
    });

    it('has getPlayer method', () => {
      expect(playersService.getPlayer).toBeDefined();
      expect(typeof playersService.getPlayer).toBe('function');
    });

    it('has getActivePlayers method', () => {
      expect(playersService.getActivePlayers).toBeDefined();
      expect(typeof playersService.getActivePlayers).toBe('function');
    });
  });

  describe('Matches Service', () => {
    it('has getMatches method', () => {
      expect(matchesService.getMatches).toBeDefined();
      expect(typeof matchesService.getMatches).toBe('function');
    });

    it('has getUpcoming method', () => {
      expect(matchesService.getUpcoming).toBeDefined();
      expect(typeof matchesService.getUpcoming).toBe('function');
    });

    it('has getResults method', () => {
      expect(matchesService.getResults).toBeDefined();
      expect(typeof matchesService.getResults).toBe('function');
    });

    it('has getMatch method', () => {
      expect(matchesService.getMatch).toBeDefined();
      expect(typeof matchesService.getMatch).toBe('function');
    });
  });

  describe('Service Structure', () => {
    it('exports all required services', () => {
      expect(authService).toBeDefined();
      expect(partnersService).toBeDefined();
      expect(offersService).toBeDefined();
      expect(codesService).toBeDefined();
      expect(contentService).toBeDefined();
      expect(playersService).toBeDefined();
      expect(matchesService).toBeDefined();
    });
  });
});
