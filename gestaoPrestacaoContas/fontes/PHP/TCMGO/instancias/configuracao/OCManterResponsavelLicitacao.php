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
 * Página de Filtro de Responsavel Licitacao
 * Data de Criação   : 21/01/2015
 * @author Analista: Ane Caroline Fiegenbaum Pereira
 * @author Desenvolvedor: Evandro Melos
 * $Id: $
 * $Name: $
 * $Revision: $
 * $Author: $
 * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterResponsavelLicitacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once ($pgJS);

$stCtrl = $request->get('stTipoBusca');
$boTransacao = new Transacao();

// Acoes por pagina
switch ($stCtrl) {
    case "validaCGM":
        $obTCGM = new TCGM();        
        $rsCGM = new RecordSet();
        
        $inNumCGM = $request->get($request->get('stNomCampoCod'));
        if ( !$inNumCGM == "" ) {            
            
            $filtroVinculo  =" AND SW_CGM.numcgm = ".$inNumCGM; 
            $filtroVinculo .=" AND comissao_licitacao.cod_licitacao  = ".$request->get('inCodLicitacao');
            $filtroVinculo .=" AND comissao_licitacao.cod_comissao   = ".$request->get('inCodComissao');
            $filtroVinculo .=" AND comissao_licitacao.cod_modalidade = ".$request->get('inCodModalidade');
            
            $obTCGM->recuperaRelacionamentoVinculadoComissaoLicitacao($rsCGM, "" ,"" , $boTransacao, "" , "", $filtroVinculo);
                
            if ($rsCGM->getNumLinhas() < 1) {
                $stJs  = "alertaAviso('@Número do CGM (". $request->get($request->get('stNomCampoCod')) .") não encontrado no cadastro de comissão desta licitação ', 'form','erro','".Sessao::getId()."');";
                
                $stNomCampoCod = $request->get('stNomCampoCod');
                $stIdCampoDesc = $request->get('stIdCampoDesc');
                $stJs .= " d.getElementById('".$stNomCampoCod."').value = ''; ";
                $stJs .= " d.getElementById('".$stIdCampoDesc."').innerHTML = '&nbsp;'; ";
                
            }else{
                $stNomCGM = $rsCGM->getCampo('nom_cgm');
                $stJs = "retornaValorBscInner( '".$request->get('stNomCampoCod')."', '".$request->get('stIdCampoDesc')."', 'frm', '".str_replace("'", "\'", $stNomCGM)."');";
            } 

        }else{
            $stNomCampoCod = $request->get('stNomCampoCod');
            $stIdCampoDesc = $request->get('stIdCampoDesc');
            $stJs  = " d.getElementById('".$stNomCampoCod."').value = ''; ";
            $stJs .= " d.getElementById('".$stIdCampoDesc."').innerHTML = '&nbsp;'; ";
        }
    break;
}

if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}

?>
