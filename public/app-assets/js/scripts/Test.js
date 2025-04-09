// RouteManager.js
class RouteManager {
    // Private static instance variable to hold the singleton instance
    static #instance = null;

    // Private constructor to prevent direct instantiation
    constructor() {
        if (RouteManager.#instance) {
            throw new Error("Cannot instantiate Singleton class directly.");
        }
        // Initialize routes object as an empty object, to be populated dynamically
        this.routes = {};
    }

    // Public static method to get the singleton instance
    static getInstance() {
        if (!RouteManager.#instance) {
            RouteManager.#instance = new RouteManager();
        }
        return RouteManager.#instance;
    }

    // Public method to get the routes data
    getRoutes() {
        return this.routes;
    }

    // Public method to set or update routes dynamically
    setRoutes(newRoutes) {
        this.routes = { ...this.routes, ...newRoutes }; // Merge the new routes with existing ones
    }

    // Public method to update a specific route
    updateRoute(key, url) {
        this.routes[key] = url;
    }
}

// Export the RouteManager class
export default RouteManager;
