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
  * Arquivo de instância para manutenção de organograma
  * Data de Criação: 25/07/2005

  * @author Analista: Cassiano
  * @author Desenvolvedor: Cassiano

  $Id: OCManterOrganograma.php 60930 2014-11-25 16:30:37Z evandro $

  Casos de uso: uc-01.05.01

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrganograma.class.php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new ROrganogramaOrganograma;
$rsNorma = new RecordSet;

$niveis = Sessao::read('niveis');

function listaNiveis($arRecordSet, $boExecuta=true)
{
    global $obRegra;

    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );
    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Níveis cadastrados" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        $obLista->ultimoCabecalho->setWidth( 82 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Máscara" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obTxtDescNivel = new TextBox;
        $obTxtDescNivel->setName     ("stDescNivel_");
        $obTxtDescNivel->setSize     ( 100 );
        $obTxtDescNivel->setMaxLength( 100 );
        $obTxtDescNivel->setValue    ( "[stDescNivel]" );
        $obTxtDescNivel->obEvento->setOnChange("excluiDado( 'alteraNivel' , this.name.substr(12,this.name.length) );");
        $obLista->addDadoComponente( $obTxtDescNivel );
        $obLista->ultimoDado->setCampo( "stDescNivel" );
        $obLista->commitDadoComponente();

        $obTxtMascNivel = new TextBox;
        $obTxtMascNivel->setName     ("stMascNivel_");
        $obTxtMascNivel->setSize     ( 12 );
        $obTxtMascNivel->setMaxLength( 12 );
        $obTxtMascNivel->setInteiro  ( true );
        $obTxtMascNivel->setValue    ( "[stMascaraNivel]" );
        $obTxtMascNivel->obEvento->setOnChange("excluiDado( 'alteraNivel' , this.name.substr(12,this.name.length) );");
        $obLista->addDadoComponente( $obTxtMascNivel );
        $obLista->ultimoDado->setCampo( "stMascaraNivel" );
        $obLista->commitDadoComponente();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluiNivel');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnNiveis').innerHTML = '".$stHtml."';";
    $stJs .= "f.stDescNivel.value = '';";
    $stJs .= "f.stMascaraNivel.value = '';";

    if ($boExecuta==true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

// Acoes por pagina
switch ($stCtrl) {

    //monta HTML com os ATRIBUTOS relativos ao TIPO DE NORMA selecionado
    case "MontaNorma":
        $stCombo  = "inCodNorma";
        $stFiltro = "inCodTipoNorma";
        $stJs .= "limpaSelect(f.$stCombo,0); \n";
        $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
        $stJs .= "f.".$stCombo."Txt.value='';\n";
        if ($_POST[ $stFiltro ] != "") {
            $inCodTipoNorma = $_POST[ $stFiltro ];
            $obRegra->obRNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
            $obRegra->obRNorma->listar( $rsCombo );
            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("cod_norma");
                $stDesc = $rsCombo->getCampo("nom_norma");
                $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsCombo->proximo();
            }
        }
    break;

    case "MontaNivel":
        $stMensagem = false;

        if ( !empty($niveis) ) {
            $rsRecordSet = new Recordset;
            $rsRecordSet->preenche( $niveis );
            $rsRecordSet->setUltimoElemento();
            $inUltimoId = $rsRecordSet->getCampo("inId");    
        }
        if (!$inUltimoId) {
            $inProxId = 1;
        } else {
            $inProxId = $inUltimoId + 1;
        }
        $arElementos['inId']           = $inProxId;
        $arElementos['stDescNivel']    = $_POST['stDescNivel'];
        $arElementos['stMascaraNivel'] = $_POST['stMascaraNivel'];

        $niveis[] = $arElementos;
        listaNiveis( $niveis );
    break;

    case "alteraNivel":
        $stMensagem = false;

        $rsRecordSet = new Recordset;
        $rsRecordSet->preenche( $niveis );

        while ( !$rsRecordSet->eof() ) {
            if ( $rsRecordSet->getCorrente() == $_GET['inId']) {
                $stDescNivelAtual = $rsRecordSet->getCampo('stDescNivel');
                $stMascNivelAtual = $rsRecordSet->getCampo('stMascaraNivel');
                break;
            }
            $rsRecordSet->proximo();
        }
        $rsRecordSet->setPrimeiroElemento();

        if (( (trim($_POST[ 'stDescNivel_'.$_GET['inId'] ]))=="") || ((trim($_POST[ 'stMascNivel_'.$_GET['inId'] ]))=="")) {
            $stMensagem    = " O Campo deve ser preenchido.";
            $stJs = 'f.stDescNivel_'.$_GET['inId'].".value='$stDescNivelAtual';";
            $stJs = 'f.stMascNivel_'.$_GET['inId'].".value='$stMascNivelAtual';";
        }

        if ($stMensagem == false) {
            $niveis = array();
            while ( !$rsRecordSet->eof() ) {
                if ( $rsRecordSet->getCorrente() == $_GET['inId']) {
                    $arElementos['stDescNivel'] = $_POST[ 'stDescNivel_'.$_GET['inId'] ];
                    $arElementos['stMascaraNivel'] = $_POST[ 'stMascNivel_'.$_GET['inId'] ];
                } else {
                    $arElementos['stDescNivel'] = $rsRecordSet->getCampo('stDescNivel');
                    $arElementos['stMascaraNivel'] = $rsRecordSet->getCampo('stMascaraNivel');
                }
                $arElementos['inId']        = $rsRecordSet->getCampo('inId');
                $arElementos['inCodNivel']  = $rsRecordSet->getCampo('inCodNivel');
                $niveis[] = $arElementos;
                $rsRecordSet->proximo();
            }
            listaNiveis( $niveis );
        } else {
            $stJs .= "sistemaLegado::alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
    break;
    case "ordenaNivel":
        $stMensagem = false;

        $rsRecordSet = new Recordset;
        $rsRecordSet->preenche( $niveis );
        $rsRecordSet->ordena  ("inNumNivel");
        while ( !$rsRecordSet->eof() ) {
            if ( $rsRecordSet->getCorrente() == $_GET['inId']) {
                $inNumNivelAtual = $rsRecordSet->getCampo('inNumNivel');
                break;
            }
            $rsRecordSet->proximo();
        }
        $rsRecordSet->setPrimeiroElemento();

        foreach ($niveis as $campo => $valor) {
            if ($niveis[$campo]['inNumNivel'] == $_POST[ 'inNumNivel_'.$_GET['inId'] ]) {
                $stMensagem    = " Nível ".$niveis[$campo]['inNumNivel']." - já existe.";
                $stJs = 'f.inNumNivel_'.$_GET['inId'].".value=$inNumNivelAtual;";
            }
        }

        if ($stMensagem == false) {
            $niveis = array();
            while ( !$rsRecordSet->eof() ) {
                if ( $rsRecordSet->getCorrente() == $_GET['inId']) {
                    $arElementos['inNumNivel']  = $_POST[ 'inNumNivel_'.$_GET['inId'] ];
                } else {
                    $arElementos['inNumNivel']  = $rsRecordSet->getCampo('inNumNivel');
                }
                $arElementos['inId']        = $rsRecordSet->getCampo('inId');
                $arElementos['stDescNivel'] = $rsRecordSet->getCampo('stDescNivel');
                $arElementos['inCodNivel']  = $rsRecordSet->getCampo('inCodNivel');
                $niveis[] = $arElementos;
                $rsRecordSet->proximo();
            }

            listaNiveis( $niveis );
        } else {
            $stJs .= "sistemaLegado::alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
    break;
    case "excluiNivel":
        $id = $_GET['inId'];
        $stMensagem = false;
        if ($_REQUEST['stAcao']=='alterar') {
            reset($niveis);
            while ( list( $arId ) = each( $niveis ) ) {
                if ($niveis[$arId]["inId"] == $id) {
                    echo "->".$_POST['inCodOrganograma']."<br>";
                    echo "->".$niveis[$arId]["inCodNivel"]."<br>";
                    $obRegra->setCodOrganograma     ( $_POST['inCodOrganograma'] );
                    $obRegra->obRNivel->setCodNivel ( $niveis[$arId]["inCodNivel"] );
                    $obRegra->listarOrgaosRelacionados( $rsOrgao );
                    if ( !$rsOrgao->eof() && $obRegra->obRNivel->getCodNivel() ) {
                        $stMensagem = "Existe um Órgão cadastrado para este Nível";
                    }
                }
            }
        }

        if ($stMensagem==false) {
            reset($niveis);
            while ( list( $arId ) = each( $niveis ) ) {
                if ($niveis[$arId]["inId"] != $id) {
                    $arElementos['inId']           = $niveis[$arId]["inId"];
                    $arElementos['inCodNivel']     = $niveis[$arId]["inCodNivel"];
                    $arElementos['inNumNivel']     = $niveis[$arId]["inNumNivel"];
                    $arElementos['stDescNivel']    = $niveis[$arId]["stDescNivel"];
                    $arElementos['stMascaraNivel'] = $niveis[$arId]["stMascaraNivel"];
                    $arTMP[] = $arElementos;
                }
            }
            $niveis = $arTMP;
            listaNiveis( $arTMP );
        } else {
            $stJs = "sistemaLegado::alertaAviso('@ ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
    break;

    case 'preencheInner':
        if (count($niveis)) {
            $stJs = listaNiveis( $niveis, false );
        }

    break;
}

Sessao::write('niveis',$niveis);

if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}

?>
