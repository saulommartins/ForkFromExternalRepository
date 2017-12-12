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
#
# instala_ambientes_teste
#
# AUTORES       : Alex Carsoso e Rafael Garbin
# DESCRIÇÃO     : Script para automatização da instalação de ambientes de teste em servidores especificos para tal. USO INTERNO.
# USO           : sh instala_ambientes.sh
# LICENÇA       : GPL v2
#
# FUNCIONAMENTO :
#
# - Verifica se o caminho especificado representa pasta de sistema 
# - Lê as versões atual e versão nova a ser instalada;
# - Varre a raiz do caminho especificado (no caso, o nosso public_html) em busca das prefeituras
#     (ambientes de teste) a serem instaladas;
# - Permite selecionar os novos ambientes a serem instalados;
# - Permite ao usuario limpar as pastas da versão a ser instalada para todos os ambientes ou para somente os ambientes
#     selecionados (re-instalação de base de teste)
# - Efetua a limpeza das pastas selecionadas
# - Efetua copia dos fontes da versão atual para pastas da nova versão em cada ambiente;
#     Caso no ambiente selecionado não exista a pasta dos fontes da versão atual, efetua a copia a partir de um fonte
#     de outro ambiente que tenha a versão atual.
# - Cria o config yml já com nome de base correto dentro das pastas de cada ambiente;
# - Verifica existencia de base de dados da nova versão para o ambiente;
# - Verifica se não existem impedimentos para a criação da cópia da base (usuarios conectados etc) 
# - Cria base de teste da nova versão para o ambiente, baseado na ultima base de backup criada para o mesmo;
# - Seta o parametro de administracao.configuracao para o local correto dos fontes na base de teste;
# - Insere as permissões para usuario suporte (todas as gestões / funcionalidades / ações )
# - Efetua o vacuum na base de teste;
# - Emite log de todo o processo/notificações de erro
#
# TO DO         :
#
# - Possibilitar que após o usuário colocar os pacotes estáveis de cada gestão em um caminho a ser informado para este script,
#     para cada pacote, seja rodado e tratado automaticamente o atualizador.sh
#
# PRÉ-DEFINIÇÕES:
#
# - Baseado no padrão de nomenclatura das bases backup:
#     prefixo_gestao + _ + nomePrefeituraSomenteLetrasSemEspacosOuCaracteresEspeciais + _ + dataFormatoAmericanoCompletoYYYYMMDD + _bkp
#
# - Baseado na estrutura de pastas public_html da GRH nos servidores de teste
#     /home/grh/public_html
#           |
#           +---> geral (esta pasta é utilizada pelos fontes de apoio. É ignorada pelos processamentos deste script
#           |
#           +---> nomePrefeituraA
#           |          |
#           |          +---> numeroVersaoSomenteDigitos1
#           |          |                |
#           |          |                +---> fontes
#           |          |
#           |          +---> numeroVersaoSomenteDigitos2
#           |          |                |
#           |          |                +---> fontes
#           +---> nomePrefeituraB
#           |          |
#           |          +---> numeroVersaoSomenteDigitos1
#           |          |                |
#           |          |                +---> fontes
#           |          |
#           |          +---> numeroVersaoSomenteDigitos2
#           |          |                |
#           |          |                +---> fontes
#           +---> nomePrefeituraC
#                      |
#                      +---> numeroVersaoSomenteDigitos1
#                                       |
#                                       +---> fontes
#
#  - Nomes das bases a seres criadas:
#     prefixo_gestao + _ + nomePrefeituraSomenteLetrasSemEspacosOuCaracteresEspeciais + _ + numeroVersaoNovaSomenteDigitos
#
#

# Inicializa variaveis para criacao das bases

prefixo_gestao="rh"
servidor_base="190.1.1.1"
usuario_base="usuario"
senha_base="senha"
base="rh_instala_ambientes"

#!!!Cuidado ao setar o caminho dos public_html onde se localizam os fontes de teste!!!
#!!!Algumas rotinas podem ser destrutivas!!!
caminho="/home/pasta/do/usuario"

arquivoLock="$caminho/lock_instala_ambientes"
#utilizado para verificar se a instalacao esta bloqueada
boVerificaLock=0

#### VARIAVEIS PARA O ATUALIZADOR

#[0/1] padrao 1 - quando habilitado, roda o atualizador de cada gestao para a instalacao de cada ambiente de teste
boUtilizaAtualizador=1

#[0/1] padrao 1 - possibilita efetuar o download dos pacotes diretamente de urlDownloadPacotes ou
#quando desabilitado busca pacotes da pasta caminhoPacotesCompactados
boEfetuarDownloadPacotes=1

caminhoPacotesCompactados="/tmp/pacotes_instalacao_$prefixo_gestao"
caminhoPacotesDescompactados="/tmp/pacotes_instalacao_$prefixo_gestao/descompactados"
arquivoVersoesInstalar="$caminhoPacotesCompactados/instala_ambientes.txt"
gestoes=(GA GF GPC GRH GT);
usuarioWWWAtualizador="usuario"
hostDBAtualizador="190.1.1.1"
usuarioDBAtualizador="usuario"
senhaDBAtualizador="senha"
urlDownloadPacotes="http://downloads.urbem.cnm.org.br"

#### FIM VARIAVEIS PARA O ATUALIZADOR

#### CODIGO ESPECIFICO PARA QUANDO UTILIZA boUtilizaAtualizador=1

descompactaPacotesGestoes(){

    for gestaoInstalar in ${gestoes[@]}
    do
        info "Lendo versão a descompactar para $gestaoInstalar"
    
        pacoteVersao=`cat "$arquivoVersoesInstalar" | grep "$gestaoInstalar" | tail -n 1 | sed -e "s/ //g"`

        info "Identificada $pacoteVersao a descompactar para $gestaoInstalar"

        info "Buscando pacote $pacoteVersao da $gestaoInstalar para descompactar..."

        pacoteCompactado=`ls -F "$caminhoPacotesCompactados" | grep "$pacoteVersao" | grep '.tar.gz' | tail -n 1 | sed -e "s/ //g"`

        if [ -n "$pacoteCompactado" ]; then

            info "Pacote identificado: $pacoteCompactado"
            info "Descompactando pacote $pacoteCompactado para $caminhoPacotesDescompactados ..."

            tar -xzf "$caminhoPacotesCompactados/$pacoteCompactado" -C "$caminhoPacotesDescompactados"

            info "Pacote $pacoteCompactado descompactado."
        else
            erro "Não foi identificado último pacote a descompactar para a gestão $gestaoInstalar" 1
        fi

    done;

}

efetuaInstalacaoPacote(){
    caminhoAtual=`pwd`
    caminhoPacoteDescompactado=`echo "$caminhoPacotesDescompactados/$pacoteDescompactado" | sed -e "s/ //g"`
    cd "$caminhoPacoteDescompactado"
    ./atualizador.sh -i "$usuarioWWWAtualizador" "$hostDBAtualizador" "$novaBaseAmbiente" "$usuarioDBAtualizador" "$senhaDBAtualizador"
    controle="$?"
    if [ "$controle" == "" ]; then
        controle=0
    fi

    if [ $controle -gt 0 ]; then
        erro "Erro ao executar o atualizador para o pacote $pacoteDescompactado!!!" 1
    fi
    cd "$caminhoAtual"
}

validaArquivoVersoesInstalar(){

    for gestaoInstalar in ${gestoes[@]}
    do
        controle=`cat "$arquivoVersoesInstalar" | grep "$gestaoInstalar" | tail -n 1 | sed -e "s/ //g"`
        if [ -z "$controle" ]; then
            erro "\n\nATUALIZADOR: Não encontrada configuracao para $gestaoInstalar no arquivo de configuração\n\n $arquivoVersoesInstalar." 2
        fi
    done;

}

efetuaDownloadPacotes(){

    if [ $boEfetuarDownloadPacotes -eq 1 ]; then

        info "Efetuando download de pacotes..."

        for gestaoInstalar in ${gestoes[@]}
        do
            pacoteVersao=`cat "$arquivoVersoesInstalar" | grep "$gestaoInstalar" | tail -n 1 | sed -e "s/ //g"`
            
            info "Download de pacote $pacoteVersao.tar.gz\n\nDe: $urlDownloadPacotes/$pacoteVersao.tar.gz\n\nPara: $caminhoPacotesCompactados/$pacoteVersao.tar.gz..."

            wget -q --no-cache "$urlDownloadPacotes/$pacoteVersao.tar.gz" -P "$caminhoPacotesCompactados" -O "$caminhoPacotesCompactados/$pacoteVersao.tar.gz"
            if [ $? -gt 0 ]; then
                rm -f "$caminhoPacotesCompactados/$pacoteVersao.tar.gz"
                erro "Erro ao efetuar download de pacote $pacoteVersao.tar.gz de $urlDownloadPacotes/$pacoteVersao.tar.gz" 1
            fi
        done;
    else
        info "Efetuar download de pacotes desabilitado...\n\nBuscando diretamente de $caminhoPacotesCompactados"
    fi

    descompactaPacotesGestoes
}

rodaAtualizadoresAmbiente(){

    for gestaoInstalar in ${gestoes[@]}
    do
        info "Lendo pacote a ser instalado para $gestaoInstalar"

        pacoteVersao=`cat "$arquivoVersoesInstalar" | grep "$gestaoInstalar" | tail -n 1 | sed -e "s/ //g"`

        info "Identificada versão $pacoteVersao a instalar para $gestaoInstalar"

        info "Buscando pacote $pacoteVersao da $gestaoInstalar a instalar ..."

        pacoteDescompactado=`ls -F "$caminhoPacotesDescompactados" | grep "$pacoteVersao" | grep '/' | grep -v 'nao_compilados' | tail -n 1 | sed -e "s/\///g" | sed -e "s/ //g"`

        if [ -n "$pacoteDescompactado" ]; then

            info "Pacote da $gestaoInstalar identificado para instalação: $pacoteDescompactado"
            info "Rodando atualizador para $pacoteDescompactado..."

            efetuaInstalacaoPacote
        else
            erro "Não foi identificado último pacote a instalar para a gestão $gestaoInstalar" 1
        fi
    done;

}

#### FIM 

verificaLock(){
    boVerificaLock=0
    if [ -f "$arquivoLock" ]; then
        boVerificaLock=1
    fi
}

criaLock(){
    info "Criando lock para instalação..."
    touch "$arquivoLock"
}

removeLock(){
    info "Removendo lock de instalação..."

    if [ -f "$arquivoLock" ]; then
        rm "$arquivoLock"
    fi
}

setaCaminhoLog(){
        anoFormatado=`date +%Y`
        dataFormatada=`date +%Y%m%d%H%M`
        caminhoLog="/tmp/log_instalacao_ambientes_$dataFormatada"
        echo "" > $caminhoLog
}

log(){
        echo ""        >> $caminhoLog
        echo `date`    >> $caminhoLog
        echo -e "$1"   >> $caminhoLog
}

mostraLog(){
        dialog --textbox $caminhoLog 0 0
}

limpaRetornoBotoes(){
        retornoBotoes=-1
}

erro(){
        limpaRetornoBotoes
        boErro=1
        boErroGlobal=1

        if [ $2 == 1 ]; then
                log "ERRO: $1"
                dialog --stdout --yesno "[X] ERRO: $1\n\nDeseja continuar?" 10 60
                if [ $? == 1 ]; then
                        removeLock
                        exit
                fi
        fi

        if [ $2 == 2 ]; then
                log "ERRO FATAL: $1"
                dialog --stdout --msgbox "[X] ERRO FATAL: $1" 10 60
                if [ -z "$3" ]; then
                    removeLock
                fi
                exit
        fi

        # $? => 0 Repetir / 1 Abortar / 3 Ignorar
        if [ $2 == 3 ]; then
                log "ERRO: $1"
                dialog --stdout --extra-button --extra-label "Ignorar" --ok-label "Repetir" --cancel-label "Abortar" --yesno "[X] ERRO: $1\n\n'Repetir' para tentar novamente\n'Ignorar' para pular esta etapa para o ambiente de $ambiente\n'Abortar' para abortar todas instalações\n\nSelecione a opção:" 15 100
                retornoBotoes=$?
                if [ $retornoBotoes == 1 ]; then
                        removeLock
                        exit
                fi
        fi
}

warning(){
        log "AVISO: $1"
        dialog --stdout --msgbox "[!] AVISO: $1" 10 60
}

info(){
        log "INFO: $1"
        dialog --no-collapse --sleep 1 --stdout --infobox "[i] $1" 10 60
}

campoObrigatorio(){
        dialog --msgbox "[X] Informação obrigatória." 5 60
}

mensagemInicial(){
        msgInicial="Instalação de ambientes de teste URBEM\n\nGestao:$prefixo_gestao\nServidor Bases:$servidor_base\nBase Conexão:$base\nUsuario Base:$usuario_base\nSenha Base:$senha_base\nCaminho Fontes:$caminho"

        log "$msgInicial"
        dialog --msgbox "$msgInicial" 0 0
}

pegaVersoes(){

        # Lê a versao atual
        while true
        do
                versaoAtual=$(dialog --stdout --inputbox "[?] Informe a VERSÃO ATUAL: " 0 40)
                if [ $? == 1 ]; then
                        removeLock
                        exit
                fi

                if [ $(echo "$versaoAtual" | sed -e "s/[0-9]//g") != "" ]; then
                        warning "A versão NOVA deve ser informada somente com dígitos numéricos"
                else
                        if [ -z $versaoAtual ]; then
                                campoObrigatorio
                        else
                                break
                        fi
                fi
        done

        log "VERSÃO ATUAL $versaoAtual"

        # Lê a versão nova a ser instalada
        while true
        do
                versaoNova=$(dialog --stdout --inputbox "[?] Informe a VERSÃO NOVA: " 0 40)
                if [ $? == 1 ]; then
                        removeLock
                        exit
                fi

                if [ $(echo "$versaoNova" | sed -e "s/[0-9]//g") != "" ]; then
                        warning "A versão NOVA deve ser informada somente com dígitos numéricos"
                else
                        if [ -z $versaoNova ]; then
                                campoObrigatorio
                        else
                                break
                        fi
                fi
        done

        log "VERSÃO NOVA $versaoNova"

        if [ "$versaoAtual" == "$versaoNova" ]; then
                warning "Versão ATUAL e versão NOVA devem conter valores diferentes."
                pegaVersoes
        fi
}

confirmaVersoesInformadas(){
        dialog --defaultno --stdout --yesno "[!] Confirmação das versões a serem processadas:\n\nVersão ATUAL: $versaoAtual\nVersão  NOVA: $versaoNova $1\n\nDeseja continuar?" 10 60
        if [ $? == 1 ]; then
                removeLock
                exit
        fi
}

selecionaAmbientesInstalar(){
        while true
        do
                ambientesSelecionados=$(dialog --stdout --separate-output --checklist "Selecione os ambientes a efetuar instalacao:" 0 0 0 $ambientesJanelaSelecao)
                if [ $? == 1 ]; then
                        removeLock
                        exit
                fi

                if [ -z $ambientesSelecionados ]; then
                        campoObrigatorio
                else
                        break
                fi
        done
}

verificaPastasLimpar(){
        while true
        do
                dialog --stdout --ok-label "Limpar SOMENTE SELECIONADAS" --extra-button --extra-label "Limpar TODAS" --cancel-label "Abortar" --yesno "Deseja REMOVER AS PASTAS DA VERSÃO ($versaoNova), para TODAS as pastas do caminho especificado ($caminho), ou SOMENTE PARA AS SELECIONADAS para instalação?" 15 100
                retornoBotoes=$?
                if [ $retornoBotoes == 1 ]; then
                        removeLock
                        exit
                else
                        if [ $retornoBotoes == 0 ]; then
                                ambientesLimpar=$ambientesSelecionados
                        else
                                ambientesLimpar=$ambientes
                        fi
                        break;
                fi
        done
}

criaArquivoConfigYML(){
        while true
        do
                if [ -f "$caminhoCopiaFontes/config.yml" ]; then
                        cat "$caminhoCopiaFontes/config.yml" | sed -e "s/\(database:[ \t]*\([a-zA-Z0-9_-]*\)\)/database: $prefixo_gestao\_$ambiente\_$versaoNova/g" -e "s/\(host:[ \t]*\([a-zA-Z0-9_-.]*\)\)/host: $servidor_base/g" > $caminho/$ambiente/$versaoNova/config.yml
                        
                        if [ "$?" -gt "0" ]; then
                                erro "Não foi possivel criar arquivo de configuração yml para o ambiente $ambiente.\n\nNão foi possível escrever no arquivo destino." 3
                                if [ $retornoBotoes == 3 ]; then
                                        break
                                fi
                        else
                                info "Arquivo de configuração yml criado com sucesso para o ambiente $ambiente"
                                break
                        fi
                else
                        erro "Não foi possivel criar arquivo de configuração yml para o ambiente.\n\nArquivo origem não encontrado." 3
                        if [ $retornoBotoes == 3 ]; then
                                break
                        fi
                fi
        done
}

buscaUltimaBaseCriadaAmbiente(){
        while true
        do
                mascaraBuscaBaseCriadaAmbiente="sw_"$ambiente"_[0-9]*_bkp"
                selectBuscaBaseCriadaAmbiente="SELECT datname FROM pg_database WHERE datname ~ '$mascaraBuscaBaseCriadaAmbiente' ORDER BY datname DESC LIMIT 1;"
                ultimaBaseCriadaAmbiente=`psql -U $usuario_base -h $servidor_base -d $base -t -c "$selectBuscaBaseCriadaAmbiente" | sed -e "s/ //g"`

                if [ -z "$ultimaBaseCriadaAmbiente" ]; then
                        erro "Não foi possível identificar a base de backup a ser utilizada como template para a criação do ambiente $ambiente..." 3
                        if [ $retornoBotoes == 3 ]; then
                                break
                        fi
                else
                        info "Ultima base identificada para o ambiente: $ultimaBaseCriadaAmbiente"

                        info "Verificando usuários conectados na base identificada como template..."
                        verificaUsuariosBaseTemplateIdentificada
                        break
                fi
        done
}

verificaExistenciaBaseDadosVersaoNova(){
        novaBaseAmbiente=$prefixo_gestao"_"$ambiente"_"$versaoNova;

        while true
        do
                novaBaseAmbienteExiste=`psql -U $usuario_base -h $servidor_base -d $base --pset tuples_only -l | grep "$novaBaseAmbiente"`
                if [ -n "$novaBaseAmbienteExiste" ]; then
                        erro "A base a ser criada para a versao ($novaBaseAmbiente) já existe." 3
                        if [ $retornoBotoes == 3 ]; then
                                break
                        fi
                else
                        info "Busca ultima base criada no servidor $servidor_base para $ambiente"
                        buscaUltimaBaseCriadaAmbiente
                        break
                fi
        done
}

verificaUsuariosBaseTemplateIdentificada(){
        while true
        do
                selectVerificaUsuariosBaseTemplateIdentificada="SELECT usuario FROM process() WHERE base = trim('$ultimaBaseCriadaAmbiente')"
                usuariosConectados=`psql -U $usuario_base -h $servidor_base -d $base --pset tuples_only -c "$selectVerificaUsuariosBaseTemplateIdentificada"`
                if [ "$?" -gt "0" ]; then
                        erro "Não foi possível verificar os usuários conectados na base template $ultimaBaseCriadaAmbiente." 3
                        if [ $retornoBotoes == 3 ]; then
                                break
                        fi
                else
                        if [ -n "$usuariosConectados" ]; then
                                erro "Existem usuários conectados na base $ultimaBaseCriadaAmbiente.\n\nUsuarios:\n$usuariosConectados\n\nPara continuar com o processo de criação da base, efetue logout de todos os usuários." 3
                                if [ $retornoBotoes == 3 ]; then
                                        break
                                fi
                        else
                                info "Criando base de teste utilizando $ultimaBaseCriadaAmbiente como template"
                                criaCopiaUltimaBaseCriada
                                break
                        fi
                fi
        done
}

criaCopiaUltimaBaseCriada() {
        while true
        do
                psql -U $usuario_base -h $servidor_base -d $base -c "CREATE DATABASE $novaBaseAmbiente template = $ultimaBaseCriadaAmbiente;"
                if [ "$?" -gt "0" ]; then
                        erro "Não foi possível criar a base de dados para o ambiente $ambiente." 3
                        if [ $retornoBotoes == 3 ]; then
                                break
                        fi
                else
                        info "Base de teste $novaBaseAmbiente criada"

                        info "Atualizando administracao.configuracao setando caminho dos fontes da nova versão"
                        atualizaCaminhoFontesNovaBase
                        break;
                fi
        done
}

atualizaCaminhoFontesNovaBase() {
        while true
        do
                psql -U $usuario_base -h $servidor_base -d $novaBaseAmbiente -c "UPDATE administracao.configuracao SET valor = '$caminho/$ambiente/$versaoNova' WHERE parametro = 'diretorio'"
                if [ "$?" -gt "0" ]; then
                        erro "Não foi possível setar o caminho dos fontes na base de dados do ambiente $ambiente." 3
                        if [ $retornoBotoes == 3 ]; then
                                break
                        fi
                else
                        if [ $boUtilizaAtualizador -eq 1 ]; then
                            rodaAtualizadoresAmbiente
                        fi

                        info "Emitindo todas permissoes para usuario suporte $novaBaseAmbiente"
                        atualizaPermissoesUsuarioSuporte
                        break
                fi
        done
}

atualizaPermissoesUsuarioSuporte() {
        while true
        do
                selectPermissoesUsuarioSuporte="INSERT INTO administracao.permissao\
                                                     SELECT numcgm,cod_acao, '$anoFormatado'\
                                                       FROM administracao.acao\
                                                          , ( SELECT numcgm FROM administracao.usuario\
                                                               WHERE username = 'suporte') AS usuario\
                                                      WHERE NOT EXISTS (SELECT numcgm\
                                                                          FROM administracao.permissao\
                                                                         WHERE numcgm = usuario.numcgm\
                                                                           AND cod_acao = acao.cod_acao\
                                                                           AND ano_exercicio = '$anoFormatado');";

                psql -U $usuario_base -h $servidor_base -d $novaBaseAmbiente -c "$selectPermissoesUsuarioSuporte"
                if [ "$?" -gt "0" ]; then
                        erro "Não foi possível setar as permissoes para o usuario suporte na base $novaBaseAmbiente." 3
                        if [ $retornoBotoes == 3 ]; then
                                break
                        fi
                else
                        info "Efetuando VACUUM na nova base de teste ($novaBaseAmbiente)"
                        efetuaVacuumNovaBase
                        break
                fi
        done
}

efetuaVacuumNovaBase() {
        while true
        do
                psql -U $usuario_base -h $servidor_base -d $novaBaseAmbiente -c "VACUUM FULL ANALYZE;"
                if [ "$?" -gt "0" ]; then
                        erro "Não foi possível efetuar o VACUUM na base de dados do ambiente $ambiente." 3
                        if [ $retornoBotoes == 3 ]; then
                                break
                        fi
                else
                        break
                fi
        done
}

consistenciaVariaveisInicializacao(){
            if [ ! -d "$caminho" ]; then
                erro "Variável de inicialização CAMINHO não representa pasta válida de sistema.\n\n$caminho\n\nFavor corrigir e reiniciar o script." 2
            fi

            #verifica caso o modo atualizador esteja habilitado () se existe o arquivo que especifica as versoes a instalar
            if [ $boUtilizaAtualizador -eq 1 ]; then

                if [ ! -f "$arquivoVersoesInstalar" ]; then
                    erro "\n\nATUALIZADOR: Arquivo de versões a instalar não está presente em $arquivoVersoesInstalar" 2
                fi

                validaArquivoVersoesInstalar
            fi

}

limpaPastas(){
        #efetua limpeza das pastas da nova versão nos ambientes de teste presentes em ambientesLimpar
        for ambiente in $ambientesLimpar
        do
                if [ -d "$caminho/$ambiente/$versaoNova" ]; then
                        info "Limpando pastas previamente instaladas do ambiente $ambiente para versão $versaoNova\n\napagando pasta $caminho/$ambiente/$versaoNova"
                        rm -rf $caminho/$ambiente/$versaoNova 2>&1
                        if [ "$?" -gt "0" ]; then
                                erro "Não foi possível apagar a pasta\n\n$caminho/$ambiente/$versaoNova" 2
                        else
                                info "A pasta foi removida com com sucesso"
                        fi
                else
                        info "Pasta com fontes de versão nova ($versaoNova) não encontrada para ambiente $ambiente\n\n$caminho/$ambiente/$versaoNova\n\nIniciando limpeza do proximo ambiente..."
                        continue
                fi
        done;
}

copiaPastaFontes(){

        if [ -d $caminho/$ambiente/$versaoAtual ]; then
                info "Copiando pasta de versão atual ($versaoAtual) para nova versão ($versaoNova)\n\ncopiando pasta $caminho/$ambiente/$versaoAtual para $caminho/$ambiente/$versaoNova"
                caminhoCopiaFontes="$caminho/$ambiente/$versaoAtual"
        else
                info "Copiando pasta de versão atual ($versaoAtual) para nova versão ($versaoNova) UTILIZANDO FONTES TEMPLATE $ambienteVersaoAtualFonteTemplate\n\ncopiando pasta $caminho/$ambienteVersaoAtualFonteTemplate/$versaoAtual para $caminho/$ambiente/$versaoNova"
                caminhoCopiaFontes="$caminho/$ambienteVersaoAtualFonteTemplate/$versaoAtual"
        fi

        fontesCopiadosSucesso=1
        cp -R $caminhoCopiaFontes $caminho/$ambiente/$versaoNova

        if [ "$?" -gt "0" ]; then
                erro "Não foi possível copiar as pastas da versão anterior para a nova versão" 2
                fontesCopiadosSucesso=0
        else
                info "Cria arquivo de configuração para versão nova\n\n$caminho/$ambiente/$versaoNova/config.yml"
                criaArquivoConfigYML
        fi
}

###################################
#       INICIALIZA ROTINAS        #
###################################

export PGPASSWORD=$senha_base

consistenciaVariaveisInicializacao

setaCaminhoLog

mensagemInicial

verificaLock
if [ $boVerificaLock -eq 1 ]; then
    erro "Foi verificado que existe uma atualizacao em andamento por outro usuário. Arquivo de lock de instalação está presente em $arquivoLock" 2 1
else
    criaLock
fi

pegaVersoes

confirmaVersoesInformadas

ambientes=`ls -F $caminho | grep / | sed -e "s/\// /g" | grep -v geral`;
ambientesJanelaSelecao=''
ambientesLimpar=''
ambientesInstalar=''
ambienteVersaoAtualFonteTemplate=''
boErroGlobal=0

#verifica os ambientes presentes no caminho especificado
for ambiente in $ambientes
do
        if [ -d "$caminho/$ambiente/$versaoAtual" ]; then
                ambienteVersaoAtualFonteTemplate=$ambiente
        fi
        ambientesJanelaSelecao="$ambientesJanelaSelecao $ambiente $ambiente off"
done;

if [ -z $ambienteVersaoAtualFonteTemplate ]; then
        erro "Nenhum ambiente no caminho especificado ($caminho) apresenta fontes da versão atual ($versaoAtual)\n\nRotina Abortada." 2
        removeLock 
        exit
fi

selecionaAmbientesInstalar

verificaPastasLimpar

limpaPastas

info "Limpeza de pastas efetuada..."

ambientesInstalar=$ambientesSelecionados

info 'Iniciando processos de instalação...'

if [ $boUtilizaAtualizador -eq 1 ]; then
    efetuaDownloadPacotes
fi

for ambiente in $ambientesInstalar
do
        boErro=0

        info "Iniciando instalação de ambiente para $ambiente"

        copiaPastaFontes

        info "Verificando existência da base a ser criada para a versão nova..."
        verificaExistenciaBaseDadosVersaoNova

        if [ $fontesCopiadosSucesso == 1 ]; then

                info "Ajustando permissões de pasta da nova versão"
                chmod -R 700 $caminho/$ambiente/$versaoNova
                if [ "$?" -gt "0" ]; then
                        erro "Não foi possível atribuir as permissões 700 para a pasta\n\n$caminho/$ambiente/$versaoNova" 1
                fi
        fi

        if [ $boErro == 0 ]; then
                info "Instalação do ambiente para $ambiente efetuado com sucesso!"
        else
                info "Instalação do ambiente para $ambiente finalizada com erros! Verifique o log!"
        fi
done;

unset PGPASSWORD

removeLock

if [ $boErroGlobal == "0" ]; then
        log "Rotina Finalizada"
else
        log "Rotina Finalizada com erros"
fi

mostraLog

if [ $boErroGlobal == "0" ]; then
        echo "Rotina Finalizada. Log gerado em $caminhoLog"
else
        echo "Rotina Finalizada com erros. Verifique o Log! Log gerado em $caminhoLog"
fi
