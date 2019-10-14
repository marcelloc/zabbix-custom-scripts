# First, edit the script to fill the usernames, vnxe ip address and passowrds

### To do the discovery
```shell
./vnxe/storages.php 192.168.10.11 sistema discovery
./vnxe/storages.php 192.168.10.11 procs discovery
./vnxe/storages.php 192.168.10.11 discos discovery
./vnxe/storages.php 192.168.10.11 bateria discovery
./vnxe/storages.php 192.168.10.11 pools discovery
./vnxe/storages.php 192.168.10.11 luns discovery
./vnxe/storages.php 192.168.10.11 fontes discovery
```

### To see discovered fields
```shell
./vnxe/storages.php 192.168.10.11 sistema debug
./vnxe/storages.php 192.168.10.11 procs debug
./vnxe/storages.php 192.168.10.11 discos debug
./vnxe/storages.php 192.168.10.11 bateria debug
./vnxe/storages.php 192.168.10.11 pools debug
./vnxe/storages.php 192.168.10.11 luns discovery
./vnxe/storages.php 192.168.10.11 fontes debug
```

### To colect a field based on discovered fields
```shell
./vnxe/storages.php 192.168.10.11 discos disk_dae_2_11 Name
./vnxe/storages.php 192.168.10.11 pools global_pool_7 Total_space
./vnxe/storages.php 192.168.10.11 bateria BAT_SPA_0 Health_state
```
