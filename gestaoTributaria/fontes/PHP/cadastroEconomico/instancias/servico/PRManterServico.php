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
    * Página de Processamento de Inclusao/Alteracao de Serviços
    * Data de Criação   : 22/11/2004

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: PRManterServico.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.02.03
*/

/*
$Log$
Revision 1.8  2006/09/15 14:33:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMServico.class.php"       );

$stAcao = $request->get('stAcao');

$link= Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterServico";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgFormNivel = "FM".$stPrograma."Nivel.php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";

$obRCEMServico = new RCEMServico;

switch ($stAcao) {

    case "incluir":

        // VERIFICA ESPAÇO ANTES DO NOME

        $obRCEMServico->setCodigoVigencia  ( $_REQUEST["inCodigoVigencia"]   );
        $obRCEMServico->setCodigoNivel     ( $_REQUEST["inCodigoNivel"]      );
        $obRCEMServico->setNomeServico     ( ltrim($_REQUEST["stNomeServico"])  );
        $obRCEMServico->setValorComposto   ( $_REQUEST['stChaveServico'] );
        //$obRCEMServico->setCodigoServico   ( $_REQUEST['inCodigoServico'] );

        if ($_REQUEST["flAliquota"]) {
            $obRCEMServico->setAliquotaServico( $_REQUEST["flAliquota"] );
        }
        $obRCEMServico->setValor ( preg_replace( "/^0*/", "", trim( $_REQUEST["inValorServico"] ) ) );
        //MONTAR UM LOOP PARA PEGAR O VALOR DOS COMBOS

        //for ($inContCombos = 1; $inContCombos < $_REQUEST["inNumNiveisServico"] ; $inContCombos++) {
        for ($inContCombos = 1; $inContCombos < $_REQUEST["inCodigoNivel"] ; $inContCombos++) {

            $arChaveServico = explode( "-", $_REQUEST["inCodServico_".$inContCombos] );
            //[0] = cod_nivel | [1] = cod_servico | [2] = valor | [3] = valor_reduzido
            $obRCEMServico->addCodigoServico( $arChaveServico );
        }

        $stLink  = "&stChaveNivel=".$_REQUEST["inCodigoVigencia"]."-".$_REQUEST["inCodigoNivel"];

        $obErro = $obRCEMServico->incluirServico();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgFormNivel.$stLink,"Nome Serviço: ".$_REQUEST["stNomeServico"],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":

        $obRCEMServico->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obRCEMServico->setCodigoNivel       ( $_REQUEST["inCodigoNivel"]    );
        $obRCEMServico->setCodigoServico     ( $_REQUEST["inCodServico"]     );
        $obRCEMServico->setNomeServico       ( $_REQUEST["stNomeServico"]    );
        $obRCEMServico->setValorReduzido     ( $_REQUEST["stValorReduzido"]  );
        $obRCEMServico->setValor             ( $_REQUEST["inValorServico"]   );
        $obRCEMServico->setDataInicio        ( $_REQUEST["dtDataInicio"]     );

        $obRCEMServico->setValor  ( preg_replace( "/^0*/", "",trim( $_REQUEST["inValorServico"] ) ) );
        if ($_REQUEST["flAliquota"]) {
            $obRCEMServico->setAliquotaServico( $_REQUEST["flAliquota"] );
        }
        $obErro = $obRCEMServico->alterarServico();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome Serviço: ".$_REQUEST["stNomeServico"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        $obRCEMServico->setCodigoNivel       ( $_REQUEST["inCodigoNivel"]       );
        $obRCEMServico->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"]    );
        $obRCEMServico->setCodigoServico     ( $_REQUEST["inCodigoServico"] );
        $obRCEMServico->setValorReduzido     ( $_REQUEST["stValorReduzido"]     );
        if ($_REQUEST["flAliquota"]) {
            $obRCEMServico->setAliquotaServico( $_REQUEST["flAliquota"] );
        }

        $obErro = $obRCEMServico->verificaFilhosServico($boTransacao);

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMServico->excluirServico($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome Serviço: ".$_REQUEST["stNomeServico"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
    case "aliquota":
        $obErro = new Erro;
        $obRCEMServico->setCodigoServico  ( $_REQUEST["inCodServico"] );
        $obRCEMServico->setAliquotaServico( $_REQUEST["flAliquota"]      );
        $obRCEMServico->setDataInicio     ( $_REQUEST["dtDataInicio"]    );
        if ( sistemaLegado::comparaDatas($_REQUEST["dtVigenciaAntiga"],$obRCEMServico->getDataInicio()) ) {
            $obErro->setDescricao("A data de início deve ser igual ou maior que a data atual.");
        }
        if (!$obErro->ocorreu()) {
            $obErro = $obRCEMServico->alterarAliquota();
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome Serviço: ".$_REQUEST["stNomeServico"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_aliquota","erro");
        }
    break;

}
