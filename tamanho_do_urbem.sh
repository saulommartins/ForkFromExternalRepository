#/*
#    **********************************************************************************
#    *                                                                                *
#    * @package URBEM CNM - Soluções em Gestão Pública                                *
#    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
#    * @author Confederação Nacional de Municípios                                    *
#    *                                                                                *
#    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
#    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
#    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
#    *                                                                                *
#    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
#    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
#    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
#    * para mais detalhes.                                                            *
#    *                                                                                *
#    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
#    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
#    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
#    *                                                                                *
#    **********************************************************************************
#*/
cd .

echo
echo -e "Quantidade de programas:"

echo -e \\t\\t"PHP: "\\c
PrgPHP=`find . -name "*.php" -print | wc -l`
echo -e \\t$PrgPHP

echo -e \\t\\t"PLSQL: "\\c
PrgPLSQL=`find . -name "*.plsql" -print | wc -l`
echo -e \\t$PrgPLSQL

echo -e \\t\\t"SQL: "\\c
PrgSQL=`find . -name "*.sql" -print | wc -l`
echo -e \\t$PrgSQL

echo -e \\t\\t"JS: "\\c
PrgJS=`find . -name "*.js" -print | wc -l`
echo -e \\t$PrgJS

echo -e \\t\\t"JAVA: "\\c
PrgJAVA=`find . -name "*.java" -print | wc -l`
echo -e \\t$PrgJAVA

echo -e \\t\\t"SH: "\\c
PrgSH=`find . -name "*.sh" -print | wc -l`
echo -e \\t$PrgSH

echo -e \\t\\t"AGT: "\\c
PrgAGT=`find . -name "*.agt" -print | wc -l`
echo -e \\t$PrgAGT

echo -e \\t\\t"RPT: "\\c
PrgRPT=`find . -name "*.rptdesign" -print | wc -l`
echo -e \\t$PrgRPT


echo -e \\t\\t"Manuais( HTML ): "\\c
PrgManuais=`find . -name "manUC*.html" -print | wc -l`
echo -e \\t$PrgManuais

echo -e \\t\\t"------------"
echo -e \\t\\t"Total:"\\c
echo -e \\t`expr $PrgPHP + $PrgPLSQL + $PrgSQL + $PrgJS + $PrgJAVA + $PrgSH + $PrgAGT + $PrgManuais`" programas"

echo
echo -e "Quantidade de linhas de programas:"

echo -e \\t\\t"PHP: "\\c
LinPHP=`find . -name "*.php" -print -exec cat {} \; | wc -l`
echo -e \\t$LinPHP

echo -e \\t\\t"PLSQL: "\\c
LinPLSQL=`find . -name "*.plsql" -print -exec cat {} \; | wc -l`
echo -e \\t$LinPLSQL

echo -e \\t\\t"SQL: "\\c
LinSQL=`find . -name "*.sql" -print -exec cat {} \; | wc -l`
echo -e \\t$LinSQL

echo -e \\t\\t"JS: "\\c
LinJS=`find . -name "*.js" -print -exec cat {} \; | wc -l`
echo -e \\t$LinJS

echo -e \\t\\t"JAVA: "\\c
LinJAVA=`find . -name "*.java" -print -exec cat {} \; | wc -l`
echo -e \\t$LinJAVA

echo -e \\t\\t"SH: "\\c
LinSH=`find . -name "*.sh" -print -exec cat {} \; | wc -l`
echo -e \\t$LinSH

echo -e \\t\\t"AGT: "\\c
LinAGT=`find . -name "*.agt" -print -exec cat {} \; | wc -l`
echo -e \\t$LinAGT

echo -e \\t\\t"RPT: "\\c
LinRPT=`find . -name "*.rptdesign" -print -exec cat {} \; | wc -l`
echo -e \\t$LinRPT


echo -e \\t\\t"Manuais ( HTML ): "\\c
LinManuais=`find . -name "manUC*.html" -print -exec cat {} \; | wc -l`
echo -e \\t$LinManuais

echo -e \\t\\t"--------------"
echo -e \\t\\t"Total:"\\c
echo -e \\t`expr $LinPHP + $LinPLSQL + $LinSQL + $LinJS + $LinJAVA + $LinSH + $LinAGT + $LinManuais`" linhas"

echo
echo -e "Banco de Dados:"

adump=/tmp/dumpp.sql
export PGUSER=domluc
export PGPASSWORD=176187
pg_dump -h 172.16.30.4 -d mig_urbem > /tmp/dumpp.sql
#rsh gf@172.16.30.4 "pg_dump dba_urbem > /tmp/dump_ccanet.sql"
#scp gf@10.27.68.58:/var/tmp/dump_ccanet.sql $adump
#su - postgres -c "pg_dump urbem > $adump"

echo -e \\t\\t"FUNCOES: "\\c
grep "CREATE FUNCTION" $adump | wc -l 

echo -e \\t\\t"ESQUEMAS: "\\c
grep "CREATE SCHEMA"   $adump | wc -l

echo -e \\t\\t"TABELAS: "\\c
grep "CREATE TABLE"    $adump | wc -l

echo -e \\t\\t"GATILHOS: "\\c
grep "CREATE TRIGGER"  $adump | wc -l

echo -e \\t\\t"INDICES: "\\c
grep "CREATE INDEX"    $adump | wc -l

echo

