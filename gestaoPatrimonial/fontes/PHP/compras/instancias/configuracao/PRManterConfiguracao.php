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
    * Página de Processamento de Configuração para Relatórios MODELOS
    * Data de Criação   : 22/05/2006

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * Casos de uso: uc-03.04.08

    $Id: PRManterConfiguracao.php 65448 2016-05-23 18:05:46Z michel $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TCOM."TComprasConfiguracao.class.php";
include_once TCOM."TComprasConfiguracaoEntidade.class.php";
include_once TORC."TOrcamentoEntidade.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

switch ($stAcao) {
    case 'alterar':
        $obTConfiguracao = new TComprasConfiguracao();

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTConfiguracao );

        # Seta alterações para o módulo 35 (Compras).
        $obTConfiguracao->setDado("cod_modulo" , 35 );
        $obTConfiguracao->setDado("parametro"  , "homologacao_automatica");
        $obTConfiguracao->setDado("valor"      , $_REQUEST['stHomologacao']);
        $obTConfiguracao->alteracao();

        $obTConfiguracao->setDado("parametro" , "dotacao_obrigatoria_solicitacao");
        $obTConfiguracao->setDado("valor"     , $_REQUEST['stExigeDotacao']);
        $obTConfiguracao->alteracao();

        if ( isset($_REQUEST['boTipoReserva']) ) {
            $boReservaRigida        = ( $_REQUEST['boTipoReserva'] == 'rigida'      ) ? 'true' : 'false';
            $boReservaAutorizacao   = ( $_REQUEST['boTipoReserva'] == 'autorizacao' ) ? 'true' : 'false';

            $obTConfiguracao->setDado( 'parametro' , 'reserva_rigida' );
            $obTConfiguracao->setDado( 'valor'     , $boReservaRigida );
            $obTConfiguracao->alteracao();

            $obTConfiguracao->setDado( 'parametro' , 'reserva_autorizacao');
            $obTConfiguracao->setDado( 'valor'     , $boReservaAutorizacao );
            $obTConfiguracao->recuperaPorChave($rsConfiguracao);

            if($rsConfiguracao->getNumLinhas()==1)
                $obTConfiguracao->alteracao();
            else
                $obTConfiguracao->inclusao();
        }

        $obTConfiguracao->setDado( 'parametro' , 'tipo_valor_referencia' );
        $obTConfiguracao->setDado( 'valor'     , $_REQUEST['stValorReferencia'] );
        $obTConfiguracao->alteracao();

        $obTConfiguracao->setDado("parametro","numeracao_licitacao");
        if($_REQUEST['stNumeracaoPorEntidade'] == '' && $_REQUEST['stNumeracaoPorModalidade'] == '')
            $obTConfiguracao->setDado("valor"    , 'geral');
        elseif($_REQUEST['stNumeracaoPorEntidade'] == 'on' && $_REQUEST['stNumeracaoPorModalidade'] == '')
            $obTConfiguracao->setDado("valor"    , 'entidade');
        elseif($_REQUEST['stNumeracaoPorEntidade'] == '' && $_REQUEST['stNumeracaoPorModalidade'] == 'on')
            $obTConfiguracao->setDado("valor"    , 'modalidade');
        elseif($_REQUEST['stNumeracaoPorEntidade'] == 'on' && $_REQUEST['stNumeracaoPorModalidade'] == 'on')
            $obTConfiguracao->setDado("valor"    , 'entidademodalidade');

        $obTConfiguracao->alteracao();

        $obTComprasConfiguracaoEntidade = new TComprasConfiguracaoEntidade;
        $obTComprasConfiguracaoEntidade->deletaResponsaveis();
        $arRespSessao = Sessao::read('arResponsaveisEntidades');
        if ($arRespSessao) {
            foreach ($arRespSessao as $registro) {
                $obTComprasConfiguracaoEntidade->setDado( 'parametro'    , 'responsavel'             );
                $obTComprasConfiguracaoEntidade->setDado( 'cod_entidade' , $registro['cod_entidade'] );
                $obTComprasConfiguracaoEntidade->setDado( 'valor'        , $registro['valor']        );
                $obTComprasConfiguracaoEntidade->inclusao();
            }
        }

        // Altera o parâmetro que define se o ID da Licitação vai ser criado automático ou manual.
        $obTConfiguracao->setDado( 'cod_modulo' , 35 );
        $obTConfiguracao->setDado( 'exercicio'  , Sessao::getExercicio() );
        $obTConfiguracao->setDado( 'parametro'  , 'numeracao_automatica' );
        $obTConfiguracao->setDado( 'valor', $_REQUEST['boIdCompraAutomatica'] );
        $obTConfiguracao->alteracao();
        
        // Altera o parâmetro que define se o ID da Licitação vai ser criado automático ou manual.
        $obTConfiguracao->setDado( 'cod_modulo' , 35 );
        $obTConfiguracao->setDado( 'exercicio'  , Sessao::getExercicio() );
        $obTConfiguracao->setDado( 'parametro'  , 'numeracao_automatica_licitacao' );
        $obTConfiguracao->setDado( 'valor', $_REQUEST['boIdLicitacaoAutomatica'] );
        $obTConfiguracao->alteracao();

        $obTConfiguracao->setDado( 'parametro' , 'data_fixa_solicitacao_compra' );
        $obTConfiguracao->recuperaPorChave($rsConfiguracao);

        if($rsConfiguracao->getNumLinhas()==1)
            $obTConfiguracao->exclusao();

        $obTConfiguracao->setDado( 'parametro' , 'data_fixa_compra_direta' );
        $obTConfiguracao->recuperaPorChave($rsConfiguracao);

        if($rsConfiguracao->getNumLinhas()==1)
            $obTConfiguracao->exclusao();

        foreach( $request->getAll() AS $key => $value ){
            $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
            $obTAdministracaoConfiguracaoEntidade->setDado("exercicio"    , Sessao::getExercicio());
            $obTAdministracaoConfiguracaoEntidade->setDado("cod_modulo"   , 35);

            if(strpos($key, 'stDtSolicitacao')!==FALSE){
                list ( $stRequest, $inCodEntidade, $inLinha ) = explode("_", $key);

                $obTAdministracaoConfiguracaoEntidade->setDado("cod_entidade" , $inCodEntidade);
                $obTAdministracaoConfiguracaoEntidade->setDado("parametro"    , "data_fixa_solicitacao_compra");
                $obTAdministracaoConfiguracaoEntidade->setDado("valor"        , $value);
                $obTAdministracaoConfiguracaoEntidade->recuperaPorChave($rsConfiguracao);

                if($rsConfiguracao->getNumLinhas()==1)
                    $obTAdministracaoConfiguracaoEntidade->alteracao();
                else
                    $obTAdministracaoConfiguracaoEntidade->inclusao();
            }

            if(strpos($key, 'stDtCompraDireta')!==FALSE){
                list ( $stRequest, $inCodEntidade, $inLinha ) = explode("_", $key);

                $obTAdministracaoConfiguracaoEntidade->setDado("cod_entidade" , $inCodEntidade);
                $obTAdministracaoConfiguracaoEntidade->setDado("parametro"    , "data_fixa_compra_direta");
                $obTAdministracaoConfiguracaoEntidade->setDado("valor"        , $value);
                $obTAdministracaoConfiguracaoEntidade->recuperaPorChave($rsConfiguracao);

                if($rsConfiguracao->getNumLinhas()==1)
                    $obTAdministracaoConfiguracaoEntidade->alteracao();
                else
                    $obTAdministracaoConfiguracaoEntidade->inclusao();
            }
        }

        Sessao::encerraExcecao();

        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração","alterar","aviso", Sessao::getId(), "../");
    break;
}

?>
