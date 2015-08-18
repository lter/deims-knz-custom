#
# 
#  /usr/bin/perl
#
#  perl polygon-wkt-maker3.pl
#
#  takes one argument; the name of the file containing the mangled coordinates
#  extracted from GIS, files named after watershed.
#
#  output file d{orig file name} with the WKT polygon.
#
#  like POLYGON(long lat,long1 lat1, ... ,long lat)
#

open (F,$ARGV[0]) or die "couldnt open ARGV[0]\n";

$fname=$ARGV[0];
if($fname=~/\.txt/){
  $name = $`;
  $foutname = 'd'.$name.'.csv';
}else{
  die "no txt extension found in $ARGV[0], quiting\n"
}
open(FOUT,">$foutname") or die "couldnt write to $foutname \n";

$polygon = 'POLYGON(';

$line=<F>;
@arr = split(',0',$line);

#  $polygon .= '-'.$arr[0].' '.$arr[1].',';

for( $i=0;$i<=($#arr-1);$i++){
  @ar2 = split(/,/,$arr[i]);
  $polygon .= ' '.$ar2[0].' '.$ar2[1].',';
}
@ar2 = split(/,/,$arr[$#arr]);
$polygon.=  $ar2[0].' '.$ar2[1].')';

close(F);
print FOUT "$name,$polygon\n";
close(FOUT);
