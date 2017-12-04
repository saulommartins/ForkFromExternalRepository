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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00, uc-02.02.02, uc-03.01.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once '../../legado/mascarasLegado.lib.php';
//include_once( CAM_REGRA."REntidadeOrcamento.class.php"   );
include_once (  CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php");

$obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;

switch ($_GET['stCtrl']) {

    case 'buscaPopup':

 if ($_POST[$_GET['stNomCampoCod']] != "") {

        if ($_GET['stTipoBusca'] == "banco") {
             $obRContabilidadePlanoContaBanco = new RContabilidadePlanoBanco;

             $obRContabilidadePlanoContaBanco->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
             $obRContabilidadePlanoContaBanco->setExercicio( Sessao::getExercicio() );
             $obErro = $obRContabilidadePlanoContaBanco->consultar();

             $codAgencia =     $obRContabilidadePlanoContaBanco->obRConfiguracaoAgencia->getCodAgencia();
             if ($codAgencia <> "") {
                $stDescricao = $obRContabilidadePlanoContaBanco->getNomConta();
             } else {
                 exibeAviso(urlencode($obRContabilidadePlanoContaBanco->getNomConta()." não é uma conta de banco"),"n_incluir","erro");
             }

        } elseif ($_GET['stTipoBusca'] == 'orcamento_extra') {
            if ($_POST['stTipoReceita'] == 'orcamentaria') {
                $obRContabilidadePlanoContaAnalitica->setCodEstrutural( '4' );
            } elseif ($_POST['stTipoReceita'] == 'extra') {
                $obRContabilidadePlanoContaAnalitica->setCodEstrutural( '1.1.2' );
            }
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->listarPlanoConta( $rsRecordSet );
            if( substr( $rsRecordSet->getCampo( 'cod_estrutural' ), 0, 1 ) == '4' or substr( $rsRecordSet->getCampo( 'cod_estrutural' ), 0, 5 ) == '1.1.2' )
                $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );

        } else {
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->consultar();
            $stDescricao = $obRContabilidadePlanoContaAnalitica->getNomConta();
        }
 }
 executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
 break;
}
