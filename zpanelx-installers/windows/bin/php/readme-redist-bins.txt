1. libmagic (ext/fileinfo) see ext/fileinfo/libmagic/LICENSE
2. Oniguruma (ext/mbstring) see ext/mbstring/oniguruma/COPYING
3. libmbfl (ext/mbstring) see ext/mbstring/libmbfl/LICENSE
4. pcrelib (ext/pcre) see ext/pcre/pcrelib/LICENCE
5. ext/standard crypt
6. ext/standard crypt's blowfish implementation
7. Sqlite/Sqlite3 ext/sqlite3 ext/sqlite
8. ext/json/json_parser
9. ext/standard/rand
10. ext/standard/scanf
11. ext/standard/strnatcmp.c
12. ext/standard/uuencode
13. libxmlrpc ext/xmlrpc
14. libzip ext/zip
15. main/snprintf.c
16. main/strlcat
17. main/strlcpy
18. libgd see ext/gd/libgd/COPYING

5. ext/standard crypt

FreeSec: libcrypt for NetBSD

Copyright (c) 1994 David Burren
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
	 notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
	 notice, this list of conditions and the following disclaimer in the
	 documentation and/or other materials provided with the distribution.
3. Neither the name of the author nor the names of other contributors
	 may be used to endorse or promote products derived from this software
	 without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
SUCH DAMAGE.
 

6. ext/standard crypt's blowfish implementation

The crypt_blowfish homepage is:

http://www.openwall.com/crypt/

This code comes from John the Ripper password cracker, with reentrant
and crypt(3) interfaces added, but optimizations specific to password
cracking removed.

Written by Solar Designer <solar at openwall.com> in 1998-2011.
No copyright is claimed, and the software is hereby placed in the public
domain. In case this attempt to disclaim copyright and place the software
in the public domain is deemed null and void, then the software is
Copyright (c) 1998-2011 Solar Designer and it is hereby released to the
general public under the following terms:

Redistribution and use in source and binary forms, with or without
modification, are permitted.

There's ABSOLUTELY NO WARRANTY, express or implied.

It is my intent that you should be able to use this on your system,
as part of a software package, or anywhere else to improve security,
ensure compatibility, or for any other purpose. I would appreciate
it if you give credit where it is due and keep your modifications in
the public domain as well, but I don't require that in order to let
you place this code and any modifications you make under a license
of your choice.

This implementation is mostly compatible with OpenBSD's bcrypt.c (prefix
"$2a$") by Niels Provos <provos at citi.umich.edu>, and uses some of his
ideas. The password hashing algorithm was designed by David Mazieres
<dm at lcs.mit.edu>. For more information on the level of compatibility,
please refer to the comments in BF_set_key() and to the crypt(3) man page
included in the crypt_blowfish tarball.

There's a paper on the algorithm that explains its design decisions:

http://www.usenix.org/events/usenix99/provos.html

Some of the tricks in BF_ROUND might be inspired by Eric Young's
Blowfish library (I can't be sure if I would think of something if I
hadn't seen his code).
 

7. Sqlite/Sqlite3 ext/sqlite3 ext/sqlite

The author disclaims copyright to this source code.  In place of
a legal notice, here is a blessing:
  May you do good and not evil.
  May you find forgiveness for yourself and forgive others.
  May you share freely, never taking more than you give.


8. ext/json/json_parser
Copyright (c) 2005 JSON.org

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

The Software shall be used for Good, not Evil.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.


9. ext/standard/rand
The following php_mt_...() functions are based on a C++ class MTRand by
Richard J. Wagner. For more information see the web page at
http://www-personal.engin.umich.edu/~wagnerr/MersenneTwister.html

Mersenne Twister random number generator -- a C++ class MTRand
Based on code by Makoto Matsumoto, Takuji Nishimura, and Shawn Cokus
Richard J. Wagner  v1.0  15 May 2003  rjwagner@writeme.com

The Mersenne Twister is an algorithm for generating random numbers.  It
was designed with consideration of the flaws in various other generators.
The period, 2^19937-1, and the order of equidistribution, 623 dimensions,
are far greater.  The generator is also fast; it avoids multiplication and
division, and it benefits from caches and pipelines.  For more information
see the inventors' web page at http://www.math.keio.ac.jp/~matumoto/emt.html

Reference
M. Matsumoto and T. Nishimura, "Mersenne Twister: A 623-Dimensionally
Equidistributed Uniform Pseudo-Random Number Generator", ACM Transactions on
Modeling and Computer Simulation, Vol. 8, No. 1, January 1998, pp 3-30.

Copyright (C) 1997 - 2002, Makoto Matsumoto and Takuji Nishimura,
Copyright (C) 2000 - 2003, Richard J. Wagner
All rights reserved.                          

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

1. Redistributions of source code must retain the above copyright
	 notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright
	 notice, this list of conditions and the following disclaimer in the
	 documentation and/or other materials provided with the distribution.

3. The names of its contributors may not be used to endorse or promote 
	 products derived from this software without specific prior written 
	 permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE COPYRIGHT OWNER OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


10. ext/standard/scanf
scanf.c --

This file contains the base code which implements sscanf and by extension
fscanf. Original code is from TCL8.3.0 and bears the following copyright:

This software is copyrighted by the Regents of the University of
California, Sun Microsystems, Inc., Scriptics Corporation,
and other parties.  The following terms apply to all files associated
with the software unless explicitly disclaimed in individual files.

The authors hereby grant permission to use, copy, modify, distribute,
and license this software and its documentation for any purpose, provided
that existing copyright notices are retained in all copies and that this
notice is included verbatim in any distributions. No written agreement,
license, or royalty fee is required for any of the authorized uses.
Modifications to this software may be copyrighted by their authors
and need not follow the licensing terms described here, provided that
the new terms are clearly indicated on the first page of each file where
they apply.

IN NO EVENT SHALL THE AUTHORS OR DISTRIBUTORS BE LIABLE TO ANY PARTY
FOR DIRECT, INDIRECT, SPECIAL, INCIDENTAL, OR CONSEQUENTIAL DAMAGES
ARISING OUT OF THE USE OF THIS SOFTWARE, ITS DOCUMENTATION, OR ANY
DERIVATIVES THEREOF, EVEN IF THE AUTHORS HAVE BEEN ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.

THE AUTHORS AND DISTRIBUTORS SPECIFICALLY DISCLAIM ANY WARRANTIES,
INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT.  THIS SOFTWARE
IS PROVIDED ON AN "AS IS" BASIS, AND THE AUTHORS AND DISTRIBUTORS HAVE
NO OBLIGATION TO PROVIDE MAINTENANCE, SUPPORT, UPDATES, ENHANCEMENTS, OR
MODIFICATIONS.

GOVERNMENT USE: If you are acquiring this software on behalf of the
U.S. government, the Government shall have only "Restricted Rights"
in the software and related documentation as defined in the Federal
Acquisition Regulations (FARs) in Clause 52.227.19 (c) (2).  If you
are acquiring the software on behalf of the Department of Defense, the
software shall be classified as "Commercial Computer Software" and the
Government shall have only "Restricted Rights" as defined in Clause
252.227-7013 (c) (1) of DFARs.  Notwithstanding the foregoing, the
authors grant the U.S. Government and others acting in its behalf
permission to use and distribute the software in accordance with the
terms specified in this license.

11. ext/standard/strnatcmp.c

strnatcmp.c -- Perform 'natural order' comparisons of strings in C.
Copyright (C) 2000 by Martin Pool <mbp@humbug.org.au>

This software is provided 'as-is', without any express or implied
warranty.  In no event will the authors be held liable for any damages
arising from the use of this software.

Permission is granted to anyone to use this software for any purpose,
including commercial applications, and to alter it and redistribute it
freely, subject to the following restrictions:

1. The origin of this software must not be misrepresented; you must not
	 claim that you wrote the original software. If you use this software
	 in a product, an acknowledgment in the product documentation would be
	 appreciated but is not required.
2. Altered source versions must be plainly marked as such, and must not be
	 misrepresented as being the original software.
3. This notice may not be removed or altered from any source distribution.

12. ext/standard/uuencode
Portions of this code are based on Berkeley's uuencode/uudecode
implementation.

Copyright (c) 1983, 1993
The Regents of the University of California.  All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
	notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
	notice, this list of conditions and the following disclaimer in the
	documentation and/or other materials provided with the distribution.
3. All advertising materials mentioning features or use of this software
	must display the following acknowledgement:
This product includes software developed by the University of
California, Berkeley and its contributors.
4. Neither the name of the University nor the names of its contributors
	may be used to endorse or promote products derived from this software
	without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED.  IN NO EVENT SHALL THE REGENTS OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
SUCH DAMAGE.


13. libxmlrpc ext/xmlrpc

Copyright 2000 Epinions, Inc. 

Subject to the following 3 conditions, Epinions, Inc.  permits you, free 
of charge, to (a) use, copy, distribute, modify, perform and display this 
software and associated documentation files (the "Software"), and (b) 
permit others to whom the Software is furnished to do so as well.  

1) The above copyright notice and this permission notice shall be included 
without modification in all copies or substantial portions of the 
Software.  

2) THE SOFTWARE IS PROVIDED "AS IS", WITHOUT ANY WARRANTY OR CONDITION OF 
ANY KIND, EXPRESS, IMPLIED OR STATUTORY, INCLUDING WITHOUT LIMITATION ANY 
IMPLIED WARRANTIES OF ACCURACY, MERCHANTABILITY, FITNESS FOR A PARTICULAR 
PURPOSE OR NONINFRINGEMENT.  

3) IN NO EVENT SHALL EPINIONS, INC. BE LIABLE FOR ANY DIRECT, INDIRECT, 
SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES OR LOST PROFITS ARISING OUT 
OF OR IN CONNECTION WITH THE SOFTWARE (HOWEVER ARISING, INCLUDING 
NEGLIGENCE), EVEN IF EPINIONS, INC.  IS AWARE OF THE POSSIBILITY OF SUCH 
DAMAGES.

14. libzip ext/zip
zip.h -- exported declarations.
Copyright (C) 1999-2009 Dieter Baron and Thomas Klausner

This file is part of libzip, a library to manipulate ZIP archives.
The authors can be contacted at <libzip@nih.at>

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
	 notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
	 notice, this list of conditions and the following disclaimer in
	 the documentation and/or other materials provided with the
	 distribution.
3. The names of the authors may not be used to endorse or promote
	 products derived from this software without specific prior
	 written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHORS ``AS IS'' AND ANY EXPRESS
OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER
IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

15. main/snprintf.c
Copyright (c) 2002, 2006 Todd C. Miller <Todd.Miller@courtesan.com>

Permission to use, copy, modify, and distribute this software for any
purpose with or without fee is hereby granted, provided that the above
copyright notice and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.

Sponsored in part by the Defense Advanced Research Projects
Agency (DARPA) and Air Force Research Laboratory, Air Force
Materiel Command, USAF, under agreement number F39502-99-1-0512.

main/spprintf
Copyright (c) 1995-1998 The Apache Group.  All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

1. Redistributions of source code must retain the above copyright
	 notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright
	 notice, this list of conditions and the following disclaimer in
	 the documentation and/or other materials provided with the
	 distribution.

3. All advertising materials mentioning features or use of this
	 software must display the following acknowledgment:
	 "This product includes software developed by the Apache Group
	 for use in the Apache HTTP server project (http://www.apache.org/)."

4. The names "Apache Server" and "Apache Group" must not be used to
	 endorse or promote products derived from this software without
	 prior written permission.

5. Redistributions of any form whatsoever must retain the following
	 acknowledgment:
	 "This product includes software developed by the Apache Group
	 for use in the Apache HTTP server project (http://www.apache.org/)."

THIS SOFTWARE IS PROVIDED BY THE APACHE GROUP ``AS IS'' AND ANY
EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE APACHE GROUP OR
ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
OF THE POSSIBILITY OF SUCH DAMAGE.
====================================================================

This software consists of voluntary contributions made by many
individuals on behalf of the Apache Group and was originally based
on public domain software written at the National Center for
Supercomputing Applications, University of Illinois, Urbana-Champaign.
For more information on the Apache Group and the Apache HTTP server
project, please see <http://www.apache.org/>.

This code is based on, and used with the permission of, the
SIO stdio-replacement strx_* functions by Panos Tsirigotis
<panos@alumni.cs.colorado.edu> for xinetd.

16. main/strlcat
17. main/strlcpy
Copyright (c) 1998 Todd C. Miller <Todd.Miller@courtesan.com>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
	notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
	notice, this list of conditions and the following disclaimer in the
	documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
	derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL
THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS;
OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

