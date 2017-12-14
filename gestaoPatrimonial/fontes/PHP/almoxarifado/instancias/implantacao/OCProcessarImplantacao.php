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
    * Página Oculta de Processar Implantacao
    * Data de Criação   : 08/06/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.03.16

    $Id: OCProcessarImplantacao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_REQUEST['stCtrl'];

function montaListaLotes($arRecordSet)
{
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecordSet );
    $rsRecordSet->addFormatacao('stNumLote', 'HTML');
    $rsRecordSet->addFormatacao('stDataFabricacao', 'HTML');
    $rsRecordSet->addFormatacao('stDataValidade', 'HTML');
    $rsRecordSet->addFormatacao('nmQuantidadeLote', 'HTML');
    if ( $rsRecordSet->getNumLinhas() != 0 ) {
        $obFormulario = new Formulario;

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );

        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Lote" );
        $obLista->ultimoCabecalho->setWidth( 7 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Data de Fabricação" );
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Data de Validade" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Quantidade" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "stNumLote" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "stDataFabricacao" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "stDataValidade" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "nmQuantidadeLote" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "javascript:alteraLote();" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "javascript:excluiLote();" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();

    }

        $html = $obLista->getHTML();

        $html = str_replace("\n","",$html);
        $html = str_replace("  ","",$html);
        $html = str_replace("'","\\'",$html);

        $stJs = "d.getElementById('spnListaLotes').innerHTML = '".$html."';";

        return $stJs;
}

function montaCampoAlmoxarifado()
{
    include_once(CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifadoAlmoxarife.class.php");
    include_once(CAM_GP_ALM_COMPONENTES."ILabelAlmoxarifado.class.php");

    $obFormulario = new Formulario;

    $obAlmoxarifado = new ISelectAlmoxarifadoAlmoxarife($obForm);
    $obAlmoxarifado->setNull( false );

    $obLblAlmoxarifado = new ILabelAlmoxarifado($obForm);
    $obLblAlmoxarifado->setMostraCodigo( true );

    $obHdnAlmoxarifado = new Hidden;
    $obHdnAlmoxarifado->setName ( 'inCodAlmoxarifado' );
    $obHdnAlmoxarifado->setValue( $_REQUEST['inCodAlmoxarifado'] );

    $obLblAlmoxarifado->setCodAlmoxarifado( $_REQUEST['inCodAlmoxarifado'] );

    if (!$_REQUEST['inCodAlmoxarifado']) {
        $obFormulario->addComponente( $obAlmoxarifado );
    } else {
        $obFormulario->addHidden    ( $obHdnAlmoxarifado );
        $obFormulario->addComponente( $obLblAlmoxarifado );
    }

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs  = " d.getElementById('spnAlmoxarifado').innerHTML = '". $stHtml. "';\n";
    $stJs .= " d.getElementById('stExercicio').innerHTML = '".Sessao::getExercicio()."';\n";

    return $stJs;
}

function montaSpnDadosItem()
{
    $obLabelUnidadeMedida = new Label;
    $obLabelUnidadeMedida->setRotulo('Unidade de Medida' );
    $obLabelUnidadeMedida->setId    ('stUnidadeMedida'   );

    $obHiddenCodUnidadeMedida = new Hidden;
    $obHiddenCodUnidadeMedida->setName( 'inCodUnidadeMedida' );
    $obHiddenCodUnidadeMedida->setId  ( 'inCodUnidadeMedida' );

    $obHiddenNomUnidadeMedida = new Hidden;
    $obHiddenNomUnidadeMedida->setName( 'stNomUnidade' );
    $obHiddenNomUnidadeMedida->setId  ( 'stNomUnidade' );

    $obLabelTipo = new Label;
    $obLabelTipo->setRotulo( 'Tipo'     );
    $obLabelTipo->setId    ( 'stTipo'   );
    $obLabelTipo->setValue ( "&nbsp;" );

    $obHiddenCodTipo = new Hidden;
    $obHiddenCodTipo->setName  ( 'inCodTipo' );
    $obHiddenCodTipo->setId    ( 'inCodTipo' );
    $obHiddenCodTipo->setValue ( ''  );

    $obHiddenNomTipo = new Hidden;
    $obHiddenNomTipo->setName  ( 'stNomTipo' );
    $obHiddenNomTipo->setId    ( 'stNomTipo' );
    $obHiddenNomTipo->setValue ( ''  );

    $obFormularioLabel = new Formulario;
    $obFormularioLabel->addComponente( $obLabelUnidadeMedida );
    $obFormularioLabel->addHidden    ( $obHiddenCodUnidadeMedida );
    $obFormularioLabel->addHidden    ( $obHiddenNomUnidadeMedida );
    $obFormularioLabel->addComponente( $obLabelTipo );
    $obFormularioLabel->addHidden    ( $obHiddenCodTipo );
    $obFormularioLabel->addHidden    ( $obHiddenNomTipo );
    $obFormularioLabel->montaInnerHTML();
    $stHtmlLabel = $obFormularioLabel->getHTML();

    $stJs .= "document.getElementById('spnInformacoesItem').innerHTML = '".$stHtmlLabel."'; \n";
    $stJs .= "document.getElementById('stUnidadeMedida').innerHTML = '&nbsp;'; \n";
    $stJs .= "document.frm.stNomUnidade.value = ''; \n";
    $stJs .= "document.frm.inCodUnidadeMedida.value = ''; \n";

    return $stJs;
}

function limparItens()
{
    $stJs .= " d.getElementById('stNomItem').innerHTML = '&nbsp;';\n ";
    $stJs .= " f.inCodItem.value = '';\n ";
    $stJs .= " d.getElementById('stNomMarca').innerHTML = '&nbsp;';\n ";
    $stJs .= " f.inCodMarca.value = '';\n ";
    $stJs .= " d.getElementById('stNomCentroCusto').innerHTML = '&nbsp;';\n ";
    $stJs .= " f.inCodCentroCusto.value = '';\n ";
    $stJs .= " f.nuQuantidade.value = '';\n ";
    $stJs .= " f.nuVlTotal.value = '';\n ";

    $stJs .= " d.getElementById('stLote') ? f.stLote.value = '' : false ;\n";
    $stJs .= " d.getElementById('dtFabricacao') ? f.dtFabricacao.value = '' : false ;\n";
    $stJs .= " d.getElementById('dtValidade') ? f.dtValidade.value = '' : false ;\n";
    $stJs .= " d.getElementById('spnAtributos').innerHTML = '';";

    $stJs .= montaSpnDadosItem();

    return $stJs;
}

function montaFormLotes()
{
    $obFormulario = new Formulario;

    $obHdnIdLote = new Hidden;
    $obHdnIdLote->setName   ( "inIdLote" );
    $obHdnIdLote->setValue ( ''  );

    $obTxtNumeroLote= new TextBox();
    $obTxtNumeroLote->setName("stNumLote");
    $obTxtNumeroLote->setId("stNumLote");
    $obTxtNumeroLote->setObrigatorioBarra(true);
    $obTxtNumeroLote->setRotulo("Número do lote");
    $obTxtNumeroLote->setTitle("Informe o número do lote");

    $obTxtDataFabricacao = new Data();
    $obTxtDataFabricacao->setName("stDataFabricacao");
    $obTxtDataFabricacao->setId("stDataFabricacao");
    $obTxtDataFabricacao->setObrigatorioBarra(true);
    $obTxtDataFabricacao->setRotulo("Data de Fabricação");
    $obTxtDataFabricacao->setTitle("Informe a data de fabricação.");

    $obTxtDataValidade = new Data();
    $obTxtDataValidade->setName("stDataValidade");
    $obTxtDataValidade->setId("stDataValidade");
    $obTxtDataValidade->setObrigatorioBarra(true);
    $obTxtDataValidade->setRotulo("Data de Validade");
    $obTxtDataValidade->setTitle("Informe a data de validade.");

    $obTxtQuantidadeLote = new Quantidade;
    $obTxtQuantidadeLote->setRotulo ( "Quantidade" );
    $obTxtQuantidadeLote->setName("nmQuantidadeLote");
    $obTxtQuantidadeLote->setId("nmQuantidadeLote");
    $obTxtQuantidadeLote->setObrigatorioBarra(true);
    $obTxtQuantidadeLote->setTitle("Informe a quantidade do lote.");

    $obSpnListaLotes = new Span;
    $obSpnListaLotes->setId("spnListaLotes");

    $obFormulario->addTitulo    ( "Perecível"     );
    $obFormulario->addHidden    ( $obHdnIdLote    );
    $obFormulario->addComponente( $obTxtNumeroLote );
    $obFormulario->addComponente( $obTxtDataFabricacao );
    $obFormulario->addComponente( $obTxtDataValidade );
    $obFormulario->addComponente( $obTxtQuantidadeLote );
    $obFormulario->Incluir        ('Lotes', array( $obTxtNumeroLote, $obTxtDataFabricacao, $obTxtDataValidade, $obTxtQuantidadeLote) );
    $obFormulario->addSpan( $obSpnListaLotes );

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = " d.getElementById('spnFormLotes').innerHTML = '".$stHtml."';\n";
    $stJs.= " d.getElementById('nuQuantidade').readOnly = 'true' ;\n";
    $stJs.= " d.getElementById('nuQuantidade').value = '0,0000';\n";
    $stJs.= $obFormulario->getInnerJavascriptBarra();

    return $stJs;
}

function montaSpnAtributos($inCodItem , $arAtributosValor = array() )
{
     include_once(CAM_GP_ALM_COMPONENTES."IMontaAtributosEntrada.class.php");
     if ( ($inCodItem != "") && ($inCodItem != "0") ) {
            $obIMontaAtributosEntrada = new IMontaAtributosEntrada( $inCodItem, $arAtributosValor );
            $obIMontaAtributosEntrada->setIdCampoQuantidadeTotal( "nuQuantidade" );

            $obHdnTipoAtributos = new Hidden;
            $obHdnTipoAtributos->setName     ( "hdnTipoAtributos" );

            $obFormulario = new Formulario;
            $obFormulario->addHidden      ( $obHdnTipoAtributos    );

            $obIMontaAtributosEntrada->geraFormulario($obFormulario);
            $obFormulario->montaInnerHTML();
            $stHtmlFormulario = $obFormulario->getHTML();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);

            $js = "document.getElementById('spnAtributos').innerHTML = '".$stHtmlFormulario."';\n";
            $js .= "f.hdnTipoAtributos.value = '".$stEval."';\n";

      if ( $obIMontaAtributosEntrada->possuiAtributos() || $_REQUEST['inCodTipo'] == 2) {
         $js .= "document.getElementById('nuQuantidade').readOnly = 'true';\n";
      } else {
        $js .= "document.getElementById('nuQuantidade').readOnly = false;    ;\n";
      }
   } else {
     $js  = "document.getElementById('spnAtributos').innerHTML = '';\n";
     $js .= "document.getElementById('nuQuantidade').readOnly = 'false';\n";
   }

   return $js;
}

$arrayItens = Sessao::read('itens');
$arrayLotes = Sessao::read('lotes');
switch ($stCtrl) {

    case 'limpaItens':
        $stJs .= limparItens();
    break;

    case "incluirLotes":
        $boIncluir = true;
        if ($_REQUEST['nmQuantidadeLote'] <= "0,0000") {
            $stMensagem = "A Quantidade do Lote deve ser maior que zero.";
            $boIncluir = false;
        } elseif ( count( $arrayLotes ) > 0 ) {

            $stChave = trim($_REQUEST['stNumLote']);

            foreach ($arrayLotes as $key => $array) {

                $stChaveItem = $array['stNumLote'];

                if ($stChave == $stChaveItem and $array['inId'] != $_REQUEST['inIdLote']) {
                    $boIncluir = false;
                    $stMensagem = "Este registro já existe na lista.";
                    $break;
                }
            }
        }

        if ( !sistemaLegado::comparaDatas($_REQUEST['stDataValidade'],$_REQUEST['stDataFabricacao']) ) {
            $boIncluir = false;
            $stMensagem = "Data de validade <i><b>".$_REQUEST['stDataValidade']."</i></b> deve ser maior que a data da fabricação <i><b>".$_REQUEST['stDataFabricacao']."</i></b>.";
        }

        if ( sistemaLegado::comparaDatas($_REQUEST['stDataFabricacao'],date('d/m/Y') ) ) {
            $boIncluir = false;
            $stMensagem = "Data de fabricação <i><b>".$_REQUEST['stDataFabricacao']."</i></b> deve ser menor ou igual a data atual.";
        }

        $arrayLotes = Sessao::read('lotes');

        if ($boIncluir) {
            $inId = count( $arrayLotes ) + 1;

            $arLotes['inId'            ] = $inId;
            $arLotes['stNumLote'       ] = trim($_REQUEST['stNumLote'       ]);
            $arLotes['stDataValidade'  ] = $_REQUEST['stDataValidade'  ];
            $arLotes['stDataFabricacao'] = $_REQUEST['stDataFabricacao'];
            $arLotes['nmQuantidadeLote'] = $_REQUEST['nmQuantidadeLote'];

            if ($_REQUEST["inIdLote"]) {
                foreach ($arrayLotes as $inIndice => $arTmpLote) {
                    if ($arTmpLote['inId'] == $_REQUEST["inIdLote"]) {
                        $arItens['inId'] = $arTmpLote['inId'];

                        $arTempLotes[$inIndice] = $arLotes;
                        Sessao::write('lotes',$arTempLotes);
                        break;
                    }
                }
            } else {
                $arTempLotes = Sessao::read('lotes');
                $arTempLotes[] = $arLotes;
                Sessao::write('lotes',$arTempLotes);
            }

            $arrayLotes = Sessao::read('lotes');

            $stJs .= montalistaLotes( $arrayLotes );
            $stJs .= " f.inIdLote.value = '';\n";
            $stJs.= "f.nuQuantidade.value = parseToMoeda(parseToFloat(f.nuQuantidade.value) + parseToFloat(f.nmQuantidadeLote.value), 4);";
            $stJs .= "f.btIncluirLotes.value= 'Incluir';\n";
            $stJs.= "f.stNumLote.focus();\n";
            $stJs.= "limpaFormularioLotes();\n";
        } else {
            $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
        }
    break;

    case "montaFormLotes":

        if ($_REQUEST['inCodItem']) {
           include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php");
           $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
           $obTAlmoxarifadoCatalagoItem->setDado("cod_item", $_REQUEST['inCodItem']);
           $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem);
           //verifica c o tipo do item é perecivel
           if ($rsCatalogoItem->getCampo("cod_tipo") == 2) {
               $stJs .= montaFormLotes();
           } else {
               $stJs .= "d.getElementById('spnFormLotes').innerHTML = '';\n";
               $stJs .= "f.nuQuantidade.readOnly = 'false';\n";
            }
         } else {
               $stJs .= "d.getElementById('spnFormLotes').innerHTML = '';\n";
               $stJs .= "f.nuQuantidade.readOnly = 'false';\n";
         }
    break;

    case 'montaCampoAlmoxarifado':
        $stJs .= montaCampoAlmoxarifado();
    break;

    case 'limpaTodaTela':
        $stJs .= montaCampoAlmoxarifado();
        $stJs .= limparItens();

    break;

    case 'montaLote':
        if ( $_REQUEST['inCodItem']  &&  ( $_REQUEST['inCodTipo'] == 2 )) {

            $obTxtLote = new TextBox();
            $obTxtLote->setId('stLote');
            $obTxtLote->setName('stLote');
            $obTxtLote->setRotulo ( 'Lote');
            $obTxtLote->setTitle ( 'Informe o lote');
            $obTxtLote->setObrigatorioBarra( true );

            $obTxtDataFabricacao = new Data();
            $obTxtDataFabricacao->setId('dtFabricacao');
            $obTxtDataFabricacao->setName( 'dtFabricacao' );
            $obTxtDataFabricacao->setRotulo ( 'Data de Fabricação' );
            $obTxtDataFabricacao->setObrigatorioBarra( true );

            $obTxtDataValidade = new Data();
            $obTxtDataValidade->setId('dtValidade');
            $obTxtDataValidade->setName( 'dtValidade' );
            $obTxtDataValidade->setRotulo ( 'Data de Validade' );
            $obTxtDataValidade->setObrigatorioBarra( true );

            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTxtLote );
            $obFormulario->addComponente( $obTxtDataFabricacao );
            $obFormulario->addComponente( $obTxtDataValidade );

            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();

            $stJs  = "var x = document.getElementById('spnInformacoesItem').innerHTML;\n";
            $stJs .= "x = x + '".$stHtml."';\n";
            $stJs .= "document.getElementById('spnInformacoesItem').innerHTML = x;\n";

        }
    break;

    case 'excluiLote':
        $arVariaveis = $arTMP = array();
        $id = $_REQUEST['inIdLote'];
        $inCount = 0;

        if (!empty($_REQUEST[inIdItem])) {
            foreach ($arrayItens as $inItemLote => $arItens) {
                if ($arItens['inId'] == $_REQUEST[inIdItem]) {
                    $arLote = $arItens['lotes'];
                    $inIndiceLista = $inItemLote;
                }
            }
        } else {
            $arLote = $arrayLotes;
        }
        foreach ($arLote as $campo => $valor) {
            $stIds .= " - ".$valor['inId'];
            if ($valor['inId'] != $id) {
                $arLotes['inId'            ] =  ++$inCount;
                $arLotes['stNumLote'       ] =  $valor['stNumLote'       ];
                $arLotes['stDataFabricacao'] =  $valor['stDataFabricacao'];
                $arLotes['stDataValidade'  ] =  $valor['stDataValidade'  ];
                $arLotes['nmQuantidadeLote'] =  $valor['nmQuantidadeLote'];
                $arTMP[] = $arLotes;
            } else {
                $nmQuantidadeLote = $valor['nmQuantidadeLote'];
            }
        }

        if (!empty($_REQUEST[inIdItem])) {
            $arTempItens = Sessao::read('itens');
            $arTempItens[$inIndiceLista]['lotes'] = $arTMP;
            Sessao::write('itens',$arTMP);
        }

        Sessao::write('lotes',$arTMP);

        $stJs .= montaListaLotes( $arTMP );
        $stJs .= "f.nuQuantidade.value = parseToMoeda(parseToFloat(f.nuQuantidade.value) - parseToFloat('".$nmQuantidadeLote."'), 4);";
        $stJs.= "f.stNumLote.focus();\n";
    break;

    case 'alteraLote':
        $arValores = array();
        $id = $_REQUEST['inIdLote'];
        $inCount = 0;
        $stJs = "";

        if (!empty($_REQUEST[inIdItem])) {
            foreach ($arrayItens as $inItemLote => $arItens) {
                if ($arItens['inId'] == $_REQUEST[inIdItem]) {
                    $arLotes = $arItens['lotes'];
                    $inIndiceLista = $inItemLote;
                }
            }
        } else {
            $arLotes = $arrayLotes;
        }

        foreach ($arLotes as $campo => $valor) {
            if ($arLotes[$campo]['inId'] == $id) {
                $arValores = $arLotes[$campo];
                $stJs .= "f.inIdLote.value=".$id.";\n";
                $stJs .= "f.stNumLote.value='".$arValores['stNumLote']."';\n";
                $stJs .= "f.stDataFabricacao.value='".$arValores['stDataFabricacao']."';\n";
                $stJs .= "f.stDataValidade.value='".$arValores['stDataValidade']."';\n";
                $stJs .= "f.nmQuantidadeLote.value='".$arValores['nmQuantidadeLote']."';\n";
                $stJs .= "f.btIncluirLotes.value= 'Alterar';\n";
                break;
            }
        }
    break;

    case 'montaSpnAtributos':
        $stJs = montaSpnAtributos($_GET['inCodItem']);
    break;
}

echo $stJs;
