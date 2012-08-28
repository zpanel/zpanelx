
#include <arpa/nameser.h>
#include <lwres/lwres.h>
#include <strings.h>
#include <stdio.h>
#include <pthread.h>


#define BUFLEN 1024
#define MAXSTR 80
#define MAXTHREADS 50
#define MAXRBLS 40
#define DEFTTL 600

extern int errno;
extern int h_errno;


struct ipnode;
typedef struct ipnode *iplist;
struct ipnode {
   char *IP;
   iplist next;
};

iplist IPs;

pthread_mutex_t *mutexp;
pthread_mutex_t *mutexoutput;

char *dnsrbls[MAXRBLS];
int numrbls, numthreads, numqueries, defttl;

void do_queries () {
   iplist tIP;
   lwres_context_t *ctx = NULL;
   lwres_grbnresponse_t *response = NULL;
   int n, i;


   pthread_mutex_lock(mutexp);
   tIP = IPs;
   if (IPs != NULL) {
      IPs = tIP->next;
   }
   pthread_mutex_unlock(mutexp);

   while (tIP != NULL) {
//fprintf (stderr, "making query %s\n", tIP->IP); fflush(stderr);
      if (lwres_context_create(&ctx, NULL, NULL, NULL, 0) != 0) {
         fprintf (stderr, "Couldn't create context\n");
	 return;
      } else {
         lwres_conf_parse(ctx, lwres_resolv_conf);
         //pthread_mutex_lock(mutexoutput);
	 n = lwres_getrdatabyname(ctx, tIP->IP, ns_c_in, ns_t_a, 0, &response);
         //pthread_mutex_unlock(mutexoutput);
	 if (n == LWRES_R_SUCCESS) {
            printf ("%s,%d.%d.%d.%d,%d\n", tIP->IP, 
			    response->rdatas[0][0], response->rdatas[0][1],
			    response->rdatas[0][2], response->rdatas[0][3],
			    response->ttl);
	    //fprintf (stderr, "freeing response\n"); fflush(stderr);
	    lwres_grbnresponse_free(ctx, &response);
	 } else {
	    //fprintf (stderr, "Nothing found\n");
            printf ("%s, %s, %d\n", tIP->IP, tIP->IP, defttl);
	 }
	 //fprintf (stderr, "freeing context\n"); fflush(stderr);
	 lwres_context_destroy(&ctx);
	 //fprintf (stderr, "done freeing\n"); fflush(stderr);
      }

      pthread_mutex_lock(mutexp);
      tIP = IPs;
      if (IPs != NULL) {
         IPs = tIP->next;
      }
      pthread_mutex_unlock(mutexp);
   }
}

void GetRBLs() {
   char instr[MAXSTR];
   numrbls = 0;
   while ((fgets(instr, MAXSTR, stdin) != NULL) && (numrbls < MAXRBLS)) {
      instr[strlen(instr)-1] = 0;   // strip off newline
      if (strncmp(instr, "----------", 10) == 0) {
         return;
      }
      dnsrbls[numrbls] = (char *) malloc(strlen(instr)+1);
      if (dnsrbls[numrbls] == NULL) {
         fprintf (stderr, "Couldn't allocate memory for %d DNS RBLs\n", numrbls);
	 exit (10);
      } else {
         strcpy (dnsrbls[numrbls], instr);
         numrbls++;
      }
   }
}


main () {
   pthread_t threads[MAXTHREADS];
   char instr[MAXSTR];
   iplist tIP;
   int loop1;

   if (fgets(instr, MAXSTR, stdin) != NULL) {
      defttl = atoi(instr);
   }
   if (defttl < 0)
      defttl = DEFTTL;

   GetRBLs();

//   for (loop1=0; loop1<numrbls; loop1++)
//      fprintf (stderr, "%s\n", dnsrbls[loop1]);
//   fprintf (stderr, "----------\n");

   numqueries = 0;
   IPs = NULL;
   while (fgets(instr, MAXSTR, stdin) != NULL) {
      instr[strlen(instr)-1] = 0;
      for (loop1 = 0; loop1 < numrbls; loop1++) {
         tIP = (iplist)malloc(sizeof(struct ipnode));
         tIP->IP = (char *)malloc(strlen(instr)+strlen(dnsrbls[loop1])+2);
         strcpy (tIP->IP, instr);
	 strcat (tIP->IP, dnsrbls[loop1]);
         tIP->next = IPs;
         IPs = tIP;
         numqueries++;
      }
   }

//   fprintf (stderr, "%d queries to make\n", numqueries);
//   tIP = IPs;
//   while (tIP != NULL) {
//      fprintf (stderr, "%s\n", tIP->IP);
//      tIP = tIP->next;
//   }
//   fprintf (stderr, "done\n");
//   exit (0);

   mutexp=(pthread_mutex_t *) malloc(sizeof(pthread_mutex_t));
   pthread_mutex_init(mutexp, NULL);
   mutexoutput=(pthread_mutex_t *) malloc(sizeof(pthread_mutex_t));
   pthread_mutex_init(mutexoutput, NULL);

   numthreads = 0; // number of threads created successfully
   for (loop1 = 0; ((loop1<MAXTHREADS) && (loop1<numqueries)); loop1++) {
      if (pthread_create(&threads[loop1], NULL,
                         (void *) do_queries, NULL) != 0) {
	 fprintf (stderr, "Couldn't make more than %d threads\n", numthreads);
         break;
      } else {
         numthreads++;
      }
   }

   for (loop1 = 0; loop1 < numthreads ; loop1++) {
      pthread_join(threads[loop1], NULL);
   }

//do_queries();

   exit (0);
}

