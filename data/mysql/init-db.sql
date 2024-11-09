-- Permissão do usuário 'travel-user'
GRANT ALL PRIVILEGES ON `travel-orders`.* TO 'travel-user'@'%';
FLUSH PRIVILEGES;