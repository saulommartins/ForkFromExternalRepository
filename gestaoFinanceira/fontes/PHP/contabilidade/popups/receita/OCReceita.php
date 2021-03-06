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
    * Página de Listagem de Plano Conta
    * Data de Criação   : 13/07/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: zank $
    $Date: 2007-02-14 09:07:57 -0200 (Qua, 14 Fev 2007) $

    * Casos de uso: uc-02.02.02,uc-02.04.04
*/

/*
$Log$
Revision 1.7  2007/02/14 11:07:57  luciano
#8363#

Revision 1.6  2006/07/05 20:51:45  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );

$obROrcamentoReceita = new ROrcamentoReceita();

switch ($_GET['stCtrl']) {

case 'buscaPopup':
    if ($_POST[$_GET['stNomCampoCod']] != "") {
        $obROrcamentoReceita->setExercicio( Sessao::getExercicio() );
        $obROrcamentoReceita->setCodReceita( $_POST[$_GET["stNomCampoCod"]] );
        $obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade( $_POST["inCodEntidade"] );

        if ($_REQUEST['stTipoBusca']=="bancoDeducao") {
            $obErro = $obROrcamentoReceita->listarReceitaDedutora( $rsLista );
        } elseif ($_REQUEST['stTipoBusca']=="receitaArrec") {
            $obErro = $obROrcamentoReceita->listarReceitaAnalitica( $rsLista );
            if (!$rsLista->getCampo('descricao')) {
                SistemaLegado::exibeAviso($obROrcamentoReceita->getCodReceita().' - Código de receita não existe ou não pertence a entidade selecionada!');
            }
        } elseif ($_REQUEST['stTipoBusca']=="receitaDeducaoArrec") {
            $obErro = $obROrcamentoReceita->listarReceitaDedutoraAnalitica( $rsLista );
        } else {
            $obErro = $obROrcamentoReceita->listarReceita( $rsLista );
        }

        if ( !$obErro->ocorreu() ) {
            $stDescricao = $rsLista->getCampo( "descricao" );
        }
    }
    SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
 break;
}
