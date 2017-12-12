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
# Script para empacotamento do sistema.
#
# URBEM Soluções de Gestão Pública Ltda

# $Id: empacotador2.sh 59614 2014-09-02 12:20:19Z gelson $

#!/bin/bash

#
# Inicializando vairáveis globais.
#


#
# Monta o menu principal.
#
svnVA=$1 # versao anterior ex:  1.10.0 
svnVN=$2 # nova versao ex: 1.10.1
newDirDestino=$3 # local aonde deve gerar a versao
newValidade=$4 # validade no formato Br ex: 31/07/2008
newGestaoId=$5 # gestao id 
boCompilaFontes=$6 # compilar fontes?
inVersaoBase=$7 # versao base minima
gerartag=$8 # gerar tag

# branch ou trunk
if [ "$9" != "trunk" ] && [ "$9" != "" ]
then
    empBranch="branchs/$9"
else
    empBranch="trunk"
fi

echo "Local Svn escolhido: "$empBranch

# data a ser incluida no pacote temp
dirTempPacote=`date +'%y%m%d%H%M%S'`
mkdir -p '/tmp/'$svnVN'_'$dirTempPacote''
dirTempPacote='/tmp/'$svnVN'_'$dirTempPacote''

principal() {  
    clear
    echo -e '1 Versao Ante: '$svnVA
    echo -e '2 Versao Nova: '$svnVN
    echo -e '3 Dir Destino: '$newDirDestino
    echo -e '4 Validade   : '$newValidade
    echo -e '5 Id Gestao  : '$newGestaoId
    echo -e '6 Compila    : '$boCompilaFontes
    echo -e '7 Versao Base: '$inVersaoBase
    echo -e '8 Gerar Tag  : '$gerartag
    echo -e '9 Branch     : '$empBranch
    echo -e ''
    echo -e 'Local de Desova  : '$dirTempPacote

    case $newGestaoId in
      1) gestaoAdministrativa ;;
      2) gestaoFinanceira ;;
      3) gestaoPatrimonial ;;
      4) gestaoRH ;;
      5) gestaoTributaria ;;
      6) gestaoPrestacaoContas ;;
      0) exit 0 ;;
      *) outros ;;
   esac
   
   # criar pacote
   dirAnt=$PWD
   cd $dirTempPacote
   tar -zcf $newDirDestino"/"$nomePacote".tar.gz" *
   cd $dirAnt
   rm -rf $dirTempPacote

   # colocar no diretorio visivel
   rm "/var/empacotador/pacotes/"$nomePacote".tar.gz"
   ln -s $newDirDestino""$nomePacote".tar.gz" /var/empacotador/pacotes/
}

#
# Gera pacote para gestão administrativa.
#
gestaoAdministrativa(){
    echo ""
    inicializa "gestaoAdministrativa" "GA" "1"
    #addTagArquivo
    #controle=`cvs -Q tag $tagCVS leiame.txt`
    #controle=`cvs -Q tag $tagCVS index.php`
    #controle=`cvs -Q tag $tagCVS gestaoAdministrativa/fontes/PHP/framework/temas/`
    #controle=`cvs -Q tag $tagCVS gestaoAdministrativa/fontes/PHP/framework/legado/feriadosLegado.csv`
    #controle=`cvs -Q tag $tagCVS gestaoAdministrativa/fontes/PHP/framework/applets/`
    exportArquivos
    # deleter pacotes.inc de outras gestoes
    rm $dirTempPacote/gestaoAdministrativa/fontes/PHP/pacotes/GF.inc.php
    rm $dirTempPacote/gestaoAdministrativa/fontes/PHP/pacotes/GRH.inc.php
    rm $dirTempPacote/gestaoAdministrativa/fontes/PHP/pacotes/GP.inc.php
    rm $dirTempPacote/gestaoAdministrativa/fontes/PHP/pacotes/GT.inc.php
    rm $dirTempPacote/gestaoAdministrativa/fontes/PHP/pacotes/GPC.inc.php
    setVersaoValidade
    #geraHistorico
    geraMakeBD
    #mantemPersistente
    #exportaAnexos
    compilaFontes
    #substituiPersistente
    #empacotaAnexos
    echo ""
}

#
# Gera pacote para gestão financeira.
#
gestaoFinanceira(){
    echo ""
    inicializa "gestaoFinanceira" "GF" "2"
    #addTagArquivo
    exportArquivos
    setVersaoValidade
    #geraHistorico
    geraMakeBD
    #exportaAnexos
    compilaFontes
    #empacotaAnexos
    echo ""
}

#
# Gera pacote para gestão patrimonial.
#
gestaoPatrimonial(){
    echo ""
    inicializa "gestaoPatrimonial" "GP" "3"
    #addTagArquivo
    exportArquivos
    setVersaoValidade
    #geraHistorico
    geraMakeBD
    #exportaAnexos
    compilaFontes
    #empacotaAnexos
    echo ""
}

#
# Gera pacote para gestão de RH.
#
gestaoRH(){
    echo ""
    inicializa "gestaoRH" "GRH" "4"
    #addTagArquivo
    exportArquivos
    setVersaoValidade
    #geraHistorico
    geraMakeBD
    #exportaAnexos
    compilaFontes
    #empacotaAnexos
    echo ""
}

#
# Gera pacote para gestão tributaria.
#
gestaoTributaria(){
    echo ""
    inicializa "gestaoTributaria" "GT" "5"
    #addTagArquivo
    exportArquivos
    setVersaoValidade
    #geraHistorico
    geraMakeBD
    #exportaAnexos
    compilaFontes
    #empacotaAnexos
    echo ""
}

#
# Gera pacote para gestão prestação de contas.
#
gestaoPrestacaoContas(){
    echo ""
    inicializa "gestaoPrestacaoContas" "GPC" "6"
    #addTagArquivo
    exportArquivos
    setVersaoValidade
    #geraHistorico
    geraMakeBD
    #exportaAnexos
    compilaFontes
    #empacotaAnexos
    echo ""
}


#
# Trata as exceções de menu.
#
outros(){
    principal
}

#
# Inicialização
#
inicializa(){
    echo -e "Gestao: $1"
    echo -e "Silga: $2"
    echo -e "ID: $3"
    echo -e "Versao Atual: $svnVA"
    echo -e "Versao Nova: $svnVN"
    url_svn='http://svn.sw.cnm.org.br/urbem'
    # Gestão
    gestao=$1
    # Sigla da Gestão
    siglaGestao=$2
    # Repositório CVs
    moduloCVS="URBEM-1.0"
    # Arquivo de geração do pacote
    arqMake="$gestao/make.pkg"
    # Versão atual
    #versaoAtual=`cat $arqMake | head -n 1 | tail -1`
    versaoAtual=$svnVA
    controle=`expr "$versaoAtual" : '[1][\.][0-9]*[\.][0-9]*[0-9]\>'`
    if [ $controle = 0 ]
    then
        echo -e "Versão Atual\t\tERRO"
        exit 1        
    fi
    # Versão nova
    #versaoNova=`cat $arqMake | head -n 2 | tail -1`
    versaoNova=$svnVN
    controle=`expr "$versaoNova" : '[1][\.][0-9]*[\.][0-9]*[0-9]\>'`
    if [ $controle = 0 ]
    then
        echo -e "Versão Nova\t\tERRO"
        exit 1        
    fi
    # Data de validade
    #validade=`cat $arqMake | head -n 3 | tail -1`
    validade=$newValidade
    controle=`expr "$validade" : '[0-9][0-9][\/][0-9][0-9][\/][0-9][0-9][0-9][0-9]\>'`
    if [ "$controle" -gt "0" ]
    then
        dia=`echo $validade | cut -d/ -f1`
        mes=`echo $validade | cut -d/ -f2`
        ano=`echo $validade | cut -d/ -f3`
        if [ "$mes" -eq "1" ] || [ "$mes" -eq "3" ] || [ "$mes" -eq "5" ] || [ "$mes" -eq "7" ] || [ "$mes" -eq "8" ] || [ "$mes" -eq "10" ] || [ "$mes" -eq "12" ]
        then
            if [ "$dia" -lt "1" ] || [ "$dia" -gt "31" ]
            then
	        echo -e "Validade\tERRO"
	        exit 1
	    fi
	else
	    if [ "$mes" -eq "4" ] || [ "$mes" -eq "6" ] || [ "$mes" -eq "9" ] || [ "$mes" -eq "11" ]
	    then
	        if [ "$dia" -lt "1" ] || [ "$dia" -gt "30" ]
		then
		    echo -e "Validade\tERRO"
		    exit 1
		fi
	    else
	        if [ "$mes" -eq "2" ]
		then
		    if [ "`expr $ano % 400`" -eq "0" ] || [[ "`expr $ano % 4`" -eq "0"  &&  "`expr $ano % 100`" -eq "0" ]]
                    then
                        if [ "$dia" -lt "1" ] || [ "$dia" -gt "29" ]
		        then
			    echo -e "Validade\tERRO"
		            exit 1
		        fi
                    else 
	                if [ "$dia" -lt "1" ] || [ "$dia" -gt "28" ]
		        then
			    echo -e "Validade\tERRO"
		            exit 1
		        fi
                    fi
		else
		    echo -e "Validade\tERRO"
		    exit 1
		fi
            fi
	fi	    
    else
        echo -e "Validade\tERRO"
	exit 1
    fi
    validade="$dia\/$mes\/$ano"
    # Diretório de origem
    dirOrigem=`pwd`
    # Diretório de destino
    #dirDestino=`cat $arqMake | head -n 4 | tail -1`
    dirDestino=$newDirDestino
    if test -d $dirDestino 
    then
        controle=""
    else
        echo -e "Dir. Destino\tERRO"
	exit 1
    fi
    # Arquivos para o pacote
    arquivos=""
    # Tag CVS
    #tagCVS=$siglaGestao"_"`echo $versaoNova | cut -d. -f1`-`echo $versaoNova | cut -d. -f2`-`echo $versaoNova | cut -d. -f3`
    # Nome do pacote
    nomePacote=$siglaGestao"_"$versaoNova
    # Define ID da gestão
    IDGestao=$3
    # Update e commit CVS
    #controle=`cvs -Q update`
    #controle=`cvs -Q commit -m "Commit realiado automatiamente pelo programa empacotador.sh." $arqMake`
    echo -e "Inicialização\t\tOK"
}

#
# Adiciona tag CVS aos arquivos selecionados.
#
addTagArquivo(){
    controle=`cvs -Q tag -d $tagCVS`
    controle=`cvs -Q tag $tagCVS atualizador.sh`
    controle=`cvs -Q tag $tagCVS $gestao/index.php`
    controle=`cvs -Q tag $tagCVS gestaoAdministrativa/fontes/PHP/pacotes/$siglaGestao.inc.php`
    setArquivos "$gestao/fontes" "$gestao/Manuais"
    for i in $arquivos
    do
        controle=`cvs -Q tag $tagCVS $i`
	if [ "$?" -gt "0" ]
	then
	    echo $i
	fi
    done
    echo -e "Tag CVS\t\t\tOK"
}

#
# Exporta os arquivos marcados com a tag.
#
exportArquivos(){
    #cd $dirDestino
    #rm -rf $nomePacote
    #rm -rf URBEM-1.0
    echo -e 'Delete'
    #vai=`rm -rf $dirTempPacote/*`
    #controle=`cvs -Q export -N -r $tagCVS $moduloCVS`
    #controle=`svn -q export -N -r $tagCVS $moduloCVS`
    #url_raiz_svn = '%s/trunk/' % url_svn
    #url_raiz_svn = '%s/%s/' % (url_svn, branch_svn,)
    #args = ['svn','export',url_raiz_svn,checkout_tmp_dir,
    #            '--username','trac','--password','trac','-q','--force','-N'

    # exportar raiz
    echo -e 'Exporta Raiz'    
    #controle=`svn export $url_svn/trunk $dirTempPacote --username trac --password t1o0o0r -q --force -N`
    #controle=`svn export $url_svn/branchs/GA_1.40 $dirTempPacote --username trac --password t1o0o0r -q --force -N`
    controle=`svn export $url_svn/branchs/urbem-1.0-stable $dirTempPacote --username trac --password t1o0o0r -q --force -N`

    #   svn_gestao = '%s/%s/%s/' % (url_svn,branch_svn,svn_path,)
    #   checkout_tmp_dir = '%s/%s' % (checkout_tmp_dir, svn_path, )
    #   args = ['svn','export',svn_gestao, checkout_tmp_dir,
    #           '--username','trac','--password','trac','-q','--force']

    #        if not sigla == 'ga':
    #      import commands, string
    #      dir_pacotes = '%s/gestaoAdministrativa/fontes/PHP/pacotes/' % checkout_tmp_dir
    #      args = 'mkdir -p %s' % dir_pacotes
    #      retcode = commands.getstatusoutput(args)

    #      url_raiz_svn = '%s/%s/gestaoAdministrativa/fontes/PHP/pacotes/%s.inc.php' % (url_svn,branch_svn,string.upper(sigla),)
    #      checkout_tmp_dir_ga = '%s/%s.inc.php' % (dir_pacotes, string.upper(sigla), )
    #      args = ['svn','export',url_raiz_svn,checkout_tmp_dir_ga,
    #              '--username','trac','--password','trac','-q','--force']

    # caso nao GA, exporta PACOTE.inc.php	
    echo -e 'Se nao for GA, pega o pacote tambem'
    if [ "$siglaGestao" != "GA" ]
    then
      echo -e 'Nao eh GA!! :-) '
      dirPkg=$dirTempPacote'/gestaoAdministrativa/fontes/PHP/pacotes/'
      controle=`mkdir -p $dirPkg`
      cmddd='svn export '$url_svn'/trunk/gestaoAdministrativa/fontes/PHP/pacotes/'$siglaGestao'.inc.php '$dirPkg''$siglaGestao'.inc.php --username trac --password t1o0o0r -q --force'
      #cmddd='svn export '$url_svn'/branchs/sw_conversao_sessao/gestaoAdministrativa/fontes/PHP/pacotes/'$siglaGestao'.inc.php '$dirPkg''$siglaGestao'.inc.php --username trac --password t1o0o0r -q --force'
      #echo -e 'Comando'$cmddd 
      controle=`$cmddd`
      #controle=`svn export $url_svn/trunk/gestaoAdministrativa/fontes/PHP/pacotes/$siglaGestao.inc.php $dirPkg --username trac --password trac -q --force`
    fi
 
    echo -e 'Exporta Gestao'
    # exportar gestao
    controle=`mkdir -p $dirTempPacote/$gestao/`
    #controle=`svn export $url_svn/trunk/$gestao/ $dirTempPacote/$gestao --username trac --password t1o0o0r -q --force`
    controle=`svn export $url_svn/$empBranch/$gestao/ $dirTempPacote/$gestao --username trac --password t1o0o0r -q --force`
    
    # limpar manuais
    rm -rf $dirTempPacote/$gestao/'Manuais'
    rm -rf $dirTempPacote/$gestao/'documentos'

    # monta historico

    if [ "$gerartag" != "gerartag" ] && [ "$gerartag" != "" ]
    then
       echo '' >  $dirTempPacote/$gestao/'historico.html'
    else
       # gerar historico
       controle=`python /var/empacotador/geradorHistorico.py "$siglaGestao"_"$svnVN" > $dirTempPacote/$gestao/'historico.html'`
    
       # gerar tag
       controle=`svn copy -m 'Gerado automaticamente pelo Empacotador' $url_svn/branchs/urbem-1.0-stable $url_svn"/tags/"$siglaGestao"_"$svnVN --username trac --password t1o0o0r `

    fi

    rm -rf $dirTempPacote/$gestao/'fontes/SQL/*'
    
    controle=`svn export --force $url_svn/trunk/$gestao/fontes/SQL $dirTempPacote/$gestao/fontes/SQL --username trac --password t1o0o0r -q --force`

    #cd $dirTempPacote/$gestao/'fontes/SQL/'

    #if [ "$?" -eq "0" ]
    #then
    #    mv $moduloCVS $nomePacote
    #fi
    #cd $dirOrigem
    #controle=`cvs -Q tag $tagCVS $arqMake`
    echo -e "Exportação\t\tOK"
}

#
# Define a nova versao e valida da gestão.
#
setVersaoValidade(){
    # Ajuste do arquivo atualizador $inVersaoBase
    arquivo="$dirTempPacote/atualizador.sh"
    controle=`pwd`
    controle=`sed s/"<VERSAOATUAL>"/"$versaoAtual"/ $arquivo > /tmp/.empacotador.tmp`
    controle=`mv /tmp/.empacotador.tmp $arquivo`
    arquivo="$dirTempPacote/atualizador.sh"
    controle=`sed s/"<VERSAONOVA>"/"$versaoNova"/ $arquivo > /tmp/.empacotador.tmp`
    controle=`mv /tmp/.empacotador.tmp $arquivo`
    arquivo="$dirTempPacote/atualizador.sh"
    controle=`sed s/"<IDGESTAO>"/"$IDGestao"/ $arquivo > /tmp/.empacotador.tmp`
    controle=`mv /tmp/.empacotador.tmp $arquivo`
    arquivo="$dirTempPacote/atualizador.sh"
    controle=`sed s/"<DIRGESTAO>"/"$gestao"/ $arquivo > /tmp/.empacotador.tmp`
    controle=`mv /tmp/.empacotador.tmp $arquivo`
    arquivo="$dirTempPacote/atualizador.sh"
    controle=`sed s/"<VERSAOBASE>"/"$inVersaoBase"/ $arquivo > /tmp/.empacotador.tmp`
    controle=`mv /tmp/.empacotador.tmp $arquivo`    
    controle=`chmod 755 $arquivo`
    # Ajuste dp arquivo de define
    arquivo="$dirTempPacote/gestaoAdministrativa/fontes/PHP/pacotes/$siglaGestao.inc.php"
    controle=`sed s/"<VERSAO>"/"$versaoNova"/ $arquivo > /tmp/.empacotador.tmp`
    controle=`mv /tmp/.empacotador.tmp $arquivo`
    echo -e "Versao\t\t\tOK"
    controle=`sed s/"<VALIDADE>"/"$validade"/ $arquivo > /tmp/.empacotador.tmp`
    controle=`mv /tmp/.empacotador.tmp $arquivo`
    echo -e "Validade\t\tOK"
}

#
# Gera arquivo de histórico
#
geraHistorico(){
    # OBS.: É preciso resover o problema de entrega de novos casos de uso.
    # Atualmente, só serão disponibilizados os casos de uso já entregues
    # e com bugs verificados.
    j=0
    password="phpbt"
    export PGPASSWORD="$password"
    for i in `cat $arqMake`
    do
        if [ "$j" -gt "2" ]
	then    
	    echo $i >> .empacotador.tmp
	fi
	let "j += 1"
    done
    echo "**** MANUTENÇÕES DISPONIBILIZADAS ****" >  $dirDestino/$nomePacote/$gestao/historico.txt
    for i in `cat .empacotador.tmp`
    do
        filtro=`echo $i | cut -d- -f2`	
	controle=`psql -U phpbt -h 172.16.30.10 -d phpbt -t -c "SELECT  count(*) 
	                                                         FROM  casos_uso 
								 WHERE uc_id = '$filtro'"`
        if [ "$controle" -eq "1" ]
	then
	    controle=`psql -U phpbt -h 172.16.30.10 -d phpbt -t -c "SELECT  count(*)
							             FROM  bugs_verificados
							             WHERE uc_id='$filtro'"`
	    if [ "$controle" -gt "0" ]
	    then
	        echo ""                            >> $dirDestino/$nomePacote/$gestao/historico.txt
	        echo ""                            >> $dirDestino/$nomePacote/$gestao/historico.txt
                controle=`psql -U phpbt -h 172.16.30.10 -d phpbt -t -c "SELECT  distinct uc_id||' '||descricao
							                 FROM  casos_uso
								         WHERE uc_id='$filtro'" >> $dirDestino/$nomePacote/$gestao/historico.txt`
	        controle=`psql -U phpbt -h 172.16.30.10 -d phpbt -c "SELECT  bug_id    AS \"Chamado\",
							                    title     AS \"Módulo :: Funcionalidade :: Ação\"
							              FROM  bugs_verificados
							              WHERE uc_id='$filtro'" >> $dirDestino/$nomePacote/$gestao/historico.txt`
	    fi							     
	fi
    done
    unset PGPASSWORD
    controle=`rm -rf .empacotador.tmp`
    echo -e "Arq. Histórico\t\tOK"
}

#
# Função que gera o arquivo de chamada para scripts de BD.
#
geraMakeBD(){
   echo "-- Arquivo de atualização de banco de dados." >  $dirTempPacote/make.db
   echo ""                                             >> $dirTempPacote/make.db
   echo "\set ON_ERROR_STOP 1"                         >> $dirTempPacote/make.db
   echo "BEGIN;"                                       >> $dirTempPacote/make.db
   #echo "-- Arquivos SQL."                             >> $dirTempPacote/make.db
   #dirAnt=$PWD
   #cd $dirTempPacote
   #arquivos=`find $gestao/fontes/SQL -name '*sql*' | grep -v .svn`
   #cd $dirAnt
   
   #setArquivos "$dirTempPacote/$gestao/fontes/SQL"
   #for i in $arquivos
   #do
   #    echo "\i "$i                                    >> $dirTempPacote/make.db
   #done 
   
   dirAnt=$PWD
   cd $dirTempPacote
   arquivos=`find $gestao/fontes/PLPGSQL -name '*plsql*' | grep -v .svn`
   cd $dirAnt
   #setArquivos "$dirTempPacote/$gestao/fontes/PLPGSQL"
   echo "-- Arquivos PLPGSQL."                         >> $dirTempPacote/make.db
   for i in $arquivos
   do
       echo "\i "$i                                    >> $dirTempPacote/make.db
   done
   #echo "UPDATE administracao.gestao"                  >> $dirTempPacote/make.db
   #echo "  SET versao = '$versaoNova'"                 >> $dirTempPacote/make.db
   #echo "  WHERE cod_gestao = '$IDGestao';"            >> $dirTempPacote/make.db
   echo "COMMIT;"                                      >> $dirTempPacote/make.db
   echo "VACUUM ANALYZE;"                              >> $dirTempPacote/make.db
   echo "\unset ON_ERROR_STOP"                         >> $dirTempPacote/make.db
   echo -e "Arq. p/ BD\t\tOK"
}

#
# Função que compila o fonte PHP
#
compilaFontes(){
    # arrumando a casa
    controle=`mkdir /tmp/lixo_empacotador`
    controle=`mv $dirTempPacote/* /tmp/lixo_empacotador/`
    controle=`mkdir $dirTempPacote/$nomePacote` 
    controle=`mv /tmp/lixo_empacotador/* $dirTempPacote/$nomePacote/`
    controle=`rm -rf /tmp/lixo_empacotador`
    
    # cria diretorios
    controle=`cp -r $dirTempPacote/$nomePacote $dirTempPacote/$nomePacote"_nao_compilados"`  
    
    # compila se necessario
    if [ "$boCompilaFontes" -eq "1" ]
    then    
        controle=`/usr/local/Zend/ZendGuard-5_0_0/bin/zendenc5 --exclude-file Persistente.class.php --exclude-file db.php --ignore-errors --silent --recursive $dirTempPacote/$nomePacote"_nao_compilados" $dirTempPacote/$nomePacote`
    fi
    #controle=`/usr/local/Zend/ZendGuard-4.0.1/bin/zendenc --ignore-errors --silent --recursive $dirDestino/$nomePacote $dirDestino/$nomePacote"-bin"`
    #contrle=`rm -rf $dirDestino/$nomePacote`
    #controle=`cp -r $dirTempPacote/$nomePacote $dirTempPacote/$nomePacote"_nao_compilados"`  
    #controle=`mv  $dirDestino/$nomePacote $dirDestino/$nomePacote"_nao_compilados"`
    #controle=`mv $dirDestino/$nomePacote"-bin" $dirDestino/$nomePacote`
    echo -e "Compilação de fontes\tOK"
}

#
# Função que mantem a persistente sem compilar
#
mantemPersistente(){
    controle=`mv $dirDestino/$nomePacote/gestaoAdministrativa/fontes/PHP/framework/bancoDados/postgreSQL/Persistente.class.php ~/Persistente.class.php`
    echo -e "Persistente copiada\tOK"
}

#
# Função para substituir a persistente compilada pela não compilada
#
substituiPersistente(){
    controle=`mv ~/Persistente.class.php $dirDestino/$nomePacote/gestaoAdministrativa/fontes/PHP/framework/bancoDados/postgreSQL/Persistente.class.php`
    echo -e "Persistente substituida\tOK";
}

exportaAnexos(){
    comando=`mkdir ~/.tmp$versaoNova`
    arrayAnexos=(`ls $gestao/fontes/PHP`)
    len=${#arrayAnexos[*]}
    i=0
    while [ $i -lt $len ]; do
        if test -d $gestao"/fontes/PHP/"${arrayAnexos[$i]}"/anexos"
        then
            cd ~/.tmp$versaoNova
            cvs -Q export -N -r HEAD URBEM-1.0/$gestao/fontes/PHP/${arrayAnexos[$i]}/anexos/
            cd -
        fi
    let i++
    done
    echo -e "Anexos exportados\tOK";
}

empacotaAnexos(){
    cd ~/.tmp$versaoNova/URBEM-1.0/$gestao/fontes/PHP/
    array=(`ls`)
    len=${#array[*]}
    i=0
    while [ $i -lt $len ]; do
        cp -ru ${array[$i]}/anexos $dirDestino/$nomePacote/$gestao/fontes/PHP/${array[$i]}/
    let i++
    done
    cd -
    rm -rf ~/.tmp$versaoNova
    echo -e "Empacota arquivos \t Ok"    
}

#
# Seleciona os arquivo que contém a tag especificada.
#
setArquivos(){
    fontes="$1"
    manuais="$2"
    j=0

#    arquivos=`fgrep -f .empacotador.tmp -Rl $fontes/*`
#    controle=`rm -rf .empacotador.tmp`
    cat .empacotador.tmp | grep -v muc               > .empacotador_prg.tmp
    cat .empacotador.tmp | grep    muc | cut -c2-100 > .empacotador_man.tmp
    #arquivos=`fgrep -f .empacotador_prg.tmp -Rl $fontes/*`
    #dirAnt=$PWD
    #cd $dirTempPacote
    arquivos=`find $fontes -name '*sql*' | grep -v .`
    #cd $dirAnt
    
    # somente para manuais
    #if [ "$manuais" != "" ]
    #then
    	#arquivos=$arquivos" "`find $manuais -print | grep -f .empacotador_man.tmp -i| grep -v .svn`
    #fi
    #controle=`rm -rf .empacotador.tmp`
    #controle=`rm -rf .empacotador_prg.tmp`
    #controle=`rm -rf .empacotador_man.tmp`
}

#
# Chamada da função principal.
#
principal
