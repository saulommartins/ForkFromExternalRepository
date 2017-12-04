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
    * Página de Formulario de Anulação de Inscrição de Restos a Pagar do Exercício
    * Data de Criação   : 07/12/2007

    * @author Desenvolvedor: Anderson cAko Konze

    $Id: PRAnularRestosAPagar.php 65191 2016-04-29 20:02:55Z franver $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );
include_once ( CAM_GF_CONT_MAPEAMENTO."FContabilidadeEncerramento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AnularRestosAPagar";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$rsContas = $rsSaldo = new recordSet();
$obErro = new Erro;

$obTransacao = new Transacao;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
if ( !$obErro->ocorreu() ) {
    if ( !$obErro->ocorreu() ) {
        $obFContabilidadeEncerramento = new FContabilidadeEncerramento;
        $obFContabilidadeEncerramento->setDado("stExercicio",Sessao::getExercicio());
        $obFContabilidadeEncerramento->setDado("inCodEntidade",$_REQUEST['inCodEntidadeCredito']);
        $obErro = $obFContabilidadeEncerramento->anularRestosEncerramento($rsRetorno, $boTransacao);
        $inLotes = $rsRetorno->getCampo('fn_anular_restos_encerramento');
    }

    if ( !$obErro->ocorreu()) {
        $obRConfiguracaoConfiguracao = new RConfiguracaoConfiguracao;
        $obRConfiguracaoConfiguracao->setParametro( "virada_GF" );
        $obRConfiguracaoConfiguracao->setValor( "F" );
        $obRConfiguracaoConfiguracao->setCodModulo( 10 );
        $obRConfiguracaoConfiguracao->setExercicio( Sessao::getExercicio());
        $obErro = $obRConfiguracaoConfiguracao->alterar( $boTransacao );

        if ((Sessao::getExercicio() >= '2013') && (!$obErro->ocorreu())) {
            $obRConfiguracaoEntidade = new RConfiguracaoConfiguracao;
            $obRConfiguracaoEntidade->setParametro("virada_GF_entidade_".$_REQUEST['inCodEntidadeCredito']);
            $obRConfiguracaoEntidade->setValor( "F" );
            $obRConfiguracaoEntidade->setCodModulo( 10 );
            $obRConfiguracaoEntidade->setExercicio( Sessao::getExercicio());
            $obErro = $obRConfiguracaoEntidade->alterar( $boTransacao );

            if(!$obErro->ocorreu()){
                require_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php';
                $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
                $obTAdministracaoConfiguracaoEntidade->setDado("cod_modulo", 10 );
                $obTAdministracaoConfiguracaoEntidade->setDado("cod_entidade",$_REQUEST['inCodEntidadeCredito']);
                $obTAdministracaoConfiguracaoEntidade->setDado("exercicio" , Sessao::getExercicio() );
                $obTAdministracaoConfiguracaoEntidade->setDado("parametro" , 'virada_GF' );
                $obTAdministracaoConfiguracaoEntidade->setDado("valor" , 'f' );
                $obErro = $obTAdministracaoConfiguracaoEntidade->alteracao($boTransacao);
            }
            
        }
    }
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRConfiguracaoConfiguracao->obTConfiguracao );
echo "<script type='text/javascript'>LiberaFrames(true,false);</script>";
if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgFilt, Sessao::getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
} else
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
?>
