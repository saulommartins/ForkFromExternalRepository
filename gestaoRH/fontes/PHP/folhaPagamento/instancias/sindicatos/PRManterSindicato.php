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
* Página de processamento Folha Pagamento Sindicato
* Data de Criação   : 26/11/2004

* @author Analista: ???
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 31694 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSindicato.class.php" );

$link = Sessao::read("link");
$stAcao = $request->get('stAcao');
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterSindicato";
$pgFilt      = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgList      = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgForm      = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgProc      = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgOcul      = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;

$obRSindicato  = new RFolhaPagamentoSindicato;

switch ($stAcao) {

    case "incluir":
        $obRSindicato->obRCGM->setNumCGM       ( $_POST['inNumCGM']    );
        $obRSindicato->setDataBase             ( $_POST['inDataBase']  );
        $obRSindicato->obRFolhaPagamentoEvento->setCodigo ( $_POST['inCodEvento'] );
        $obRSindicato->obRFolhaPagamentoEvento->consultarCodigo();
        $obErro = $obRSindicato->incluir();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Sindicato: ".$_POST['inNumCGM'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obRSindicato->obRCGM->setNumCGM ( $_POST['inNumCGM']    );
        $obRSindicato->setDataBase       ( $_POST['inDataBase']  );
        $obRSindicato->obRFolhaPagamentoEvento->setCodigo ( $_POST['inCodEvento'] );

        $obErro = $obRSindicato->alterar();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Sindicato: ".$_POST['inNumCGM'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        $obRSindicato->obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );
        $obErro = $obRSindicato->excluir();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Sindicato: ".$_REQUEST['inNumCGM'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}
?>
