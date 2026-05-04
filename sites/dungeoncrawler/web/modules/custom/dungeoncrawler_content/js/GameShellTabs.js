/**
 * @file
 * Tabbed shell controller for the simplified hexmap UI.
 */

function activateGameShellTab(shell, tabId) {
  const tabs = shell.querySelectorAll('[data-game-tab]');
  const panels = shell.querySelectorAll('.game-shell__panel');

  tabs.forEach((tab) => {
    const active = tab.dataset.gameTab === tabId;
    tab.classList.toggle('game-shell__tab--active', active);
    tab.setAttribute('aria-selected', active ? 'true' : 'false');
  });

  panels.forEach((panel) => {
    const active = panel.id === `game-panel-${tabId}`;
    panel.classList.toggle('game-shell__panel--active', active);
    panel.hidden = !active;
  });

  window.dispatchEvent(new Event('resize'));
}

function initGameShellTabs(shell) {
  if (!shell || shell.dataset.gameShellTabsBound === 'true') {
    return;
  }

  const tabs = shell.querySelectorAll('[data-game-tab]');
  const panels = shell.querySelectorAll('.game-shell__panel');
  if (!tabs.length || !panels.length) {
    return;
  }

  shell.dataset.gameShellTabsBound = 'true';

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      activateGameShellTab(shell, tab.dataset.gameTab);
    });
  });
}

document.querySelectorAll('[data-game-shell]').forEach(initGameShellTabs);
