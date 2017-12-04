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
    * Arquivo instância para popup de Centro de Custo
    * Data de Criação: 07/03/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * Casos de uso: uc-03.03.06
                    uc-03.03.16
                    uc-03.03.17

    $Id: OCManterItem.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php";
include_once CAM_GP_ALM_COMPONENTES."IMontaClassificacao.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterial.class.php";

$stCampoCod  = $_REQUEST['stNomCampoCod'];
$stCampoDesc = $_REQUEST['stIdCampoDesc'];
$inCodigo    = $_REQUEST['inCodigo'];

$obRegra = new RAlmoxarifadoCatalogoItem;
$obIMontaClassificacao = new IMontaClassificacao();

switch ($_REQUEST['stCtrl']) {

    case "MontaNiveisCombo":
       if ($_REQUEST['inCodCatalogo']) {
          $obFormulario   = new Formulario;
          $obIMontaClassificacao->setCodigoCatalogo( $_REQUEST['inCodCatalogo'] );
          $obIMontaClassificacao->setUltimoNivelRequerido( false );
          $obIMontaClassificacao->setClassificacaoRequerida( false );
          $obIMontaClassificacao->geraFormulario( $obFormulario );
          $obFormulario->montaInnerHTML();
          $js = ' d.getElementById(\'spnListaClassificacao\').innerHTML = \''.$obFormulario->getHtml() .'\';';

          $obFormulario->obJavaScript->montaJavaScript();
          $stValida = $obFormulario->obJavaScript->getInnerJavaScript();
          $js .= " f.stValida.value = '".$stValida."';";

          if ( Sessao::read('transf3') ) {
             $obIMontaClassificacao->setCodigoCatalogo       ( $_REQUEST["inCodCatalogo"]   );
             $obIMontaClassificacao->setCodEstruturalReduzido( Sessao::read('transf3') );
             $js .= $obIMontaClassificacao->preencheCombos($_REQUEST['inNumNiveis']);
          }
       } else {
          $js = ' d.getElementById(\'spnListaClassificacao\').innerHTML = \'\';';
       }
       SistemaLegado::executaIFrameOculto($js);
    break;

    case "preencheProxCombo":
        $stNomeComboClassificacao  = "inCodClassificacao_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocalClassificacao = $_REQUEST[$stNomeComboClassificacao];
        $arChaveLocalClassificacao = explode('-', $stChaveLocalClassificacao );
        $inPosicao = $_REQUEST["inPosicao"];
        $obIMontaClassificacao->setCodigoCatalogo     ( $_REQUEST["inCodCatalogo"] );
        $obIMontaClassificacao->setCodigoNivel        ( $arChaveLocalClassificacao[0] );
        $obIMontaClassificacao->setCodigoClassificacao( $arChaveLocalClassificacao[1] );

        $stReduzido = $arChaveLocalClassificacao[2];
        if (!$stReduzido) {
            //Se não encontrar o estrutural reduzido no combo selecionado, recupera o reduzido do combo anterior
            $arChaveLocalClassificacao = explode('-', $_REQUEST[ "inCodClassificacao_".( $_REQUEST["inPosicao"] - 2) ] );
            $stReduzido = $arChaveLocalClassificacao[2];
        }
        while( preg_match('/\.0+$/', $stReduzido ))
            $stReduzido = preg_replace('/\.0+$/', '', $stReduzido );

        $obIMontaClassificacao->setCodEstruturalReduzido( $stReduzido );
        $js = $obIMontaClassificacao->preencheProxCombo     ( $inPosicao , $_REQUEST["inNumNiveis"] );
        sistemaLegado::executaIFrameOculto($js);
    break;

    case "preencheCombos":
        $obIMontaClassificacao->setCodigoCatalogo       ( $_REQUEST["inCodCatalogo"]   );
        $obIMontaClassificacao->setCodEstruturalReduzido( $_REQUEST['stChaveClassificacao']);
        $js = $obIMontaClassificacao->preencheCombos($_REQUEST['inNumNiveis']);
        sistemaLegado::executaIFrameOculto($js);
    break;

    case "buscaPopupItemRequisicao":
        if ($inCodigo) {
            $obRegra = new RAlmoxarifadoCatalogoItem();
            $obRegra->setCodigo( $_REQUEST['inCodigo'] );
            $obRegra->setVerificaSaldo(false);
            $obRegra->setUnidadeNaoInformado(true);
            $obRegra->setTipoNaoInformado(false);
            $obRegra->setVerificarMovimentacaoItem(false);
            $obRegra->listar( $rsItem );

            $stDescricao   = $rsItem->getCampo( "descricao"    ) ? $rsItem->getCampo( "descricao"    ) : "&nbsp;";
            $stUnidade     = $rsItem->getCampo( "nom_unidade"  ) ? $rsItem->getCampo( "nom_unidade"  ) : "&nbsp;";
            $stNomTipo     = $rsItem->getCampo( "desc_tipo"    ) ? $rsItem->getCampo( "desc_tipo"    ) : "&nbsp;";
        } else {
            $stDescricao   = "";
            $stUnidade     = "";
            $stNomTipo     = "";
        }
        //DEFINIÇÃO DOS POSSÍVEIS COMPONENTES
        //Unidade de Medida em Label
        $obLabelUnidadeMedida = new Label;
        $obLabelUnidadeMedida->setRotulo ('Unidade de Medida' );
        $obLabelUnidadeMedida->setId     ('stUnidadeMedida'   );
        $obLabelUnidadeMedida->setValue  ( $stUnidade         );

        $obHiddenCodUnidadeMedida = new Hidden;
        $obHiddenCodUnidadeMedida->setName  ( 'inCodUnidadeMedida'             );
        $obHiddenCodUnidadeMedida->setId    ( 'inCodUnidadeMedida'             );
        $obHiddenCodUnidadeMedida->setValue ( $inCodUnidade."-".$inCodGrandeza );

        $obHiddenNomUnidadeMedida = new Hidden;
        $obHiddenNomUnidadeMedida->setName  ( 'stNomUnidade' );
        $obHiddenNomUnidadeMedida->setId    ( 'stNomUnidade' );
        $obHiddenNomUnidadeMedida->setValue ( $stUnidade     );

        //Unidade de Medida em Combo
        include_once(CAM_GA_ADM_COMPONENTES . "ISelectUnidadeMedida.class.php");
        $obISelectUnidadeMedida = new ISelectUnidadeMedida;
        $obISelectUnidadeMedida->setObrigatorioBarra( true );
        $obISelectUnidadeMedida->setName  ( 'inCodUnidadeMedida' );
        $obISelectUnidadeMedida->setId    ( 'inCodUnidadeMedida' );
        $obISelectUnidadeMedida->setTitle ( 'Informe a Unidade de Medida.' );

        //Tipo em Label
        $obLabelTipo = new Label;
        $obLabelTipo->setRotulo( 'Tipo'     );
        $obLabelTipo->setId    ( 'stTipo'   );
        $obLabelTipo->setValue ( $stNomTipo );

        $obHiddenCodTipo = new Hidden;
        $obHiddenCodTipo->setName  ( 'inCodTipo' );
        $obHiddenCodTipo->setId    ( 'inCodTipo' );
        $obHiddenCodTipo->setValue ( $inCodTipo  );

        $obHiddenNomTipo = new Hidden;
        $obHiddenNomTipo->setName  ( 'stNomTipo' );
        $obHiddenNomTipo->setId    ( 'stNomTipo' );
        $obHiddenNomTipo->setValue ( $stNomTipo  );

        //Tipo em Radio
        include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoTipoItem.class.php");
        $obTipoItem = new RAlmoxarifadoTipoItem;
        $obTipoItem->listar( $rsTipoItem );
        $arRdTipo = array();
        for ($i = 0; $i < $rsTipoItem->getNumLinhas(); $i++) {
            if ($rsTipoItem->getCampo('cod_tipo') != 0) {
                $obRdTipo = new Radio;
                $obRdTipo->setRotulo           ( "Tipo"                             );
                $obRdTipo->setName             ( "inCodTipo"                        );
                $obRdTipo->setId               ( "inCodTipo$i"                      );
                $obRdTipo->setLabel            ( $rsTipoItem->getCampo('descricao') );
                $obRdTipo->setValue            ( $rsTipoItem->getCampo('cod_tipo')  );
                $obRdTipo->setChecked          ( ( $i == 0 )                        );
                $obRdTipo->setDisabled         ( ( $rsTipoItem->getCampo('cod_tipo') == 3 ) );
                $obRdTipo->setObrigatorioBarra ( true                               );
                $arRdTipo[] = $obRdTipo;
                $rsTipoItem->proximo();
            }
        }
        //Inicio Atributos dinâmicos

        $rsAtributosDisponiveis = $rsAtributosSelecionados = new RecordSet;
        $boTemMovimentacao      = false;
        $boPermiteManutencao    = true;
        if ($inCodigo!= "") {
            $obRegra->obRCadastroDinamico->setChavePersistenteValores   ( array("cod_item"=>$inCodigo ) );
            $obRegra->obRCadastroDinamico->recuperaAtributosDisponiveis ( $rsAtributosDisponiveis       );
            $obRegra->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosSelecionados      );

            $obMontaAtributos = new MontaAtributos;
            $obMontaAtributos->setTitulo   ( "Atributos"              );
            $obMontaAtributos->setName     ( "Atributo_"              );
            $obMontaAtributos->setRecordSet( $rsAtributosSelecionados );
        }
        //Final Atributos dinâmicos

        //MONTA HTML
        //Se existe nomCampoUnidade é que é para mostrar informações do ítem no span
        $jsFocus = "";
        if ($_REQUEST['nomCampoUnidade'] != '') {
            $obFormulario = new Formulario;
            $obJavaScript = new JavaScript ('thururu');

            if ( ( $_REQUEST['boPreencheUnidadeNaoInformada'] == "true" ) && ( $inCodUnidade == "0" ) ) {
                $obFormulario->addComponente ( $obISelectUnidadeMedida );
                $obJavaScript->addComponente ( $obISelectUnidadeMedida );
                $jsFocus = "f.inCodUnidadeMedida.focus();";
            } else {
                $obFormulario = new Formulario;
                $obFormulario->addComponente    ( $obLabelUnidadeMedida     );
                $obFormulario->addHidden        ( $obHiddenCodUnidadeMedida );
                $obFormulario->addHidden        ( $obHiddenNomUnidadeMedida );
            }
            if ($_REQUEST['boExibeTipo'] == "true") {
                if ( ( $_REQUEST['boPreencheTipoNaoInformado'] == "true" ) && ( $inCodTipo == "0" ) ) {
                    $obFormulario->agrupaComponentes ( $arRdTipo                 );
                    $jsFocus = "d.getElementById('inCodTipo0').focus();";
                } else {
                    $obFormulario->addComponente     ( $obLabelTipo              );
                    $obFormulario->addHidden         ( $obHiddenCodTipo          );
                    $obFormulario->addHidden         ( $obHiddenNomTipo          );
                }
            }

            if (isset($_REQUEST['boParametroDinamico'])) {
                if (!(empty($_REQUEST['boParametroDinamico']))) {
                    if ($_REQUEST['boParametroDinamico']=="true") {
                        //Inicio Atributos dinâmicos
                        if ($inCodigo!= "") {
                            $obMontaAtributos->geraFormulario( $obFormulario );
                        }
                    }
                }
            }

            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();

            $obJavaScript->montaJavaScript();
            $jsValida = $obJavaScript->getInnerJavaScript();
            $jsValida = str_replace( "\n", "", $jsValida );
        }
        $stDescricao = addslashes($stDescricao);
        //MONTA JAVA SCRIPT
        $stJs .= "retornaValorBscInner( '".$stCampoCod."', '".$stCampoDesc."', '".$_GET['stNomForm']."', '".$stDescricao."'); \n";
        $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '".$stDescricao."'; \n";
        $stJs .= "f.".$stCampoCod.".value = '".$inCodigo."'; \n";
        if ($_REQUEST['nomCampoUnidade'] != '') {
            $stJs .= "document.getElementById('spnInformacoesItem').innerHTML = '".$stHtml."'; \n";
            $stJs .= "document.getElementById('hdnUnidadeMedidaValida').value = '".$jsValida."'; \n";
        }

        if ($stErro) {
            $stJs .= "alertaAviso('".$stErro."', 'form','erro','".Sessao::getId()."');";
        }
        $stJs .=  $jsFocus;

        echo $stJs;

    break;

    case 'buscaPopup':
    default:
        //CONSULTA

        if ($inCodigo) {
            $obRegra = new RAlmoxarifadoCatalogoItem();
            $obRegra->setCodigo( $_REQUEST['inCodigo'] );

            if (isset($_REQUEST['boAtivo']) && !empty($_REQUEST['boAtivo']))
                $obRegra->setAtivo(true);

            if (isset($_REQUEST['boServico'])) {
                if ($_REQUEST['boServico']) {
                   $obRegra->setServico(true);
                } else {

                    $obRegra->setServico(false);
                }
            } else {
                $obRegra->setServico(true);
            }

            if (isset($_REQUEST['boVerificaSaldo'])) {
                $obRegra->setVerificaSaldo(true);
            }

            if ($_REQUEST["boUnidadeNaoInformado"] == 1) {
                $obRegra->setUnidadeNaoInformado(true);
            } else {
                $obRegra->setUnidadeNaoInformado(false);
            }

            if ( $request->get("boTipoNaoInformado") == 1 ) {
                $obRegra->setTipoNaoInformado(true);
            } else {
                $obRegra->setTipoNaoInformado(false);
            }

            $boVerificaMovimentacaoItem = $_REQUEST['boVerificaMovimentacaoItem'];
            if ($boVerificaMovimentacaoItem) {
                $obRegra->setVerificarMovimentacaoItem(true);
            } else {
                $obRegra->setVerificarMovimentacaoItem(false);
            }

            if ( $request->get('inCodCatalogo') ) {
                $obRegra->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo($request->get('inCodCatalogo'));
            }
            if ( $request->get('stCodEstruturalReduzido') ) {
                $obRegra->obRAlmoxarifadoClassificacao->setEstrutural($request->get('stCodEstruturalReduzido'));
            }
            if ( $request->get('stFiltroBusca') ) {
                if ( strpos($request->get('stFiltroBusca'), 'SomenteComMovimentacao')!==false ) {
                    $obRegra->boSomenteComMovimentacao = true;
                }
            }

            $obRegra->listar( $rsItem );

            if ( $rsItem->getNumLinhas() < 1 ) {

                $obVerificaItem = new RAlmoxarifadoCatalogoItem();
                $obVerificaItem->setCodigo( $_REQUEST['inCodigo'] );

                $obVerificaItem->listar($rsItemVerificado);

                $boComMovimentacao = true;
                if ($obRegra->boSomenteComMovimentacao) {
                    $obTLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial();
                    $obTLancamentoMaterial->recuperaTodos($rsLancamentos, " where cod_item = ".$_REQUEST['inCodigo']);
                    $boComMovimentacao = ($rsLancamentos->getNumLinhas()>0);
                }

                if (!$boComMovimentacao) {
                    $stErro = "@Este item (".$inCodigo.") não possui movimentação no estoque! ".$_REQUEST['stMsgComplementarSemSaldo'];
                } else
                if ($rsItemVerificado->getNumLinhas() > 0 ) {

                    if ($_REQUEST['stCodEstruturalReduzido']) {
                        $stErro = "@Este item não pertence à classificação selecionada! Item:(". $inCodigo .")";
                    } else {
                        if ($_REQUEST['boVerificaMovimentacaoItem']) {
                            $stErro = "@Este item não pode ser implantado pois já possui movimentação em estoque! Item:(". $inCodigo .")";
                        } elseif ($_REQUEST['boVerificaSaldo']) {
                            $stErro = "@Este Item: (". $inCodigo .") não possui saldo em estoque!";
                        } elseif ($_REQUEST['boAtivo']) {
                            $stErro = "@Este Item: (". $inCodigo .") está inativo!";
                        }
                    }

                } else {
                    $stFiltroTipoItem = " and tipo_item.cod_tipo = 3";
                    $tAlmoxarifadoTipoItem = new TAlmoxarifadoCatalogoItem();
                    $tAlmoxarifadoTipoItem->setDado('cod_item',$_REQUEST['inCodigo']);
                    $tAlmoxarifadoTipoItem->verificaTipoItem($rsTipoItem,$stFiltroTipoItem);

                    if ($rsTipoItem->getNumLinhas() > 0) {
                        $stErro = "@Tipo do item selecionado é inválido (Serviço).";
                    } else {
                        $stErro = "@Código do Item (". $inCodigo .") não encontrado.";
                    }
                }

                $inCodigo      = "";
                $inCodUnidade  = "";
                $inCodGrandeza = "";
                $inCodTipo     = "";
            } else {
                $inCodUnidade  = $rsItem->getCampo( "cod_unidade"  ) ? $rsItem->getCampo( "cod_unidade"  ) : "0";
                $inCodGrandeza = $rsItem->getCampo( "cod_grandeza" ) ? $rsItem->getCampo( "cod_grandeza" ) : "0";
                $inCodTipo     = $rsItem->getCampo( "cod_tipo"     ) ? $rsItem->getCampo( "cod_tipo"     ) : "0";
            }
            $stDescricao   = $rsItem->getCampo( "descricao"    ) ? $rsItem->getCampo( "descricao"    ) : "&nbsp;";
            $stUnidade     = $rsItem->getCampo( "nom_unidade"  ) ? $rsItem->getCampo( "nom_unidade"  ) : "&nbsp;";
            $stNomTipo     = $rsItem->getCampo( "desc_tipo"    ) ? $rsItem->getCampo( "desc_tipo"    ) : "&nbsp;";
        } else {
            $inCodigo      = "";
            $stDescricao   = "&nbsp;";
            $inCodUnidade  = "";
            $inCodGrandeza = "";
            $stUnidade     = "&nbsp;";
            $inCodTipo     = "";
            $stNomTipo     = "&nbsp;";
        }

        //DEFINIÇÃO DOS POSSÍVEIS COMPONENTES
        //Unidade de Medida em Label
            $obLabelUnidadeMedida = new Label;
            $obLabelUnidadeMedida->setRotulo ('Unidade de Medida' );
            $obLabelUnidadeMedida->setId     ('stUnidadeMedida'   );
            $obLabelUnidadeMedida->setValue  ( $stUnidade         );

            $obHiddenCodUnidadeMedida = new Hidden;
            $obHiddenCodUnidadeMedida->setName  ( 'inCodUnidadeMedida'             );
            $obHiddenCodUnidadeMedida->setId    ( 'inCodUnidadeMedida'             );
            $obHiddenCodUnidadeMedida->setValue ( $inCodUnidade."-".$inCodGrandeza );

            $obHiddenNomUnidadeMedida = new Hidden;
            $obHiddenNomUnidadeMedida->setName  ( 'stNomUnidade' );
            $obHiddenNomUnidadeMedida->setId    ( 'stNomUnidade' );
            $obHiddenNomUnidadeMedida->setValue ( $stUnidade     );

        //Unidade de Medida em Combo
            include_once(CAM_GA_ADM_COMPONENTES . "ISelectUnidadeMedida.class.php");
            $obISelectUnidadeMedida = new ISelectUnidadeMedida;
            $obISelectUnidadeMedida->setObrigatorioBarra( true );
            $obISelectUnidadeMedida->setName  ( 'inCodUnidadeMedida' );
            $obISelectUnidadeMedida->setId    ( 'inCodUnidadeMedida' );
            $obISelectUnidadeMedida->setTitle ( 'Informe a Unidade de Medida.' );

        //Tipo em Label
            $obLabelTipo = new Label;
            $obLabelTipo->setRotulo( 'Tipo'     );
            $obLabelTipo->setId    ( 'stTipo'   );
            $obLabelTipo->setValue ( $stNomTipo );

            $obHiddenCodTipo = new Hidden;
            $obHiddenCodTipo->setName  ( 'inCodTipo' );
            $obHiddenCodTipo->setId    ( 'inCodTipo' );
            $obHiddenCodTipo->setValue ( $inCodTipo  );

            $obHiddenNomTipo = new Hidden;
            $obHiddenNomTipo->setName  ( 'stNomTipo' );
            $obHiddenNomTipo->setId    ( 'stNomTipo' );
            $obHiddenNomTipo->setValue ( $stNomTipo  );

        //Tipo em Radio
            include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoTipoItem.class.php");
            $obTipoItem = new RAlmoxarifadoTipoItem;
            $obTipoItem->listar( $rsTipoItem );
            $arRdTipo = array();
            for ($i = 0; $i < $rsTipoItem->getNumLinhas(); $i++) {
                if ($rsTipoItem->getCampo('cod_tipo') != 0) {
                    $obRdTipo = new Radio;
                    $obRdTipo->setRotulo           ( "Tipo"                             );
                    $obRdTipo->setName             ( "inCodTipo"                        );
                    $obRdTipo->setId               ( "inCodTipo$i"                      );
                    $obRdTipo->setLabel            ( $rsTipoItem->getCampo('descricao') );
                    $obRdTipo->setValue            ( $rsTipoItem->getCampo('cod_tipo')  );
                    $obRdTipo->setChecked          ( ( $i == 0 )                        );
                    $obRdTipo->setDisabled         ( ( $rsTipoItem->getCampo('cod_tipo') == 3 ) );
                    $obRdTipo->setObrigatorioBarra ( true                               );
                    $arRdTipo[] = $obRdTipo;
                    $rsTipoItem->proximo();
                }
            }
            //Inicio Atributos dinâmicos

            $rsAtributosDisponiveis = $rsAtributosSelecionados = new RecordSet;
            $boTemMovimentacao      = false;
            $boPermiteManutencao    = true;
            if ($inCodigo!= "") {
                $obRegra->obRCadastroDinamico->setChavePersistenteValores   ( array("cod_item"=>$inCodigo ) );
                $obRegra->obRCadastroDinamico->recuperaAtributosDisponiveis ( $rsAtributosDisponiveis       );
                $obRegra->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosSelecionados      );

                $obMontaAtributos = new MontaAtributos;
                $obMontaAtributos->setTitulo   ( "Atributos"              );
                $obMontaAtributos->setName     ( "Atributo_"              );
                $obMontaAtributos->setRecordSet( $rsAtributosSelecionados );
            }
            //Final Atributos dinâmicos

        //MONTA HTML
        //Se existe nomCampoUnidade é que é para mostrar informações do ítem no span
        $jsFocus = "";
        if ($_REQUEST['nomCampoUnidade'] != '') {
            $obFormulario = new Formulario;
            $obJavaScript = new JavaScript ('thururu');

            if ( ( $_REQUEST['boPreencheUnidadeNaoInformada'] == "true" ) && ( $inCodUnidade == "0" ) ) {
                $obFormulario->addComponente ( $obISelectUnidadeMedida );
                $obJavaScript->addComponente ( $obISelectUnidadeMedida );
                $jsFocus = "f.inCodUnidadeMedida.focus();";
            } else {
                $obFormulario = new Formulario;
                $obFormulario->addComponente    ( $obLabelUnidadeMedida     );
                $obFormulario->addHidden        ( $obHiddenCodUnidadeMedida );
                $obFormulario->addHidden        ( $obHiddenNomUnidadeMedida );
            }
            if ($_REQUEST['boExibeTipo'] == "true") {
                if ( ( $_REQUEST['boPreencheTipoNaoInformado'] == "true" ) && ( $inCodTipo == "0" ) ) {
                    $obFormulario->agrupaComponentes ( $arRdTipo                 );
                    $jsFocus = "d.getElementById('inCodTipo0').focus();";
                } else {
                    $obFormulario->addComponente     ( $obLabelTipo              );
                    $obFormulario->addHidden         ( $obHiddenCodTipo          );
                    $obFormulario->addHidden         ( $obHiddenNomTipo          );
                }
            }

            if (isset($_REQUEST['boParametroDinamico'])) {
                if (!(empty($_REQUEST['boParametroDinamico']))) {
                    if ($_REQUEST['boParametroDinamico']=="true") {
                        //Inicio Atributos dinâmicos
                        if ($inCodigo!= "") {
                            $obMontaAtributos->geraFormulario( $obFormulario );
                        }
                        //Final Atributos dinâmicos
                    }
                }
            }

            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();

            $obJavaScript->montaJavaScript();
            $jsValida = $obJavaScript->getInnerJavaScript();
            $jsValida = str_replace( "\n", "", $jsValida );
        }
        $stDescricao = addslashes($stDescricao);
        $stErro = isset($stErro) ? $stErro : "";

        //MONTA JAVA SCRIPT
        $stJs = isset($stJs) ? $stJs : "";
        $stJs .= "retornaValorBscInner( '".$stCampoCod."', '".$stCampoDesc."', '".$_GET['stNomForm']."', '".$stDescricao."'); \n";
        $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '".$stDescricao."'; \n";
        $stJs .= "f.".$stCampoCod.".value = '".$inCodigo."'; \n";
        if ($_REQUEST['nomCampoUnidade'] != '') {
            $stJs .= "document.getElementById('spnInformacoesItem').innerHTML = '".$stHtml."'; \n";
            $stJs .= "document.getElementById('hdnUnidadeMedidaValida').value = '".$jsValida."'; \n";
        }

        if ($stErro) {
            $stJs .= "alertaAviso('".$stErro."', 'form','erro','".Sessao::getId()."');";
        }
        $stJs .=  $jsFocus;

        echo $stJs;
    break;
}

?>
