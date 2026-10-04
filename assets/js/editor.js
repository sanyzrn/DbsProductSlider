/* Elementor editor helper: search box that appends item IDs to a text control. Editor only. */
(function () {
    'use strict';
    var config = window.pceEditor;
    if (!config) { return; }

    function setting(name) {
        return document.querySelector('#elementor-panel [data-setting="' + name + '"]');
    }

    function addId(targetName, id, note) {
        var input = setting(targetName);
        if (!input) { return; }
        var ids = input.value.split(/[\s,]+/).filter(Boolean);
        if (ids.indexOf(String(id)) === -1) { ids.push(String(id)); }
        input.value = ids.join(',');
        // Elementor stores text controls on the input event.
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
        note.textContent = config.i18n.added + ': ' + id;
    }

    function build(root) {
        root.setAttribute('data-ready', '1');
        var input = document.createElement('input');
        input.type = 'search';
        input.className = 'pce-picker-input';
        input.placeholder = config.i18n.placeholder;
        input.setAttribute('autocomplete', 'off');
        var note = document.createElement('div');
        note.className = 'pce-picker-note';
        note.setAttribute('role', 'status');
        var list = document.createElement('ul');
        list.className = 'pce-picker-results';
        list.style.cssText = 'list-style:none;margin:6px 0 0;padding:0;max-height:180px;overflow:auto';
        input.style.cssText = 'width:100%';
        root.appendChild(input);
        root.appendChild(note);
        root.appendChild(list);

        var timer = null;
        var request = 0;

        function show(rows) {
            list.textContent = '';
            if (!rows.length) { note.textContent = config.i18n.empty; return; }
            note.textContent = '';
            rows.forEach(function (row) {
                var item = document.createElement('li');
                var button = document.createElement('button');
                button.type = 'button';
                button.style.cssText = 'width:100%;text-align:start;padding:4px 6px;cursor:pointer;background:transparent;border:0;color:inherit';
                button.textContent = row.title + ' (#' + row.id + ')';
                button.addEventListener('click', function () { addId(root.getAttribute('data-target'), row.id, note); });
                item.appendChild(button);
                list.appendChild(item);
            });
        }

        function run() {
            var typeControl = root.getAttribute('data-type-control');
            var typeInput = typeControl ? setting(typeControl) : null;
            var postType = typeInput ? typeInput.value : root.getAttribute('data-post-type');
            var current = ++request;
            var url = config.ajaxUrl + '?action=' + encodeURIComponent(config.action) + '&nonce=' + encodeURIComponent(config.nonce) +
                '&post_type=' + encodeURIComponent(postType || '') + '&term=' + encodeURIComponent(input.value);
            fetch(url, { credentials: 'same-origin' })
                .then(function (response) { return response.json(); })
                .then(function (payload) {
                    if (current !== request) { return; }
                    if (!payload || !payload.success) { throw new Error('search'); }
                    show(payload.data);
                })
                .catch(function () { if (current === request) { list.textContent = ''; note.textContent = config.i18n.error; } });
        }

        input.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(run, 300); });
        input.addEventListener('focus', function () { if (!list.children.length) { run(); } });
    }

    function scan() {
        var roots = document.querySelectorAll('.pce-picker:not([data-ready])');
        for (var i = 0; i < roots.length; i++) { build(roots[i]); }
    }

    new MutationObserver(scan).observe(document.body, { childList: true, subtree: true });
    scan();
}());
