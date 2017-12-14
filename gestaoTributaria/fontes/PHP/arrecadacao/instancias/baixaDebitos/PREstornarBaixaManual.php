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
    * Página de processamento para estornar baixa manual
    * Data de criação : 24/05/2006

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Fernando Piccini Cercato

    * $Id: PREstornarBaixaManual.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.10
**/

/*
$Log$
Revision 1.5  2007/06/13 14:01:52  cercato
Bug #9387#

Revision 1.4  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"                                            );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma    = "EstornarBaixaManual";
$pgList        = "LS".$stPrograma.".php";

$obErro          = new Erro;
$obRARRPagamento = new RARRPagamento;
switch ($stAcao) {
    case "estornar":
        $obRARRPagamento->setCodLote( $_REQUEST["inCodLote"] );
        $obRARRPagamento->setExercicio( $_REQUEST["inExercicio"] );
        $obRARRPagamento->obRARRCarne->setNumeracao( $_REQUEST["inNumeracao"] );
        $obRARRPagamento->obRMONAgencia->obRCGM->setNumCGM( $_REQUEST["inNumCGM"] );
        $obRARRPagamento->setOcorrenciaPagamento( $_REQUEST["inOcorrencia"] );
        $obRARRPagamento->obRARRCarne->obRMONConvenio->setCodigoConvenio( $_REQUEST["inConvenio"] );
        $obRARRPagamento->obRMONBanco->setCodBanco( $_REQUEST["inCodBanco"] );
        $obRARRPagamento->obRMONAgencia->setCodAgencia( $_REQUEST["inCodAgencia"] );

        $obErro = $obRARRPagamento->efetuarEstornoBaixaManual();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Estorno efetivado! Carnê: ".$_REQUEST['inNumeracao'],"estornar","aviso", Sessao::getId(), "../");

        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()), "n_estornar","erro" , Sessao::getId(), "../");
        }
    break;
}
