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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(CAM_GRH_BEN_MAPEAMENTO."TBeneficioLayoutFornecedor.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ConfiguracaoPlanoSaude";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stJs = '';
$stCtrl = $_REQUEST['stCtrl'];

$arLista = Sessao::read('arLista');

switch ($stCtrl) {
    case 'montaLista':
        $inCount = 0;
        $obTBeneficioLayoutFornecedor = new TBeneficioLayoutFornecedor();
        $obTBeneficioLayoutFornecedor->recuperaListaCompleta($rsLista);
        
        if ($rsLista->getNumLinhas() > 0) {
            foreach ($rsLista->getElementos() AS $arValue) {
                $arLista[$inCount]['id']             = $inCount;
                $arLista[$inCount]['cgm_fornecedor'] = $arValue['cgm_fornecedor'];
                $arLista[$inCount]['nom_cgm']        = $arValue['nom_cgm'];
                $arLista[$inCount]['cod_layout']     = $arValue['cod_layout'];
                $arLista[$inCount]['padrao']         = $arValue['padrao'];
                $arLista[$inCount]['excluido']       = 'n';            
                $inCount++;
            }
            
            Sessao::write('arLista', $arLista);
            echo montaLista( $arLista );
        }
        
    break;
    
    case 'incluiVinculo' :
        $arLista = Sessao::read('arLista');
        $stMensagem = executaValidacao($_REQUEST);
        
        if ($stMensagem == "" ) {
            if($_REQUEST['id'] == "") {
                   $arTmp['id']             = count($arLista);
                   $arTmp['cgm_fornecedor'] = $_REQUEST['inCGMFornecedor'];
                   $arTmp['nom_cgm']        = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='.$_REQUEST['inCGMFornecedor']);
                   $arTmp['cod_layout']     = $_REQUEST['inLayout'];
                   $arTmp['padrao']         = SistemaLegado::pegaDado('padrao', 'beneficio.layout_plano_saude',  'WHERE cod_layout ='.$_REQUEST['inLayout']);  
                   $arTmp['excluido'] = 'n';                  
                   $arLista[] = $arTmp;  
            } else {
                 for($i=0; $i< count($arLista); $i++){      
                    
                    if($arLista[$i]['id'] == $_REQUEST['id'] &&($_REQUEST['inCGMFornecedor']!= $lista['cgm_fornecedor'] || $_REQUEST['inLayout'] != $lista['cod_layout'] ) ){              
                        $cont = count($arLista);
                        $arLista[$cont]['id']             = $cont;
                        $arLista[$cont]['cgm_fornecedor'] = $arLista[$i]['cgm_fornecedor'];
                        $arLista[$cont]['nom_cgm']        = $arLista[$i]['nom_cgm'] ;
                        $arLista[$cont]['cod_layout']     = $arLista[$i]['cod_layout']  ;
                        $arLista[$cont]['padrao']         = $arLista[$i]['padrao'] ;    
                        $arLista[$cont]['excluido'] = 's';
                        
                        $arLista[$i]['id']             = $_REQUEST['id'];
                        $arLista[$i]['cgm_fornecedor'] = $_REQUEST['inCGMFornecedor'];
                        $arLista[$i]['nom_cgm']        = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='.$_REQUEST['inCGMFornecedor']);
                        $arLista[$i]['cod_layout']     = $_REQUEST['inLayout'];
                        $arLista[$i]['padrao']         = SistemaLegado::pegaDado('padrao', 'beneficio.layout_plano_saude',  'WHERE cod_layout ='.$_REQUEST['inLayout']);    
                        $arLista[$i]['excluido'] = 'n';
                    } 
                }
            }
            Sessao::write('arLista', $arLista);
            echo montaLista($arLista);    
        } else {
               echo "alertaAviso('".$stMensagem."!','form','erro','".Sessao::getId()."');";
        }
   
        $stJs .= "d.getElementById('btIncluir').value = 'Incluir';\n";   
        
    break;

    case 'excluirListaItens':
        $arLista = Sessao::read('arLista');
        $inCount = 0;
        
        foreach ($arLista as $key => $value) {
            if ($value['id'] == $_REQUEST['id']) {
                $arLista[$key]['excluido'] = 's';
            }
        }
        
        Sessao::write('arLista', $arLista);
        echo montaLista( $arLista );
    break;
    
     case 'alterarListaItens':
        $arLista   = array();
        $arTemp = Sessao::read('arLista');
        $inCount = 0;
        
        foreach ($arTemp as $key => $value) {

            if ($value['id'] == $_REQUEST['id']) {
                $stJs .= "f.inCGMFornecedor.value = '".$value['cgm_fornecedor']."'\n";                
                $stJs .= "d.getElementById('stCGMFornecedor').innerHTML ='" .$value['nom_cgm']."'\n";        
                $stJs .= "f.inLayout.value = '".$value['cod_layout']."'\n";          
                $stJs .= "f.id.value = ".$value['id']."\n;";
                $stJs .= "d.getElementById('btIncluir').value = 'Alterar';\n";        
            }
              
        }
    break;

    case 'limparCampos':
        $stJs .= "f.inCGMFornecedor.value = '';\n";                
        $stJs .= "d.getElementById('stCGMFornecedor').innerHTML = '&nbsp;';\n";        
        $stJs .= "f.inLayout.value = ''; \n";          
        $stJs .= "f.id.value = ''; \n";
        $stJs .= "d.getElementById('btIncluir').value = 'Incluir';\n";        
    break;
}

function montaLista ($arLista)
{    
    $rsLista = new RecordSet();
    
    if ($arLista) {
        foreach ($arLista as $value){
            if ($value['excluido'] == 'n'){
                $arTemp[] = $value;
            }
        }
        $rsLista->preenche( $arTemp );
    }
    
    $obTable = new Table();
    $obTable->setRecordSet( $rsLista );
    $obTable->setSummary('Lista de Layouts Vinculados');
    
    $obTable->Head->addCabecalho( 'Fornecedor' , 15 );
    $obTable->Head->addCabecalho( 'Layout' , 15 );
    
    $obTable->Body->addCampo( '[cgm_fornecedor] - [nom_cgm]', 'C' );
    $obTable->Body->addCampo( 'padrao', 'C' );
    $obTable->Body->addAcao( 'alterar' ,  'alterarListaItens(%s)', array( 'id' ) );
    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s)', array( 'id' ) );
    
    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );
    
    $stJs = " window.parent.frames['telaPrincipal'].document.getElementById('spnLista').innerHTML = '".$stHTML."';";
    $stJs.= "window.parent.frames['telaPrincipal'].limpaVinculo();";
    
    return $stJs;
}

function executaValidacao($array)
{
    $stMensagem = "";

    if ($array['inCGMFornecedor'] == "") {
        $stMensagem = 'Fornecedor inválido';
        
    } elseif ($array['inLayout'] == "") {
        $stMensagem = 'Selecione um Layout de Importação';
        
    }

    return $stMensagem;
}

echo $stJs;

?>