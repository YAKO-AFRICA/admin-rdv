const url = window.location.origin;
// console.log('CRON RDV:', url);
// setInterval(() => {
//     fetch(`${url}/rdv/expire_rdv`)
//         .then(r => r.json())
//         .then(data => console.log('CRON RDV:', data))
//         .catch(err => console.error(err));
// }, 24 * 60 * 60 * 1000);


// function runCronRDV() {
//     fetch(`${url}/rdv/expire_rdv`)
//         .then(r => r.json())
//         .then(data => console.log('CRON RDV:', data))
//         .catch(err => console.error('CRON RDV ERREUR:', err));
// }

// runCronRDV(); // exécution immédiate
// setInterval(runCronRDV, 60 * 1000); // exécution toutes 1 minutes 
// setInterval(runCronRDV, );