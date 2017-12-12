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
#Desenvolvido em 27/01/2006 por Fernando Zank Correa Evangelista
#fernandozank@gmail.com

Principal() {
   echo ""	
   echo "Script para gerar arquivo CSV atráves do postgrsql"
   echo "------------------------------------------"
   echo "Selecione as Opções de conexão no Banco de Dados:"
   echo
   echo "1. Conectar com paramêtros [SCRIPT.SQL][ARQUIVO.CSV][HOST][BASE_DO_BD]"
   echo "2. Conectar com paramêtros [SCRIPT.SQL][ARQUIVO.CSV][HOST][BASE_DO_BD][CARACTER_SUBSTITUICAO]"
   echo "3. Conectar com paramêtros [SCRIPT.SQL][ARQUIVO.CSV][HOST][BASE_DO_BD][USUARIO][SENHA]"
   echo "4. Conectar com paramêtros [SCRIPT.SQL][ARQUIVO.CSV][HOST][BASE_DO_BD][CARACTER_SUBSTITUICAO][USUARIO][SENHA]"
   echo "5. Setar configurações para geração do arquivo de item do catálogo .(utilizada somente na exportação do catálogo de item)"
   echo "6. Setar configurações para geração do arquivo de grupo do catálogo.(utilizada somente na exportação do catálogo de grupo)"
   echo "7. Gerar o arquivo Catalogo.csv"
   echo "8. Gerar arquivos relacionados aos atributos. (atributos.csv e itemAtributos.csv)"
   echo "9. Help"
   echo "0. Sair do script"
   echo
   echo -n "Qual a opção desejada? "
   read opcao
   case $opcao in
      1) parametros4 ;;
      2) parametros5;;
      3) parametros6 ;;
      4) parametros7 ;;
      5) config_Catalogo_item ;;
      6) config_Catalogo_grupo ;;	
      7) catalogo_csv;;
      8) touch ${dir}itemAtributos.csv;touch ${dir}atributos.csv;
      echo ""  
      echo "arquivos itemAtributos.csv e atributos.csv gerados com sucesso !!";echo "" ;;
      9) uso ;;
      0) exit ;;
      *) echo "";echo "Opção inválida";echo ""; Principal ;;
   esac
}

#exporta senha do banco de dados postgresql para a variável de ambiente.
##echo "exportando senha para a variável de ambiente";
exportSenha () {
	echo  "Digite o usuário do banco de dados: "
	read user
	echo  "Digite a senha do Banco de dados: "
	read  -s senha
	export PGPASSWORD=$senha;
    echo -n ""
}


#funcao para limpar a senha do postgresql da variável de ambiente.
limpaSenha () {
    unset PGPASSWORD;
}

catalogo_csv(){
padrao=catalogo.csv
echo  "Digite o nome arquivo de saida ( padrão $padrao ): "
read csv
[ ! $csv ] && csv=$padrao
echo "Escreva o nome do catálogo a ser gerado (Padrão CNM)";
read catalogo
[ ! $catalogo ] && catalogo=CNM
echo '"'$catalogo'";"Grupo";99' | cat >${dir}${csv}
echo '"'$catalogo'";"Subgrupo";99' | cat >>${dir}${csv}
echo ""
echo "catalogo.csv criado com sucesso !!"
echo ""
}

config_Catalogo_item (){

    echo  "Seu script sql possui sequencia( Ex: CREATE SEQUENCE ...) ? [s/n]: "
	read sequencia
   case $sequencia in
      s)  sequencia="ok";;
      n)  sequencia="no";;
      S)  sequencia="ok";;
      N)  sequencia="no";;
      *) echo "";echo "Opção inválida";exit;;
   esac
   echo  "Digite o caractere de concatenacao utilizado na query para configurar"
   echo  "o código da unidade do catálogo de item de acordo com a estrutura do Urbem ( Padrão @%):"
   read concatenacao_unidade;
   [ ! $concatenacao_unidade ] && concatenacao_unidade=@%
   echo  "Digite o caractere de concatenacao utilizado na query para configurar"
   echo  "o código do tipo da unidade do catálogo de item de acordo com a estrutura do Urbem ( Padrão <->):"
   read concatenacao_tipo;
   [ ! $concatenacao_tipo ] && concatenacao_tipo="<->"
   concatena_estrutural	
   echo "";
   echo  "Caracter utilizado na query para configurar o código da unidade = $concatenacao_unidade  !!!";
   echo "";
   echo  "Caracter utilizado na query para configurar o código do tipo de unidade = $concatenacao_tipo  !!!";
   echo  ""
   echo  "Caracter unidade na query para configurar o código estrutural = $concatenacao_estrutural !!!"
   padrao=item.csv	
   Principal; 
}

concatena_estrutural(){
	echo  "Digite o caracter de concatenacao utilizado na query para configurar"
	echo  "o  Código estrutural de acordo com a estrutura do Urbem (Padrão @_@): "
	read concatenacao_estrutural;
	[ ! $concatenacao_estrutural ] && concatenacao_estrutural=@_@
        echo ""
	echo  "Caracter unidade na query para configurar o código estrutural = $concatenacao_estrutural !!!"


}

config_Catalogo_grupo() {
concatena_estrutural
concatenacao_classificacao=ok
padrao=classificacao.csv
Principal;
}





perguntasBase(){
    echo  "Digite o nome do arquivo com o script sql a ser carregado: "
	read sql
    echo  "Digite o nome arquivo de saida ( padrão $padrao ): "
	read csv
	[ ! $csv ] && csv=$padrao
	echo  "Digite o host do banco de dados: "
	read host
	echo  "Digite a base do banco de dados: "
	read base

}

perguntaCaracter(){
	echo  "Digite o caractere de substituição utilizado na query(Padrão ¢): "
	read subs
	[ ! $subs ] && subs=¢
}


##função para chamar o help
uso () {
    echo "";
    echo "help do $0";
    echo "";
    echo "--help                Mostra este arquivo de help.";
    echo "";
    echo "SCRIPT.SQL            Corresponde a um script sql válido para gerar os dados de saída.";
    echo "";
    echo "ARQUIVO.CSV           Corresponde ao nome do arquivo com a extensão csv.";
    echo "";
    echo "HOST                  Corresponde ao host do banco de dados.";
    echo "";
    echo "BASE_DO_BD            Corresponde ao nome da base do banco de dados."
    echo "";
    echo "USUARIO               Corresponde ao usuário da base do banco de dados.";
    echo "";
    echo "SENHA                 Corresponde a senha do usuário no banco de dados.";
    echo "";
    echo "CARACTER_SUBSTITUICAO Corresponde ao caracter que foi usado na query para substituir o \", o padrão é ¢ ";
    echo "";
    echo "Para Maiores informações leia o arquivo o manual do script (bancoCSV.manual)";
    echo "";
#    limpaSenha
    echo "";
    Principal			
}

##testa se foi passado 4 paramêtros
#elif [ $# = 4 ]; then 
parametros4(){
	perguntasBase
    echo "\i $sql" |psql -h $host $base  --no-align -F ';' --output $csv.bkp --tuples-only;
#	echo "psql -h $host $base  --no-align -F ';' --output $csv.bkp --tuples-only;"
    sed -e 's/¢/\\"/g'  $csv.bkp | cat >$csv.bkp2;
#só imprime as linhas  que começem com números
#    cat $csv.bkp2 | sed '/^[0-9]/!d' | cat > $csv;
#remove a primeira e a ultima linha do arquivo
    if [ $sequencia == "ok" ]; then
    	cat $csv.bkp2 | sed -e '1d' | sed -e '$d'| cat > $csv;
    else
    	cat $csv.bkp2 | cat >$csv;	
    fi	
    rm -f $csv.bkp;
    rm -f $csv.bkp2

    if [  $concatenacao_unidade != 'vazio' ]; then
	echo""
	echo "Favor aguarde enquanto o sistema configura os códigos de tipo de itens.(Este processo pode demorar alguns minutos...)"
	echo ""
	sh subCatalogoItem.sh $csv $concatenacao_unidade $concatenacao_estrutural $concatenacao_tipo
    fi
    if [ $concatenacao_classificacao == 'ok' ]; then
	echo""
	echo "Favor aguarde enquanto o sistema configura os códigos estruturais.(Este processo pode demorar alguns minutos...)"
	echo ""
	sh subCatalogoGrupo.sh $csv $concatenacao_estrutural
    fi
    mv $csv $dir$csv
    echo "Arquivo" $csv "gerado com sucesso !";
#    limpaSenha
    exit
}

##testa se foi passado 5 paramêtros
#elif [ $# = 5 ]; then
parametros5() { 
	perguntasBase
	perguntaCaracter
    echo "\i $sql" |psql -h $host $base  --no-align -F ';' --output $csv.bkp --tuples-only;
    sed -e 's/'$subs'/\\"/g' $csv.bkp | cat > $csv.bkp2;
#só imprime as linhas  que começem com números
#    cat $csv.bkp2 | sed '/^[0-9]/!d' | cat > $csv;
#remove a primeira e a ultima linha do arquivo
	if [ $sequencia == "ok" ]; then
    	cat $csv.bkp2 | sed -e '1d' | sed -e '$d'| cat > $csv;
	else
		cat $csv.bkp2 | cat >$csv;	
	fi	
    rm -f $csv.bkp;
    rm -f $csv.bkp2
	if [  $concatenacao_unidade != 'vazio' ]; then
	echo""
	echo "Favor aguarde enquanto o sistema configura os códigos de tipo de itens.(Este processo pode demorar alguns minutos...)"
	echo ""
    	sh subCatalogoItem.sh $csv $concatenacao_unidade $concatenacao_estrutural $concatenacao_tipo
	fi
    if [ $concatenacao_classificacao == 'ok' ]; then
	echo""
	echo "Favor aguarde enquanto o sistema configura os códigos estruturais.(Este processo pode demorar alguns minutos...)"
	echo ""
	sh subCatalogoGrupo.sh $csv $concatenacao_estrutural
    fi
    mv $csv $dir$csv
    echo "Arquivo" $csv "gerado com sucesso !";
#    limpaSenha
    exit
}
##testa se foi passado 6 paramêstros
#elif [ $# = 6 ]; then
parametros6() {
	perguntasBase
	exportSenha
    echo "\i $sql" | psql -U$user  -h $host  $base --no-align -F ';' --output $csv.bkp --tuples-only;
    sed -e 's/¢/\\"/g' $csv.bkp | cat > $csv.bkp2;
#só imprime as linhas  que começem com números
#    cat $csv.bkp2 | sed '/^[0-9]/!d' | cat > $csv;
#remove a primeira e a ultima linha do arquivo
	if [ $sequencia == "ok" ]; then
    	cat $csv.bkp2 | sed -e '1d' | sed -e '$d'| cat > $csv;
	else
		cat $csv.bkp2 | cat >$csv;	
	fi	
    rm -f $csv.bkp;
    rm -f $csv.bkp2	
	if [  $concatenacao_unidade != 'vazio' ]; then
	echo""
	echo "Favor aguarde enquanto o sistema configura os códigos de tipo de itens.(Este processo pode demorar alguns minutos...)"
	echo ""
    	sh subCatalogoItem.sh $csv $concatenacao_unidade $concatenacao_estrutural $concatenacao_tipo
    fi
    if [ $concatenacao_classificacao == 'ok' ]; then
	echo""
	echo "Favor aguarde enquanto o sistema configura os códigos estruturais.(Este processo pode demorar alguns minutos...)"
	echo ""
	sh subCatalogoGrupo.sh $csv $concatenacao_estrutural
    fi
    mv $csv $dir$csv
	echo "Arquivo" $csv "gerado com sucesso !";
    limpaSenha	
	exit
}
##testa se foi passado 7 paramêstros
#elif [ $# = 7 ]; then
parametros7() {
    perguntasBase
    perguntaCaracter	
    exportSenha
    echo "\i $sql" | psql -U$user  -h $host   $base --no-align -F ';' --output $csv.bkp --tuples-only;
    sed -e 's/'$subs'/\\"/g' $csv.bkp | cat > $csv.bkp2;
#só imprime as linhas  que começem com números
#    cat $csv.bkp2 | sed '/^[0-9]/!d' | cat > $csv;
#remove a primeira e a ultima linha do arquivo
	if [ $sequencia == "ok"]; then
    	cat $csv.bkp2 | sed -e '1d' | sed -e '$d'| cat > $csv;
	else
		cat $csv.bkp2 | cat >$csv;	
	fi	
    rm -f $csv.bkp;
    rm -f $csv.bkp2	
	if [  $concatenacao_unidade != 'vazio' ]; then
	echo""
	echo "Favor aguarde enquanto o sistema configura os códigos de tipo de itens.(Este processo pode demorar alguns minutos...)"
	echo ""
    	sh subCatalogoItem.sh $csv $concatenacao_unidade $concatenacao_estrutural $concatenacao_tipo
	fi	
    if [ $concatenacao_classificacao == 'ok' ]; then
	echo""
	echo "Favor aguarde enquanto o sistema configura os códigos estruturais.(Este processo pode demorar alguns minutos...)"
	echo ""
	sh subCatalogoGrupo.sh $csv $concatenacao_estrutural
    fi
    mv $csv $dir$csv
    echo "Arquivo" $csv "gerado com sucesso !";
    limpaSenha
    exit
#
}
##caso o programa não combine com nenhuma das condições acima
#else
#	uso
#fi
dir=ARQUIVOS_CSV/
if [ ! -d "ARQUIVOS_CSV" ];then
	mkdir $dir
fi

[ ! $padrao ] && padrao=arquivo.csv
[ ! $sequencia ] && sequencia=no
[ ! $concatenacao_unidade ] && concatenacao_unidade=vazio
[ ! $concatenacao_tipo ] && concatenacao_unidade="<->"
[ ! $concatenacao_classificacao ] && concatenacao_classificacao=vazio123
Principal
