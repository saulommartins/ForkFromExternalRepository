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
    * Arquivo de processamento do popup de busca de Instituição/Entidade
    * Data de Criação: 02/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"               );
include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"             );

function buscaPopup()
{
    if ($_REQUEST[ $_GET['stNomCampoCod'] ] != "") {
        if (!$_REQUEST['boFiltro']) {
            $obRegra = new RCGMPessoaJuridica();
            $obRegra->setNumCGM( $_GET[$_GET['stNomCampoCod']] );
            $obRegra->consultarCGM($rsCGM);
            $stNomCGM = addslashes($obRegra->getNomCGM());
            $stMensagem = "@Número do CGM (". $_GET[ $_GET['stNomCampoCod'] ] .") não encontrado no cadastro de Pessoa ".$_REQUEST["stTipoBusca"];
        } else {
            if ($_REQUEST['boInstituicao']) {
                include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioInstituicaoEnsino.class.php");
                $obTEstagioInstituicaoEnsino = new TEstagioInstituicaoEnsino();
                $stFiltro = " AND sw_cgm.numcgm = ".$_GET[$_GET['stNomCampoCod']];
                $obTEstagioInstituicaoEnsino->recuperaRelacionamento($rsLista,$stFiltro," sw_cgm.nom_cgm");
                $stNomCGM = addslashes($rsLista->getCampo("nom_cgm"));
                $stMensagem = "@Número do CGM (". $_GET[ $_GET['stNomCampoCod'] ] .") não encontrado no cadastro de Instituição de Ensino ";
            } else {
                include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadora.class.php");
                $obTEstagioEntidadeIntermediadora = new TEstagioEntidadeIntermediadora();
                $stFiltro = " AND sw_cgm.numcgm = ".$_GET[$_GET['stNomCampoCod']];
                $obTEstagioEntidadeIntermediadora->recuperaEntidadesIntermediarias($rsLista,$stFiltro," sw_cgm.nom_cgm");
                $stNomCGM = addslashes($rsLista->getCampo("nom_cgm"));
                $stMensagem = "@Número do CGM (". $_GET[ $_GET['stNomCampoCod'] ] .") não encontrado no cadastro de Entidade Intermediadora ";
            }
        }
    }

    if ($stNomCGM == '' && $_REQUEST["stTipoBusca"]) {
        $stJs .= "alertaAviso('".$stMensagem."', 'form','erro','".Sessao::getId()."');";
    }
    $stJs .= "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_REQUEST['stIdCampoDesc']."', 'frm', '".$stNomCGM."');";

    return $stJs;
}

function preencherDados()
{
    $stNomCGM       = "";
    $stCNPJ         = "";
    $stEndereco     = "";
    $stBairro       = "";
    $stCidade       = "";
    $stTelefone     = "";
    if ($_GET['inCGM'] != "") {
        $rsCGM = new RecordSet();
        $rsMunicipio = new RecordSet();
        include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php");
        $obTCGMPessoaJuridica = new TCGMPessoaJuridica();
        $stFiltro = " AND sw_cgm.numcgm = ".$_GET['inCGM'];
        $obTCGMPessoaJuridica->recuperaDadosPessoaJuridica($rsCGM,$stFiltro);
        $stNomCGM       = $rsCGM->getCampo("nom_cgm");
        $stCNPJ         = $rsCGM->getCampo("cnpj");
        $stEndereco     = $rsCGM->getCampo("endereco");
        $stBairro       = $rsCGM->getCampo("bairro");
        $stCidade       = $rsCGM->getCampo("nom_municipio");
        $stTelefone     = $rsCGM->getCampo("fone_comercial");
    }
    $stJs .= "d.getElementById('stCNPJ').innerHTML = '$stCNPJ';             \n";
    $stJs .= "d.getElementById('stEndereco').innerHTML = '$stEndereco';     \n";
    $stJs .= "d.getElementById('stBairro').innerHTML = '$stBairro';         \n";
    $stJs .= "d.getElementById('stCidade').innerHTML = '$stCidade';         \n";
    $stJs .= "d.getElementById('stTelefone').innerHTML = '$stTelefone';     \n";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case 'buscaPopup':
       $stJs = buscaPopup();
    break;
    case 'preencherDados':
       $stJs = preencherDados();
    break;
}
if ($stJs) echo $stJs;

?>
