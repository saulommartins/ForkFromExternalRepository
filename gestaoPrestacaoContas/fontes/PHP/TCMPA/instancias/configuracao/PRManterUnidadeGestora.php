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
?>
<?php
/**
    * Página de Processamento - Parâmetros do Arquivo
    * Data de Criação   : 18/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterUnidadeGestora";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = 'incluir';

/***********************
 *  VALIDAÇÃO DOS DADOS
 ***********************/

// inicializada as variaveis
$inUnidadeGestora = "";
$codTipoUnidadeGestora = "";
$boErro = false;
// É feita a validação dos dados para inclusão na tabela

foreach ($_POST as $chave => $inValor) {
    $arIdentificador = explode('_',$chave);
    $inCod = $arIdentificador[1];

    // se for a unidade gestora, apenar adiciona um valor a variavel
    if ($arIdentificador[0] == "inUnidadeGestora") {
        $inUnidadeGestora = $inValor;

    // como na ordem o código do tipo vem após, é feita as validações dentro desse if.
    } elseif ($arIdentificador[0] == "codTipoUnidadeGestora") {
        $codTipoUnidadeGestora = $inValor;

        $inContVazio = 0;
        $inContCheio = 0;

        // se houver um dado preenchido e outro em branco, entra e da erro.
        if ( trim($inUnidadeGestora) == "" ) $inContVazio++;
        else $inContCheio++;

        if ( trim($codTipoUnidadeGestora) == "" ) $inContVazio++;
        else $inContCheio++;

        if ( ($inContCheio != 2) && ($inContVazio != 2) ) {
            $boErro = true;
            break;

        // caso contrario inicializa os campos para a proxima verificação
        } else {
            $inUnidadeGestora = "";
            $codTipoUnidadeGestora = "";
        }
    }
}

// verifica se houve algum erro para poder acusar na tela e não deixar fazer as inserções na tabela.
if ($boErro) {
    SistemaLegado::exibeAviso(urlencode("Preencher todos os dados necessários do Órgão ".$inCod),"n_alterar","erro");
} else {

    // inicia a sessao
    Sessao::setTrataExcecao( true );

    // deleta todos os dados para poder inseri-los novamente
    $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
    $obTAdministracaoConfiguracaoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
    $obTAdministracaoConfiguracaoEntidade->setDado( 'parametro', 'tcm_tipo_unidade_gestora' );
    $obErro = $obTAdministracaoConfiguracaoEntidade->exclusao();
    $obTAdministracaoConfiguracaoEntidade->setDado( 'parametro', 'tcm_unidade_gestora' );
    $obErro = $obTAdministracaoConfiguracaoEntidade->exclusao();

    if ( !$obErro->ocorreu() ) {

        /********************************
         *      INCLUSÃO DOS DADOS
         ********************************/
        $inCodAnterior = "";
        $arIdentificador = array();
        foreach ($_POST as $key => $value) {
            $arIdentificador = explode('_',$key);
            // se o valor é diferente de vazio ou o identificador é a unidade gestora ou o tipo de unidade
            // é feita as verificações para adicionar os dados nas tabelas
            if ((trim($value) != "") && ($arIdentificador[0] == "inUnidadeGestora" || $arIdentificador[0] == "codTipoUnidadeGestora" )) {

                $inCod = $arIdentificador[1];
                $obTAdministracaoConfiguracaoEntidade->setDado( 'cod_entidade', $inCod );
                $obTAdministracaoConfiguracaoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
                $obTAdministracaoConfiguracaoEntidade->setDado( 'cod_modulo', 48 ); // TCM - PA

                // verifica se é para incluir a unidade gestora ou o tipo da unidade
                if ($arIdentificador[0] == "inUnidadeGestora") {
                    $obTAdministracaoConfiguracaoEntidade->setDado( 'parametro', 'tcm_unidade_gestora' );
                    $obTAdministracaoConfiguracaoEntidade->setDado( 'valor', $value );
                    $obErro = $obTAdministracaoConfiguracaoEntidade->inclusao();
                    if( $obErro->ocorreu() ) break;
                } else {
                    $obTAdministracaoConfiguracaoEntidade->setDado( 'parametro', 'tcm_tipo_unidade_gestora' );
                    $obTAdministracaoConfiguracaoEntidade->setDado( 'valor', $value );
                    $obErro = $obTAdministracaoConfiguracaoEntidade->inclusao();
                    if( $obErro->ocorreu() ) break;
                }

            }
        }

        // inclui o último registro.
        if ($inCodAnterior != "") {
            $obTAdministracaoConfiguracaoEntidade->setDado( 'cod_entidade', $inCodAnterior );
            $obTAdministracaoConfiguracaoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
            $obTAdministracaoConfiguracaoEntidade->setDado( 'cod_modulo', 45 );
            $obErro = $obTAdministracaoConfiguracaoEntidade->inclusao();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Dados alterados ", "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

        Sessao::encerraExcecao();

    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    }
}

SistemaLegado::LiberaFrames();

?>
