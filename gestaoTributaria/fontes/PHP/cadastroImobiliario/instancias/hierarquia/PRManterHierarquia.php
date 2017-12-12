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
    * Página de processamento para o cadastro de hierarquia
    * Data de Criação   : 25/08/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: PRManterHierarquia.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.01.02
*/

/*
$Log$
Revision 1.4  2006/09/18 10:30:39  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php"       );

$stAcao = $request->get('stAcao');

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterHierarquia";
$pgFilt = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS   = "JS".$stPrograma.".js";

$obRCIMNivel = new RCIMNivel;
$inCodAtributosSelecionados = $_REQUEST["inCodAtributoSelecionados"];
switch ($stAcao) {
    case "incluir":
        $obRCIMNivel->setCodigoVigencia ( $_REQUEST["inCodigoVigencia"] );
        $obRCIMNivel->setNomeNivel      ( $_REQUEST["stNomeNivel"]      );
        $obRCIMNivel->setMascara        ( $_REQUEST["stMascaraNivel"]   );
        for ($inCount=0; $inCount<count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRCIMNivel->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo);
        }
        $obErro = $obRCIMNivel->incluirNivel();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Nome do nível: ".$_REQUEST['stNomeNivel'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obRCIMNivel->setCodigoNivel    ( $_REQUEST["inCodigoNivel"]    );
        $obRCIMNivel->setCodigoVigencia ( $_REQUEST["inCodigoVigencia"] );
        $obRCIMNivel->setNomeNivel      ( $_REQUEST["stNomeNivel"]      );
        $obRCIMNivel->setMascara        ( $_REQUEST["stMascaraNivel"]   );
        for ($inCount=0; $inCount<count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRCIMNivel->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo);
        }
        $obErro = $obRCIMNivel->alterarNivel();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome do nível: ".$_REQUEST['stNomeNivel'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        $obRCIMNivel->setCodigoNivel    ( $_REQUEST["inCodigoNivel"]    );
        $obRCIMNivel->setCodigoVigencia ( $_REQUEST["inCodigoVigencia"] );
        $obErro = $obRCIMNivel->excluirNivel();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome do nível: ".$_REQUEST['stNomeNivel'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
}
?>
