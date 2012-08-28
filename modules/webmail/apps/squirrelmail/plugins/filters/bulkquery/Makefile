
DESTDIR=/usr/local/bin
BINDROOT=/usr/local

# Solaris
#CC=gcc
#CFLAGS=-I$(BINDROOT)/bind/include -I$(BINDROOT)/include
#LDFLAGS=-L$(BINDROOT)/lib -llwres -lsocket -lnsl -lpthread

# Linux
CC=gcc
CFLAGS=-I$(BINDROOT)/bind/include -I$(BINDROOT)/include
LDFLAGS=-L$(BINDROOT)/lib -lpthread -llwres

all: bulkquery

bulkquery: bulkquery.c
	$(CC) $(CFLAGS) -o bulkquery bulkquery.c $(LDFLAGS)

clean:
	rm bulkquery

install:
	cp bulkquery $(DESTDIR)/bulkquery
	chmod 755 $(DESTDIR)/bulkquery
