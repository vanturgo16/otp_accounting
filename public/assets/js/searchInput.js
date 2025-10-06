document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    let currentMatchIndex = 0;
    let matches = [];
    input.addEventListener('input', function () {
        removeHighlights();
        const term = input.value.trim();
        if (term.length > 0) {
            matches = highlightMatches(term);
            currentMatchIndex = 0;
            scrollToMatch(currentMatchIndex);
        }
    });
    // Keyboard arrow up/down to navigate matches
    input.addEventListener('keydown', function (e) {
        if (matches.length === 0) return;

        if (e.key === 'ArrowDown') {
            currentMatchIndex = (currentMatchIndex + 1) % matches.length;
            scrollToMatch(currentMatchIndex);
            e.preventDefault();
        } else if (e.key === 'ArrowUp') {
            currentMatchIndex = (currentMatchIndex - 1 + matches.length) % matches.length;
            scrollToMatch(currentMatchIndex);
            e.preventDefault();
        }
    });
    function highlightMatches(term) {
        const regex = new RegExp(`(${term})`, 'gi');
        const elements = Array.from(document.body.querySelectorAll('*:not(script):not(style):not(input):not(textarea)'));
        let found = [];
        elements.forEach(el => {
            if (el.childNodes.length === 1 && el.childNodes[0].nodeType === 3) {
                const text = el.textContent;
                if (regex.test(text)) {
                    const newHTML = text.replace(regex, '<mark class="highlight">$1</mark>');
                    el.innerHTML = newHTML;
                    found.push(...el.querySelectorAll('mark.highlight'));
                }
            }
        });
        return found;
    }
    function removeHighlights() {
        document.querySelectorAll('mark.highlight').forEach(mark => {
            const parent = mark.parentNode;
            parent.replaceChild(document.createTextNode(mark.textContent), mark);
            parent.normalize(); // Merge text nodes
        });
    }
    function scrollToMatch(index) {
        if (matches.length > 0 && matches[index]) {
            matches.forEach(m => m.classList.remove('current-match'));
            matches[index].classList.add('current-match');
            matches[index].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});