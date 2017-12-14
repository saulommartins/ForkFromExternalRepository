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
    * Página de Oculto de Gestao Fiscal Medidas
    * Data de Criação: 29/07/2013

    * @author Analista:
    * @author Desenvolvedor: Carolina Schwaab Marcal

    * @ignore

    * Casos de uso:

    $Id:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGMedidas.class.php" );
$stCtrl = $_REQUEST['stCtrl'];

function configuracoesIniciais()
{
        $rsMedidas = Sessao::read('rsMedidas');

        $inId = 0;
        $arMedidas = array();

        while (!$rsMedidas->eof()) {

            if ($rsMedidas->getCampo("riscos_fiscais") == 't') {
                $boRiscosFiscais = 'Sim';
            } elseif ($rsMedidas->getCampo("riscos_fiscais") == 'f') {
                $boRiscosFiscais = 'Não';
            }
            if ($rsMedidas->getCampo("metas_fiscais") == 't') {
                $boMetasFiscais = 'Sim';
            } elseif ($rsMedidas->getCampo("metas_fiscais") == 'f') {
                $boMetasFiscais = 'Não';
            }
            if ($rsMedidas->getCampo("contratacao_aro") == 't') {
                $boContratacaoARO = 'Sim';
            } elseif ($rsMedidas->getCampo("contratacao_aro") == 'f') {
                $boContratacaoARO = 'Não';
            }

            $arMedidas[$inId]['inId']              = $inId;
            $arMedidas[$inId]["inMes"]             = $rsMedidas->getCampo("cod_mes");
            $arMedidas[$inId]["mes"]               = $rsMedidas->getCampo("mes");
            $arMedidas[$inId]["cod_poder"]         = $rsMedidas->getCampo("cod_poder");
            $arMedidas[$inId]["poder_publico"]     = $rsMedidas->getCampo("poder_publico");
            $arMedidas[$inId]["boRiscosFiscais"]   = $boRiscosFiscais;
            $arMedidas[$inId]["boMetasFiscais"]    = $boMetasFiscais;
            $arMedidas[$inId]["boContratacaoARO"]  = $boContratacaoARO;
            $arMedidas[$inId]["medida"]            = $rsMedidas->getCampo("medida");
            $arMedidas[$inId]["cod_medida"]        = $rsMedidas->getCampo("cod_medida");

            $rsMedidas->proximo();
            $inId++;
        }
        Sessao::write('arMedidas', $arMedidas);
}

function montaSpnListaMedidas()
{

    $arMedidas = Sessao::read('arMedidas');
    $inCodPoder = Sessao::read('cod_poder');
    $inCodMes = Sessao::read('cod_mes');

    $rsMedidas = new RecordSet;
    $rsMedidas->preenche($arMedidas);

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );

    $obLista->setRecordSet( $rsMedidas );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

     $obLista->addCabecalho();
     $obLista->ultimoCabecalho->addConteudo( "Mês" );
     $obLista->ultimoCabecalho->setWidth( 10 );
     $obLista->commitCabecalho();

     $obLista->addCabecalho();
     $obLista->ultimoCabecalho->addConteudo( "Poder Público" );
     $obLista->ultimoCabecalho->setWidth( 10 );
     $obLista->commitCabecalho();

     if ($inCodPoder == 1) {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Riscos Fiscais" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Metas Fiscais" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
      }

      if ($inCodPoder == 1 && $inCodMes == 12) {
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Contratação ARO" );
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();
      }
     $obLista->addCabecalho();
     $obLista->ultimoCabecalho->addConteudo( "Medida" );
     $obLista->commitCabecalho();

      $obLista->addCabecalho();
      $obLista->ultimoCabecalho->addConteudo("&nbsp;");
      $obLista->ultimoCabecalho->setWidth( 5 );
      $obLista->commitCabecalho();

      $obLista->addDado();
      $obLista->ultimoDado->setCampo( "[mes]" );
      $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
      $obLista->commitDado();

      $obLista->addDado();
      $obLista->ultimoDado->setCampo( "[poder_publico]" );
      $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
      $obLista->commitDado();

      if ($inCodPoder == 1) {
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[boRiscosFiscais]" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[boMetasFiscais]" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
      }

       if ($inCodPoder == 1 && $inCodMes == 12) {
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "[boContratacaoARO]" );
         $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
         $obLista->commitDado();
       }
      $obLista->addDado();
      $obLista->ultimoDado->setCampo( "[medida]" );
      $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
      $obLista->commitDado();

      $obLista->addAcao();
      $obLista->ultimaAcao->setAcao( "ALTERAR" );
      $obLista->ultimaAcao->setFuncaoAjax(true);
      $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax( 'carregaMedida' );" );
      $obLista->ultimaAcao->addCampo("1","inId");
      $obLista->commitAcao();

      $obLista->addAcao();
      $obLista->ultimaAcao->setAcao( "EXCLUIR" );
      $obLista->ultimaAcao->setFuncaoAjax(true);
      $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax( 'excluir' );" );
      $obLista->ultimaAcao->addCampo("1","inId");
      $obLista->commitAcao();

      $obLista->montaHTML();
      $stHtml = $obLista->getHTML();
      $stHtml = str_replace("\n","",$stHtml);
      $stHtml = str_replace("  ","",$stHtml);
      $stHtml = str_replace("'","\\'",$stHtml);

      $stJs = "document.getElementById('spnListaMedidas').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function incluir()
{

     $arMedidas = Sessao::read('arMedidas');

     $inI = count($arMedidas);
     $obMedidas = new TTCEMGMedidas();
     $obMedidas->setDado('inTipoPoder', $_REQUEST['inTipoPoder']);
     $obMedidas->setDado('inMes', $_REQUEST['inMes']);
     $obMedidas->setDado('stMedida', $_REQUEST['stMedida']);
     $obErro = $obMedidas->recuperaPorChave( $rsRecordSet, $boTransacao );

     if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
        $stMensagem = "Essa Medida já foi inserida na lista.";
        $stJs  = "alertaAviso('@$stMensagem','form','erro','".Sessao::getId()."');";
     } else {

        $rsMes = new RecordSet();
        $obMes = new TTCEMGMedidas();
        $obMes->setDado('cod_mes', $_REQUEST['inMes']);
        $obMes->recuperaMes($rsMes);

        $rsPoder = new RecordSet();
        $obPoder = new TTCEMGMedidas();
        $obPoder->setDado('cod_poder', $_REQUEST['inTipoPoder']);
        $obPoder->recuperaPoder($rsPoder);

         if ($_REQUEST['boRiscosFiscais'] == 'true') {
            $boRiscosFiscais= "Sim";
        } elseif ($_REQUEST['boRiscosFiscais'] == 'false') {
            $boRiscosFiscais = "Não";
        }
        if ($_REQUEST['boMetasFiscais'] == 'true') {
            $boMetasFiscais = "Sim";
        } elseif ($_REQUEST['boMetasFiscais'] == 'false') {
            $boMetasFiscais= "Não";
        }
        if ($_REQUEST['boContratacaoARO'] == 'true') {
            $boContratacaoARO= "Sim";
        } elseif ($_REQUEST['boContratacaoARO'] == '') {
           $boContratacaoARO = "";
        } else {
            $boContratacaoARO = "Não";
        }
        $arMedidas[$inI]['inId']             = $inI;
        $arMedidas[$inI]['mes']              = $rsMes->getCampo("mes");
        $arMedidas[$inI]['inMes']            = $_REQUEST['inMes'];
        $arMedidas[$inI]['poder_publico']    = $rsPoder->getCampo("poder_publico");
        $arMedidas[$inI]['cod_poder']        = $_REQUEST['inTipoPoder'];
        $arMedidas[$inI]["boRiscosFiscais"]  = $boRiscosFiscais;
        $arMedidas[$inI]["boMetasFiscais"]   = $boMetasFiscais;
        $arMedidas[$inI]["boContratacaoARO"] = $boContratacaoARO;
        $arMedidas[$inI]['medida']           = $_REQUEST['stMedida'];
        }
        Sessao::write('arMedidas', $arMedidas);
        $stJs .= "f.stMedida.value=''; \n";
        $stJs .= "jQuery('input[name=boRiscosFiscais]').attr('checked', false);";
        $stJs .= "jQuery('input[name=boMetasFiscais]').attr('checked', false);";
        $stJs .= "jQuery('input[name=boContratacaoARO]').attr('checked', false);";

    return $stJs;
}

function carregaMedida($inIdCarregar)
{
    $arMedidas = Sessao::read('arMedidas');

    $inId              = $arMedidas[$inIdCarregar]['inId'];
    $inMes          = $arMedidas[$inIdCarregar]['inMes'];
    $inCodPoder = $arMedidas[$inIdCarregar]['cod_poder'];
    $stMedida     = $arMedidas[$inIdCarregar]['medida'];

    if ($arMedidas[$inIdCarregar]['boRiscosFiscais'] == 'Sim') {
        $arMedidas[$inIdCarregar]['boRiscosFiscais'] = "true";
    } elseif ($arMedidas[$inIdCarregar]['boRiscosFiscais'] == 'Não') {
        $arMedidas[$inIdCarregar]['boRiscosFiscais'] = "false";
    }
    if ($arMedidas[$inIdCarregar]['boMetasFiscais'] == 'Sim') {
        $arMedidas[$inIdCarregar]['boMetasFiscais'] = "true";
    } elseif ($arMedidas[$inIdCarregar]['boMetasFiscais'] == 'Não') {
        $arMedidas[$inIdCarregar]['boMetasFiscais'] = "false";
    }
    if ($arMedidas[$inIdCarregar]['boContratacaoARO'] == 'Sim') {
        $arMedidas[$inIdCarregar]['boContratacaoARO'] = "true";
    } elseif ($arMedidas[$inIdCarregar]['boContratacaoARO'] == '') {
        $arMedidas[$inIdCarregar]['boContratacaoARO'] = "";
    } else {
        $arMedidas[$inIdCarregar]['boContratacaoARO'] = "false";
    }
    $inCodMes = Sessao::read('cod_mes');

    $stJs = "f.inId.value = ".$inId.";";
    $stJs.= "f.inMes.value = ".$inMes.";";
    $stJs.= "f.inTipoPoder.value = ".$inCodPoder.";";

    if ( Sessao::read('cod_poder') == 1) {
      $stJs.= "jQuery('input[name=boRiscosFiscais][value=". $arMedidas[$inIdCarregar]['boRiscosFiscais']."]').attr('checked', true);";
      $stJs.= "jQuery('input[name=boMetasFiscais][value=". $arMedidas[$inIdCarregar]['boMetasFiscais']."]').attr('checked', true);";
    }

    if ( Sessao::read('cod_poder') == 1 && Sessao::read('cod_mes') == 12) {
      $stJs.= "jQuery('input[name=boContratacaoARO][value=". $arMedidas[$inIdCarregar]['boContratacaoARO']."]').attr('checked', true);";
    }
    $stJs.= "f.stMedida.value = '".$stMedida."';";

    $stJs .= "document.frm.btIncluir.disabled = true;";
    $stJs .= "document.frm.btAlterar.disabled = false;";

    return $stJs;
}

function alterar($inIdAlterar)
{
    $arMedidas = array();
    $arMedidas = Sessao::read('arMedidas');
    Sessao::remove('arMedidas');

    $inId = 0;

    foreach ($arMedidas as $arMedida => $dados) {

        if ($_REQUEST['boRiscosFiscais'] == 'true') {
            $boRiscosFiscais= "Sim";
        } elseif ($_REQUEST['boRiscosFiscais'] == 'false') {
            $boRiscosFiscais = "Não";
        }
        if ($_REQUEST['boMetasFiscais'] == 'true') {
            $boMetasFiscais = "Sim";
        } elseif ($_REQUEST['boMetasFiscais'] == 'false') {
            $boMetasFiscais= "Não";
        }
        if ($_REQUEST['boContratacaoARO'] == 'true') {
            $boContratacaoARO= "Sim";
        } elseif ($_REQUEST['boContratacaoARO'] == 'false') {
            $boContratacaoARO = "Não";
        }
        if ($dados['inId'] == $inIdAlterar) {
            $arMedidas[$arMedida]['inId']             = $dados['inId'];
            $arMedidas[$arMedida]['mes']              = $dados['mes'];
            $arMedidas[$arMedida]['inMes']            = $dados['inMes'];
            $arMedidas[$arMedida]['poder_publico']    = $dados['poder_publico'];
            $arMedidas[$arMedida]['cod_poder']        = $dados['cod_poder'];
            $arMedidas[$arMedida]["boRiscosFiscais"]  = $boRiscosFiscais;
            $arMedidas[$arMedida]["boMetasFiscais"]   = $boMetasFiscais;
            $arMedidas[$arMedida]["boContratacaoARO"] = $boContratacaoARO;
            $arMedidas[$arMedida]['medidaAnterior']   = $dados['medida'];
            $arMedidas[$arMedida]['medida']           = $_REQUEST['stMedida'];
            $arMedidas[$arMedida]['cod_medida']       = $dados['cod_medida'];
        }
    }

    Sessao::write('arMedidas',$arMedidas);

    $stJs = montaSpnListaMedidas();
    $stJs .= "f.stMedida.value=''; \n";
    $stJs .= "jQuery('input[name=boRiscosFiscais]').attr('checked', false);";
    $stJs .= "jQuery('input[name=boMetasFiscais]').attr('checked', false);";
    $stJs .= "jQuery('input[name=boContratacaoARO]').attr('checked', false);";

    return $stJs;
}

function excluir($inIdExcluir)
{
    $inId = 0;
    $inIdExcluidos = 0;
    $arMedidaTmp = array();
    $arMedidaTmpExcluidos = Sessao::read('arMedidasExcluidas');
    
    if (!empty($arMedidaTmpExcluidos)) {
        end($arMedidaTmpExcluidos);
        $inIdExcluidos = key($arMedidaTmpExcluidos);
        $inIdExcluidos++;
    }
    
    foreach ( Sessao::read('arMedidas') as $arMedida ) {
        
        if ($arMedida['inId'] != $inIdExcluir) {
            
            $arMedidaTmp[$inId]['inId']               = $inId ;
            $arMedidaTmp[$inId]['mes']                = $arMedida['mes'];
            $arMedidaTmp[$inId]['inMes']              = $arMedida['inMes'];
            $arMedidaTmp[$inId]['poder_publico']      = $arMedida['poder_publico'];
            $arMedidaTmp[$inId]['cod_poder']          = $arMedida['cod_poder'];
            $arMedidaTmp[$inId]["boRiscosFiscais"]    = $arMedida['boRiscosFiscais'];
            $arMedidaTmp[$inId]["boMetasFiscais"]     = $arMedida['boMetasFiscais'];
            $arMedidaTmp[$inId]["boboContratacaoARO"] = $arMedida['boContratacaoARO'];
            $arMedidaTmp[$inId]['medida']             = $arMedida['medida'];
            $arMedidaTmp[$inId]['cod_medida']         = $arMedida['cod_medida'];
            $inId++;
            
        } else {
            
            $arMedidaTmpExcluidos[$inIdExcluidos]['inId']               = $inIdExcluidos ;
            $arMedidaTmpExcluidos[$inIdExcluidos]['inMes']              = $arMedida['inMes'];
            $arMedidaTmpExcluidos[$inIdExcluidos]['cod_poder']          = $arMedida['cod_poder'];
            $arMedidaTmpExcluidos[$inIdExcluidos]["boRiscosFiscais"]    = $arMedida['boRiscosFiscais'];
            $arMedidaTmpExcluidos[$inIdExcluidos]["boMetasFiscais"]     = $arMedida['boMetasFiscais'];
            $arMedidaTmpExcluidos[$inIdExcluidos]["boboContratacaoARO"] = $arMedida['boContratacaoARO'];
            $arMedidaTmpExcluidos[$inIdExcluidos]['medida']             = $arMedida['medida'];
            $arMedidaTmpExcluidos[$inIdExcluidos]['cod_medida']         = $arMedida['cod_medida'];
        }
    }

    Sessao::write('arMedidas',$arMedidaTmp);
    Sessao::write('arMedidasExcluidas', $arMedidaTmpExcluidos);
    
    $stJs = montaSpnListaMedidas();

    return $stJs;
}

switch ($stCtrl) {
    case "configuracoesIniciais":
        configuracoesIniciais();
        $js = montaSpnListaMedidas();
    break;
    case "incluir":
        $js  = incluir();
        $js .= montaSpnListaMedidas();

    break;
    case "alterar":
        $js  = alterar( $_GET['inId'] );

    break;
    case "excluir":
        $js  = excluir( $_GET['inId'] );
    break;
    case "carregaMedida":
        $js  = carregaMedida( $_GET['inId'] );
    break;
}

if ($js) {
    echo $js;
}
