if(!$ARGV[0] || !$ARGV[1])	{
print "Usage: perl comparse.pl english/english.lang german/german.lang\n";
print "Compare which language files are different\n";
exit;
}

#print "Opening $ARGV[1]\n";

open(F, $ARGV[1]) || die "$ARGV[0] : $!\n";
while(<F>)	{
my $line = $_;
$line =~ /(.*?)=(.*)/;

$h{$1} = $2;

}

close(F);

#print "Opening $ARGV[0]\n";

open(F, $ARGV[0]) || die "$ARGV[0] : $!\n";

while(<F>)      {
my $line = $_;
$line =~ /(.*?)=(.*)/;

$a{$1} = $2;

}

foreach(keys %a)	{

print "$_=$a{$_}\n" if(!$h{$_} && $_ );
}

