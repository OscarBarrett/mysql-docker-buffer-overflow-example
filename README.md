# mysql docker 5.7.16/5.7.17 buffer overflow
A buffer overflow occurs when:
- You are using `mysql:5.7.16` or `mysql:5.7.17`
- You map a nonexistent/empty folder to `/etc/mysql/conf.d`
- Your docker-compose project has a long name
- And you connect to mysql.

## To break it:

`docker-compose --project-name some-long-name-that-will-break-mysql up`

Wait for mysql to come up. Then run the following in another terminal:

```bash
$ docker-compose --project-name some-long-name-that-will-break-mysql run web php -f index.php mysql-5.7.15 # No issues here
$ docker-compose --project-name some-long-name-that-will-break-mysql run web php -f index.php mysql-5.7.17-novol # This works too
$ docker-compose --project-name some-long-name-that-will-break-mysql run web php -f index.php mysql-5.7.17
PHP Warning:  mysqli_real_connect(): MySQL server has gone away in /app/index.php on line 4
PHP Warning:  mysqli_real_connect(): (HY000/2006): MySQL server has gone away in /app/index.php on line 4
```

Trace:
```
mysql-5.7.17_1  | *** buffer overflow detected ***: mysqld terminated
mysql-5.7.17_1  | ======= Backtrace: =========
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libc.so.6(+0x731af)[0x7f0597c681af]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libc.so.6(__fortify_fail+0x37)[0x7f0597cedaa7]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libc.so.6(+0xf6cc0)[0x7f0597cebcc0]
mysql-5.7.17_1  | mysqld(_Z19find_or_create_hostP10PFS_threadPKcj+0x395)[0xeadfe5]
mysql-5.7.17_1  | mysqld(_Z22find_or_create_accountP10PFS_threadPKcjS2_j+0x3d2)[0xef8792]
mysql-5.7.17_1  | mysqld(_Z18set_thread_accountP10PFS_thread+0x36)[0xeb5d96]
mysql-5.7.17_1  | mysqld(pfs_set_thread_account_v1+0xa0)[0xe97ef0]
mysql-5.7.17_1  | mysqld(_Z16acl_authenticateP3THD19enum_server_command+0xfb2)[0x7b3ac2]
mysql-5.7.17_1  | mysqld[0xc1755d]
mysql-5.7.17_1  | mysqld(_Z22thd_prepare_connectionP3THD+0x53)[0xc18613]
mysql-5.7.17_1  | mysqld(handle_connection+0x24f)[0xd15f7f]
mysql-5.7.17_1  | mysqld(pfs_spawn_thread+0x1b4)[0xe97824]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libpthread.so.0(+0x8064)[0x7f059921a064]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libc.so.6(clone+0x6d)[0x7f0597cdd62d]
mysql-5.7.17_1  | ======= Memory map: ========
mysql-5.7.17_1  | 00400000-01a3c000 r-xp 00000000 00:2c 66                                 /usr/sbin/mysqld
mysql-5.7.17_1  | 01c3c000-01d2e000 r--p 0163c000 00:2c 66                                 /usr/sbin/mysqld
mysql-5.7.17_1  | 01d2e000-01dda000 rw-p 0172e000 00:2c 66                                 /usr/sbin/mysqld
mysql-5.7.17_1  | 01dda000-01e99000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 03702000-04278000 rw-p 00000000 00:00 0                                  [heap]
mysql-5.7.17_1  | 7f0544000000-7f0544021000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0544021000-7f0548000000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f054c000000-7f054c426000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f054c426000-7f0550000000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0550000000-7f0550021000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0550021000-7f0554000000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0554000000-7f0554021000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0554021000-7f0558000000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f055c000000-7f055c021000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f055c021000-7f0560000000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0561ffc000-7f0561ffd000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0561ffd000-7f05627fd000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f05627fd000-7f05627fe000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f05627fe000-7f0562ffe000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0562ffe000-7f0562fff000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0562fff000-7f05637ff000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f05637ff000-7f0563800000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0563800000-7f0564000000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0564000000-7f0564024000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0564024000-7f0568000000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0568000000-7f0568024000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0568024000-7f056c000000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f056c000000-7f056c024000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f056c024000-7f0570000000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0570000000-7f0570021000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0570021000-7f0574000000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f05743dc000-7f05743f0000 r-xp 00000000 00:2c 2026                       /lib/x86_64-linux-gnu/libresolv-2.19.so
mysql-5.7.17_1  | 7f05743f0000-7f05745ef000 ---p 00014000 00:2c 2026                       /lib/x86_64-linux-gnu/libresolv-2.19.so
mysql-5.7.17_1  | 7f05745ef000-7f05745f0000 r--p 00013000 00:2c 2026                       /lib/x86_64-linux-gnu/libresolv-2.19.so
mysql-5.7.17_1  | 7f05745f0000-7f05745f1000 rw-p 00014000 00:2c 2026                       /lib/x86_64-linux-gnu/libresolv-2.19.so
mysql-5.7.17_1  | 7f05745f1000-7f05745f3000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f05745f3000-7f05745f8000 r-xp 00000000 00:2c 131                        /lib/x86_64-linux-gnu/libnss_dns-2.19.so
mysql-5.7.17_1  | 7f05745f8000-7f05747f7000 ---p 00005000 00:2c 131                        /lib/x86_64-linux-gnu/libnss_dns-2.19.so
mysql-5.7.17_1  | 7f05747f7000-7f05747f8000 r--p 00004000 00:2c 131                        /lib/x86_64-linux-gnu/libnss_dns-2.19.so
mysql-5.7.17_1  | 7f05747f8000-7f05747f9000 rw-p 00005000 00:2c 131                        /lib/x86_64-linux-gnu/libnss_dns-2.19.so
mysql-5.7.17_1  | 7f05747f9000-7f05747fa000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f05747fa000-7f0574ffa000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0574ffa000-7f0574ffb000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0574ffb000-7f05757fb000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f05757fb000-7f05757fc000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f05757fc000-7f0575ffc000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0575ffc000-7f0575ffd000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0575ffd000-7f05767fd000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f05767fd000-7f05767fe000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f05767fe000-7f0576ffe000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0576ffe000-7f0576fff000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0576fff000-7f05777ff000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f05777ff000-7f0577800000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0577800000-7f0578000000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0578000000-7f0578021000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0578021000-7f057c000000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f057c053000-7f057c715000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f057c715000-7f057c716000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f057c716000-7f057cf16000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f057cf16000-7f057cf17000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f057cf17000-7f057d93d000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f057d93d000-7f057d93e000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f057d93e000-7f057e13e000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f057e13e000-7f057e13f000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f057e13f000-7f057e93f000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f057e93f000-7f057e940000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f057e940000-7f057f140000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f057f140000-7f057f141000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f057f141000-7f057f941000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f057f941000-7f057f942000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f057f942000-7f0580142000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0580142000-7f0580143000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0580143000-7f0580943000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0580943000-7f0580944000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0580944000-7f0581144000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0581144000-7f0581145000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0581145000-7f0581945000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0581945000-7f0581946000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0581946000-7f0582146000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0582146000-7f0582147000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0582147000-7f0582947000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0582947000-7f0582948000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f0582948000-7f058323e000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f058324e000-7f0583259000 r-xp 00000000 00:2c 57                         /lib/x86_64-linux-gnu/libnss_files-2.19.so
mysql-5.7.17_1  | 7f0583259000-7f0583458000 ---p 0000b000 00:2c 57                         /lib/x86_64-linux-gnu/libnss_files-2.19.so
mysql-5.7.17_1  | 7f0583458000-7f0583459000 r--p 0000a000 00:2c 57                         /lib/x86_64-linux-gnu/libnss_files-2.19.so
mysql-5.7.17_1  | 7f0583459000-7f058345a000 rw-p 0000b000 00:2c 57                         /lib/x86_64-linux-gnu/libnss_files-2.19.so
mysql-5.7.17_1  | 7f058345a000-7f058345b000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f05835d9000-7f05835da000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f05835da000-7f05835db000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f05835db000-7f058361b000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f058361b000-7f058361c000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f058361c000-7f058dc99000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f058dc99000-7f058dc9e000 rw-s 00000000 00:0c 205610                     /[aio] (deleted)
mysql-5.7.17_1  | 7f058dc9e000-7f058dca3000 rw-s 00000000 00:0c 205609                     /[aio] (deleted)
mysql-5.7.17_1  | 7f058dca3000-7f058dca8000 rw-s 00000000 00:0c 205608                     /[aio] (deleted)
mysql-5.7.17_1  | 7f058dca8000-7f058dcad000 rw-s 00000000 00:0c 205607                     /[aio] (deleted)
mysql-5.7.17_1  | 7f058dcad000-7f058dcec000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f058dcec000-7f058dcf1000 rw-s 00000000 00:0c 205606                     /[aio] (deleted)
mysql-5.7.17_1  | 7f058dcf1000-7f058dcf6000 rw-s 00000000 00:0c 205605                     /[aio] (deleted)
mysql-5.7.17_1  | 7f058dcf6000-7f058dcfb000 rw-s 00000000 00:0c 205604                     /[aio] (deleted)
mysql-5.7.17_1  | 7f058dcfb000-7f058dd00000 rw-s 00000000 00:0c 205603                     /[aio] (deleted)
mysql-5.7.17_1  | 7f058dd00000-7f058dd05000 rw-s 00000000 00:0c 205602                     /[aio] (deleted)
mysql-5.7.17_1  | 7f058dd05000-7f058dd0a000 rw-s 00000000 00:0c 205601                     /[aio] (deleted)
mysql-5.7.17_1  | 7f058dd0a000-7f058ea6a000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f058ea6a000-7f058ea6b000 ---p 00000000 00:00 0
mysql-5.7.17_1  | 7f058ea6b000-7f0597bf5000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0597bf5000-7f0597d96000 r-xp 00000000 00:2c 46                         /lib/x86_64-linux-gnu/libc-2.19.so
mysql-5.7.17_1  | 7f0597d96000-7f0597f96000 ---p 001a1000 00:2c 46                         /lib/x86_64-linux-gnu/libc-2.19.so
mysql-5.7.17_1  | 7f0597f96000-7f0597f9a000 r--p 001a1000 00:2c 46                         /lib/x86_64-linux-gnu/libc-2.19.so
mysql-5.7.17_1  | 7f0597f9a000-7f0597f9c000 rw-p 001a5000 00:2c 46                         /lib/x86_64-linux-gnu/libc-2.19.so
mysql-5.7.17_1  | 7f0597f9c000-7f0597fa0000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0597fa0000-7f0597fb6000 r-xp 00000000 00:2c 81                         /lib/x86_64-linux-gnu/libgcc_s.so.1
mysql-5.7.17_1  | 7f0597fb6000-7f05981b5000 ---p 00016000 00:2c 81                         /lib/x86_64-linux-gnu/libgcc_s.so.1
mysql-5.7.17_1  | 7f05981b5000-7f05981b6000 rw-p 00015000 00:2c 81                         /lib/x86_64-linux-gnu/libgcc_s.so.1
mysql-5.7.17_1  | 7f05981b6000-7f05982b6000 r-xp 00000000 00:2c 80                         /lib/x86_64-linux-gnu/libm-2.19.so
mysql-5.7.17_1  | 7f05982b6000-7f05984b5000 ---p 00100000 00:2c 80                         /lib/x86_64-linux-gnu/libm-2.19.so
mysql-5.7.17_1  | 7f05984b5000-7f05984b6000 r--p 000ff000 00:2c 80                         /lib/x86_64-linux-gnu/libm-2.19.so
mysql-5.7.17_1  | 7f05984b6000-7f05984b7000 rw-p 00100000 00:2c 80                         /lib/x86_64-linux-gnu/libm-2.19.so
mysql-5.7.17_1  | 7f05984b7000-7f05985a3000 r-xp 00000000 00:2c 78                         /usr/lib/x86_64-linux-gnu/libstdc++.so.6.0.20
mysql-5.7.17_1  | 7f05985a3000-7f05987a3000 ---p 000ec000 00:2c 78                         /usr/lib/x86_64-linux-gnu/libstdc++.so.6.0.20
mysql-5.7.17_1  | 7f05987a3000-7f05987ab000 r--p 000:2c 78                         /usr/lib/x86_64-linux-gnu/libstdc++.so.6.0.20
mysql-5.7.17_1  | 7f05987ab000-7f05987ad000 rw-p 000f4000 00:2c 78                         /usr/lib/x86_64-linux-gnu/libstdc++.so.6.0.20
mysql-5.7.17_1  | 7f05987ad000-7f05987c2000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f05987c2000-7f05987c9000 r-xp 00000000 00:2c 76                         /lib/x86_64-linux-gnu/librt-2.19.so
mysql-5.7.17_1  | 7f05987c9000-7f05989c8000 ---p 00007000 00:2c 76                         /lib/x86_64-linux-gnu/librt-2.19.so
mysql-5.7.17_1  | 7f05989c8000-7f05989c9000 r--p 00006000 00:2c 76                         /lib/x86_64-linux-gnu/librt-2.19.so
mysql-5.7.17_1  | 7f05989c9000-7f05989ca000 rw-p 00007000 00:2c 76                         /lib/x86_64-linux-gnu/librt-2.19.so
mysql-5.7.17_1  | 7f05989ca000-7f05989cd000 r-xp 00000000 00:2c 44                         /lib/x86_64-linux-gnu/libdl-2.19.so
mysql-5.7.17_1  | 7f05989cd000-7f0598bcc000 ---p 00003000 00:2c 44                         /lib/x86_64-linux-gnu/libdl-2.19.so
mysql-5.7.17_1  | 7f0598bcc000-7f0598bcd000 r--p 00002000 00:2c 44                         /lib/x86_64-linux-gnu/libdl-2.19.so
mysql-5.7.17_1  | 7f0598bcd000-7f0598bce000 rw-p 00003000 00:2c 44                         /lib/x86_64-linux-gnu/libdl-2.19.so
mysql-5.7.17_1  | 7f0598bce000-7f0598bd6000 r-xp 00000000 00:2c 74                         /lib/x86_64-linux-gnu/libcrypt-2.19.so
mysql-5.7.17_1  | 7f0598bd6000-7f0598dd5000 ---p 00008000 00:2c 74                         /lib/x86_64-linux-gnu/libcrypt-2.19.so
mysql-5.7.17_1  | 7f0598dd5000-7f0598dd6000 r--p 00007000 00:2c 74                         /lib/x86_64-linux-gnu/libcrypt-2.19.so
mysql-5.7.17_1  | 7f0598dd6000-7f0598dd7000 rw-p 00008000 00:2c 74                         /lib/x86_64-linux-gnu/libcrypt-2.19.so
mysql-5.7.17_1  | 7f0598dd7000-7f0598e05000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0598e05000-7f0598e0f000 r-xp 00000000 00:2c 72                         /usr/lib/x86_64-linux-gnu/libnuma.so.1.0.0
mysql-5.7.17_1  | 7f0598e0f000-7f059900e000 ---p 0000a000 00:2c 72                         /usr/lib/x86_64-linux-gnu/libnuma.so.1.0.0
mysql-5.7.17_1  | 7f059900e000-7f059900f000 r--p 00009000 00:2c 72                         /usr/lib/x86_64-linux-gnu/libnuma.so.1.0.0
mysql-5.7.17_1  | 7f059900f000-7f0599010000 rw-p 0000a000 00:2c 72                         /usr/lib/x86_64-linux-gnu/libnuma.so.1.0.0
mysql-5.7.17_1  | 7f0599010000-7f0599011000 r-xp 00000000 00:2c 68                         /lib/x86_64-linux-gnu/libaio.so.1.0.1
mysql-5.7.17_1  | 7f0599011000-7f0599210000 ---p 00001000 00:2c 68                         /lib/x86_64-linux-gnu/libaio.so.1.0.1
mysql-5.7.17_1  | 7f0599210000-7f0599211000 r--p 00000000 00:2c 68                         /lib/x86_64-linux-gnu/libaio.so.1.0.1
mysql-5.7.17_1  | 7f0599211000-7f0599212000 rw-p 00001000 00:2c 68                         /lib/x86_64-linux-gnu/libaio.so.1.0.1
mysql-5.7.17_1  | 7f0599212000-7f059922a000 r-xp 00000000 00:2c 65                         /lib/x86_64-linux-gnu/libpthread-2.19.so
mysql-5.7.17_1  | 7f059922a000-7f0599429000 ---p 00018000 00:2c 65                         /lib/x86_64-linux-gnu/libpthread-2.19.so
mysql-5.7.17_1  | 7f0599429000-7f059942a000 r--p 00017000 00:2c 65                         /lib/x86_64-linux-gnu/libpthread-2.19.so
mysql-5.7.17_1  | 7f059942a000-7f059942b000 rw-p 00018000 00:2c 65                         /lib/x86_64-linux-gnu/libpthread-2.19.so
mysql-5.7.17_1  | 7f059942b000-7f059942f000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f059942f000-7f059944f000 r-xp 00000000 00:2c 37                         /lib/x86_64-linux-gnu/ld-2.19.so
mysql-5.7.17_1  | 7f059944f000-7f0599450000 rw	.-p 00000000 00:00 0
mysql-5.7.17_1  | 7f0599450000-7f0599452000 rw-s 00000000 00:0c 205611                     /[aio] (deleted)
mysql-5.7.17_1  | 7f0599452000-7f0599453000 rw-s 00000000 00:0c 205600                     /[aio] (deleted)
mysql-5.7.17_1  | 7f0599453000-7f059964c000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f059964c000-7f059964f000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7f059964f000-7f0599650000 r--p 00020000 00:2c 37                         /lib/x86_64-linux-gnu/ld-2.19.so
mysql-5.7.17_1  | 7f0599650000-7f0599651000 rw-p 00021000 00:2c 37                         /lib/x86_64-linux-gnu/ld-2.19.so
mysql-5.7.17_1  | 7f0599651000-7f0599652000 rw-p 00000000 00:00 0
mysql-5.7.17_1  | 7ffee0a27000-7ffee0a48000 rw-p 00000000 00:00 0                          [stack]
mysql-5.7.17_1  | 7ffee0b2f000-7ffee0b31000 r--p 00000000 00:00 0                          [vvar]
mysql-5.7.17_1  | 7ffee0b31000-7ffee0b33000 r-xp 00000000 00:00 0                          [vdso]
mysql-5.7.17_1  | ffffffffff600000-ffffffffff601000 r-xp 00000000 00:00 0                  [vsyscall]
mysql-5.7.17_1  | 08:40:24 UTC - mysqld got signal 6 ;
mysql-5.7.17_1  | This could be because you hit a bug. It is also possible that this binary
mysql-5.7.17_1  | or one of the libraries it was linked against is corrupt, improperly built,
mysql-5.7.17_1  | or misconfigured. This error can also be caused by malfunctioning hardware.
mysql-5.7.17_1  | Attempting to collect some information that could help diagnose the problem.
mysql-5.7.17_1  | As this is a crash and something is definitely wrong, the information
mysql-5.7.17_1  | collection process might fail.
mysql-5.7.17_1  |
mysql-5.7.17_1  | key_buffer_size=8388608
mysql-5.7.17_1  | read_buffer_size=131072
mysql-5.7.17_1  | max_used_connections=1
mysql-5.7.17_1  | max_threads=151
mysql-5.7.17_1  | thread_count=1
mysql-5.7.17_1  | connection_count=1
mysql-5.7.17_1  | It is possible that mysqld could use up to
mysql-5.7.17_1  | key_buffer_size + (read_buffer_size + sort_buffer_size)*max_threads = 68190 K  bytes of memory
mysql-5.7.17_1  | Hope that's ok; if not, decrease some variables in the equation.
mysql-5.7.17_1  |
mysql-5.7.17_1  | Thread pointer: 0x7f0544000ae0
mysql-5.7.17_1  | Attempting backtrace. You can use the following information to find out
mysql-5.7.17_1  | where mysqld died. If you see no messages after this, something went
mysql-5.7.17_1  | terribly wrong...
mysql-5.7.17_1  | stack_bottom = 7f0583619e80 thread_stack 0x40000
mysql-5.7.17_1  | mysqld(my_print_stacktrace+0x2c)[0xe7fdcc]
mysql-5.7.17_1  | mysqld(handle_fatal_signal+0x459)[0x7a9d39]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libpthread.so.0(+0xf890)[0x7f0599221890]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libc.so.6(gsignal+0x37)[0x7f0597c2a067]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libc.so.6(abort+0x148)[0x7f0597c2b448]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libc.so.6(+0x731b4)[0x7f0597c681b4]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libc.so.6(__fortify_fail+0x37)[0x7f0597cedaa7]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libc.so.6(+0xf6cc0)[0x7f0597cebcc0]
mysql-5.7.17_1  | Amysqld(_Z19find_or_create_hostP10PFS_threadPKcj+0x395)[0xeadfe5]
mysql-5.7.17_1  | Hmysqld(_Z22find_or_create_accountP10PFS_threadPKcjS2_j+0x3d2)[0xef8792]
mysql-5.7.17_1  | ;mysqld(_Z18set_thread_accountP10PFS_thread+0x36)[0xeb5d96]
mysql-5.7.17_1  | 1mysqld(pfs_set_thread_account_v1+0xa0)[0xe97ef0]
mysql-5.7.17_1  | Gmysqld(_Z16acl_authenticateP3THD19enum_server_command+0xfb2)[0x7b3ac2]
mysql-5.7.17_1  | mysqld[0xc1755d]
mysql-5.7.17_1  | mysqld(_Z22thd_prepare_connectionP3THD+0x53)[0xc18613]
mysql-5.7.17_1  | mysqld(handle_connection+0x24f)[0xd15f7f]
mysql-5.7.17_1  | mysqld(pfs_spawn_thread+0x1b4)[0xe97824]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libpthread.so.0(+0x8064)[0x7f059921a064]
mysql-5.7.17_1  | /lib/x86_64-linux-gnu/libc.so.6(clone+0x6d)[0x7f0597cdd62d]
mysql-5.7.17_1  |
mysql-5.7.17_1  | Trying to get some variables.
mysql-5.7.17_1  | Some pointers may be invalid and cause the dump to abort.
mysql-5.7.17_1  | Query (0): Connection ID (thread ID): 3
mysql-5.7.17_1  | Status: NOT_KILLED
mysql-5.7.17_1  |
mysql-5.7.17_1  | The manual page at http://dev.mysql.com/doc/mysql/en/crashing.html contains
mysql-5.7.17_1  | information that should help you find out what is causing the crash.
somelongnamethatwillbreakmysql_mysql-5.7.17_1 exited with code 2
```


## To make it work:


`docker-compose --project-name some-short-name up`

When the mysql services are up:

```bash
$ docker-compose --project-name some-short-name run web php -f index.php mysql-5.7.15 # No issues here
$ docker-compose --project-name some-short-name run web php -f index.php mysql-5.7.17 # Nothing broke here either
```
