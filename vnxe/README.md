# First, edit the script to fill the usernames, vnxe ip address and passowrds

### To do the discovery
```shell
./vnxe/storages.php 172.17.32.131 sistema discovery
./vnxe/storages.php 172.17.32.131 procs discovery
./vnxe/storages.php 172.17.32.131 discos discovery
./vnxe/storages.php 172.17.32.131 bateria discovery
./vnxe/storages.php 172.17.32.131 pools discovery 
./vnxe/storages.php 172.17.32.131 fontes discovery
```

### To see discovered fields
```shell
- ./vnxe/storages.php 172.17.32.131 sistema debug
- ./vnxe/storages.php 172.17.32.131 procs debug
- ./vnxe/storages.php 172.17.32.131 discos debug
- ./vnxe/storages.php 172.17.32.131 bateria debug
- ./vnxe/storages.php 172.17.32.131 pools debug
- ./vnxe/storages.php 172.17.32.131 fontes debug
```

### To colect a field based on discovered fields
```shell
- ./vnxe/storages.php 172.17.32.131 discos disk_dae_2_11 Name
- ./vnxe/storages.php 172.17.32.131 pools global_pool_7 Total_space
- ./vnxe/storages.php 172.17.32.131 bateria BAT_SPA_0 Health_state
```
