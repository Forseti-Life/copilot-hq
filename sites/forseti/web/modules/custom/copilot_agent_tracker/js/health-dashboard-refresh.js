/**
 * Health Dashboard Auto-Refresh
 *
 * Automatically refreshes health data every 30 seconds without page reload.
 */

(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.healthDashboardRefresh = {
    attach: function (context, settings) {
      once('health-dashboard-init', document, context).forEach(function () {
        // Only initialize if we're on the health dashboard page.
        if (!document.querySelector('#health-refresh-time')) {
          return;
        }

        const refreshInterval = 30000; // 30 seconds
        const healthJsonUrl = '/langgraph-console/admin/health.json';
        const refreshTimeEl = document.querySelector('#health-refresh-time');
        const agentsSection = document.querySelector('.agent-status-table');

        // Start auto-refresh.
        setInterval(function () {
          fetch(healthJsonUrl, {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
            },
          })
            .then(function (response) {
              if (response.ok) {
                return response.json();
              }
              throw new Error('Failed to fetch health data');
            })
            .then(function (data) {
              // Update refresh timestamp.
              const now = new Date();
              const timeStr = now.getHours().toString().padStart(2, '0') + ':' +
                             now.getMinutes().toString().padStart(2, '0') + ':' +
                             now.getSeconds().toString().padStart(2, '0');
              if (refreshTimeEl) {
                refreshTimeEl.textContent = 'Last refreshed: ' + timeStr;
              }

              // Update orchestrator status if visible.
              const orchStatusEl = document.querySelector('[data-health-section="orchestrator"]');
              if (orchStatusEl && data.last_tick_time) {
                const ageSec = (Date.now() / 1000) - data.last_tick_time;
                let statusColor = 'red';
                let statusIcon = '✗';
                let statusText = 'Offline';

                if (ageSec < 300) { // 5 minutes
                  statusColor = 'green';
                  statusIcon = '✓';
                  statusText = 'Online (last tick ' + Math.floor(ageSec / 60) + 'm ago)';
                } else if (ageSec < 900) { // 15 minutes
                  statusColor = 'orange';
                  statusIcon = '⚠';
                  statusText = 'Slow (last tick ' + Math.floor(ageSec / 60) + 'm ago)';
                }

                // Update orchestrator status display.
                const statusBox = orchStatusEl.querySelector('.status-box');
                if (statusBox) {
                  statusBox.textContent = statusIcon + ' ' + statusText;
                  statusBox.style.borderColor = statusColor;
                  statusBox.style.color = statusColor;
                }
              }
            })
            .catch(function (error) {
              console.warn('Health dashboard refresh error:', error);
            });
        }, refreshInterval);
      });
    },
  };
})(Drupal, once);
