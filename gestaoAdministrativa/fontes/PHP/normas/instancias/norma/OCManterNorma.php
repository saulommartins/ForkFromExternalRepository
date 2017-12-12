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
* Arquivo de instância para manutenção de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 22984 $
$Name$
$Author: andre.almeida $
$Date: 2007-05-30 18:19:19 -0300 (Qua, 30 Mai 2007) $

Casos de uso: uc-01.04.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once ( CAM_GA_NORMAS_NEGOCIO . "RNorma.class.php"    );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );
include_once (CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php");

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RNorma;
$rsAtributos = new RecordSet;

// Acoes por pagina
switch ($stCtrl) {
    case "PreencheNorma":

        $inCodNorma = $_GET['stL'];

        $obTNorma = new TNorma;

        if (is_numeric($_GET[$inCodNorma]) && isset($_GET[$inCodNorma])) {

            $stFiltro = " WHERE cod_norma = ".$_GET[$inCodNorma];

            $obTNorma->recuperaNormasDecreto( $rsNorma, $stFiltro );

            if ( $rsNorma->eof() ) {

                $stJs = "f.".$inCodNorma.".value ='';\n";
                $stJs .= "f.".$inCodNorma.".focus();\n";
                $stJs .= "d.getElementById('".$_GET['st']."').innerHTML = '&nbsp;';\n";

                if( $_GET["boExibeDataNorma"] )
                    $stJs .= "d.getElementById('stDataNorma').innerHTML = '&nbsp;';\n";

                if( $_GET["boExibeDataPublicacao"] )
                    $stJs .= "d.getElementById('stDataPublicacao').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso('@Código informado não existe. (".$_GET[$inCodNorma].")','form','erro','".Sessao::getId()."');";

            } else {

                $stJs = "d.getElementById('".$_GET['st']."').innerHTML = '".$rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma')."';\n"; 

                if ($_GET["boExibeDataNorma"]) {
                    $stJs .= "d.getElementById('".$_GET['stL']."').innerHTML = '".$rsNorma->getCampo( "dt_assinatura_formatado" )."';\n";
                    $stJs .= "d.getElementById('stDataNorma').innerHTML = '".$rsNorma->getCampo( "dt_assinatura_formatado" )."';\n";
                }

                if( $_GET["boExibeDataPublicacao"] )
                    $stJs .= "d.getElementById('stDataPublicacao').innerHTML = '".$rsNorma->getCampo( "dt_publicacao" )."';\n";
            }

        } else {

            $stJs = "d.getElementById('".$_GET['st']."').innerHTML = '&nbsp;';\n";
            if( $_GET["boExibeDataNorma"] )
                $stJs .= "d.getElementById('stDataNorma').innerHTML = '&nbsp;';\n";
            if( $_GET["boExibeDataPublicacao"] )
                $stJs .= "d.getElementById('stDataPublicacao').innerHTML = '&nbsp;';\n";
        }
        echo $stJs;
    break;

    case "limpaLink":
        $stLink = "";
        Sessao::remove("stNormaLink");

        $js .= "window.parent.frames['telaPrincipal'].document.getElementById('spnlink').innerHTML = '&nbsp;'";
        sistemaLegado::executaFrameOculto($js);
    break;

    //monta HTML com os ATRIBUTOS relativos ao TIPO DE NORMA selecionado
    case "MontaAtributos":
        
        if ($_POST["inCodTipoNorma"] != "") {
            $inCodTipoNorma = $_POST["inCodTipoNorma"];
            $inCodNorma = $_POST[$inCodNorma];
            $obRegra->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
            if(!$inCodNorma)
                $inCodNorma = 0;
            $obRegra->obRTipoNorma->obRCadastroDinamico->setChavePersistenteValores( array("cod_tipo_norma"=>$inCodTipoNorma, "cod_norma"=>$inCodNorma) );
            $obRegra->obRTipoNorma->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
            if ( $rsAtributos->eof() ) {
                $obRegra->obRTipoNorma->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
            }

            // define novo OBJETO para ATRIBUTOS
            $obMontaAtributos = new MontaAtributos;
            $obMontaAtributos->setName ("Atributo_");

            $obFormulario = new Formulario;
            $obMontaAtributos->setRecordSet( $rsAtributos );
            $obMontaAtributos->recuperaValores();
            $obMontaAtributos->geraFormulario( $obFormulario );

            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();

            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);
            
            if (($_REQUEST["inCodTipoNorma"] == 1 || $_REQUEST["inCodTipoNorma"] == 2) && (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 11) && Sessao::getExercicio() >= "2015" ){
                $js .= TipoLeiDecretoTCEMG($_REQUEST["stAcao"]);
            }else{
                $js .= "jq_('#spanTipoLeiDecreto').html('');";
                $js .= "jq_('#spanTipoLeiAlteracaoOrcamentaria').html('');";  
            }
            
        } else {
            $stHTML = " ";
            $stEval = " ";
            $js .= "jq_('#spanTipoLeiDecreto').html('');";
            $js .= "jq_('#spanTipoLeiAlteracaoOrcamentaria').html('');";  
        }
        
        $js .= "f.stEval.value = '$stEval'; \n";
        $js .= "d.getElementById('spanAtributos').innerHTML = '".$stHTML."';";
        sistemaLegado::executaFrameOculto($js);
        
    break;

    case "Anexos":
        $file = trim(CAM_NORMAS."anexos/".$_REQUEST['cod_norma']."_".$anexo);

        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Length: ' . filesize($file));
        header('Content-Disposition: attachment; filename='.$anexo );
        readfile($file);

        sistemaLegado::executaFrameOculto($js);
    break;
    
    case "MontaBuscaNorma":
        
        $obTxtPercentualCreditoAdicional = new TextBox;
        $obTxtPercentualCreditoAdicional->setRotulo    ("Percentual de Crédito Adicional");
        $obTxtPercentualCreditoAdicional->setTitle     ("Informe um valor de percentual");
        $obTxtPercentualCreditoAdicional->setName      ("numPercentualCreditoAdicional");
        $obTxtPercentualCreditoAdicional->setId        ("numPercentualCreditoAdicional");
        $obTxtPercentualCreditoAdicional->setInteiro   (true);
        $obTxtPercentualCreditoAdicional->setMaxLength ( 3 );
        $obTxtPercentualCreditoAdicional->setSize      ( 3 );
        $obTxtPercentualCreditoAdicional->setObrigatorio (true);
        
        ### Leis de Alteracao  ###
        $obBscNorma = new IBuscaInnerNorma(false,false);
        $obBscNorma->obBscNorma->setRotulo('Lei Alterada');
              
        if($_REQUEST['stTipoLeiAlteracao'] != ""){
            
            $obFormulario = new Formulario();
            if(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 27){
                $obFormulario->addComponente( $obTxtPercentualCreditoAdicional );
            }
            
            $obBscNorma->geraFormulario($obFormulario);
            $obFormulario->montaInnerHTML();
                        
            $stHtml = $obFormulario->getHTML();
            
            $stJs .= "jq_('#spanNormas').html('".$stHtml."');";
            
            if($_REQUEST['hdnstNormaAlteracao'] && $_REQUEST['stTipoLeiAlteracao'] == $_REQUEST['hdnInLeiAlteracao']){ 
                $arNormaAlteracao    = explode('-',$_REQUEST['hdnstNormaAlteracao']);
                $stCodNormaAlteracao = str_pad($arNormaAlteracao[0]."/".$arNormaAlteracao[1],11,"0",STR_PAD_LEFT);
                $stJs .= "jq_('#stCodNorma').val('".$stCodNormaAlteracao."');\n";
                $stJs .= "jq_('#stNorma').html('".$arNormaAlteracao[2]."');";
            }else{
                $stJs .= "jq_('#stCodNorma').val('');\n";
                $stJs .= "jq_('#stNorma').html('&nbsp;');";
            }
            
            if(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 27){
                $numPercentual = Sessao::read( 'numPercentualCreditoAdicional');
                            
                if(isset($numPercentual) && $_REQUEST['stTipoLeiAlteracao'] == $_REQUEST['hdnInLeiAlteracao']){
                    $stJs .= "jq_('#numPercentualCreditoAdicional').val('".$numPercentual."');\n";
                }else{
                    $stJs .= "jq_('#numPercentualCreditoAdicional').val('');\n";
                }
            }
            
        }else{
            $stJs .= "jq_('#spanNormas').html('');";
            $stJs .= "jq_('#stCodNorma').val('');";
            $stJs .= "jq_('#stNorma').html('&nbsp;');";
            $stJs .= "jq_('#numPercentualCreditoAdicional').val('');";
        }
         
        sistemaLegado::executaFrameOculto($stJs);
        
    break;

    case "montaLeiOrcamentaria":
        if($_REQUEST['stTipoLeiOrigemDecreto'] == 3){
          include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTipoLeiAlteracaoOrcamentaria.class.php"  );
          $obTTCEMGTipoLeiAlteracaoOrcamentaria= new TTCEMGTipoLeiAlteracaoOrcamentaria;
          $obTTCEMGTipoLeiAlteracaoOrcamentaria->recuperaTodos($rsTipoLeiAlteracaoOrcamentaria    );

          $obCmbTipoLeiAlteracaoOrcamentaria = new Select;
          $obCmbTipoLeiAlteracaoOrcamentaria->setRotulo       ( 'Tipo de Lei de Alteração Orçamentária'             );
          $obCmbTipoLeiAlteracaoOrcamentaria->setName         ( 'stTipoLeiAlteracaoOrcamentaria'                    );
          $obCmbTipoLeiAlteracaoOrcamentaria->setId           ( 'stTipoLeiAlteracaoOrcamentaria'                    );
          $obCmbTipoLeiAlteracaoOrcamentaria->setCampoId      ( 'cod_tipo_lei'                                      );
          $obCmbTipoLeiAlteracaoOrcamentaria->setCampoDesc    ( 'descricao'                                         );
          $obCmbTipoLeiAlteracaoOrcamentaria->addOption       ( "", "Selecione"                                     );
          $obCmbTipoLeiAlteracaoOrcamentaria->preencheCombo   ( $rsTipoLeiAlteracaoOrcamentaria                     );
          $obCmbTipoLeiAlteracaoOrcamentaria->setTitle        ( "Selecione o Tipo de Lei de Aleteração Orçamentária");
          
          if($_REQUEST['stAcao'] == 'alterar' and $_REQUEST['hdnTipoLeiAlteracaoOrcamentaria'] ){
            $obTTCEMGTipoLeiAlteracaoOrcamentaria->setDado  ('cod_tipo_lei' ,$_REQUEST['hdnTipoLeiAlteracaoOrcamentaria'] );
            $obTTCEMGTipoLeiAlteracaoOrcamentaria->recuperaPorChave($rsLeiAlteracaoOrcamentaria, $boTransacao);
            
            $inTipoLeiAlteracaoOrcamentaria = $rsLeiAlteracaoOrcamentaria->getCampo('cod_tipo_lei');
            $stDescLeiAlteracaoOrcamentaria = $rsLeiAlteracaoOrcamentaria->getCampo('descricao');
          }
          
          $obFormulario = new Formulario();
          $obFormulario->addComponente( $obCmbTipoLeiAlteracaoOrcamentaria );
          $obFormulario->montaInnerHTML();
          $stHtml = $obFormulario->getHTML();
          $stJs .= "jq_('#spanTipoLeiAlteracaoOrcamentaria').html('".$stHtml."');";
          if($inTipoLeiAlteracaoOrcamentaria){
              $stJs .= "d.getElementById('stTipoLeiAlteracaoOrcamentaria').value ='".$inTipoLeiAlteracaoOrcamentaria."';\n";
          }else{
              $stJs .= "d.getElementById('stTipoLeiAlteracaoOrcamentaria').value ='';\n";
              $stJs .= "d.getElementById('stTipoLeiAlteracaoOrcamentaria').value ='';\n";
          }
          sistemaLegado::executaFrameOculto($stJs);
        }else{
            $stJs .= "jq_('#spanTipoLeiAlteracaoOrcamentaria').html('');";
            sistemaLegado::executaFrameOculto($stJs);
        }
    break;
}

function TipoLeiDecretoTCEMG($stAcao){
    
    include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNormaDetalhe.class.php"           );
    include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTipoLeiOrigemDecreto.class.php"  );
    
    $obHdnTipoLeiAlteracaoOrcamentaria = new Hidden;
    $obHdnTipoLeiAlteracaoOrcamentaria->setName ( "hdnTipoLeiAlteracaoOrcamentaria" );
            
    $obTTCEMGTipoLeiOrigemDecreto = new TTCEMGTipoLeiOrigemDecreto;
    $obTTCEMGTipoLeiOrigemDecreto->recuperaTodos($rsTipoLeiOrigemDecreto);
    
    $obTTCEMGNormaDetalhe = new TTCEMGNormaDetalhe;
    $obTTCEMGNormaDetalhe->setDado( 'cod_norma' , $_REQUEST['inCodNorma'] );
    $obTTCEMGNormaDetalhe->recuperaPorChave($rsNormaDetalhe);
    
    $obCmbTipoLeiOrigemDecreto = new Select;
    $obCmbTipoLeiOrigemDecreto->setRotulo       ( 'Tipo de Lei que Originou Decreto');
    $obCmbTipoLeiOrigemDecreto->setName         ( 'stTipoLeiOrigemDecreto'          );
    $obCmbTipoLeiOrigemDecreto->setId           ( 'stTipoLeiOrigemDecreto'          );
    $obCmbTipoLeiOrigemDecreto->setCampoId      ( 'cod_tipo_lei'                    );
    $obCmbTipoLeiOrigemDecreto->setCampoDesc    ( 'descricao'                       );
    $obCmbTipoLeiOrigemDecreto->addOption       ( "", "Selecione"                   );
    $obCmbTipoLeiOrigemDecreto->preencheCombo   ( $rsTipoLeiOrigemDecreto           );
    $obCmbTipoLeiOrigemDecreto->setTitle        ( "Selecione o Tipo de Lei que Originou Decreto"      );
    $obCmbTipoLeiOrigemDecreto->obEvento->setOnChange("buscaValor('montaLeiOrcamentaria');");
    
    if ( $stAcao == "alterar" ) {
        if($rsNormaDetalhe->getCampo('tipo_lei_origem_decreto')){
           $obCmbTipoLeiOrigemDecreto->setValue         ( $rsNormaDetalhe->getCampo('tipo_lei_origem_decreto') );
           if($rsNormaDetalhe->getCampo('tipo_lei_origem_decreto')==3 ){
              $obHdnTipoLeiAlteracaoOrcamentaria->setValue($rsNormaDetalhe->getCampo('tipo_lei_alteracao_orcamentaria'));
           }
        }
    }
    
    $obFormulario = new Formulario();
    $obFormulario->addHidden        ( $obHdnTipoLeiAlteracaoOrcamentaria  );
    $obFormulario->addComponente    ( $obCmbTipoLeiOrigemDecreto          );
    $obFormulario->montaInnerHTML();
    
    $stHtml = $obFormulario->getHTML();
    $stJs .= "jq_('#spanTipoLeiDecreto').html('".$stHtml."');";
    
    return $stJs;
}

?>