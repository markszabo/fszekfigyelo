<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibrariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('libraries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        DB::statement("INSERT INTO libraries (id, name) VALUES" .
                      "(1,'Központi Könyvtár (KK)')," .
                      "(2,'KK Zenei Gyűjtemény')," .
                      "(3,'KK Budapest Gyűjtemény')," .
                      "(4,'KK Sárkányos Gyerekkönyvtár')," .
                      "(5,'1016 Krisztina krt. 87.')," .
                      "(6,'1028 Hűvösvölgyi u.85.')," .
                      "(7,'1023 Török u. 7-9.')," .
                      "(8,'1039 Bajáki u. 5-7.')," .
                      "(9,'1039 Füst Milán u. 26.')," .
                      "(10,'1033 Fő tér 5.')," .
                      "(11,'1042 Király u. 5.')," .
                      "(12,'1046 Lóverseny tér 5/a')," .
                      "(13,'1054 Vadász u. 42.')," .
                      "(14,'1062 Andrássy út 52.')," .
                      "(15,'1061 Liszt F. tér 6.')," .
                      "(16,'1074 Rottenbiller u. 10.')," .
                      "(17,'1073 Kertész u. 15.')," .
                      "(18,'1089 Kálvária tér 12.')," .
                      "(19,'1098 Börzsöny u. 13.')," .
                      "(20,'1093 Boráros tér 2.')," .
                      "(21,'1108 Újhegy sétány 16.')," .
                      "(22,'1105 Szt. László tér 7-14.')," .
                      "(23,'1118 Nagyszeben tér 1.')," .
                      "(24,'1119 Etele u. 55.')," .
                      "(25,'1117 Karinthy u. 11.')," .
                      "(26,'1126 Ugocsa u. 10.')," .
                      "(27,'1133 Pannónia u. 88-90.')," .
                      "(28,'1134 Lehel u. 31.')," .
                      "(29,'1131 Mosoly u. 40.')," .
                      "(30,'1138 Dagály u. 9.')," .
                      "(31,'1145 Uzsoki u. 57.')," .
                      "(32,'1145 Bosnyák u. 1/a')," .
                      "(33,'1144 Rákosfalva park 1.')," .
                      "(34,'1153 Eötvös u. 8.')," .
                      "(35,'1157 Zsókavár u. 28.')," .
                      "(36,'1158 Szűcs I. u. 45.')," .
                      "(37,'1162 Rákosi út 119.')," .
                      "(38,'1163 Veres P. u. 57.')," .
                      "(39,'1171 Péceli út 232.')," .
                      "(40,'1173 Pesti út 167.')," .
                      "(41,'1188 Vasút utca 48.')," .
                      "(42,'1181 Csontváry u. 32.')," .
                      "(43,'1183 Thököly u. 5.')," .
                      "(44,'1191 Üllői u. 255.')," .
                      "(45,'1203 Bíró M. u.7.')," .
                      "(46,'1204 Pacsirta u. 157/b')," .
                      "(47,'1211 II.Rákóczi F. u.106.')," .
                      "(48,'1213 Szt. István u.230.')," .
                      "(49,'1214 Vénusz u. 2.')," .
                      "(50,'1225 Nagytétényi u. 283.')," .
                      "(51,'1221 Kossuth u. 30.')," .
                      "(52,'1238 Grassalkovich u.128.')," .
                      "(53,'KK Általános olvasóterem')," .
                      "(54,'KK Böngészde')," .
                      "(55,'KK Általános olvasóterem, Jogi olvasó')," .
                      "(56,'KK EU')," .
                      "(57,'KK e-Olvasóterem')," .
                      "(58,'KK Bölcseleti olvasóterem')," .
                      "(59,'KK Internetterem')," .
                      "(60,'KK Irodalmi olvasóterem')," .
                      "(61,'KK Szabadpolc')," .
                      "(62,'KK Művészeti olvasóterem')," .
                      "(63,'KK Multimédia terem')," .
                      "(64,'KK Szociológiai folyóiratgaléria')," .
                      "(65,'KK Szociológiai olvasóterem')," .
                      "(66,'KK Társadalomtudományi olvasóterem')," .
                      "(67,'Budapest Gyűjtemény olvasóterem')," .
                      "(68,'Külső raktár (Zrínyi u.)')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('libraries');
    }
}
