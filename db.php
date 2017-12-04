#!/usr/bin/env php
<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
#parametros da gestao
//$id_gestao = (int) $_SERVER['argv'][6]; -- deprecated em funcao de pacote unificado
//$versao_app = $_SERVER['argv'][7];      -- deprecated em funcao de pacote unificado
$versao_app = $_SERVER['argv'][6];

#parametros de versao
$versao_db_min = (int) $_SERVER['argv'][5];
$versao_db_cliente = null;

#parametros de conexao ao banco
$db_name = $_SERVER['argv'][4];
$host = $_SERVER['argv'][3];
$port = $_SERVER['argv'][7];
$db_user = $_SERVER['argv'][1];
$db_pass = $_SERVER['argv'][2];

#string de conexao
$stConnection  = " host=".$host;
$stConnection .= " port=".$port;
$stConnection .= " dbname=".$db_name;
$stConnection .= " user=".$db_user;
$stConnection .= " password=".$db_pass;

# Abrir conexao
if ($conn  = pg_connect( $stConnection )) {
    echo "Conexao Aberta\n";
}

# Abrir transacao
$result = pg_query( $conn, "BEGIN;");

# Pesquisar versao de base instalada no cliente
$sql = "SELECT cod_gestao, nom_gestao, versao_db from administracao.gestao ORDER BY ordem ASC";

$result = pg_query( $conn, $sql );
if (false ==  $result) {
    echo "Erro ao Iniciar Transação." . $stArquivoSql . "\n";
    echo "FALHA: " . pg_last_error() . "\n";
    pg_close($conn);
    exit(2); // termina a execução com erro;
}

$ar_versao = pg_fetch_all( $result );
foreach($ar_versao as $ar_dados_gestao):

    $versao_db_cliente = $ar_dados_gestao['versao_db'];

    echo "Gestao: ".$ar_dados_gestao['nom_gestao']."\n";
    echo "Versao Alvo: $versao_db_min \n";
    echo "Versao Cliente: $versao_db_cliente \n";
    echo "Versao App: $versao_app\n";

    if ($versao_db_min > $versao_db_cliente) {
        # diretorio dos sql
        $sql = "SELECT nom_diretorio from administracao.gestao where cod_gestao = " . $ar_dados_gestao['cod_gestao'];
        $result = pg_query( $conn, $sql );
        $ar_dir = pg_fetch_all( $result );
        $tmp = $ar_dir[0]['nom_diretorio'];
        $ar_tmp = explode('/',$tmp);
        $stDirFontesGestao = dirname(__FILE__)."/".$ar_tmp[6]."/fontes/";
        $arArquivosSql = all_files($stDirFontesGestao."SQL");
        $arArquivosPlSql = all_files($stDirFontesGestao."PLPGSQL");

        // arquivo que acumula tudo(SQL e PL's, na ordem)
        $stConteudoDeBanco = "";

        #buscar arquivos sql a se executar
        foreach($arArquivosSql as $stArquivoSql):
            list($null,$stNomArquivo) = explode('_',substr($stArquivoSql, 0, strpos($stArquivoSql,'.')));
            // busca no array de arquivos somente pelos nomes e versao
            preg_match("/(G.*)\_(.*)\.sql$/", $stArquivoSql, $arquivos);
            list($stNomArquivo,$stSiglaGestao,$stVersaoBanco ) = $arquivos;

            if ((integer) $stVersaoBanco > $versao_db_cliente && (integer) $stVersaoBanco <= $versao_db_min) {
                echo "Executando arquivo : " . $stNomArquivo . "\n";
                $stConteudoDeBanco = file_get_contents($stArquivoSql);
                $res1 = @pg_query( $conn, $stConteudoDeBanco);
                if (false ==  $res1) {
                    echo "Saindo por falha ao executar o arquivo" . $stArquivoSql . "\n";
                    echo "FALHA: " . pg_last_error() . "\n";
                    pg_query( $conn, 'ROLLBACK;');
                    pg_close($conn);
                    exit(2); // termina a execução com erro;
                }
            }
        endforeach;

        foreach($arArquivosPlSql as $stArquivoPlSql):
            echo "Executando arquivo PL : " . basename($stArquivoPlSql) . "\n";
            $stConteudoDeBanco = file_get_contents($stArquivoPlSql);
            $res1 = @pg_query( $conn, $stConteudoDeBanco);
            if (false == $res1) {
                echo "Saindo por falha ao executar o arquivo" . $stArquivoPlSql . "\n";
                echo "FALHA: " . pg_last_error(). "\n";
                pg_query( $conn, 'ROLLBACK;');
                pg_close($conn);
                exit(2); // termina a execução com erro;
            }
        endforeach;

    } elseif ($versao_db_min < $versao_db_cliente) {
        exit("Versão Alvo menor do que a instalada na base, contate o suporte!\n");
    } else {
        echo "Versão Alvo == Versão Cliente, Nada a fazer!\n";
    }

    # atualizar versao do banco
    $sql = "insert into administracao.historico_versao(cod_gestao, versao, versao_db) VALUES (".$ar_dados_gestao['cod_gestao'].", '$versao_app', $versao_db_min);";
    if ($result = pg_query( $conn, $sql )) {
        echo "Versao App/Db Sincronizada\n";
    } else {
        echo "Problema ao sincronizar base e fontes";
    }
endforeach;

$result = pg_query( $conn, "COMMIT;" );

if (pg_close($conn)) {
    echo "Conexão Fechada\n";
}

unset($stConteudoDeBanco);

function all_files($dir)
{
    $files = Array();
    $file_tmp= glob($dir.'*',GLOB_MARK );

    foreach ($file_tmp as $item) {
        if (substr($item,-1)!=DIRECTORY_SEPARATOR) {
            if (preg_match("/sql$/", $item)) {
                $files[] = $item;
            }
        } else {
            $files = array_merge($files,all_files($item));
        }
    }

    return $files;
}
