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
* Data de Criação: 01/09/2006

* @author Analista: Cleisson Barboza
* @author Desenvolvedor: Jose Eduardo Porto

$Id: OCEstruturalReceita.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-02.01.06
*/

/*
$Log: OCEstruturalReceita.php,v $
Revision 1.3  2007/08/20 19:55:32  hboaventura
Correção de bug com valores nulos

Revision 1.2  2007/07/18 14:58:39  vitor
Bug#8925#

Revision 1.1  2006/09/01 15:06:37  jose.eduardo
Inclusão de componente

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoReceita.class.php" );
switch ($_GET['stCtrl']) {
    case 'buscaPopup':
        if ($_GET[$_GET['stNomCampoCod']]) {
            $obROrcamentoClassificacaoReceita = new ROrcamentoClassificacaoReceita;

            $obROrcamentoClassificacaoReceita->setMascClassificacao( $_GET[$_GET['stNomCampoCod']] );
            $obROrcamentoClassificacaoReceita->setExercicio( Sessao::getExercicio() );
            $obROrcamentoClassificacaoReceita->consultar( $rsClassificacaoReceita );

            if ($rsClassificacaoReceita->getNumLinhas() > 0 ) {
                $stDescricao = $rsClassificacaoReceita->getCampo("descricao");
                $stJs .= "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."')";
            } else {
                $stJs  = "document.frm.".$_GET['stNomCampoCod'].".value='';";
                $stJs .= "document.frm.".$_GET['stNomCampoCod'].".focus();";
                $stJs .= "alertaAviso('Esta conta não é Orçamentária.','form','erro','".Sessao::getId()."');";
                $stJs .= "document.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';";
            }
        } else {
            $stJs .= "document.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';";
        }

    break;

    case 'receitaDedutora':
        if ($_GET[$_GET['stNomCampoCod']]) {
            $obROrcamentoClassificacaoReceita = new ROrcamentoClassificacaoReceita;
            $obROrcamentoClassificacaoReceita->setDedutora ( true );
            $obROrcamentoClassificacaoReceita->setMascClassificacao( $_GET[$_GET['stNomCampoCod']] );
            $obROrcamentoClassificacaoReceita->setExercicio( Sessao::getExercicio() );
            $obROrcamentoClassificacaoReceita->consultar( $rsClassificacaoReceita );
            if ($rsClassificacaoReceita->getNumLinhas() > 0 ) {
                $stDescricao = $rsClassificacaoReceita->getCampo("descricao");
                $stJs .= "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."')";
            } else {
                $stJs  = "document.frm.".$_GET['stNomCampoCod'].".value='';";
                $stJs .= "document.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';";
                $stJs .= "document.frm.".$_GET['stNomCampoCod'].".focus();";
                $stJs .= "alertaAviso('Esta conta não é uma Dedutora.','form','erro','".Sessao::getId()."');";
                $stJs .= "document.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';";
            }
        } else {
            $stJs .= "document.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';";
        }
    break;
}

if ($stJs) {
    echo $stJs;
}
?>
