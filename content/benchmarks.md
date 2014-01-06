---
name=benchmarks
title=Benchmarks
---

# Greengrape Benchmarks

Here are some benchmarks for running greengrape. What I did is loaded the
homepage using apache benchmark (`ab`) and making 100 requests and finding out
the statistics for requests per second (RPS) and time per request (TPR).

| Config                             | RPS    | TPR (ms)|
| ---------------------------------- | -----: | ------: |
| Without cache, without autoloading | 9.51   | 109.852 |
| With cache, without autoloading    | 101.13 | 9.888   |
| Without cache, with autoloading    | 9.61   | 104.021 |
| With cache, with autoloading       | 298.45 | 3.351   |

Without the autoloading, every single greengrape class file is included in
`init.php`. When the cache is on only a few classes need to be loaded (maybe 1
or 2), so the overall filesystem load is much less.

Obviously autoloading is the way to go, and in production, the cache should
definitely be turned on.

The output of each test follows below.

## Without cache, without autoloading

```
Benchmarking greengrape.lvh.me (be patient).....done


Server Software:        Apache/2.2.24
Server Hostname:        greengrape.lvh.me
Server Port:            80

Document Path:          /
Document Length:        4915 bytes

Concurrency Level:      1
Time taken for tests:   10.518 seconds
Complete requests:      100
Failed requests:        0
Write errors:           0
Total transferred:      516800 bytes
HTML transferred:       491500 bytes
Requests per second:    9.51 [#/sec] (mean)
Time per request:       105.177 [ms] (mean)
Time per request:       105.177 [ms] (mean, across all concurrent requests)
Transfer rate:          47.98 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    0   0.1      0       0
Processing:   101  105  10.6    103     205
Waiting:      101  105  10.6    103     205
Total:        101  105  10.6    103     205

Percentage of the requests served within a certain time (ms)
  50%    103
  66%    104
  75%    106
  80%    106
  90%    108
  95%    109
  98%    125
  99%    205
 100%    205 (longest request)
```

## Without cache, without autoloading

```
Benchmarking learn-oop.lvh.me (be patient).....done


Server Software:        Apache/2.2.24
Server Hostname:        learn-oop.lvh.me
Server Port:            80

Document Path:          /
Document Length:        4903 bytes

Concurrency Level:      1
Time taken for tests:   10.985 seconds
Complete requests:      100
Failed requests:        0
Write errors:           0
Total transferred:      515600 bytes
HTML transferred:       490300 bytes
Requests per second:    9.10 [#/sec] (mean)
Time per request:       109.852 [ms] (mean)
Time per request:       109.852 [ms] (mean, across all concurrent requests)
Transfer rate:          45.84 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:       52   64  19.9     55     121
Processing:     0   45  19.6     52      73
Waiting:        0   45  19.6     52      73
Total:        104  110   4.5    108     126

Percentage of the requests served within a certain time (ms)
  50%    108
  66%    110
  75%    112
  80%    113
  90%    115
  95%    121
  98%    123
  99%    126
 100%    126 (longest request)
```

## With cache, no autoloading

```
Benchmarking learn-oop.lvh.me (be patient).....done


Server Software:        Apache/2.2.24
Server Hostname:        learn-oop.lvh.me
Server Port:            80

Document Path:          /
Document Length:        4903 bytes

Concurrency Level:      1
Time taken for tests:   0.989 seconds
Complete requests:      100
Failed requests:        99
   (Connect: 0, Receive: 0, Length: 99, Exceptions: 0)
Write errors:           0
Total transferred:      517679 bytes
HTML transferred:       492379 bytes
Requests per second:    101.13 [#/sec] (mean)
Time per request:       9.888 [ms] (mean)
Time per request:       9.888 [ms] (mean, across all concurrent requests)
Transfer rate:          511.28 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    0   0.1      0       0
Processing:     8   10  11.2      8     120
Waiting:        8   10  11.2      8     120
Total:          8   10  11.2      8     121

Percentage of the requests served within a certain time (ms)
  50%      8
  66%      8
  75%      9
  80%      9
  90%     11
  95%     12
  98%     12
  99%    121
 100%    121 (longest request)
```

## Without cache, with autoloading

```
Benchmarking learn-oop.lvh.me (be patient).....done


Server Software:        Apache/2.2.24
Server Hostname:        learn-oop.lvh.me
Server Port:            80

Document Path:          /
Document Length:        4903 bytes

Concurrency Level:      1
Time taken for tests:   10.402 seconds
Complete requests:      100
Failed requests:        0
Write errors:           0
Total transferred:      515600 bytes
HTML transferred:       490300 bytes
Requests per second:    9.61 [#/sec] (mean)
Time per request:       104.021 [ms] (mean)
Time per request:       104.021 [ms] (mean, across all concurrent requests)
Transfer rate:          48.41 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    0   0.1      0       0
Processing:   101  104   2.0    103     111
Waiting:      101  104   1.9    103     111
Total:        102  104   1.9    103     111

Percentage of the requests served within a certain time (ms)
  50%    103
  66%    104
  75%    106
  80%    106
  90%    107
  95%    107
  98%    110
  99%    111
 100%    111 (longest request)
```

## With cache, with autoloading

```
Benchmarking learn-oop.lvh.me (be patient).....done


Server Software:        Apache/2.2.24
Server Hostname:        learn-oop.lvh.me
Server Port:            80

Document Path:          /
Document Length:        4924 bytes

Concurrency Level:      1
Time taken for tests:   0.335 seconds
Complete requests:      100
Failed requests:        0
Write errors:           0
Total transferred:      517700 bytes
HTML transferred:       492400 bytes
Requests per second:    298.45 [#/sec] (mean)
Time per request:       3.351 [ms] (mean)
Time per request:       3.351 [ms] (mean, across all concurrent requests)
Transfer rate:          1508.86 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        3    3   0.3      3       5
Processing:     0    0   0.0      0       0
Waiting:        0    0   0.0      0       0
Total:          3    3   0.3      3       5

Percentage of the requests served within a certain time (ms)
  50%      3
  66%      3
  75%      3
  80%      3
  90%      3
  95%      4
  98%      5
  99%      5
 100%      5 (longest request)
```
