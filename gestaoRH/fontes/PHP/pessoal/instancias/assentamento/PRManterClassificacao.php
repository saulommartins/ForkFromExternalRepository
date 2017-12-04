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
* Página de processamento Classificação Assentamento
* Data de Criação   : 28/01/2005

* @author Analista:
* @author Programador: Lucas Leusin Oaigen

* @ignore

$Revision: 30860 $
$Name$
$Author: andre $
$Date: 2007-04-10 10:19:08 -0300 (Ter, 10 Abr 2007) $

Caso de uso: uc-04.04.08
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalClassificacaoAssentamento.class.php"  );

$obRPessoalClassificacaoAssentamento  = new RPessoalClassificacaoAssentamento;

$arLink = Sessao::read("pg");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"]."&inFiltroCodTipo=".$_POST['inFiltroCodTipo'];

//Define o nome dos arquivos PHP
$stPrograma = "ManterClassificacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case "incluir":
        $obRPessoalClassificacaoAssentamento->setDescricao  ( $_POST['stDescricao'] );
        $obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );
        $obErro = new erro;
        if ( $rsClassificacao->getNumLinhas() > 0 ) {
            $obErro->setDescricao('Já existe uma classificação com essa descrição!');
        }
        if ( !$obErro->ocorreu() ) {
            $obRPessoalClassificacaoAssentamento->setCodTipo    ( $_POST['inCodTipo']   );
            $obErro = $obRPessoalClassificacaoAssentamento->incluirClassificacao();
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm,$_POST['stDescricao'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obErro = new erro;

        $obRPessoalClassificacaoAssentamento->setDescricao                      ( $_POST['stDescricao']         );
        $obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento   ( $_POST['inCodClassificacao']  );
        $obRPessoalClassificacaoAssentamento->setCodTipo                        ( $_POST['inCodTipoTxt']        );
        $obErro = $obRPessoalClassificacaoAssentamento->alterarClassificacao();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,$_POST['stDescricao'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":

        $obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $_GET['inCodClassificacao'] );
        $obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );
        $obErro = $obRPessoalClassificacaoAssentamento->excluirClassificacao();

        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgList,"Classificação: ".$_GET['stDescricao'],"excluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::alertaAviso($pgList," Classificação '".$_GET['stDescricao']."', está sendo utilizada pelo sistema." ,"n_excluir","erro", Sessao::getId(), "../");
    break;

}
?>
