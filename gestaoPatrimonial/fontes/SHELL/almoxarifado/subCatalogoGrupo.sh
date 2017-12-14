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
cat $1 | sed 's/\('$2'0\)/10/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'1\)/01/g' | cat >$1
cat $1 | sed 's/\('$2'2\)/02/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'3\)/03/g' | cat >$1
cat $1 | sed 's/\('$2'4\)/04/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'5\)/05/g' | cat >$1
cat $1 | sed 's/\('$2'6\)/06/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'7\)/07/g' | cat >$1
cat $1 | sed 's/\('$2'8\)/08/g' | cat >$1.sed
cat $1.sed | sed 's/\('$2'9\)/09/g' | cat >$1
cat $1 | sed 's/\(;""\)/;"A_CADASTRAR"/g' | cat >$1.sed
mv $1.sed $1
