// Helper: format number into bold + muted decimals
function formatAmountDT(amount) {
    const formatted = numberFormat(amount, 2, ',', '.');
    const [whole, decimal] = formatted.split(',');
    return decimal
        ? `<span class="text-bold">${whole}</span><span class="text-muted">,${decimal}</span>`
        : `<span class="text-bold">${whole}</span>`;
}
// Helper: badge builder
function badgeDT(color, text, icon = '') {
    return `<span class="badge bg-${color} text-white">${icon ? `<span class="mdi ${icon}"></span> | ` : ''}${text}</span>`;
}
// Helper: format user + timestamp
function userTimestampDT(user, timestamp) {
    if (!user) user = '-';
    if (!timestamp) return user;
    const d = new Date(timestamp);
    const formatted = d.toISOString().slice(0, 19).replace('T', ' ');
    return `${user}<br><b>At. </b>${formatted}`;
}
// Helper: format timestamp
function timestampDT(timestamp) {
    const d = new Date(timestamp);
    const formatted = d.toISOString().slice(0, 19).replace('T', ' ');
    return `${formatted}`;
}



// Helper: format user + timestamp
function fmtActionBy(username, datetime) {
    if (!datetime) return username;
    const d = new Date(datetime);
    const formatted =
        d.getFullYear() + '-' +
        String(d.getMonth() + 1).padStart(2, '0') + '-' +
        String(d.getDate()).padStart(2, '0') + ' ' +
        String(d.getHours()).padStart(2, '0') + ':' +
        String(d.getMinutes()).padStart(2, '0') + ':' +
        String(d.getSeconds()).padStart(2, '0');
    return `
        ${username}<br>
        <span class="text-nowrap"><b>At.</b> ${formatted}</span>
    `;
}
// Helper: format timestamp
function fmtTimeDT(datetime) {
    const d = new Date(datetime);
    const formatted =
        d.getFullYear() + '-' +
        String(d.getMonth() + 1).padStart(2, '0') + '-' +
        String(d.getDate()).padStart(2, '0') + ' ' +
        String(d.getHours()).padStart(2, '0') + ':' +
        String(d.getMinutes()).padStart(2, '0') + ':' +
        String(d.getSeconds()).padStart(2, '0');
    return `<span class="text-nowrap">${formatted}</span>`;
}
