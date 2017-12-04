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
    * Página de Processamento de Duplicação de Autorização
    * Data de Criação   : 09/05/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08
*/

/*
$Log$
Revision 1.8  2007/09/06 20:39:10  luciano
Ticket#9094#

Revision 1.7  2007/02/23 15:15:05  gelson
Sempre que for autorização tem que ir a reserva. Adicionado em todos arquivos o caso de uso da reserva.

Revision 1.6  2006/10/19 19:25:44  larocca
Bug #7245#

Revision 1.5  2006/07/05 20:47:28  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
//Define o nome dos arquivos PHP
$stPrograma = "DuplicarAutorizacao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRegra = new REmpenhoEmpenhoAutorizacao;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
switch ($stAcao) {
    case "duplicar":
        $obRegra->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao                       ( $_REQUEST['inCodAutorizacao'] );
        $obRegra->obREmpenhoAutorizacaoEmpenho->setExercicio                            ( $_REQUEST['stExercicio']      );
        $obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade ( $_REQUEST['inCodEntidade']    );
        $obRegra->obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho                        ( $_REQUEST['inCodPreEmpenho']  );
        $obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setCodReserva      ( $_REQUEST['inCodReserva']     );

        if ($_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3) {
            $obRegra->obREmpenhoAutorizacaoEmpenho->obTEmpenhoContrapartidaAutorizacao->setDado( 'cod_autorizacao'     , $_REQUEST['inCodAutorizacao'] );
            $obRegra->obREmpenhoAutorizacaoEmpenho->obTEmpenhoContrapartidaAutorizacao->setDado( 'exercicio'           , $_REQUEST['stExercicio']      );
            $obRegra->obREmpenhoAutorizacaoEmpenho->obTEmpenhoContrapartidaAutorizacao->setDado( 'cod_entidade'        , $_REQUEST['inCodEntidade']    );
            $obRegra->obREmpenhoAutorizacaoEmpenho->obTEmpenhoContrapartidaAutorizacao->recuperaContrapartidaLancamento( $rsContrapartida );

            if( $rsContrapartida->getNumLinhas() > 0 )
                $obRegra->obREmpenhoAutorizacaoEmpenho->obTEmpenhoContrapartidaAutorizacao->setDado( 'conta_contrapartida' , $rsContrapartida->getCampo('conta_contrapartida'));
        }

        $obErro = $obRegra->obREmpenhoAutorizacaoEmpenho->duplicar($boTransacao);

        if ( !$obErro->ocorreu() ) {
            $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&inCodAutorizacao=".$obRegra->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao();
            $stCampos .= '&inCodAutorizacaoAnulada='.$_REQUEST['inCodAutorizacao'];
            $stCampos .= '&inCodPreEmpenho='.$obRegra->obREmpenhoAutorizacaoEmpenho->getCodPreEmpenho();
            $stCampos .= '&inCodPreEmpenhoAnulada='.$_REQUEST['inCodPreEmpenho'];
            $stCampos .= '&inCodEntidade='.$obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade();
            $stCampos .= '&inCodDespesa='.$_POST['inCodDespesa'];
            $stCampos .= '&stExercicio='.$_REQUEST['stExercicio'];

            SistemaLegado::alertaAviso($pgFilt.$stCampos,$obRegra->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()."/".Sessao::getExercicio(),"incluir","aviso", Sessao::getId(), "../");
        } else
            SistemaLegado::alertaAviso($pgList.$stCampos,urlencode($obErro->getDescricao()),"erro","erro", Sessao::getId(), "../");
    break;
}
?>
