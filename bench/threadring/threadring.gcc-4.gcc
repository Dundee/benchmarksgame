/* The Computer Language Benchmarks Game
 * http://benchmarksgame.alioth.debian.org/

   Contributed by Andrew Gottemoller
*/
#include <pthread.h>
#include <semaphore.h>
#include <stdio.h>
#include <stdlib.h>


#define LIKELY(expression)   __builtin_expect(!!(expression), 1)
#define UNLIKELY(expression) __builtin_expect(!!(expression), 0)


#define THREAD_COUNT 503
#define PASS_COUNT   1000

#define THREAD_CREATED 0x01


struct thread_data
{
   unsigned int flags;
   unsigned int id;

   sem_t pass_semaphore;

   pthread_t thread;
};


static struct thread_data passing_threads[THREAD_COUNT] = {{0}};
static unsigned int      passes_remaining           = PASS_COUNT+1;


static inline void  CreateThread (unsigned int);
static void*      PerformPass  (void*);


static inline void CreateThread (unsigned int id)
{
   struct thread_data* restrict thread;

   thread = &passing_threads[id];

   thread->id = id;

   sem_init(&thread->pass_semaphore, 0, 0);
   pthread_create(&thread->thread, NULL, &PerformPass, thread);

   __sync_fetch_and_or(&thread->flags, THREAD_CREATED);
}

static void* PerformPass (void* argument)
{
   struct thread_data* restrict thread;
   struct thread_data* restrict next_thread;
   unsigned int             id;
   unsigned int             next_id;
   unsigned int             thread_ready;

   thread = argument;

   id     = thread->id;
   next_id = (id+1)%THREAD_COUNT;

   next_thread = &passing_threads[next_id];

   sem_wait(&thread->pass_semaphore);

   passes_remaining--;
   if(LIKELY(passes_remaining != 0))
   {
      do
      {
         thread_ready = __sync_fetch_and_or(&next_thread->flags, 0);
      }while(!(thread_ready&THREAD_CREATED));

      while(1)
      {
         sem_post(&next_thread->pass_semaphore);
         sem_wait(&thread->pass_semaphore);

         passes_remaining--;
         if(UNLIKELY(passes_remaining == 0))
            break;
      }
   }

   printf("%d\n", id+1);

   exit(EXIT_SUCCESS);
}


int main (int argument_count, char** arguments)
{
   struct thread_data* restrict initial_thread;

   if(argument_count > 1)
      passes_remaining = atoi(arguments[1])+1;

   CreateThread(0);

   initial_thread = &passing_threads[0];

   sem_post(&initial_thread->pass_semaphore);

   for(unsigned int index = 1; index < THREAD_COUNT; index++)
      CreateThread(index);

   pthread_join(initial_thread->thread, NULL);

   return 0;
}
