/* The Computer Language Benchmarks Game
 * http://benchmarksgame.alioth.debian.org/

   contributed by Alex Burlyga
*/

#define _GNU_SOURCE
#include <pthread.h>
#include <sched.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <inttypes.h>

#define NUMBER_OF_THREADS 503

pthread_mutex_t cv_mutex = PTHREAD_MUTEX_INITIALIZER;
pthread_cond_t cv_main = PTHREAD_COND_INITIALIZER;
pthread_cond_t *cvs = NULL;
uint32_t token = 0;
uint32_t token_count = 1000;
uint32_t threads_started = 0;
uint32_t number_of_cpus = 0;

void *thread_function(void *arg) {
    uint32_t thread_num = *(uint32_t *)arg;
    uint32_t next_thread_num = (thread_num + 1) % NUMBER_OF_THREADS;
    cpu_set_t cpu_mask;

    CPU_ZERO(&cpu_mask);
    CPU_SET(0, &cpu_mask);
    pthread_setaffinity_np(pthread_self(), sizeof(cpu_set_t), &cpu_mask);

    pthread_mutex_lock(&cv_mutex);
    while (1) {
        threads_started++;
        pthread_cond_signal(&cv_main);
        pthread_cond_wait(cvs+thread_num, &cv_mutex);
        token++;
        if (token == token_count + 1) {
            printf("%d\n", thread_num + 1);
            token++;
            pthread_cond_signal(cvs+next_thread_num);
            pthread_mutex_unlock(&cv_mutex);
            break;
        } else if (token > token_count + 1) {
            pthread_cond_signal(cvs+next_thread_num);
            pthread_mutex_unlock(&cv_mutex);
            break;
        }
        pthread_cond_signal(cvs+next_thread_num);
    }

    pthread_exit(NULL);
}

int
main(int argc, char **argv) {
    int errno = 0;
    pthread_t *threads = NULL;
    uint32_t *thread_args = NULL;

    if (argc > 1) {
        token_count = strtol(argv[1], NULL, 0);
    }

    number_of_cpus = sysconf(_SC_NPROCESSORS_CONF);

    threads = (pthread_t *)malloc(sizeof(pthread_t)*NUMBER_OF_THREADS);
    if (threads == NULL) {
        perror("pthread_t array malloc");
        exit(1);
    }
    memset(threads, 0, sizeof(pthread_t)*NUMBER_OF_THREADS);

    thread_args = (uint32_t *)malloc(sizeof(uint32_t)*NUMBER_OF_THREADS);
    if (thread_args == NULL) {
        perror("thread_args array malloc");
        exit(1);
    }
    memset(thread_args, 0, sizeof(uint32_t)*NUMBER_OF_THREADS);

    cvs = (pthread_cond_t *)malloc(sizeof(pthread_cond_t)*NUMBER_OF_THREADS);
    if (cvs == NULL) {
        perror("cvs array malloc");
        exit(1);
    }

    pthread_mutex_lock(&cv_mutex);
    for (uint32_t i = 0; i < NUMBER_OF_THREADS; i++) {
        *(thread_args + i) = i;
        errno = pthread_cond_init(cvs+i, NULL);
        if (errno) {
            perror("pthread_cond_init");
            exit(1);
        }

        errno = pthread_create(threads+i, NULL, thread_function, (void *)(thread_args + i));
        if (errno) {
            perror("pthread_create");
            exit(1);
        }
    }

    while(threads_started < NUMBER_OF_THREADS) {
        pthread_cond_wait(&cv_main, &cv_mutex);
    }
    pthread_cond_signal(cvs);
    pthread_mutex_unlock(&cv_mutex);

    for (int i = 0; i < NUMBER_OF_THREADS; i++) {
        pthread_join(*(threads + i), NULL);
    }

    free(cvs);
    free(thread_args);
    free(threads);
    pthread_exit(NULL);
}
