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
 * Página Oculta - Configuração Unidade Orçamentária
 * Data de Criação   : 16/01/2014

 * @author Analista: Eduardo Schitz
 * @author Desenvolvedor: Franver Sarmento de Moraes

 * @ignore

 * $Id: OCManterConfiguracaoUnidadeOrcamentaria.php 60484 2014-10-23 18:43:36Z lisiane $
 * $Name: $
 * $Revision: 60484 $
 * $Author: lisiane $
 * $Date: 2014-10-23 16:43:36 -0200 (Thu, 23 Oct 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);
include_once( CAM_GF_ORC_NEGOCIO. "ROrcamentoDespesa.class.php"                      );


$stCtrl = $_REQUEST['stCtrl'];
$boTransacao = new Transacao();

function processarForm($boExecuta = false, $stArquivo = "Form", $stAcao = "incluir", $rsRecordSet)
{       
    $stJs .= preencheValoresUnidade($rsRecordSet);
    SistemaLegado::executaFrameOculto($stJs);
}
// Acoes por pagina
switch ($stCtrl) {
    case "validaCGM":
        $obRCGM = new RCGM();
        $rsCGM = new RecordSet();
        
        $inNumCGM = $request->get($request->get('stNomCampoCod'));

        if ( !$inNumCGM == "" ) {
            $obRCGM->setNumCGM( $request->get($request->get('stNomCampoCod')) );
            $obRCGM->consultarCGM( $rsCGM, $boTransacao );
                if ($rsCGM->getNumLinhas() < 1) {
                    $stJs  = "alertaAviso('@Número do CGM (". $request->get($request->get('stNomCampoCod')) .") não encontrado no cadastro de Pessoa ', 'form','erro','".Sessao::getId()."');";
                    
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

    case "buscaValoresUnidade":
        $stJs .= buscaValoresUnidade();
    break;    
    
}


function buscaValoresUnidade()
{  
    if ($_REQUEST['stCodOrgaoUnidade']!= "") {
        $arIndice = explode('_' , $_REQUEST['stCodOrgaoUnidade']);
        $arCodOrgao = explode('-' , $_REQUEST['inCodOrgao_'.$arIndice[1]]);
        
        $obRDespesa = new ROrcamentoDespesa;
        $obRDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arCodOrgao[1]);
        $obRDespesa->obROrcamentoUnidadeOrcamentaria->listar($rsUnidade, " ORDER BY num_unidade");

        if ($rsUnidade->getNumLinhas() > -1) {
            $inContador = 1;
            $js .= "limpaSelect(f.inMontaCodUnidadeM_".$arIndice[1].",0); \n";
            $js .= "f.inMontaCodUnidadeM_".$arIndice[1].".options[0] = new Option('Selecione','');\n";
            while (!$rsUnidade->eof()) {
                $inMontaCodUnidadeM   = $rsUnidade->getCampo("num_orgao")."-".$rsUnidade->getCampo("num_unidade")."-".$rsUnidade->getCampo("exercicio");
                $stNomUnidade   = $rsUnidade->getCampo("num_unidade")."-".$rsUnidade->getCampo("nom_unidade");
               
                $js .= "f.inMontaCodUnidadeM_".$arIndice[1].".options[$inContador] = new Option('".$stNomUnidade."','".$inMontaCodUnidadeM."'); \n";
                $inContador++;
                   
                $rsUnidade->proximo();
            }
        } else {
            $js .= "limpaSelect(f.inMontaCodUnidadeM_".$arIndice[1].",0); \n";
            $js .= "f.inMontaCodUnidadeM_".$arIndice[1].".options[0] = new Option('Selecione','');\n";
            $js .= "f.inMontaCodUnidadeM_".$arIndice[1].".value = ''\n;";
        }
    } else {
        $js .= "limpaSelect(f.inMontaCodUnidadeM_".$arIndice[1].",0); \n";
        $js .= "f.inMontaCodUnidadeM_".$arIndice[1].".options[0] = new Option('Selecione','');\n";
    }

    return $js;
   
}  // fim buscaValoresUnidade.



function preencheValoresUnidade($rsRecordSet)
{
    $inContador =0;
    if ($rsRecordSet->getNumLinhas() > -1) {
        foreach($rsRecordSet->getElementos() as $unidadeOrcamentaria){
          $inContador++;
          if($unidadeOrcamentaria['num_orgao_atual']){
            $inMontaCodUnidRequest = $unidadeOrcamentaria["num_orgao_atual"]."-". $unidadeOrcamentaria["num_unidade_atual"]."-". $unidadeOrcamentaria["exercicio_atual"];
            $obRDespesa = new ROrcamentoDespesa;
            $obRDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($unidadeOrcamentaria['num_orgao_atual']);
            $obRDespesa->obROrcamentoUnidadeOrcamentaria->listar($rsUnidade, " ORDER BY num_unidade");
            
            $js .= "limpaSelect(f.inMontaCodUnidadeM_".$inContador.",0); \n";
            $js .= "f.inMontaCodUnidadeM_".$inContador.".options[0] = new Option('Selecione','');\n";
            $stFlagCombo = '';
            $inSelecionaCombo = '';
            $count = 1;
            
            while (!$rsUnidade->eof()) {
                $inMontaCodUnidadeM = $rsUnidade->getCampo("num_orgao")."-".$rsUnidade->getCampo("num_unidade")."-".$rsUnidade->getCampo("exercicio");
                $stNomUnidade   = $rsUnidade->getCampo("num_unidade")."-".$rsUnidade->getCampo("nom_unidade");
                $selected       = "";
                if ($inMontaCodUnidadeM == $inMontaCodUnidRequest) {
                    $inSelecionaCombo = $inMontaCodUnidadeM;
                    $selected = "selected";
                }
                $js .= "f.inMontaCodUnidadeM_".$inContador.".options[$count] = new Option('".$stNomUnidade."','".$inMontaCodUnidadeM."'); \n";
                $count++;
                
                if ($selected == '') {
                    $rsUnidade->proximo();
                } else {
                    $stFlagCombo = $selected;
                    $rsUnidade->proximo();
                }
            }
            $js .= "f.inMontaCodUnidadeM_".$inContador.".value = '".$inSelecionaCombo."'\n;";
          }
        }
    }
    return $js;
}

if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}

?>
