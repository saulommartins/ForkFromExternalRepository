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
#!/usr/bin/env bash
# analyze_entidades
#
# AUTORES    	: Rafael Garbin
# DESCRIÇÃO     : Script que faz a verificação se existem fontes sem as entidades.
# USO        	: analyze_entidades <diretorio> php_analyze <arquivo>
# OBS           : Só funciona para as entidade ponto | pessoal | folhapagamento | diarias | estagio
# LICENÇA    	: GPL v2
#

if [ "$1" == "" ]
then
    arquivos=$(find . -name "*.php")
elif [ "${1##*.}" == "php" ]
then
    arquivos=$1
else
    arquivos=$(find $1 -name "*.php")
fi

if [ -e ~/Desktop/log.log ]
then
    rm ~/Desktop/log.log
fi
touch ~/Desktop/log.log

for file in $arquivos
do
    lines=`wc -l ~/Desktop/log.log`
    analize=` grep -riH ' ponto\.' $file | grep -v .svn | grep -v .rptdesign | grep -v .agt | grep -v .sql | grep -v '*' | grep -v '//' >>  ~/Desktop/log.log 
              grep -riH ' pessoal\.' $file | grep -v .svn | grep -v .rptdesign | grep -v .agt | grep -v .sql | grep -v '*' | grep -v '//' >> ~/Desktop/log.log
              grep -riH ' folhapagamento\.' $file | grep -v .svn  | grep -v .rptdesign | grep -v .agt | grep -v .sql | grep -v '*' | grep -v '//' >> ~/Desktop/log.log
              grep -riH ' diarias\.' $file | grep -v .svn | grep -v .rptdesign | grep -v .agt | grep -v .sql | grep -v '*' | grep -v '//' >> ~/Desktop/log.log
              grep -riH ' estagio\.' $file | grep -v .svn | grep -v .rptdesign | grep -v .agt | grep -v .sql | grep -v '*' | grep -v '//' >> ~/Desktop/log.log `
    echo -n "Analizando o arquivo: ${file:2} "
    linesafter=`wc -l ~/Desktop/log.log`
    if [ lines != linesafter ] 
    then
    	echo -e "... \\033[1;30;31mError\\033[0m"
    else
    	echo "... Ok"
    fi
done

# se argumento $2 especificado, entra em silent mode. senao, mostra footer
if [ "$2" == "" ]
then
	echo 
	echo Analize concluida para as entidades, pessoal, folhapagamento, diarias, estagio, ponto:
	echo "VISUALIZAR PROBLEMAS ----> vi ~/Desktop/log.log":
fi

