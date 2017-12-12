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
#!/bin/bash
cat $1 | sed 's/\('$2'.*GR'$2'\|'$2'GR'$2'\|'$2'GRAMA'$2'\|'$2'GRAMAS'$2'\|'$2'GRS\.'$2'\|'$2'*.G'$2'\)/31/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'.*L'$2'\|'$2'.*LITRO'$2'\|'$2'LITROR'$2'\|'$2'.*LITROS'$2'\|'$2'LT'$2'\|'$2'LTS'$2'\|'$2'LT\.'$2'\)/51/g' | cat >$1
cat $1 | sed 's/\('$2'M2'$2'\|'$2'MT2'$2'\|'$2'METRO2'$2'\|'$2'm²'$2'\)/21/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'K'$2'\|'$2'.*KG'$2'\|'$2'KILO'$2'\|'$2'KILO]'$2'\|'$2'KILOS'$2'\|'$2'KILOS'$2'\|'$2'.KILO'$2'\|'$2'.KG'$2'\)/32/g' | cat >$1
cat $1 | sed 's/\('$2'TL'$2'\|'$2'TON'$2'\|'$2'TON.'$2'\|'$2'TONELA'$2'\|'$2'TONELAA'$2'\|'$2'TONELAD'$2'\)/33/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'ML'$2'\|'$2'.*ML'$2'\)/41/g' | cat >$1
cat $1 | sed 's/\('$2'CM'$2'\)/42/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'METRO]'$2'\|'$2'1\/2M'$2'\|'$2'M'$2'\|'$2'METRO'$2'\|'$2'METRO\.'$2'\|'$2'METROS'$2'\|'$2'MT'$2'\|'$2'MTS'$2'\|'$2'MTS1'$2'\|'$2'METRO1'$2'\|'$2'MT\.'$2'\|'$2'METROO'$2'\|'$2'METTRO'$2'\)/43/g' | cat >$1
cat $1 | sed 's/\('$2'QUILO'$2'\|'$2'QUILOS'$2'\|'$2'KILO'$2'\|'$2'KG'$2'\|'$2'KILOGRA'$2'\|'$2'KILO.'$2'\|'$2'KG.'$2'\)/44/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'M3'$2'\|'$2'METRO3'$2'\|'$2'METRO3'$2'\|'$2'METRO\/3'$2'\|'$2'METROS3'$2'\|'$2'MT3'$2'\|'$2'MT3'$2'\|'$2'MT-3'$2'\|'$2'MTS3'$2'\|'$2'MTSCUB'$2'\)/52/g' | cat >$1
cat $1 | sed 's/\('$2'.NI.ADE'$2'\|'$2'NIDADE'$2'\|'$2'U'$2'\|'$2'UINIDAE'$2'\|'$2'UN'$2'\|'$2'UN.'$2'\|'$2'..NIDADE'$2'\|'$2'U'$2'\|'$2'UINIDAE'$2'\|'$2'UNDADE'$2'\|'$2'UNIADDE'$2'\|'$2'UNID.'$2'\|'$2'.NID'$2'\|'$2'UND.'$2'\|'$2'UNIDA.'$2'\|'$2'UNIDA..'$2'\|'$2'UNIDA...'$2'\|'$2']UNIDAE'$2'\|'$2'UNIDDAE'$2'\|'$2'UNIDDE'$2'\|'$2'UNID.ES'$2'\|'$2'UNIT'$2'\|'$2'UNITA..'$2'\|'$2'UNMD'$2'\|'$2'UNNID'$2'\|'$2'UNUID'$2'\|'$2'UU'$2'\|'$2'UYNIDAE'$2'\|'$2'ÿUN'$2'\|'$2'UNI.'$2'\|'$2'UID'$2'\|'$2'UINID'$2'\|'$2'UNID1D'$2'\|'$2'NID'$2'\|'$2'U.N'$2'\|'$2'U.ND'$2'\|'$2'UM'$2'\|'$2'U.NIDAE'$2'\|'$2'.ND'$2'\|'$2'.UN'$2'\|'$2'UMD'$2'\|'$2'UNUM'$2'\|'$2'UNIADE'$2'\|'$2'.N'$2'\|'$2'U.'$2'\|'$2'.*UN'$2'\|'$2'.*UN\.'$2'\|'$2'.*UNI'$2'\|'$2'.*UNI\.'$2'\|'$2'UND..'$2'\)/71/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'PCA'$2'\|'$2'PCS'$2'\|'$2'PE'$2'\|'$2'PEC'$2'\|'$2'PECA.'$2'\|'$2'PECAUNI'$2'\|'$2'PECEA'$2'\|'$2'PC'$2'\|'$2'PECA'$2'\|'$2'PC'\'''$2'\|'$2'PPECA'$2'\|'$2'P[Çç]'$2'\|'$2'P[Çç].'$2'\)/72/g' | cat >$1
cat $1 | sed 's/\('$2'CAI..'$2'\|'$2'CAIXA.'$2'\|'$2'CX'$2'\|'$2'CX.'$2'\|'$2'CX..'$2'\)/73/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'DUZIA'$2'\|'$2'DUZIAS'$2'\|'$2'DZ'$2'\|'$2'.*DUZIA'$2'\)/74/g' | cat >$1
cat $1 | sed 's/\('$2'PA'$2'\|'$2'PACA'$2'\|'$2'P.COTE'$2'\|'$2'P.COTE.'$2'\|'$2'P..TE'$2'\|'$2'PCT.'$2'\|'$2'PCT.'$2'\|'$2'PACT'$2'\|'$2'PCTE'$2'\|'$2'PCTES'$2'\)/76/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'FARDO'$2'\|'$2'FARDOS'$2'\|'$2'FD'$2'\)/77/g' | cat >$1
cat $1 | sed 's/\('$2'RESMA'$2'\)/78/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'LATA'$2'\|'$2'LATA.'$2'\|'$2'..ATA'$2'\|'$2'..ATA.'$2'\)/79/g' | cat >$1
cat $1 | sed 's/\('$2'MOLHO'$2'\|'$2'MOLHO.'$2'\)/75/g' | cat >$1.sed
cat $1.sed | sed 's/\('$4'[6789]'$4'\)/1/g' | cat >$1
cat $1 | sed 's/\('$4'0'$4'\)/2/g' | cat >$1.sed
cat $1.sed | sed 's/\('$4'[45]'$4'\)/3/g' | cat >$1
cat $1 | sed 's/\('$4'[123]'$4'\)/4/g' | cat >$1.sed
cat $1.sed | sed 's/\('$3'0\)/10/g' | cat >$1
cat $1 | sed 's/\('$3'1\)/01/g' | cat >$1.sed
cat $1.sed | sed 's/\('$3'2\)/02/g' | cat >$1
cat $1 | sed 's/\('$3'3\)/03/g' | cat >$1.sed
cat $1.sed | sed 's/\('$3'4\)/04/g' | cat >$1
cat $1 | sed 's/\('$3'5\)/05/g' | cat >$1.sed
cat $1.sed | sed 's/\('$3'6\)/06/g' | cat >$1
cat $1 | sed 's/\('$3'7\)/07/g' | cat >$1.sed
cat $1.sed | sed 's/\('$3'8\)/08/g' | cat >$1
cat $1 | sed 's/\('$3'9\)/09/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'.*'$2'\)/00/g' | cat >$1
rm $1.sed
#mv $1.sed $1
