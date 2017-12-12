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

$Revision: 30824 $
$Name$
$Author: tonismar $
$Date: 2007-07-27 19:40:00 -0300 (Sex, 27 Jul 2007) $

Casos de uso: uc-02.01.33, uc-02.01.06
*/

/*
$Log$
Revision 1.7  2007/07/27 22:40:00  tonismar
Bug#9112#

Revision 1.6  2007/05/24 20:42:45  hboaventura
Bug #8231#

Revision 1.5  2007/03/15 18:01:46  vitor
#8632#

Revision 1.4  2006/10/30 13:11:44  rodrigo
Correção da função js retornaValorBscInner aonde faltava um ponto e virgula.

Revision 1.3  2006/08/28 11:05:28  jose.eduardo
Ajustes no componente

Revision 1.2  2006/08/25 17:03:12  jose.eduardo
caso de uso

Revision 1.1  2006/08/25 16:15:41  jose.eduardo
Inclusao

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php" );

function buscaPopup()
{
    global $request;
    $stJs = isset($stJs) ? $stJs : "";
    $stNomDespesa = "";

    if ($_GET['stUsaEntidade'] == "S") {
        if ( ( $request->get($request->get('stNomSelectMultiplo')) && is_array($request->get($request->get('stNomSelectMultiplo'))) ) || ( $request->get('inCodEntidade') && !is_array($request->get('inCodEntidade')) ) ) {
            if ( $_GET[$_GET['stNomSelectMultiplo']] && is_array($_GET[$_GET['stNomSelectMultiplo']]) ) {
                $stEntidades = "";
                foreach ($_GET[$_GET['stNomSelectMultiplo']] as $key => $valor) {
                    $stEntidades .= $valor . ",";
                }
                $stEntidades = substr($stEntidades, 0, strlen($stEntidades) - 1);
            } elseif ( $_GET['inCodEntidade'] && !is_array($_GET['inCodEntidade']) ) {
                $stEntidades = $_GET['inCodEntidade'];
            }
            $obROrcamentoDespesa = new ROrcamentoDespesa;
            $obROrcamentoDespesa->setExercicio(Sessao::getExercicio() );
            $obROrcamentoDespesa->setCodDespesa( $_GET[$_GET['stNomCampoCod']] );
            $obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
            $obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $stEntidades );
            if (!$_REQUEST['stAutorizacao']) {
                $obErro = $obROrcamentoDespesa->listarDespesa( $rsDespesa );
            } else {
                $obErro = $obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );
            }
           if (!$obErro->ocorreu()) {
                $stNomDespesa = $rsDespesa->getCampo( "descricao" );
            }
           if ($rsDespesa->eof()) {
            $stJs .= "alertaAviso('Dotação inválida para entidade selecionada.','frm','erro','".Sessao::getId()."'); \n";
           }

      } else {
            $stJs .= "alertaAviso('É necessário informar uma entidade para a conta.','frm','erro','".Sessao::getId()."'); \n";
        }
    } else {
        $obROrcamentoDespesa = new ROrcamentoDespesa;
        $obROrcamentoDespesa->setExercicio(Sessao::getExercicio() );
        $obROrcamentoDespesa->setCodDespesa( $_GET[$_GET['stNomCampoCod']] );
        $obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        if ($_REQUEST['autorizacaoOrcamento']) {
            $obErro = $obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );
        } else {
            $obErro = $obROrcamentoDespesa->listarDespesaUsuarioOrcamento( $rsDespesa );
        }
        if ($rsDespesa->getNumLinhas() > 0) {
            $stNomDespesa = $rsDespesa->getCampo( "descricao" );
        } else {
            $stJs .= "alertaAviso('Dotação inválida.(".$_GET[$_GET['stNomCampoCod']].")','aviso','aviso','" . Sessao::getId() . "');\n";
        }
    }
   $stJs .= "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stNomDespesa."');";

    return $stJs;
}

$stJs = isset($stJs) ? $stJs : "";

switch ($_GET['stCtrl']) {
    case 'buscaPopup':
        $stJs .= buscaPopup();
    break;
}
echo $stJs;
if ($stJs) {
    echo $stJs;
}
?>
