#!D:/programs/perl/bin/perl.exe
#

# To permit this cgi, replace # on the first line above with the
# appropriate #!/path/to/perl shebang, and set this script executable
# with chmod 755.
#
# Note that it is subject to cross site scripting attacks on MS IE
# and any other browser which fails to honor RFC2616, so never use
# it in a live server environment, it is provided only for testing.

##
##  printenv -- demo CGI program which just prints its environment
##

print "Content-type: text/plain; charset=iso-8859-1\n\n";
foreach $var (sort(keys(%ENV))) {
    $val = $ENV{$var};
    $val =~ s|\n|\\n|g;
    $val =~ s|"|\\"|g;
    print "${var}=\"${val}\"\n";
}

