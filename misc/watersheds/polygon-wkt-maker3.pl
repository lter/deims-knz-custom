#  /usr/bin/perl
#
#  perl polygon-wkt-maker3.pl
#
#  takes no arguments, but assumes 'list-files.txt' exists, and has a list (1 col)
#  of geofilenames extracted from GIS, with coordinates.  files named after watershed.
#
#  aggregates all watersheds in one file, intended to use with a migration class
# 
#  two col output: name and polygon like POLYGON(long lat,long1 lat1, ... ,long lat)
#

open (FL,'list-files.txt') or die "couldnt open list-files\n";
  
$foutname = 'knz-polygons.csv';

open(FOUT,">$foutname") or die "couldnt write to $foutname \n";

while(<FL>){
  chomp;
  $file = $_;

  open (F,$file) or die "couldnt open $file\n";
  $fname=$file;
  if($fname=~/\.txt/){
    $name = $`;
  }else{
    die "no txt extension found in $fname, quiting\n"
  }

  $polygon = '"POLYGON(';

  $line=<F>;
  @arr = split(',0',$line);    #  print "$name is $line\n\n";  #  print "Size of $name is $#arr\n\n";

  for( $i=0; $i<=($#arr-1); $i++){

    undef @ar2;
    @ar2 = split(/,/,$arr[$i]);    
    $polygon .= ' '.$ar2[0].' '.$ar2[1].',';

  }
  @ar2 = split(/,/,$arr[$#arr]);
  $polygon.=  $ar2[0].' '.$ar2[1].')"';

  close(F);
  print FOUT "$name,$polygon\n";
}
close(FOUT);
close(FL);
