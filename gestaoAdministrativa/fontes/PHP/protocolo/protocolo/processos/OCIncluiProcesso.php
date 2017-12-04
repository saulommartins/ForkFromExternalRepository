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
    * Arquivo de implementação de manutenção de processo
    * Data de Criação: 22/11/2006

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * Casos de uso: uc-01.06.98

    $Id: OCIncluiProcesso.php 62525 2015-05-18 16:28:24Z michel $
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once(CAM_FW_COMPONENTES."Table/TableTree.class.php"								       );
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
include_once (CAM_FW_URBEM."SistemaLegado.class.php");

header("HTTP/1.1 200 OK");

$stCtrl = $_REQUEST['stCtrl'];

function montaListaInteressados($arInteressados, $vinculo = "")
{
    $stPrograma = "IncluiProcesso";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsRecordSet = new RecordSet;

    $rsRecordSet->setPrimeiroElemento();
    $rsRecordSet->preenche( $arInteressados );

    $table = new Table();
    $table->setRecordset( $rsRecordSet );
    $table->setSummary('Interessados');

    $table->Head->addCabecalho( 'CGM'          , 8  );
    $table->Head->addCabecalho( 'Lista de Interessados' , 30 );

    $stTitle = "[stTitle]";

    $table->Body->addCampo( 'numCgm' , "C", $stTitle );
    $table->Body->addCampo( 'nomCgm' , "E", $stTitle );

    $table->Body->addAcao('EXCLUIR', "ajaxJavaScript('OCIncluiProcesso.php?inId=%d&vinculo=".$vinculo."', 'excluiInteressado');", array( 'numCgm' , $numCgm));

    $table->montaHTML();
    $html = $table->getHtml();

    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\'",$html);

    $stJs = "";
    $stJs .= " if ($('HdnnumCgm')) { $('HdnnumCgm').value = '';}\n";
    $stJs .= " if ($('nomCgm')   ) { $('nomCgm').value = '';}\n";
    $stJs .= " if ($('numCgm')   ) { $('numCgm').value= ''; }\n";
    $stJs .= " if ($('nomCGM')   ) { $('nomCGM').innerHTML = '&nbsp;';}\n";

    $stJs .= " if ($('spnInteressados')) { $('spnInteressados').innerHTML = '&nbsp;'; $('spnInteressados').innerHTML = '".$html."';}\n";

    $stJs .= "if (f.codClassificacao) { f.codClassificacao.focus(); } ";
    $stJs .= "if (f.codProcesso) { f.codProcesso.focus(); }else{ if (f.observacoes) { f.observacoes.focus(); } }";
    
    return $stJs;

}


function montaListaPermitidos($arInteressados, $vinculo = "")
{
    $stPrograma = "IncluiProcesso";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsRecordSet = new RecordSet;

    $rsRecordSet->setPrimeiroElemento();
    $rsRecordSet->preenche( $arInteressados );

    $table = new Table();
    $table->setRecordset( $rsRecordSet );
    $table->setSummary('CGM com Acesso ao Processo');

    $table->Head->addCabecalho( 'CGM'          , 8  );
    $table->Head->addCabecalho( 'Lista de Permissão' , 30 );

    $stTitle = "[stTitle]";

    $table->Body->addCampo( 'numCgmAcesso' , "C", $stTitle );
    $table->Body->addCampo( 'nomCgmAcesso' , "E", $stTitle );

    $table->Body->addAcao('EXCLUIR', "ajaxJavaScript('OCIncluiProcesso.php?inId=%d&vinculo=".$vinculo."', 'excluiPermitido');", array( 'numCgmAcesso' , $numCgmAcesso));

    $table->montaHTML();
    $html = $table->getHtml();

    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\'",$html);

    $stJs = "";
    $stJs .= " if ($('HdnnumCgmAcesso')) { $('HdnnumCgmAcesso').value = '';}\n";
    $stJs .= " if ($('nomCgmAcesso')   ) { $('nomCgmAcesso').value = '';}\n";
    $stJs .= " if ($('numCgmAcesso')   ) { $('numCgmAcesso').value= ''; }\n";
    $stJs .= " if ($('nomCGMAcesso')   ) { $('nomCGMAcesso').innerHTML = '&nbsp;';}\n";

    $stJs .= " if ($('spnPermitidos')) { $('spnPermitidos').innerHTML = '&nbsp;'; $('spnPermitidos').innerHTML = '".$html."';}\n";

    $stJs .= "if (f.codClassificacao) { f.codClassificacao.focus(); } ";
    $stJs .= "if (f.codProcesso) { f.codProcesso.focus(); } else { if (f.numCgmAcesso) { f.numCgmAcesso.focus(); } }";
    
    return $stJs;

}
switch ($stCtrl) {
    case 'imobiliaria':
            include_once( CAM_GT_CIM_MAPEAMENTO."TCIMProprietario.class.php");
            $obTCIMProprietario = new TCIMProprietario();
            $obTCIMProprietario->setDado('inscricao_municipal',$_GET['inInscricao']);
            $obTCIMProprietario->recuperaProprietarioProcesso( $rsRecordSet );
            if (!$rsRecordSet->eof() ) {
               echo "document.frm.HdnnumCgm.value='".$rsRecordSet->getCampo('numcgm')."';\n";
               echo "document.frm.nomCgm.value='".stripslashes($rsRecordSet->getCampo('nom_cgm'))."';\n";
               echo "document.frm.numCgm.value='".$rsRecordSet->getCampo('numcgm')."';\n";
               echo "document.getElementById('nomCGM').innerHTML='".stripslashes($rsRecordSet->getCampo('nom_cgm'))."';";
            } else {
               echo "document.frm.numMatricula.value = '';\n";
               echo "document.frm.numMatricula.focus();\n";
               echo "var mensagem = 'Inscrição Imobiliária inválida ou inexistente!(".$_GET['inInscricao'].")';\n";
               echo "var mensagem = 'Inscrição inválida ou inexistente!(".$_GET['inInscricao'].")';\n";
               echo "alertaAviso(mensagem,'form','erro','".Sessao::getId()."');";
            }

            $arInteressados = Sessao::getRequestProtocolo('arRequestProtocolo');

            if (count($arInteressados['interessados']) > 0) {
                // Limpa os campos do CGM para evitar a carga
                $stJs .= "if (jQuery('#numMatricula').attr('value') != '') {";
                $stJs .= "jQuery('#nomCGM').html('&nbsp;');";
                $stJs .= "jQuery('#numCgm').attr('value', '');";
                $stJs .= "jQuery('#numCgm').focus();";
                $stJs .= "jQuery('#HdnnumCgm').attr('value', '');";
                $stJs .= "jQuery('#nomCgm').attr('value', '');";
                $stJs .= "}";
            }

    break;

        case 'inscricao':
            include_once( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php");
            $obTCEMCadastroEconomico = new TCEMCadastroEconomico();
            $stFiltro = " AND   CE.inscricao_economica = ".$_GET['inInscricao'];
            $obTCEMCadastroEconomico->recuperaInscricao( $rsRecordSet, $stFiltro );
            if (!$rsRecordSet->eof() ) {
               echo "document.frm.HdnnumCgm.value='".$rsRecordSet->getCampo('numcgm')."';\n";
               echo "document.frm.nomCgm.value='".stripslashes($rsRecordSet->getCampo('nom_cgm'))."';\n";
               echo "document.frm.numCgm.value='".$rsRecordSet->getCampo('numcgm')."';\n";
               echo "document.getElementById('nomCGM').innerHTML='".stripslashes($rsRecordSet->getCampo('nom_cgm'))."';";
            } else {
               echo "document.frm.numInscricao.value = '';\n";
               echo "document.frm.numInscricao.focus();\n";
               echo "var mensagem = 'Inscrição Econômica inválida ou inexistente!(".$_GET['inInscricao'].")';\n";
               echo "alertaAviso(mensagem,'form','erro','".Sessao::getId()."');";
            }

            $arInteressados = Sessao::getRequestProtocolo('arRequestProtocolo');

            if (count($arInteressados['interessados']) > 0) {
                // Limpa os campos do CGM para evitar a carga
                $stJs .= "if (jQuery('#numInscricao').attr('value') != '') {";
                $stJs .= "jQuery('#nomCGM').html('&nbsp;');";
                $stJs .= "jQuery('#numCgm').attr('value', '');";
                $stJs .= "jQuery('#numCgm').focus();";
                $stJs .= "jQuery('#HdnnumCgm').attr('value', '');";
                $stJs .= "jQuery('#nomCgm').attr('value', '');";
                $stJs .= "}";
            }

    break;

    case 'incluiInteressado' :

        $numCgm    = $_REQUEST['numCgm'];
        $nomCgm    = $_REQUEST['nomCgm'];
        $vinculo   = $_REQUEST['vinculo'];
        $boIncluir = false;

        //$nomCgm    = str_replace("\'","'",$nomCgm);
        $nomCgm = stripslashes ($nomCgm);

        $arInteressados = Sessao::getRequestProtocolo('arRequestProtocolo');

        if (!empty($numCgm) && !empty($nomCgm)) {

            $inCountInteressados = count($arInteressados['interessados']);

            if ($inCountInteressados > 0) {

                foreach ($arInteressados['interessados'] as $campo => $valor) {
                    if ($numCgm == $arInteressados['interessados'][$campo]['numCgm']) {
                        $stJs .= "var mensagem = 'Esse CGM já está na lista!(".$numCgm.")';\n";
                        $stJs .= "alertaAviso(mensagem,'form','erro','".Sessao::getId()."');";
                        $boIncluir = false;
                        break;
                    }else
                        $boIncluir = true;
                }
            } else {
                $arInteressados['interessados'][0]['nomCgm'] = $nomCgm;
                $arInteressados['interessados'][0]['numCgm'] = $numCgm;
            }

            if ($boIncluir) {
                $arInteressados['interessados'][($inCountInteressados)]['nomCgm'] = $nomCgm;
                $arInteressados['interessados'][($inCountInteressados)]['numCgm'] = $numCgm;
            }

            Sessao::setRequestProtocolo($arInteressados);

            // Desabilita os campos de Inscrição Imobiliária e Economica.
        switch ($vinculo) {
        case "imobiliaria" :
                    $stJs .= "jQuery('#tdFrmnumMatricula').css('display', 'none');";
                    $stJs .= "jQuery('#imgnumMatricula').css('display', 'none');";
                    $stJs .= "jQuery('#tdLblnumMatricula').css('display', '');";
                    $stJs .= "jQuery('#tdLblnumMatricula').html(jQuery('#numMatricula').attr('value'));";
        break;

        case "inscricao" :
                    $stJs .= "jQuery('#tdFrmnumInscricao').css('display', 'none');";
                    $stJs .= "jQuery('#imgnumInscricao').css('display', 'none');";
                    $stJs .= "jQuery('#tdLblnumInscricao').css('display', '');";
                    $stJs .= "jQuery('#tdLblnumInscricao').html(jQuery('#numInscricao').attr('value'));";
        break;
        }
        $stJs .= montaListaInteressados($arInteressados['interessados'], $vinculo);
        } else {
            $stJs .= "var mensagem = 'Você deve selecionar o CGM para incluir na lista.';\n";
            $stJs .= "alertaAviso(mensagem,'form','erro','".Sessao::getId()."');";
        }

    break;

    case 'excluiInteressado':

        $arTMP = array();
        $id = $_REQUEST['inId'];
        $vinculo = $_REQUEST['vinculo'];
        $inCount = 0;

        $arInteressados = Sessao::getRequestProtocolo('arRequestProtocolo');

        foreach ($arInteressados['interessados'] as $campo => $valor) {

            if ($arInteressados['interessados'][$campo]['numCgm'] == $id) {
                unset($arInteressados['interessados'][$campo]);
                break;
            }
        }

        sort($arInteressados['interessados']);

        if (count($arInteressados['interessados']) == 0) {
            // Desabilita os campos de Inscrição Imobiliária e Economica.
        switch ($vinculo) {
        case "imobiliaria" :
                    $stJs .= "jQuery('#tdFrmnumMatricula').css('display', '');";
                    $stJs .= "jQuery('#imgnumMatricula').css('display', '');";
                    $stJs .= "jQuery('#tdLblnumMatricula').css('display', 'none');";
                    $stJs .= "jQuery('#numMatricula').focus();";
        break;

        case "inscricao" :
                    $stJs .= "jQuery('#tdFrmnumInscricao').css('display', '');";
                    $stJs .= "jQuery('#imgnumInscricao').css('display', '');";
                    $stJs .= "jQuery('#tdLblnumInscricao').css('display', 'none');";
                    $stJs .= "jQuery('#numInscricao').focus();";
                break;
        }

           $arInteressados['interessados'] = array();
        }

        Sessao::setRequestProtocolo($arInteressados);
        echo montaListaInteressados( $arInteressados['interessados'], $vinculo);
    break;

    case 'montaListaInteressados' :

            $vinculo        = $_REQUEST['vinculo'];

            $arInteressados = Sessao::getRequestProtocolo();

            if (count($arInteressados['interessados']) > 0) {
                // Desabilita os campos de Inscrição Imobiliária e Economica.
                switch ($vinculo) {
                    case "imobiliaria" :
                        $stJs .= "if (jQuery('#numMatricula').attr('value') != '') {
                        jQuery('#tdFrmnumMatricula').css('display', 'none');
                        jQuery('#imgnumMatricula').css('display', 'none');
                        jQuery('#tdLblnumMatricula').css('display', '');
                        jQuery('#tdLblnumMatricula').html(jQuery('#numMatricula').attr('value'));}";
                    break;

                    case "inscricao" :
                        $stJs .= "if (jQuery('#numInscricao').attr('value') != '') {
                        jQuery('#tdFrmnumInscricao').css('display', 'none');
                        jQuery('#imgnumInscricao').css('display', 'none');
                        jQuery('#tdLblnumInscricao').css('display', '');
                        jQuery('#tdLblnumInscricao').html(jQuery('#numInscricao').attr('value')); }";
                    break;
                }
            }

            echo montaListaInteressados($arInteressados['interessados'], $vinculo);
    break;

    case 'limpaListaInteressados' :

        $arInteressados = Sessao::getRequestProtocolo();
        $arInteressados['interessados'] = array();

        Sessao::setRequestProtocolo($arInteressados);
        $stJs = " if ($('spnInteressados')) { $('spnInteressados').innerHTML = '&nbsp;'; }";
    break;

    case 'montaEntidade':
        if ($_REQUEST['stIncluirAssinaturas'] == 'sim') {
            include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';
            $obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral();
            $obITextBoxSelectEntidade->setId('inCodEntidade');
            $obITextBoxSelectEntidade->setName('inCodEntidade');
            $obITextBoxSelectEntidade->setObrigatorio(true);
            $obITextBoxSelectEntidade->setCodEntidade($inCodEntidade);
            $obITextBoxSelectEntidade->obTextBox->obEvento->setOnChange("getIMontaAssinaturas();");
            $obITextBoxSelectEntidade->obSelect->obEvento->setOnChange("getIMontaAssinaturas();");

            $obFormulario = new Formulario();
            $obFormulario->setLarguraRotulo(30);
            $obFormulario->addComponente($obITextBoxSelectEntidade);
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();
            $stJs .= "jQuery('#spnEntidade').html('".$stHTML."');\n";
        } else {
            $stJs .= "jQuery('#spnEntidade').html('');\n";
        }
    break;

    # 
    case 'incluiAcessoCGM' :

        $numCgmAcesso = $_REQUEST['numCgmAcesso'];
        $nomCgmAcesso = $_REQUEST['nomCgmAcesso'];
        $vinculo   = $_REQUEST['vinculo'];
        $boIncluir = false;

        $arInteressados = Sessao::getRequestProtocolo('arRequestProtocolo');

        if (!empty($numCgmAcesso)) {
            $nomCgmAcesso = (!empty($nomCgmAcesso)) ? $nomCgmAcesso : pegaDado("nom_cgm", "sw_cgm"," where numcgm = ".$numCgmAcesso);
            $nomCgmAcesso = stripslashes($nomCgmAcesso);
            
            if(!empty($nomCgmAcesso)){
                $inCountInteressados = count($arInteressados['permitidos']);
    
                if ($inCountInteressados > 0) {
    
                    foreach ($arInteressados['permitidos'] as $campo => $valor) {
                        if ($numCgmAcesso == $arInteressados['permitidos'][$campo]['numCgmAcesso']) {
                            $stJs .= "var mensagem = 'Esse CGM já está na lista de Permitidos! (CGM ".$numCgmAcesso.")';\n";
                            $stJs .= "alertaAviso(mensagem,'form','erro','".Sessao::getId()."');";
                            $boIncluir = false;
                            break;
                        }else
                            $boIncluir = true;
                    }
                } else {
                    $arInteressados['permitidos'][0]['nomCgmAcesso'] = $nomCgmAcesso;
                    $arInteressados['permitidos'][0]['numCgmAcesso'] = $numCgmAcesso;
                }
    
                if ($boIncluir) {
                    $arInteressados['permitidos'][($inCountInteressados)]['nomCgmAcesso'] = $nomCgmAcesso;
                    $arInteressados['permitidos'][($inCountInteressados)]['numCgmAcesso'] = $numCgmAcesso;
                }
    
                Sessao::setRequestProtocolo($arInteressados);
    
                // Desabilita os campos de Inscrição Imobiliária e Economica.
                switch ($vinculo) {
                    case "imobiliaria" :
                        $stJs .= "jQuery('#tdFrmnumMatricula').css('display', 'none');";
                        $stJs .= "jQuery('#imgnumMatricula').css('display', 'none');";
                        $stJs .= "jQuery('#tdLblnumMatricula').css('display', '');";
                        $stJs .= "jQuery('#tdLblnumMatricula').html(jQuery('#numMatricula').attr('value'));";
                    break;
    
                    case "inscricao" :
                        $stJs .= "jQuery('#tdFrmnumInscricao').css('display', 'none');";
                        $stJs .= "jQuery('#imgnumInscricao').css('display', 'none');";
                        $stJs .= "jQuery('#tdLblnumInscricao').css('display', '');";
                        $stJs .= "jQuery('#tdLblnumInscricao').html(jQuery('#numInscricao').attr('value'));";
                    break;
                }
            
                $stJs .= montaListaPermitidos($arInteressados['permitidos'], $vinculo);
            } else {
                $stJs .= "var mensagem = 'CGM informado inválido ou inexistente! (".$numCgmAcesso.")';\n";
                $stJs .= "jQuery('#numCgmAcesso').attr('value', '');";
                $stJs .= "jQuery('#nomCGMAcesso').html('&nbsp;');";
                $stJs .= "jQuery('#nomCGMAcesso').attr('value', '');";
                $stJs .= "jQuery('#HdnnumCgmAcesso').attr('value', '');";                
                $stJs .= "alertaAviso(mensagem,'form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "var mensagem = 'Você deve selecionar o CGM para incluir na lista.';\n";
            $stJs .= "alertaAviso(mensagem,'form','erro','".Sessao::getId()."');";
        }

    break;

    case 'excluiPermitido':

        $arTMP = array();
        $id = $_REQUEST['inId'];
        $vinculo = $_REQUEST['vinculo'];
        $inCount = 0;

        $arInteressados = Sessao::getRequestProtocolo('arRequestProtocolo');

        foreach ($arInteressados['permitidos'] as $campo => $valor) {

            if ($arInteressados['permitidos'][$campo]['numCgmAcesso'] == $id) {
                unset($arInteressados['permitidos'][$campo]);
                break;
            }
        }

        sort($arInteressados['permitidos']);

        if (count($arInteressados['permitidos']) == 0) {
            // Desabilita os campos de Inscrição Imobiliária e Economica.
        switch ($vinculo) {
        case "imobiliaria" :
                    $stJs .= "jQuery('#tdFrmnumMatricula').css('display', '');";
                    $stJs .= "jQuery('#imgnumMatricula').css('display', '');";
                    $stJs .= "jQuery('#tdLblnumMatricula').css('display', 'none');";
                    $stJs .= "jQuery('#numMatricula').focus();";
        break;

        case "inscricao" :
                    $stJs .= "jQuery('#tdFrmnumInscricao').css('display', '');";
                    $stJs .= "jQuery('#imgnumInscricao').css('display', '');";
                    $stJs .= "jQuery('#tdLblnumInscricao').css('display', 'none');";
                    $stJs .= "jQuery('#numInscricao').focus();";
                break;
        }

           $arInteressados['permitidos'] = array();
        }

        Sessao::setRequestProtocolo($arInteressados);
        echo montaListaPermitidos( $arInteressados['permitidos'], $vinculo);
    break;

    case 'montaListaPermitidos' :

            $vinculo = $_REQUEST['vinculo'];

            $arInteressados = Sessao::getRequestProtocolo();

            if (count($arInteressados['permitidos']) > 0) {
                // Desabilita os campos de Inscrição Imobiliária e Economica.
                switch ($vinculo) {
                    case "imobiliaria" :
                        $stJs .= "if (jQuery('#numMatricula').attr('value') != '') {
                        jQuery('#tdFrmnumMatricula').css('display', 'none');
                        jQuery('#imgnumMatricula').css('display', 'none');
                        jQuery('#tdLblnumMatricula').css('display', '');
                        jQuery('#tdLblnumMatricula').html(jQuery('#numMatricula').attr('value'));}";
                    break;

                    case "inscricao" :
                        $stJs .= "if (jQuery('#numInscricao').attr('value') != '') {
                        jQuery('#tdFrmnumInscricao').css('display', 'none');
                        jQuery('#imgnumInscricao').css('display', 'none');
                        jQuery('#tdLblnumInscricao').css('display', '');
                        jQuery('#tdLblnumInscricao').html(jQuery('#numInscricao').attr('value')); }";
                    break;
                }
            }

        echo montaListaPermitidos($arInteressados['permitidos'], $vinculo);
    break;

    case 'limpaListaPermitidos' :

        $arInteressados = Sessao::getRequestProtocolo();
        $arInteressados['permitidos'] = array();

        Sessao::setRequestProtocolo($arInteressados);
        $stJs = " if ($('spnInteressados')) { $('spnInteressados').innerHTML = '&nbsp;'; }";
    break;

}

if (!empty($stJs)) {
    echo $stJs;
}
