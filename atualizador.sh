#!/bin/bash
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
#
#  URBEM Soluções de Gestão Pública Ltda
#  www.urbem.cnm.org.br
#
# $Revision: 28724 $
# $Name$
# $Author: gelson $
# $Date: 2015-02-12 11:36:25 -0200 (Thu, 12 Feb 2015) $

trap "" 2
export LC_ALL=pt_BR.UTF-8

# Inicializa Variaveis
usuarioWWW=
host=
nomeDB=
usuarioDB=
senhaDB=
tituloDialogo="URBEM - Solucao Integrada de Administracao Municipal"
bdAtualizado=0

setaCaminhoLog(){
    caminhoLog="atualizador.log"
    caminhoLog2="atualizador2.log"
    echo "" > $caminhoLog
    echo "" > $caminhoLog2
}

log(){
    if [ -z "$2" ]; then
        echo "" >> $caminhoLog
        echo `date` >> $caminhoLog
    fi
    echo -e "$1" >> $caminhoLog
}

mostraLog(){
        dialog --textbox $caminhoLog 0 0
}

info(){
        log "INFO: $1" $2
        dialog --title "$tituloDialogo" --no-collapse $tipoDialogoMensagem "[i] $1" 10 60
}

forceInfo(){
        log "INFO: $1" $2
        dialog --title "$tituloDialogo" --no-collapse --sleep 2 --infobox "[i] $1" 10 60
}


limpaRetornoBotoes(){
        retornoBotoes=-1
}

erro(){
        limpaRetornoBotoes
        boErro=1

        if [ $2 -eq 1 ]; then
                log "ERRO: $1"
                dialog --title "$tituloDialogo" --stdout --yesno "[X] ERRO: $1\n\nDeseja continuar?" 10 60
                if [ $? -eq 1 ]; then
                        exit 1
                fi
        fi

        if [ $2 -eq 2 ]; then
                log "ERRO FATAL: $1"
                dialog --title "$tituloDialogo" --stdout --msgbox "[X] ERRO FATAL: $1" 10 60
                exit 1
        fi

        # $? => 0 Repetir / 1 Abortar / 3 Ignorar
        if [ $2 -eq 3 ]; then
                log "ERRO: $1"
                dialog --title "$tituloDialogo" --stdout --extra-button --extra-label "Ignorar" --ok-label "Repetir" --cancel-label "Abortar" --yesno "[X] ERRO: $1\n\n'Repetir' para tentar novamente\n'Ignorar' para pular esta etapa para o ambiente de $ambiente\n'Abortar' para abortar todas instalações\n\nSelecione a opcao:" 15 100
                retornoBotoes=$?
                if [ $retornoBotoes -eq 1 ]; then
                        exit 1
                fi
        fi
}

erroTexto(){
    echo "ERRO FATAL: $1"
    exit 1
}

abortarTexto(){
    echo 'Processo abortado.'
    exit 1
}

mostraAjuda(){
    cat << EOF
    
URBEM - Solucao Integrada de Administracao Municipal

Pacote de atualizacao unificado: 2.05.4

Utilizacao: ./atualizador.sh [--help/-h] [--inline/-i <nome_usuario_apache> <host_base> <nome_base> <nome_usuario_base> <senha_base> ]
        
   --help/-h     Mostra essa mensagem

   --inline/-ni  Modo não interativo

EOF
}

apresentacao(){
    dialog --title "$tituloDialogo" --ok-label "Continuar" --no-collapse $tipoDialogoMensagem "\n $tituloDialogo\n\n   Pacote de Atualizacao: 2.05.4\n" 10 60
}

#
# Função principal de confirmação de backups
#
principal(){
    clear

    if [ $boModoNaoInterativo -eq 0 ]; then
        tipoDialogoMensagem='--msgbox'

        apresentacao

        dialog --stdout --title "$tituloDialogo" --yesno "\n
                     *** ATENCAO ***\n\n
Antes de executar a atualizacao, é recomendado realizar\n
backups de segurança da base de dados e programas objeto.\n\n
Deseja Prosseguir? " 0 0
        if [ $? -eq 1 ]; then
            abortarTexto
        fi
        
    else
        tipoDialogoMensagem='--sleep 2 --infobox'

        apresentacao

        dialog --stdout --title "$tituloDialogo" --sleep 3  --infobox "\n
  *** MODO DE ATUALIZAÇÃO NÃO INTERATIVO - ATENCAO ***\n\n
Antes de executar a atualizacao, é recomendado realizar\n
backups de segurança da base de dados e programas objeto.\n\n" 0 0
        
    fi

    inicializa

    definePermissao
    if [ $boModoNaoInterativo -eq 0 ]; then
        log "Processo de atualização finalizado."
        #mostraLog
    fi
    unset PGPASSWORD
    forceInfo "Processo de atualizacao finalizado."
}

verificaValidadeUsuarioWWW(){
    boUsuarioWWWValido=0
    controle=`grep ^"$1": /etc/passwd | wc -l`
    if [ $controle -gt 0 -a -n "$1" ]; then
        boUsuarioWWWValido=1
    fi
}

leituraUsuarioWWW(){
    if [ $boModoNaoInterativo -eq 0 ]; then
        while true
        do
            usuarioWWW=$(dialog --title "$tituloDialogo" --stdout --inputbox "[?] Qual o usuario dono do processo Apache? " 0 0 www-data)
            if [ $? -eq 1 ]; then
                exit
            fi

            verificaValidadeUsuarioWWW "$usuarioWWW"
            if [ $boUsuarioWWWValido -eq 0 ]; then
                erro "Usuario especificado para dono do processo Apache inexistente." 1
            else
                break
            fi
        done
    else
        verificaValidadeUsuarioWWW "$usuarioWWW"
        if [ $boUsuarioWWWValido -eq 0 ]; then
            erro "Usuario especificado para dono do processo Apache inexistente." 2
        fi
    fi
}

verificaValidadeInformacoesDB(){
    boValidadeInformacoesDB=0

    unset PGPASSWORD
    export PGPASSWORD="$4"
    echo " " 2>> $caminhoLog

    versaoSistema=`psql -U "$3" -h "$1" -p "$5" -d "$2" -t -c "SELECT versao FROM administracao.gestao WHERE cod_gestao = '1'"`
    if [ $? -eq 0 ]; then
        boValidadeInformacoesDB=1
    fi
}

leituraInformacoesDB(){
    if [ $boModoNaoInterativo -eq 0 ]; then
        while true
        do
            host=$(dialog --title "$tituloDialogo" --stdout --inputbox "[?] Qual o endereço IP do servidor PostgreSQL? " 0 0 127.0.0.1)
            if [ $? -eq 1 ]; then
                exit
            fi

			port=$(dialog --title "$tituloDiaglogo" --stdout --inputbox "[?] Qual a porta onde está rodando o PostgreSQL? " 0 0 2345)
			if [ $? -eq 1 ]; then
				exit
			fi

            nomeDB=$(dialog --title "$tituloDialogo" --stdout --inputbox  "[?] Qual a base de dados para conexao? " 0 0 urbem)
            if [ $? -eq 1 ]; then
                exit
            fi

            usuarioDB="postgres"
            usuarioDB=$(dialog --title "$tituloDialogo" --stdout --inputbox  "[?] Qual o usuario para conexao?($usuarioDB) " 0 0 postgres)
            if [ $? -eq 1 ]; then
                exit
            fi

            senhaDB=$(dialog --title "$tituloDialogo" --stdout --passwordbox  "[?] Qual a senhaDB para o usuario?($usuarioDB) " 0 0)
            if [ $? -eq 1 ]; then
                exit
            fi

            verificaValidadeInformacoesDB "$host" "$nomeDB" "$usuarioDB" "$senhaDB"  "$port"
            if [ $boValidadeInformacoesDB -eq 0 ]; then
                erro "Problemas ao conectar no banco" 1
            else
                break
            fi
        done
    else
        verificaValidadeInformacoesDB "$host" "$nomeDB" "$usuarioDB" "$senhaDB" "$port"
        if [ $boValidadeInformacoesDB -eq 0 ]; then
            erro "Problemas ao conectar no banco" 2
        fi
    fi
}

#
# Função que realiza a inicialização das variáveis
#
inicializa(){

    setaCaminhoLog

    log "$tituloDialogo"
    log 'Log de Atualizacao URBEM' 1

    # Define o usuario dono do processo apache
    log "Definindo usuarioWWW..."
    leituraUsuarioWWW
    log "usuarioWWW: $usuarioWWW" 1
    
    # Define informações de conexão ao banco de dados
    log "Definindo informacoes da base de dados..."
    leituraInformacoesDB

    log "host: $host" 1
    log "nomeDB: $nomeDB" 1
    log "usuarioDB: $usuarioDB" 1

    # Define o raiz do servidor web
    log "Definindo Caminho raiz para os programas objeto..."
    raizWWW=`psql -U $usuarioDB -h $host -p $port -d $nomeDB -t -c "SELECT valor FROM administracao.configuracao WHERE parametro = 'diretorio' limit 1" | sed -e "s/ //g"`
    if [ -z $raizWWW ] || [ $raizWWW = "/" ]; then
        erro "Caminho raiz para os programas objeto invalido ($raizWWW).\n\nAcesse o URBEM como admin e ajuste as configuracoes." 2
    fi

    if [ ! -d $raizWWW ]; then
        erro "Caminho raiz para os programas objeto nao existe ($raizWWW).\n\nAcesse o URBEM como admin e ajuste as configuracoes." 2
    fi
    
    log "raizWWW: $raizWWW" 1

    # Verificando dados informados
    verificaDados
}

#
# Função que verifica dados digitados
#
verificaDados(){
    if [ $boModoNaoInterativo -eq 0 ]; then
        dialog --stdout --title "$tituloDialogo" --yesno "Confirme os dados de instalacao:\n\nIP do servidor de BD: $host\nNome da base: $nomeDB\nUsuario para conexao no BD: $usuarioDB\nCaminho raiz para instalacao dos programas objeto: $raizWWW\n\nOs dados acima conferem?" 0 0
        if [ $? -eq 1 ]; then
                inicializa
        else
                forceInfo "Iniciando instalação... "
                verificaGestoes
        fi
    else
        dialog --stdout --sleep 1 --title "$tituloDialogo" --infobox "Dados de instalacao:\n\nIP do servidor de BD: $host\nNome da base: $nomeDB\nUsuario para conexao no BD: $usuarioDB\nCaminho raiz para instalacao dos programas objeto: $raizWWW\n\n" 0 0
        verificaGestoes
    fi
}

#
# Funcao que verifica se foi gerado o organograma
#
verificaOrganograma() {
    organograma=`psql -U $usuarioDB -h $host -p $port -d $nomeDB -t -c "SELECT valor FROM administracao.configuracao WHERE parametro ILIKE 'migra_organograma' AND exercicio='2009'"`
    gaCorreta=1930
    gfCorreta=1935
    gpCorreta=1913
    gpcCorreta=1912
    gtCorreta=1951
    grhCorreta=1950
    versaoGA=$( psql -U $usuarioDB -h $host -p $port -d $nomeDB -t -c "SELECT versao_db FROM administracao.gestao WHERE cod_gestao=1;" | sed -e "s/ //g" )
    versaoGF=$( psql -U $usuarioDB -h $host -p $port -d $nomeDB -t -c "SELECT versao_db FROM administracao.gestao WHERE cod_gestao=2;" | sed -e "s/ //g" )
    versaoGP=$( psql -U $usuarioDB -h $host -p $port -d $nomeDB -t -c "SELECT versao_db FROM administracao.gestao WHERE cod_gestao=3;" | sed -e "s/ //g" )
    versaoGPC=$(psql -U $usuarioDB -h $host -p $port -d $nomeDB -t -c "SELECT versao_db FROM administracao.gestao WHERE cod_gestao=6;" | sed -e "s/ //g" )
    versaoGT=$( psql -U $usuarioDB -h $host -p $port -d $nomeDB -t -c "SELECT versao_db FROM administracao.gestao WHERE cod_gestao=5;" | sed -e "s/ //g" )
    versaoGRH=$(psql -U $usuarioDB -h $host -p $port -d $nomeDB -t -c "SELECT versao_db FROM administracao.gestao WHERE cod_gestao=4;" | sed -e "s/ //g" )
    # Se for pacote da GA faz o teste
    if [ $IDGestao -eq 1 ] && [ $versaoDb -eq $gaCorreta ]; then
        # Testa se o organograma já foi migrado
        if [ $organograma == "false" ]; then
            erro "Antes de atualizar esta versão é necessário efetuar a migração do organograma." 2
        fi
        if [ $versaoGA -lt "1922" ]; then
            erro "Atualize a versÃo da GA para 1.92.2" 2
        fi
        # Verifica se as versões das gestões estão nas especificadas
        if [ $versaoGF -lt $gfCorreta ] || [ $versaoGP -lt $gpCorreta ] || [ $versaoGPC -lt $gpcCorreta ] || [ $versaoGT -lt $gtCorreta ] || [ $versaoGRH -lt $grhCorreta ]; then
        erro "Para ser feita a atualizacao desse pacote, o URBEM deve estar com as seguintes versões:\nGF - 1.93.5\nGP - 1.91.3\nGPC - 1.91.2\nGT - 1.95.1\nGRH - 1.95.0\nPor favor verifique essas atualizações e volte a atualizar a GA 1.92.2" 2
        fi
    else
        if ([ $IDGestao -eq 2 ] && [ $versaoGF -eq 1936 ]) || ([ $IDGestao -eq 3 ] && [ $versaoGP -eq 1920 ]) || ([ $IDGestao -eq 4 ] && [ $versaoGRH -eq 1951 ]) || ([ $IDGestao -eq 5 ] && [ $versaoGT -eq 1960 ]) || ([ $IDGestao -eq 6 ] && [ $versaoGPC -eq 1913 ]); then
            if [ $versaoGA -lt $gaCorreta ]; then
                erro "É necessário a instalação da GA 1.92.2 antes dessa versão" 2
            fi
        fi
    fi
}

#
# Funcao a ser gerada pelo empacotador a qual contem as chamadas para a atualizacao das gestoes
#

 verificaGestoes(){

	 verifica "1.94.1" "2.05.4" "1" "gestaoAdministrativa" "2054"
	 verifica "1.96.0" "2.05.4" "2" "gestaoFinanceira" "2054"
	 verifica "1.92.4" "2.05.4" "3" "gestaoPatrimonial" "2054"
	 verifica "1.92.6" "2.05.4" "6" "gestaoPrestacaoContas" "2054"
	 verifica "1.97.7" "2.05.4" "4" "gestaoRH" "2054"
	 verifica "1.98.0" "2.05.4" "99" "gestaoSaude" "2054"
	 verifica "1.98.0" "2.05.4" "5" "gestaoTributaria" "2054"
	 echo '' > atualizador2.log
	 atualizaGestoes
}


 atualizaGestoes(){

	 atualiza "1.94.1" "2.05.4" "1" "gestaoAdministrativa" "2054"
	 atualiza "1.96.0" "2.05.4" "2" "gestaoFinanceira" "2054"
	 atualiza "1.92.4" "2.05.4" "3" "gestaoPatrimonial" "2054"
	 atualiza "1.92.6" "2.05.4" "6" "gestaoPrestacaoContas" "2054"
	 atualiza "1.97.7" "2.05.4" "4" "gestaoRH" "2054"
	 atualiza "1.98.0" "2.05.4" "99" "gestaoSaude" "2054"
	 atualiza "1.98.0" "2.05.4" "5" "gestaoTributaria" "2054"
}


#
# Função que realiza as verificacoes referentes a versao do sistema
#

verifica(){
    versaoAtual=$1
    log "versaoAtual: $versaoAtual"
    versaoNova=$2
    log "versaoNova: $versaoNova"
    IDGestao=$3
    log "IDGestao: $IDGestao"
    dirGestao=$4
    log "dirGestao: $dirGestao"
    versaoDb=$5
    log "versaoDb: $versaoDb"

    log "Efetuando verificacoes para $dirGestao..."

    log "Verificando Organograma..."
    verificaOrganograma
    verificaVersaoDB
}

#
# Função que realiza a atualizacao do sistema
#
atualiza(){
    versaoAtual=$1
    log "versaoAtual: $versaoAtual"
    versaoNova=$2
    log "versaoNova: $versaoNova"
    IDGestao=$3
    log "IDGestao: $IDGestao"
    dirGestao=$4
    log "dirGestao: $dirGestao"
    versaoDb=$5
    log "versaoDb: $versaoDb"

    log "Efetuando atualizacao de $dirGestao..."

    if [ $bdAtualizado -eq 0 ]
    then
            bdAtualizado=1
            atualizaBD
    fi

    atualizaFontes
}

#
# Função que realiza verificação de versão do banco
#
verificaVersaoDB(){
    log "Verificando versao DB..."
    versaoBD=`psql -U $usuarioDB -h $host -p $port -d $nomeDB -t -c "SELECT substr( version(), position( ' ' in version())+1, 1)" 2>> $caminhoLog`
    if [ $versaoBD -lt 8 ]; then
        info "\n\n*** Aviso ***\n\n
        A versão 8.0 do PostgreSQL já está homologada, atualize seu servidor.\n
        Apartir de 01/03/2006 o URBEM não mais funcionará em versões inferiores.\n"
        log "versaoBD: $versaoBD"
        log "A versão 8.0 do PostgreSQL já está homologada, atualize seu servidor."
        log "A partir de 01/03/2006 o URBEM não mais funcionará em versões inferiores."
        exit 1
    fi
    log "versaoBD: $versaoBD" 1
}

#
# Função que realiza atualizacao da base de dados
#
atualizaBD(){
    forceInfo "Atualizando base de dados... "
    # executa manager db php
    controle=`./db.php $usuarioDB $senhaDB $host $nomeDB $versaoDb $versaoNova $port 1>> $caminhoLog2 2>> $caminhoLog`
    if [ "$?" -gt "0" ]; then
        erro "Problemas ao atualizar a base de dados.\nVerifique o arquivo de log que se encontra em $caminhoLog2" 2
    else
        forceInfo "Base de dados atualizada corretamente." 1
    fi
}

#
# Função que realiza a atualizacao dos programas objeto
#
atualizaFontes(){

    forceInfo "Atualizando programas objeto para $dirGestao... "
    if [ $IDGestao -eq 1 ]; then
        controle=`cp -rf --parents index.php $raizWWW 1>/dev/null`
        controle=`cp -rf --parents LICENCA.txt $raizWWW 1>/dev/null`
        controle=`cp -rf --parents .htaccess $raizWWW 1>/dev/null`
        controle=`cp -rf --parents config.php $raizWWW 1>/dev/null`
        controle=`cp -rf --parents valida_server.php $raizWWW 1>/dev/null`
        controle=`cp -rf --parents config.yml-dist $raizWWW 1>/dev/null`
        controle=`cp -rf --parents $dirGestao/index.php $raizWWW 1>/dev/null`
        controle=`cp -rf --parents $dirGestao/historico.html $raizWWW 1>/dev/null`
        controle=`cp -rf --parents $dirGestao/fontes/index.php $raizWWW 1>/dev/null`
        controle=`cp -rf --parents $dirGestao/fontes/PHP $raizWWW 1>/dev/null`
        controle=`cp -rf --parents $dirGestao/fontes/javaScript $raizWWW 1>/dev/null`
        if [ -d "$dirGestao/Manuais" ]; then
            controle=`cp -rf --parents $dirGestao/Manuais $raizWWW 1>/dev/null`
        fi
    else
        controle=`cp -rf --parents $dirGestao/index.php $raizWWW 1>/dev/null`
        controle=`cp -rf --parents $dirGestao/historico.html $raizWWW 1>/dev/null`
        controle=`cp -rf --parents $dirGestao/fontes/index.php $raizWWW 1>/dev/null`
        controle=`cp -rf --parents $dirGestao/fontes/PHP $raizWWW 1>/dev/null`
        if [ $IDGestao -eq 3 ]; then
            controle=`cp -rf --parents $dirGestao/fontes/javaScript $raizWWW 1>/dev/null`
            controle=`cp -rf --parents $dirGestao/fontes/java  $raizWWW 1>/dev/null`
        fi
        #if [ -d "$dirGestao/Manuais" ]; then
            #controle=`cp -rf --parents $dirGestao/Manuais $raizWWW 1>/dev/null`
        #fi
        controle=`cp -rf --parents gestaoAdministrativa/fontes/PHP/pacotes $raizWWW 1>/dev/null`
    fi
    
    #Limpa diretorios de fontes SQL antigos do caminho destino, caso exista
    if [ -d "$raizWWW/$dirGestao/fontes/SQL" ]; then
        controle=`rm -rf "$raizWWW/$dirGestao/fontes/SQL"`
    fi
    
    #Limpa diretorios de fontes PL antigos do caminho destino caso exista
    if [ -d "$raizWWW/$dirGestao/fontes/PLPGSQL" ]; then
        controle=`rm -rf "$raizWWW/$dirGestao/fontes/PLPGSQL"`
    fi
    
    if [ -d "$dirGestao/fontes/RPT" ]; then
        controle=`cp -rf --parents $dirGestao/fontes/RPT $raizWWW 1>/dev/null`
    fi
    forceInfo "Atualizacao de programas objeto efetuada corretamente. " 1
}

#
# Função que realiza a definição de permissões
#
definePermissao(){
    forceInfo "Ajustando permissoes... "
    controle=`chown -R $usuarioWWW. $raizWWW 1>/dev/null`
    controle=`find $raizWWW -type d -exec chmod 755 {} \;`
    controle=`find $raizWWW -type f -exec chmod 644 {} \;`
    controle=`chmod 777 $raizWWW/gestaoAdministrativa/fontes/PHP/framework/tmp 1>/dev/null`
    controle=`chmod 777 $raizWWW/gestaoAdministrativa/fontes/PHP/normas/anexos 1>/dev/null`
    forceInfo "Permissoes ajustadas. " 1
}

#
# Chamada da função principal
#

detectaArgumentos(){
    usuarioWWW="$2"
    host="$3"
    nomeDB="$4"
    usuarioDB="$5"
    senhaDB="$6"

    if [ -n "$usuarioWWW" -a -n "$host" -a -n "$nomeDB" -a -n "$usuarioDB" -a -n "$senhaDB" ]; then
        boModoNaoInterativo=1
    else
        erroTexto "Todos os argumentos devem conter valores válidos e não nulos. Rode o atualizador com a opcao --help para obter ajuda."
    fi
}

boModoNaoInterativo=0

for arg in $@
do
    if [ "$arg" == "--help" -o "$arg" == '-h' ]; then
        mostraAjuda
        exit 0
    fi
done

for arg in $@
do
    if [ "$arg" == "--inline" -o "$arg" == '-i' ]; then
        if [ $# -lt 6 ]; then
            erroTexto "Numero de argumentos inválido. Rode o atualizador com a opcao --help para obter ajuda."
        else
            detectaArgumentos $1 $2 $3 $4 $5 $6
        fi
    fi
done

principal
