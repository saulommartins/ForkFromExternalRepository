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
    * Página de Processamento de Vigencia
    * Data de Criação   : 17/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo
    * @ignore

    $Revision: 16567 $
    $Name$
    $Author: larocca $
    $Date: 2006-10-09 11:50:28 -0300 (Seg, 09 Out 2006) $

    * Casos de uso: uc-05.02.06
                    uc-03.03.03

*/

/*
$Log$
Revision 1.10  2006/10/09 14:50:28  larocca
Bug #6882#

Revision 1.8  2006/07/06 14:02:43  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:09:53  diego

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoMarca.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
//Define o nome dos arquivos PHP
$stPrograma = "ManterMarca";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RAlmoxarifadoMarca;
$obErro  = new Erro;

switch ($stAcao) {

    case "incluir":
        $obRegra->setDescricao( $_REQUEST['stDescricaoMarca'] );

            $obErro = $obRegra->incluir();
            if( !$obErro->ocorreu() )
                sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Marca: ".$obRegra->getCodigo().'-'.$obRegra->getDescricao(),"incluir","aviso", Sessao::getId(), "../");
            else
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;
    case "excluir":
        $obRegra->setCodigo( $_REQUEST['inCodigo'] );
        $obRegra->setDescricao( $_REQUEST['stDescricaoMarca'] );
            $obErro = $obRegra->excluir();
            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Marca: ".$obRegra->getCodigo().'-'.$obRegra->getDescricao(),"excluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","A Marca ".$obRegra->getCodigo()." - ".$obRegra->getDescricao()." já está sendo usada pelo sistema","n_excluir","erro", Sessao::getId(), "../");
                //sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
            }
     break;
    case "alterar":
        $obRegra->setCodigo( $_REQUEST['inCodigo'] );
        $obRegra->consultar();
        $obRegra->setDescricao( $_REQUEST['stDescricaoMarca'] );
           $obErro = $obRegra->alterar();

            if( !$obErro->ocorreu() )
                sistemaLegado::alertaAviso($pgList,"Marca: ".$obRegra->getCodigo().'-'.$obRegra->getDescricao(),"alterar","aviso", Sessao::getId(), "../");
            else
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

    break;

}
