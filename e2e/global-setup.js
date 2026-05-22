const { execFileSync } = require('node:child_process');
const path = require('node:path');

module.exports = async () => {
  execFileSync('php', [path.join(__dirname, 'setup-test-db.php')], {
    cwd: path.join(__dirname, '..'),
    env: {
      ...process.env,
      APP_ENV: 'test',
    },
    stdio: 'inherit',
  });
};
