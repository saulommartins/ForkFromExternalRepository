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
* Arquivo instância para popup de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

* $Id: OCContaBanco.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php");

function buscaPopup()
{

    if ($_GET['stUsaEntidade'] == "S") {
        if ( ( $_GET[$_GET['stNomSelectMultiplo']] && is_array($_GET[$_GET['stNomSelectMultiplo']]) ) || ( $_GET['inCodEntidade'] && !is_array($_GET['inCodEntidade']) )) {
            $obRContabilidadePlanoContaBanco = new RContabilidadePlanoBanco;

            $obRContabilidadePlanoContaBanco->setCodPlano( $_GET[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaBanco->setExercicio( Sessao::getExercicio() );
            $obErro = $obRContabilidadePlanoContaBanco->consultar();

            $codAgencia = $obRContabilidadePlanoContaBanco->obRMONAgencia->getCodAgencia();
            if ($codAgencia <> "") {
               if ($_GET[$_GET['stNomSelectMultiplo']] && is_array( $_GET[$_GET['stNomSelectMultiplo']] )) {
                   if (array_search ($obRContabilidadePlanoContaBanco->obROrcamentoEntidade->getCodigoEntidade(), $_GET[$_GET['stNomSelectMultiplo']]) === false) {
                       $stJs .= "alertaAviso('".$obRContabilidadePlanoContaBanco->getNomConta()." - Entidade diferente da informada','frm','erro','".Sessao::getId()."'); \n";
                   } else {
                       $stDescricao = $obRContabilidadePlanoContaBanco->getNomConta();
                   }
               } else {
                   if ($_GET['inCodEntidade'] AND ($obRContabilidadePlanoContaBanco->obROrcamentoEntidade->getCodigoEntidade() <> $_GET['inCodEntidade'])) {
                       $stJs .= "alertaAviso('".$obRContabilidadePlanoContaBanco->getNomConta()." - Entidade diferente da informada','frm','erro','".Sessao::getId()."'); \n";
                   } else {
                       $stDescricao = $obRContabilidadePlanoContaBanco->getNomConta();
                   }
               }
            } else {
                $stJs .= "alertaAviso('".$obRContabilidadePlanoContaBanco->getCodPlano()." - Não é uma Conta de Banco','frm','erro','".Sessao::getId()."'); \n";
            }
        } else {
            $stJs .= "alertaAviso('É necessário informar uma entidade para a conta.','frm','erro','".Sessao::getId()."'); \n";
        }
    } else {
        $obRContabilidadePlanoContaBanco = new RContabilidadePlanoBanco;

        $obRContabilidadePlanoContaBanco->setCodPlano( $_GET[$_GET['stNomCampoCod']] );
        $obRContabilidadePlanoContaBanco->setExercicio( Sessao::getExercicio() );
        $obErro = $obRContabilidadePlanoContaBanco->consultar();

        $codAgencia = $obRContabilidadePlanoContaBanco->obRMONAgencia->getCodAgencia();
        if ($codAgencia <> "") {
            $stDescricao = $obRContabilidadePlanoContaBanco->getNomConta();
        } else {
            $stJs .= "alertaAviso('".$obRContabilidadePlanoContaBanco->getCodPlano()." - Não é uma Conta de Banco','frm','erro','".Sessao::getId()."'); \n";
        }
    }
    $stJs .= "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."')";

    return $stJs;
}
switch ($_GET['stCtrl']) {
    case 'buscaPopup':
        $stJs .= buscaPopup();
    break;
}
if ($stJs) {
    echo $stJs;
}
?>
