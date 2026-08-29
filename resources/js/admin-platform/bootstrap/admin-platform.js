// 1️⃣ register plugins
import '../plugins/index.js'
// 2️⃣ core
import ModuleRegistry from '../core/module-registry.js';
import ModuleLoader from '../core/module-loader.js';
// 3️⃣ engines
// 4️⃣ renderers
// 5️⃣ start system


/*
 * no need this  now i add new register plugins
 */

// import '../plugins/columns/toggle-status.js'
// import '../plugins/columns/is-default.js'
// import '../plugins/columns/badge.js'
// import '../plugins/columns/boolean-icon.js'
// import '../plugins/columns/date-format.js'
// import '../plugins/columns/image.js'
// import '../plugins/columns/copy.js'
// import '../plugins/actions/actions.js'




class AdminPlatform {
    constructor() {
        this.registry = new ModuleRegistry();
        this.loader = new ModuleLoader();
    }

    async boot() {
        // console.log('[AdminPlatform] Booting...');
        await this.loader.discover();
        // console.log('[AdminPlatform] Ready.');
    }

    register(module) {
        return this.registry.register(module);
    }
}

window.AdminPlatform = new AdminPlatform();

document.addEventListener('DOMContentLoaded', async () => {
    await window.AdminPlatform.boot();
});

export default window.AdminPlatform;
