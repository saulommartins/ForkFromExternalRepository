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
* Página de Formulário FolhaPagamentoPrevidencia
* Data de Criação   : 29/11/2004

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 30840 $
$Name$
$Author: souzadl $
$Date: 2007-06-22 15:03:26 -0300 (Sex, 22 Jun 2007) $

* Casos de uso: uc-04.05.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php" );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php");
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

$stPrograma = "ManterPrevidencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once($pgOcul);
include_once($pgJs);

#Sessao->transf = "";
Sessao::write('Faixas',array());

$rsFaixas    = new RecordSet;
$obRFolhaPagamentoPrevidencia     = new RFolhaPagamentoPrevidencia;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

#sessao->transf = array();
#sessao->transf['Faixas'] = array();

if ( empty($stAcao)||$stAcao=="incluir" ) {
    $stAcao = "incluir";
    $obRFolhaPagamentoPrevidencia->obRFaixaDesconto->listarFaixaDesconto( $rsFaixas );
    Sessao::write('stTipo',"");
} elseif ($stAcao) {
    $obRFolhaPagamentoPrevidencia->setCodPrevidencia( $_REQUEST['inCodPrevidencia'] );
    $obRFolhaPagamentoPrevidencia->consultarPrevidencia();
    $stDescricao    = $obRFolhaPagamentoPrevidencia->getDescricao();
    $flAliquota     = $obRFolhaPagamentoPrevidencia->getAliquota();
    $flAcidente     = $obRFolhaPagamentoPrevidencia->getAcidente();
    $stTipo         = $obRFolhaPagamentoPrevidencia->getTipo();
    $dtVigencia     = $obRFolhaPagamentoPrevidencia->getVigencia();
    $inCodRegimePrevidencia = $obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia();
    $flAliquotaRat  = $obRFolhaPagamentoPrevidencia->getAliquotaRat();
    $flAliquotaFap  = $obRFolhaPagamentoPrevidencia->getAliquotaFap();

    Sessao::write('flAliquotaRat',number_format($flAliquotaRat,2,',','.'));
    Sessao::write('flAliquotaFap',number_format($flAliquotaFap,4,',','.'));
    Sessao::write('inCodRegimePrevidencia',$inCodRegimePrevidencia);
    Sessao::write('stTipo',$stTipo);
    switch ( $obRFolhaPagamentoPrevidencia->getVinculo() ) {
      case 1:
         $stVinculo = 'Ativo';
      break;
      case 2:
         $stVinculo = 'Aposentado';
      break;
      case 3:
         $stVinculo = 'Pensionista';
      break;
    }

    $obRFolhaPagamentoPrevidencia->listarPrevidenciaEvento($rsPrevidenciaEvento);
    while ( !$rsPrevidenciaEvento->eof() ) {
        $stValue          = "inCodigoPrev".$rsPrevidenciaEvento->getCampo('cod_tipo');
        $stDescricaoPrev  = "stDescricaoPrev".$rsPrevidenciaEvento->getCampo('cod_tipo');
        $$stValue         = $rsPrevidenciaEvento->getCampo('codigo');
        $$stDescricaoPrev = $rsPrevidenciaEvento->getCampo('descricao');
        $rsPrevidenciaEvento->proximo();
    }

    $obRFolhaPagamentoPrevidencia->obRFaixaDesconto->setCodPrevidencia( $_REQUEST['inCodPrevidencia'] );
    $obRFolhaPagamentoPrevidencia->obRFaixaDesconto->consultarFaixaDesconto( $rsFaixas );
    $inCount = 0;
    $rsFaixas->addFormatacao("valor_inicial"      , "NUMERIC_BR");
    $rsFaixas->addFormatacao("valor_final"        , "NUMERIC_BR");
    $rsFaixas->addFormatacao("percentual_desconto", "NUMERIC_BR");
    while ( !$rsFaixas->eof() ) {
        $arTMP['inId']              = $inCount++;
        $arTMP['inCodFaixas']       = $rsFaixas->getCampo("cod_faixa");
        $arTMP['flSalarioInicial']  = $rsFaixas->getCampo("valor_inicial");
        $arTMP['flSalarioFinal']    = $rsFaixas->getCampo("valor_final");
        $arTMP['flPercentualDesc']  = $rsFaixas->getCampo("percentual_desconto");

        $arFaixas[] = $arTMP;
        $rsFaixas->proximo();
    }
    Sessao::write("Faixas",$arFaixas);
    SistemaLegado::executaFramePrincipal("buscaValor('preencheInner');");
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodOrganograma = new Hidden;
$obHdnCodOrganograma->setName( "inCodPrevidencia" );
$obHdnCodOrganograma->setValue( $_REQUEST["inCodPrevidencia"] );

$obHdnCodNorma = new Hidden;
$obHdnCodNorma->setName( "hdninCodFaixaDesconto" );
$obHdnCodNorma->setValue( $hdninCodFaixaDesconto );

$obHdnVigenciaAntiga = new Hidden;
$obHdnVigenciaAntiga->setName( "dtVigenciaAntiga" );
$obHdnVigenciaAntiga->setValue( $dtVigencia );

//Define objeto TEXTBOX para armazenar a DESCRICAO da previdencia
$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo        ( "Descrição" );
$obTxtDescricao->setTitle         ( "Nome da previdência." );
$obTxtDescricao->setName          ( "stDescricao" );
$obTxtDescricao->setValue         ( $stDescricao  );
$obTxtDescricao->setSize          ( 40 );
$obTxtDescricao->setMaxLength     ( 80 );
$obTxtDescricao->setNull          ( false );
$obTxtDescricao->setEspacosExtras ( false );

//Define objeto TEXTBOX para armazenar o Percentual para BASE PATRONAL
$obTxtBasePatronal = new Moeda;
$obTxtBasePatronal->setRotulo     ( "Alíquota Patronal" );
$obTxtBasePatronal->setName       ( "flAliquota" );
$obTxtBasePatronal->setValue      ( $flAliquota  );
$obTxtBasePatronal->setTitle      ( "Alíquota de contribuição patronal." );
$obTxtBasePatronal->setNull       ( false );
$obTxtBasePatronal->setMaxLength  ( 6     );
$obTxtBasePatronal->obEvento->setOnChange ( "validaDesconto(document.frm.flAliquota.value, document.frm.flAliquota, 'Percentual de aliquota patronal');" );

//Define objeto TEXTBOX para armazenar o Percentual para ACIDENTE DE TRABALHO
$obTxtAcidente = new Moeda;
$obTxtAcidente->setRotulo     ( "Acidente de trabalho" );
$obTxtAcidente->setName       ( "flAcidente" );
$obTxtAcidente->setValue      ( $flAcidente  );
$obTxtAcidente->setTitle      ( "Percentual para acidente de trabalho." );
$obTxtAcidente->setNull       ( false );
$obTxtAcidente->setMaxLength  ( 6     );
$obTxtAcidente->obEvento->setOnChange ( "validaDesconto(document.frm.flAcidente.value, document.frm.flAcidente, 'Percentual para acidente de trabalho');" );

//Define objetos RADIO para armazenar o TIPO DA PREVIDENCIA
if ($stAcao == 'incluir') {
    $obRdbTipoOficial = new Radio;
    $obRdbTipoOficial->setRotulo           ( "Tipo" );
    $obRdbTipoOficial->setName             ( "stTipo" );
    $obRdbTipoOficial->setValue            ( "o" );
    $obRdbTipoOficial->setLabel            ( "Oficial" );
    $obRdbTipoOficial->setChecked          ( ($stTipo == 'o' or !$stTipoProva) );
    $obRdbTipoOficial->setNull             ( false );
    $obRdbTipoOficial->obEvento->setOnChange( "buscaValor('gerarSpan1');" );

    $obRdbTipoPrivada = new Radio;
    $obRdbTipoPrivada->setRotulo            ( "Tipo"                      );
    $obRdbTipoPrivada->setName              ( "stTipo"                    );
    $obRdbTipoPrivada->setValue             ( "p"                         );
    $obRdbTipoPrivada->setLabel             ( "Privada"                   );
    $obRdbTipoPrivada->setChecked           ( ($stTipo == 'p')            );
    $obRdbTipoPrivada->setNull              ( false                       );
    $obRdbTipoPrivada->obEvento->setOnChange( "buscaValor('gerarSpan1');" );

} else {

    $obHdnTipo = new Hidden;
    $obHdnTipo->setName  ( 'stTipo');
    $obHdnTipo->setValue ( $stTipo );

    $obLblTipo = new Label;
    $obLblTipo->setRotulo( "Tipo" );
    if ($stTipo == 'o') {
        $obLblTipo->setValue ( 'Oficial' );
    } else {
        $obLblTipo->setValue ( 'Privada' );
    }

    $obHdnRegime = new Hidden;
    $obHdnRegime->setName  ( 'inCodRegimePrevidencia' );
    $obHdnRegime->setValue ( $inCodRegimePrevidencia  );

    $obLblRegime = new Label;
    $obLblRegime->setRotulo ( 'Regime' );
    if ($inCodRegimePrevidencia == 1) {
        $obLblRegime->setValue ( 'RGPS' );
    } else {
        $obLblRegime->setValue ( 'RPPS' );
    }

}
$obSpan = new Span;
$obSpan->setId( "spnSpan1" );

//Define objetos RADIO para armazenar o Vinculo
$obRdbTipoAtivo = new Radio;
$obRdbTipoAtivo->setRotulo           ( "Vínculo" );
$obRdbTipoAtivo->setName             ( "inVinculo" );
$obRdbTipoAtivo->setValue            ( "1" );
$obRdbTipoAtivo->setLabel            ( "Ativo" );
$obRdbTipoAtivo->setChecked          ( true );
$obRdbTipoAtivo->setNull             ( false );

$obRdbTipoAposentado = new Radio;
$obRdbTipoAposentado->setRotulo           ( "Vínculo" );
$obRdbTipoAposentado->setName             ( "inVinculo" );
$obRdbTipoAposentado->setValue            ( "2" );
$obRdbTipoAposentado->setLabel            ( "Aposentado" );
$obRdbTipoAposentado->setChecked          ( false );
$obRdbTipoAposentado->setNull             ( false );

$obRdbTipoPensionista = new Radio;
$obRdbTipoPensionista->setRotulo           ( "Vínculo" );
$obRdbTipoPensionista->setName             ( "inVinculo" );
$obRdbTipoPensionista->setValue            ( "3" );
$obRdbTipoPensionista->setLabel            ( "Pensionista" );
$obRdbTipoPensionista->setChecked          ( false );
$obRdbTipoPensionista->setNull             ( false );

// Quando está em alterar ele não mostra o rádio somente um label com o valor
$obLblVinculo = new Label;
$obLblVinculo->setRotulo       ( 'Vínculo'  );
$obLblVinculo->setName         ( 'inVinculo'                           );
$obLblVinculo->setValue        ( $stVinculo                            );

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$obRFolhaPagamentoPrevidencia->addRFolhaPagamentoEvento();
$obRFolhaPagamentoPrevidencia->roRFolhaPagamentoEvento->listarTiposEventoPrevidencia($rsTiposEvento);
$arComponentes = array();
while (!$rsTiposEvento->eof()) {
    $stNomeComponente = "obBscEventoPrev".$rsTiposEvento->getCampo('cod_tipo');
    $stValue          = "inCodigoPrev".$rsTiposEvento->getCampo('cod_tipo');
    $stDescricaoPrev  = "stDescricaoPrev".$rsTiposEvento->getCampo('cod_tipo');
    $stInner          = "inCampoInnerPrev".$rsTiposEvento->getCampo('cod_tipo');
    //$stJs            .= "d.getElementById('".$stInner."').innerHTML = '".$$stDescricaoPrev."';\n";
    if ( strpos(strtolower($rsTiposEvento->getCampo('descricao')),strtolower('Base')) ) {
        $stNatureza       = 'B';
    }
    if ( strpos(strtolower($rsTiposEvento->getCampo('descricao')),strtolower('Desconto')) ) {
        $stNatureza       = 'D';
    }
    if ( strpos(strtolower($rsTiposEvento->getCampo('descricao')),strtolower('Provento')) ) {
        $stNatureza       = 'P';
    }
    $$stNomeComponente = new BuscaInner;
    $$stNomeComponente->setRotulo                        ( $rsTiposEvento->getCampo('descricao')                       );
    $$stNomeComponente->setTitle                         ( "Informe o Evento indicado."                                );
    $$stNomeComponente->setId                            ( $stInner                                                    );
    $$stNomeComponente->setNull                          ( false                                                       );
    $$stNomeComponente->obCampoCod->setName              ( $stValue                                                    );
    $$stNomeComponente->obCampoCod->setValue             ( $$stValue                                                   );
    $$stNomeComponente->obCampoCod->setAlign             ( "LEFT"                                                      );
    $$stNomeComponente->obCampoCod->setMascara           ( $stMascaraEvento                                            );
    $$stNomeComponente->obCampoCod->setPreencheComZeros  ( "E"                                                         );
    $$stNomeComponente->obCampoCod->obEvento->setOnChange( "preencherEvento ('".$rsTiposEvento->getCampo('cod_tipo')."','".$stNatureza."');" );
    $$stNomeComponente->setFuncaoBusca                   ( "abrePopUp('".CAM_GRH_FOL_POPUPS."previdencia/FLManterPrevidencia.php','frm','".$stValue."','".$stInner."','','".Sessao::getId()."&stNatureza=".$stNatureza."&boEventoSistema=true','800','550')" );

    //$arComponentes[] = $$stNomeComponente;
    $rsTiposEvento->proximo();
}

$obTxtVigencia = new Data;
$obTxtVigencia->setName                              ( "dtVigencia"                                                );
$obTxtVigencia->setTitle                             ( "Informe a data da vigência."                               );
$obTxtVigencia->setNull                              ( false                                                       );
$obTxtVigencia->setRotulo                            ( "Vigência"                                                  );
$obTxtVigencia->setValue                             ( $dtVigencia                                                 );

//Faixas de Desconto para previdencia

//Define objeto TEXTBOX para armazenar o VALOR  para SALARIO INICIAL
$obTxtSalarioInicial = new Moeda;
$obTxtSalarioInicial->setRotulo     ( "*Salário Inicial" );
$obTxtSalarioInicial->setName       ( "flSalarioInicial" );
$obTxtSalarioInicial->setValue      ( $flSalarioInicial  );
$obTxtSalarioInicial->setTitle      ( "Faixa de salário inicial." );
$obTxtSalarioInicial->setNull       ( true );
$obTxtSalarioInicial->setMaxLength  ( 10 );
$obTxtSalarioInicial->setSize       ( 10 );

//Define objeto TEXTBOX para armazenar o VALOR  para SALARIO FINAL
$obTxtSalarioFinal = new Moeda;
$obTxtSalarioFinal->setRotulo     ( "*Salário Final" );
$obTxtSalarioFinal->setName       ( "flSalarioFinal" );
$obTxtSalarioFinal->setValue      ( $flSalarioFinal  );
$obTxtSalarioFinal->setTitle      ( "Faixa de salário final." );
$obTxtSalarioFinal->setNull       ( true );
$obTxtSalarioFinal->setMaxLength  ( 10   );
$obTxtSalarioFinal->setSize       ( 10   );

//Define objeto TEXTBOX para armazenar o Percentual para DESCONTO
$obTxtDesconto = new Moeda;
$obTxtDesconto->setRotulo     ( "*Percentual" );
$obTxtDesconto->setName       ( "flPercentualDesc" );
$obTxtDesconto->setValue      ( $flPercentualDesc  );
$obTxtDesconto->setTitle      ( "Percentual de desconto." );
$obTxtDesconto->setNull       ( true );
$obTxtDesconto->setMaxLength  ( 6     );
$obTxtDesconto->obEvento->setOnChange ( "validaDesconto(document.frm.flPercentualDesc.value, document.frm.flPercentualDesc, 'Percentual de Desconto');" );

$obBtnIncluir = new Button;
$obBtnIncluir->setName ( "btnIncluir" );
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->setTipo ( "button" );
$obBtnIncluir->obEvento->setOnClick ( "return IncluiFaixa();" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "button" );
$obBtnLimpar->obEvento->setOnClick ( "limpaPrevidencia();" );

$obSpnFaixas = new Span;
$obSpnFaixas->setId ( "spnFaixas" );

// mostra atributos selecionados
if ($stAcao == "incluir") {
    $obRFolhaPagamentoPrevidencia->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
} else {
    $arChaveAtributoCandidato =  array( "cod_previdencia"    => $_REQUEST["inCodPrevidencia"] );
    $obRFolhaPagamentoPrevidencia->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
    $obRFolhaPagamentoPrevidencia->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnVigenciaAntiga );

$obFormulario->addTitulo            ( "Dados de Previdência"     );
$obFormulario->addComponente        ( $obTxtDescricao            );
$obFormulario->addComponente        ( $obTxtBasePatronal         );

if ($stAcao == 'incluir') {
    $obFormulario->addComponenteComposto( $obRdbTipoOficial , $obRdbTipoPrivada  );
} else {
    $obFormulario->addComponente ( $obLblTipo   );
    $obFormulario->addComponente ( $obLblRegime );
    $obFormulario->addHidden     ( $obHdnTipo   );
    $obFormulario->addHidden     ( $obHdnRegime );

}

$obFormulario->addSpan              ( $obSpan );

if ($stAcao != 'incluir') {
    $obFormulario->addHidden            ( $obHdnCodOrganograma );
    $obFormulario->addHidden            ( $obHdnCodNorma );
    $obFormulario->addComponente        ( $obLblVinculo         );
}

if ($stAcao != "alterar") {
   $obFormulario->agrupaComponentes( array( $obRdbTipoAtivo, $obRdbTipoAposentado, $obRdbTipoPensionista ) );
}

$rsTiposEvento->setPrimeiroElemento();
while (!$rsTiposEvento->eof()) {
    $stNomeComponente = "obBscEventoPrev".$rsTiposEvento->getCampo('cod_tipo');
    $obFormulario->addComponente( $$stNomeComponente );
    $rsTiposEvento->proximo();
}

//foreach ($arComponentes as $obBscEvento) {
//    $obFormulario->addComponente( $obBscEvento );
//}

$obFormulario->addComponente( $obTxtVigencia      );
if ($stAcao != "alterar" || $rsFaixas->eof()) {
    $obFormulario->addTitulo            ( "Faixas de Desconto"  );
    $obFormulario->addComponente        ( $obTxtSalarioInicial  );
    $obFormulario->addComponente        ( $obTxtSalarioFinal    );
    $obFormulario->addComponente        ( $obTxtDesconto        );
    $obFormulario->defineBarra          ( array( $obBtnIncluir , $obBtnLimpar ) ,'','');
    $obFormulario->addSpan              ( $obSpnFaixas );
}

$obMontaAtributos->setTitulo            ( "Atributos Dinâmicos"  );
$obMontaAtributos->geraFormulario( $obFormulario );

if ($stAcao == "incluir")
    $obFormulario->OK               ();
else
    $obFormulario->Cancelar         ();

$obFormulario->show                 ();

gerarSpan1(true);
if ($stAcao == "incluir") {
    $js .= "focusIncluir();";
    SistemaLegado::executaFramePrincipal($js);
//} else {
//    SistemaLegado::executaFramePrincipal($stJs);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
