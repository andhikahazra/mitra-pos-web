import { initNavigation } from './ui/navigation';
import { initGlobalSearch } from './ui/global-search';
import { initThemeToggle } from './ui/theme';
import { initConfirmDialog } from './ui/confirm-dialog';
import { initFlashMessages } from './ui/flash-message';
import { initProductEditorForm } from './ui/product-editor-form';
import { initProdukIndexView } from './ui/produk-index-view';
import { initCharts } from './features/dashboard/charts';
import { initOverview } from './features/dashboard/overview';

function bootDashboard() {
    initThemeToggle();
    initConfirmDialog();
    initFlashMessages();
    initProductEditorForm();
    initProdukIndexView();
    initNavigation();

    const payload = window.__DASHBOARD_DATA__;
    if (payload) {
        initOverview(payload);
        initCharts(payload.charts);
    }
}

bootDashboard();
