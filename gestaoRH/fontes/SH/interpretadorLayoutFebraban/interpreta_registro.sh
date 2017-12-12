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

#----------------------------------------------------------------------------------------------------
# Este script interpreta um layout de banco no padrão da febraban a partir de um arquivo de definição
# do tipo de registro a ser lido.
# Devem ser criados os arquivos de definição de cada um dos tipos de registro. A chamada ao programa 
# deve ser feita passando o caminho do arquivo de definição e o caminho do arquivo gerado. O nome 
# do arquivo de definição deve conter o tipo do registro, por exemplo: o nome do arquivo de definição
# do registro do tipo 0 irá se chamar registro_0.def .
# Na pasta definicao está disponível os arquivos de definição de registros do banco HSBC, que foi o 
# banco que estava sendo trabalhado na ocasião da criação deste script, que foi o ticket #16290.
# Autor: Diego Mancilha <diego.mancilha@cnm.org.br>
#----------------------------------------------------------------------------------------------------

if [[ -z $1 || -z $2 ]]; then
	echo "USE: $0 arquivo_definicao arquivo_a_validar"
	exit 1
fi

tipoRegistro=`echo $1|cut -d "_" -f2|cut -d "." -f1`
#export totalRegistro=0

cat $1 | \
while read registro 
do
	desc=`echo ${registro} |cut -d ":" -f1`
	ini=`echo ${registro} |cut -d ":" -f2`
	qtd=`echo ${registro} |cut -d ":" -f3`
    comando="awk 'substr (\$0,8,1) == ${tipoRegistro} {printf \"[%s]\n\", substr(\$0,${ini},${qtd})}' $2 > /tmp/$0.tmp"
	eval $comando  
	cat /tmp/$0.tmp | while read valor
	do
		echo -n "=> ${desc}: "
		echo -n "${valor}"
		if [[ $3 == 'completo' ]]; then
			echo " Definição {Posicao Inicial = ${ini}, Qtd Digitos = ${qtd}}"
		else
			echo " "
		fi
	done
	rm /tmp/$0.tmp
	#totalRegistro=`expr "${totalRegistro}" "+" "${qtd}"`
done
#echo "Total digitos da registro: ${totalRegistro}"
