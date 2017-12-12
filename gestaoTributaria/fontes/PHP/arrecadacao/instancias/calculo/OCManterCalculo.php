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
  * Página Oculta para Calculo
  * Data de criação : 02/06/2005

    * @author Analista:
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: OCManterCalculo.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    Caso de uso: uc-05.03.05
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
if ($_REQUEST ["stCtrl"] == 'montaReferenciaParcelamentoAjax') {
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
} else {
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
}
include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"           );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"         );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPermissao.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php"         );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"              );
include_once ( CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php" );

$stCtrl = $_REQUEST['stCtrl'];
$stJs = "";

//Define o nome dos arquivos PHP
$stPrograma      = "ManterCalculos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgFormGrupo     = "FMExecutarCalculoGrupo.php";
$pgFormCredito   = "FMExecutarCalculoCredito.php";
$pgProc          = "PRManterCalculo.php";
$pgOcul          = "OCManterCalculo.php";
$pgJs            = "JSManterCalculo.js";

$obRARRGrupo = new RARRGrupo ;
$obRFuncao   = new RFuncao   ;

/*
    funções que montam os componentes
*/
function montaBuscaContribuinte($obFormulario)
{
    $obBscContribuinte = new BuscaInnerIntervalo;
    $obBscContribuinte->setRotulo           ( "Contribuinte"    );
    $obBscContribuinte->setTitle            ( "Valor inicial para código do contribuinte.");
    $obBscContribuinte->obLabelIntervalo->setValue ( "até"          );
    $obBscContribuinte->obCampoCod->setName     ("inCodContribuinteInicial"  );
    $obBscContribuinte->obCampoCod->setValue        ( $inCodContribuinteInicio  );
    $obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor(&quot;buscaContribuinte&quot;);");
    $obBscContribuinte->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteInicial','stCampo','','".Sessao::getId()."','800','450');" ));
    $obBscContribuinte->obCampoCod2->setName        ("inCodContribuinteFinal"  );
    $obBscContribuinte->obCampoCod2->setValue       ( $inCodContribuinteFinal  );
    $obBscContribuinte->obCampoCod2->obEvento->setOnChange("buscaValor(&quot;buscaContribuinte&quot;);");
    $obBscContribuinte->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteFinal','stCampo','','".Sessao::getId()."','800','450');" ));

    $obHdnCampo2 =  new Hidden;
    $obHdnCampo2->setName   ( "stCampo" );
    $obHdnCampo2->setId     ( "stCampo" );

    $obFormulario->addComponente ( $obBscContribuinte );
    $obFormulario->addHidden     ( $obHdnCampo2 );

    return true;

}

function montaBuscaContribuinteIndividual()
{
    $obBscContribuinteIndividual = new BuscaInner;
    $obBscContribuinteIndividual->setNull           ( true );
    $obBscContribuinteIndividual->setId  ( "stContribuinte"          );
    $obBscContribuinteIndividual->setRotulo         ( "*Contribuinte Individual"    );
    $obBscContribuinteIndividual->setTitle          ( "Codigo do contribuinte.");

    $obBscContribuinteIndividual->obCampoCod->setName       ("inCodContribuinteIndividual"  );
    $obBscContribuinteIndividual->obCampoCod->obEvento->setOnChange("buscaValor(&quot;buscaContribuinte3&quot;);");
    $obBscContribuinteIndividual->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteIndividual','stContribuinte','','".Sessao::getId()."','800','450');") );

    return $obBscContribuinteIndividual;
}

function montaBuscaInscricaoImobiliaria($obFormulario)
{
    $obBscInscricaoImobiliaria = new BuscaInnerIntervalo;
    $obBscInscricaoImobiliaria->setRotulo           ( "Inscrição Imobiliária"   );
    $obBscInscricaoImobiliaria->setTitle            ( "Intervalo de valores para inscrição imobiliária.");
    $obBscInscricaoImobiliaria->obLabelIntervalo->setValue ( "até"          );
    $obBscInscricaoImobiliaria->obCampoCod->setName     ("inNumInscricaoImobiliariaInicial"  );
    $obBscInscricaoImobiliaria->obCampoCod->setValue        ( $inNumInscricaoImobiliariaInicial  );
    $obBscInscricaoImobiliaria->obCampoCod->obEvento->setOnChange("buscaValor(&quot;procuraImovel&quot;);");
    $obBscInscricaoImobiliaria->setFuncaoBusca      ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaInicial','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));
    $obBscInscricaoImobiliaria->obCampoCod2->setName        ( "inNumInscricaoImobiliariaFinal" );
    $obBscInscricaoImobiliaria->obCampoCod2->setValue       ( $inNumInscricaoImobiliariaFinal  );
    $obBscInscricaoImobiliaria->obCampoCod2->obEvento->setOnChange("buscaValor(&quot;procuraImovel&quot;);");
    $obBscInscricaoImobiliaria->setFuncaoBusca2     ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaFinal','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));

    $obHdnCampo2 =  new Hidden;
    $obHdnCampo2->setName   ( "stNomInscricaoImobiliaria" );
    $obHdnCampo2->setId     ( "stNomInscricaoImobiliaria" );

    $obFormulario->addComponente ( $obBscInscricaoImobiliaria );
    $obFormulario->addHidden     ( $obHdnCampo2 );

    return true;

}

function montaBuscaInscricaoImobiliariaIndividual()
{
    $obBscInscricaoMunicipal = new BuscaInner;
    $obBscInscricaoMunicipal->setId  ( "stInscricaoImobiliaria"          );
    $obBscInscricaoMunicipal->setRotulo                ( "Inscrição Imobiliária"      );
    $obBscInscricaoMunicipal->setNull                  ( false                        );
    $obBscInscricaoMunicipal->obCampoCod->obEvento->setOnChange("buscaValor(&#39;procuraImovel2&#39;);");
    $obBscInscricaoMunicipal->obCampoCod->setName      ( "inInscricaoImobiliaria"     );
    $obBscInscricaoMunicipal->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)  );
    $obBscInscricaoMunicipal->obCampoCod->setMascara   ( $stMascaraInscricao          );
    $obBscInscricaoMunicipal->obCampoCod->setInteiro   ( false                        );
    $obBscInscricaoMunicipal->setFuncaoBusca( "abrePopUp( &#39;".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php&#39;, &#39;frm&#39;, &#39;inInscricaoImobiliaria&#39;, &#39;stInscricaoImobiliaria&#39;, &#39;todos&#39;, &#39;".Sessao::getId()."&#39;, &#39;800&#39;, &#39;550&#39; );" );

    return $obBscInscricaoMunicipal;

}

function montaBuscaInscricaoEconomica($obFormulario)
{
    $obBscInscricaoEconomica = new BuscaInnerIntervalo;
    $obBscInscricaoEconomica->setRotulo         ( "*Inscrição Econômica"    );
    $obBscInscricaoEconomica->setTitle          ( "Intervalo de valores para inscrição econômica.");
    $obBscInscricaoEconomica->obLabelIntervalo->setValue ( "até"            );
    $obBscInscricaoEconomica->obCampoCod->setName       ("inNumInscricaoEconomicaInicial"  );
    $obBscInscricaoEconomica->obCampoCod->setValue      ( $inNumInscricaoEconomicaInicial  );
    $obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange("buscaValor(&quot;buscaIE&quot;);");
    $obBscInscricaoEconomica->setFuncaoBusca("abrePopUp(&quot;".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php&quot;,&quot;frm&quot;,&quot;inNumInscricaoEconomicaInicial&quot;,&quot;stCampo&quot;,&quot;todos&quot;,&quot;".Sessao::getId()."&quot;,&quot;800&quot;,&quot;550&quot;);");
    $obBscInscricaoEconomica->obCampoCod2->setName          ( "inNumInscricaoEconomicaFinal" );
    $obBscInscricaoEconomica->obCampoCod2->setValue         ( $inNumInscricaoEconomicaFinal  );
    $obBscInscricaoEconomica->obCampoCod2->obEvento->setOnChange("buscaValor(&quot;buscaIE&quot;);");
    $obBscInscricaoEconomica->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inNumInscricaoEconomicaFinal','stCampo','todos','".Sessao::getId()."','800','550');"));

    $obHdnCampo2 =  new Hidden;
    $obHdnCampo2->setName   ( "stCampo" );
    $obHdnCampo2->setId     ( "stCampo" );

    $obFormulario->addComponente ( $obBscInscricaoEconomica );
    $obFormulario->addHidden     ( $obHdnCampo2 );

    return true;

}
function montaBuscaInscricaoEconomicaIndividual()
{
    $obBscInscricaoEconomica = new BuscaInner;
    $obBscInscricaoEconomica->setId                   ( "stInscricaoEconomica"  );
    $obBscInscricaoEconomica->setRotulo               ( "Inscrição Econômica"   );
    $obBscInscricaoEconomica->setTitle                ( "Pessoa física ou jurídica cadastrada como inscrição econômica.");
    $obBscInscricaoEconomica->obCampoCod->setName     ( "inNumInscricaoEconomica"  );
    $obBscInscricaoEconomica->setNull                 ( false                   );
    $obBscInscricaoEconomica->obCampoCod->setMaxLength( strlen($stMascaraInscricaoEconomico ));
    $obBscInscricaoEconomica->obCampoCod->setMascara  ( $stMascaraInscricao         );
    $obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange("buscaValor(&quot;buscaIE3&quot;);");
    $obBscInscricaoEconomica->setFuncaoBusca("abrePopUp(&quot;".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php&quot;,&quot;frm&quot;,&quot;inNumInscricaoEconomica&quot;,&quot;stInscricaoEconomica&quot;,&quot;todos&quot;,&quot;".Sessao::getId()."&quot;,&quot;800&quot;,&quot;550&quot;);");

    return $obBscInscricaoEconomica;
}

function montaBuscaLocalizacao()
{
    /* consulta mascara*/
    $obRCIMNivel = new RCIMNivel;
    $obRCIMNivel->mascaraNivelVigenciaAtual($stMascara);
    $obTxtLocalizacaoInicial = new Textbox;
    $obTxtLocalizacaoInicial->setName   ( "inCodLocalizacaoInicial" );
    $obTxtLocalizacaoInicial->setTitle  ( "Localização."            );
    $obTxtLocalizacaoInicial->setRotulo ( "Localização"             );
    $obTxtLocalizacaoInicial->setMaxLength( strlen($stMascara)      );
    $obTxtLocalizacaoInicial->setMinLength( strlen($stMascara)      );
    $obTxtLocalizacaoInicial->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");
    $obTxtLocalizacaoInicial->setValue  ( $inCodLocalizacaoInicial  );

    $obLabelIntervalo = new Label;
    $obLabelIntervalo->setValue ( "até" );

    $obTxtLocalizacaoFinal = new Textbox;
    $obTxtLocalizacaoFinal->setName     ( "inCodLocalizacaoFinal"   );
    $obTxtLocalizacaoFinal->setTitle    ( "Localização."            );
    $obTxtLocalizacaoFinal->setRotulo   ( "Localização"             );
    $obTxtLocalizacaoFinal->setMaxLength( strlen($stMascara)        );
    $obTxtLocalizacaoFinal->setMinLength( strlen($stMascara)        );
    $obTxtLocalizacaoFinal->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");
    $obTxtLocalizacaoFinal->setValue    ( $inCodLocalizacaoFinal    );

    return array( $obTxtLocalizacaoInicial, $obLabelIntervalo, $obTxtLocalizacaoFinal);
}

function montaBuscaAtividade()
{
    $obLabelIntervaloAtvididade = new Label;
    $obLabelIntervaloAtvididade->setValue ( "até" );

    $obTxtAtividadeInicial = new Textbox;
    $obTxtAtividadeInicial->setName     ( "inCodAtividadeInicial"   );
    $obTxtAtividadeInicial->setTitle    ( "Atividade."              );
    $obTxtAtividadeInicial->setRotulo   ( "Atividade"               );
    $obTxtAtividadeInicial->setValue    ( $inCodAtividadeInicial    );

    $obTxtAtividadeFinal = new Textbox;
    $obTxtAtividadeFinal->setName   ( "inCodAtividadeFinal"     );
    $obTxtAtividadeFinal->setTitle  ( "Atividade."              );
    $obTxtAtividadeFinal->setRotulo ( "Atividade"               );
    $obTxtAtividadeFinal->setValue  ( $inCodAtividadeFinal  );

    return array( $obTxtAtividadeInicial, $obLabelIntervaloAtvididade, $obTxtAtividadeFinal);

}

function montaParcelamentoBase()
{
    if (!$_REQUEST['chkUsaCalendario'] || $_REQUEST['stTipoCalculo'] == 'individual') {

        //declara componentes
        $obRdoParcelaUnica = new Radio;
        $obRdoParcelaUnica->setName    ( "stTipoParcela" );
        $obRdoParcelaUnica->setTitle   ( "Opção de utilizar ou não uma cota única para o lançamento." );
        $obRdoParcelaUnica->setRotulo  ( "*Tipo de Parcela" );
        $obRdoParcelaUnica->setLabel   ( "Cotas Únicas" );
        $obRdoParcelaUnica->setValue   ( "Única" );
        $obRdoParcelaUnica->setChecked ( true );

        $obRdoParcelaNormal = new Radio;
        $obRdoParcelaNormal->setName    ( "stTipoParcela" );
        $obRdoParcelaNormal->setRotulo  ( "*la"  );
        $obRdoParcelaNormal->setLabel   ( "Parcelas Normais" );
        $obRdoParcelaNormal->setValue   ( "Normal" );

        $obTxtDesconto = new Numerico;
        $obTxtDesconto->setName      ( "flDesconto"   );
        $obTxtDesconto->setValue     ( 0.00    );
        $obTxtDesconto->setInteiro   ( true );
        $obTxtDesconto->setTitle    ( "Desconto a ser aplicado no parcelamento." );
        $obTxtDesconto->setRotulo    ( "Desconto" );
        $obTxtDesconto->setDecimais  ( 2 );
        $obTxtDesconto->setMaxValue  ( 99999.99 );
        $obTxtDesconto->setNull      ( true );
        $obTxtDesconto->setNegativo  ( false );
        $obTxtDesconto->setNaoZero   ( true );
        $obTxtDesconto->setSize      ( 10 );
        $obTxtDesconto->setMaxLength ( 10 );

        $dtdiaHOJE = date ("d/m/Y");

        $obDtVencimento = new Data;
        $obDtVencimento->setName             ( "data_vencimento"                    );
        $obDtVencimento->setValue             ( $dtdiaHOJE                        );
        $obDtVencimento->setRotulo            ( "Data do Primeiro Vencimento"             );
        $obDtVencimento->setTitle               ( "Data do vencimento da primeira parcela." );
        $obDtVencimento->setMaxLength     ( 20                                );
        $obDtVencimento->setSize                ( 10                                );
        $obDtVencimento->setNull                ( false                             );
        $obDtVencimento->obEvento->setOnChange ( "validaData1500( this );"         );

        $obRdoDescontoPercentual = new Radio;
        $obRdoDescontoPercentual->setTitle   ( "Forma para definir o desconto." );
        $obRdoDescontoPercentual->setName    ( "stTipoDesconto"   );
        $obRdoDescontoPercentual->setRotulo   ( "Forma de Desconto"    );
        $obRdoDescontoPercentual->setLabel    ( "Percentual"           );
        $obRdoDescontoPercentual->setValue    ( 'Percentual'                    );
        $obRdoDescontoPercentual->setChecked ( true );

        $obRdoDescontoAbsoluto = new Radio;
        $obRdoDescontoAbsoluto->setName    ( "stTipoDesconto" );
        $obRdoDescontoAbsoluto->setRotulo  ( "Forma de Desconto"  );
        $obRdoDescontoAbsoluto->setLabel   ( "Valor Absoluto"      );
        $obRdoDescontoAbsoluto->setValue   ( "Absoluto"         );

        $arParcelas = array();
        for ($inX=0; $inX<12; $inX++) {
            $arParcelas[$inX]["num_parcela"] = $inX + 1;
        }

        $rsParcelas = new RecordSet;
        $rsParcelas->preenche( $arParcelas);

        $obTxtQtdParcela = new TextBox;
        $obTxtQtdParcela->setName      ( "cmbQtdParcelas"   );
        $obTxtQtdParcela->setValue     ( 1 );
        $obTxtQtdParcela->setInteiro   ( true );
        $obTxtQtdParcela->setTitle     ( "Quantidade de parcelas a serem geradas." );
        $obTxtQtdParcela->setRotulo    ( "Quantidade de Parcelas" );
        $obTxtQtdParcela->setNull      ( false );
        $obTxtQtdParcela->obEvento->setOnKeyPress ("mascaraDinamico('9', this, event);");

        //BOTOES DE INCLUSAO E LIMPAR
        $obBtnIncluirParcela = new Button;
        $obBtnIncluirParcela->setName   ( "stIncluirParcela" );
        $obBtnIncluirParcela->setValue   ( "Incluir" );
        $obBtnIncluirParcela->obEvento->setOnClick( "incluirParcela();" );

        $obBtnLimparParcela = new Button;
        $obBtnLimparParcela->setName               ( "btnLimparParcela"       );
        $obBtnLimparParcela->setValue              ( "Limpar"              );
        $obBtnLimparParcela->setTipo               ( "button"              );
        $obBtnLimparParcela->obEvento->setOnClick  ( "limparParcela();"    );

        $obFormulario = new Formulario;

        $obFormulario->addTitulo             ( "Dados para Parcelas"   );

        $obFormulario->agrupaComponentes   ( array( $obRdoParcelaUnica, $obRdoParcelaNormal ) );
        $obFormulario->addComponente ( $obTxtDesconto   );
        $obFormulario->agrupaComponentes   ( array( $obRdoDescontoPercentual, $obRdoDescontoAbsoluto ) );
        $obFormulario->addComponente ( $obDtVencimento );
        $obFormulario->addComponente ( $obTxtQtdParcela );
        $obFormulario->agrupaComponentes   ( array( $obBtnIncluirParcela, $obBtnLimparParcela ) );

        $obFormulario->montaInnerHTML();
        $js .= "d.getElementById('spnModoParcelamento').innerHTML = '". $obFormulario->getHTML(). "';\n";
    } else {
        $js .= "d.getElementById('spnModoParcelamento').innerHTML = '". null . "';\n";
    }

    return $js;
}

function montaParcelamento()
{
    $js = montaParcelamentoBase();
    sistemaLegado::executaFrameOculto($js);
}

function montaParcelas($arrayParcelas)
{
        $obTxtV = new Data;
        $obTxtV->setName ('Ven');
        $obTxtV->setValue ( '[data_vencimento]' );
        $obTxtV->obEvento->setOnChange ( "buscaValor('alteraData')" );

        $rsListaParcelas = new RecordSet;
        $rsListaParcelas->preenche ( $arrayParcelas );
        $rsListaParcelas->ordena ( 'dtVencimentoOrdenacao' );

        $obLista = new Lista;
        $obLista->setRecordSet            (   $rsListaParcelas   );
        $obLista->setTitulo                    ( "Lista de Parcelas"  );
        $obLista->setMostraPaginacao           ( false                  );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho ();
        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Tipo da Parcela" );
        $obLista->ultimoCabecalho->setWidth         ( 20 );
        $obLista->commitCabecalho ();
        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Vencimento" );
        $obLista->ultimoCabecalho->setWidth         ( 10 );
        $obLista->commitCabecalho ();
        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Desconto"            );
        $obLista->ultimoCabecalho->setWidth    ( 20                      );
        $obLista->commitCabecalho ();
        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Forma de Desconto"            );
        $obLista->ultimoCabecalho->setWidth    ( 20                      );
        $obLista->commitCabecalho ();
        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho ();

        $obLista->addDado         ();
        $obLista->ultimoDado->setCampo         ( "stTipoParcela" );
        $obLista->ultimoDado->setAlinhamento ("CENTRO");
        $obLista->commitDado    ();

        $obLista->addDadoComponente                 ( $obTxtV        );
        $obLista->ultimoDado->setAlinhamento     ( 'CENTRO'                    );
        $obLista->ultimoDado->setCampo              ( "data_vencimento"             );
        $obLista->commitDadoComponente           (                                   );

        $obLista->addDado         ();
        $obLista->ultimoDado->setCampo         ( "flDesconto" );
        $obLista->ultimoDado->setAlinhamento ("DIREITA");
        $obLista->commitDado    ();
        $obLista->addDado         ();
        $obLista->ultimoDado->setCampo         ( "stTipoDesconto" );
        $obLista->ultimoDado->setAlinhamento ("CENTRO");
        $obLista->commitDado    ();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->addCampo( "1","inIndice" );
        $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirParcela');" );
        $obLista->commitAcao();

        $obLista->montaHTML                    (                        );
        $stHTML = $obLista->getHtml       (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );

    Sessao::write( 'parcelas', $arrayParcelas );

    $js = "d.getElementById('spnParcelas').innerHTML = '".$stHTML ."';\n";
    $js .= "f.data_vencimento.value = '';\n";
    $js .= "f.flDesconto.value = '0.00';\n";
    sistemaLegado::executaFrameOculto($js);
}

function OrdenaParcelas($arrayParcelasTMP)
{
    $rsParcelas = new RecordSet;
    $rsParcelas->preenche ( $arrayParcelasTMP );
    $rsParcelas->ordena ('dtVencimentoOrdenacao');

    $inContParcelas = $rsParcelas->getNumLinhas();
    $arrayParcelas= array();
    $cont = 0;
    $contParcelaNormal = 1;
    while ( !$rsParcelas->eof() ) {

        $arrayParcelas[$cont]['inIndice'] = $cont;
        $tipoParcela =  $rsParcelas->getCampo('stTipoParcela');
        if ($tipoParcela == "Única") {
            $arrayParcelas[$cont]['stTipoParcela'] = $rsParcelas->getCampo('stTipoParcela');
        } else {
            $arrayParcelas[$cont]['stTipoParcela'] = $contParcelaNormal;
            $contParcelaNormal++;
        }
        $flValor = str_replace (',','.', str_replace ( '.', '', $rsParcelas->getCampo('flDesconto') ));

        $arrayParcelas[$cont]['cod_parcela']            = null;
        $arrayParcelas[$cont]['percentual']             = null;
        $arrayParcelas[$cont]['data_vencimento']        = $rsParcelas->getCampo('data_vencimento');
        $arrayParcelas[$cont]['flDesconto']             = $rsParcelas->getCampo('flDesconto');
        $arrayParcelas[$cont]['valor']                  = $flValor;
        $arrayParcelas[$cont]['stTipoDesconto']         = $rsParcelas->getCampo('stTipoDesconto');
        $arrayParcelas[$cont]['dtVencimentoOrdenacao']  = $rsParcelas->getCampo('dtVencimentoOrdenacao');

        $cont++;
        $rsParcelas->proximo();

    }

    return $arrayParcelas;
}

function montaListaCreditos($inCodGrupoCredito, $inExercicio)
{
    include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"  );
    $obRARRGrupo = new RARRGrupo;

        $obRARRGrupo->setCodGrupo( $inCodGrupoCredito );
        $obRARRGrupo->setExercicio ( $inExercicio );
        $obRARRGrupo->listarCreditos($rsCreditos);

        // numero de creditos
        $inNumCreditos = $rsCreditos->getNumLinhas();
        // array
        $arCreditosAgrupados = array();
        $arCreditos = array();
        for ($inCount=0;$inNumCreditos > $inCount;$inCount++) {
            $arTmp["codcredito" ] = $rsCreditos->arElementos[$inCount]["cod_credito"];
            $arTmp["descricao"  ] = $rsCreditos->arElementos[$inCount]["descricao_credito"];
            $arTmp["codgenero"  ] = $rsCreditos->arElementos[$inCount]["cod_genero"];
            $arTmp["codespecie" ] = $rsCreditos->arElementos[$inCount]["cod_especie"];
            $arTmp["codnatureza"] = $rsCreditos->arElementos[$inCount]["cod_natureza"];
            $arTmp["inLinha"    ] = $inCount;
            $arCreditos[] = $arTmp;
        }

        Sessao::write( 'creditos', $arCreditos );
        $rsListaCreditos = new RecordSet;
        $rsListaCreditos->preenche( $arCreditos );

        $stAcao = $_REQUEST['stAcao'];
        $rsListaCreditos->setPrimeiroElemento();
            if ( !$rsListaCreditos->eof() ) {
                $obLista = new Lista;
                $obLista->setMostraPaginacao    ( false                      );
                $obLista->setRecordSet( $rsListaCreditos );
                $obLista->setTitulo ("Lista de Créditos");
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("&nbsp;");
                $obLista->ultimoCabecalho->setWidth( 2 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("Código");
                $obLista->ultimoCabecalho->setWidth( 20 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo( "Descrição" );
                $obLista->ultimoCabecalho->setWidth( 60 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo( "Valor (R$)" );
                $obLista->ultimoCabecalho->setWidth( 20 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("&nbsp;");
                $obLista->ultimoCabecalho->setWidth( 2 );
                $obLista->commitCabecalho();

                $obLista->addDado();
                $obLista->ultimoDado->setCampo( "[codcredito].[codgenero].[codespecie].[codnatureza]" );
                $obLista->commitDado();
                $obLista->addDado();
                $obLista->ultimoDado->setCampo( "descricao" );
                $obLista->commitDado();

                $obTxtV = new Numerico;
                $obTxtV->setName ('Valor');
                $obTxtV->setDecimais ( 2 );
                $obTxtV->setMaxValue  ( 9999999.99 );
                $obTxtV->setNull      ( false );
                $obTxtV->setNegativo  ( false );
                $obTxtV->setNaoZero   ( false );
                $obTxtV->setSize    ( 14 );
                $obTxtV->setMaxLength ( 14 );
                $obTxtV->obEvento->setOnChange ("buscaValor('somaValoresCreditos')");

                $obLista->addDadoComponente                 ( $obTxtV        );
                $obLista->ultimoDado->setAlinhamento     ( 'CENTRO'                    );
                $obLista->ultimoDado->setCampo              ( "Valor"             );
                $obLista->commitDadoComponente           (                                   );

                $obLista->montaHTML();
                $stHTML = $obLista->getHTML();
                $stHTML = str_replace("\n","",$stHTML);
                $stHTML = str_replace("  ","",$stHTML);
                $stHTML = str_replace("'","\\'",$stHTML);
            } else {
                $stHTML = "";
            }
            $stJs = "d.getElementById('spnCreditos').innerHTML = '".$stHTML."';";
            somaValoresCreditos(1);

            return $stJs;
}
function somaValoresCreditos($apaga)
{
    $contParcelas = 1;
    $soma = 0.00;
    if ($apaga != 1) {
        foreach ($_REQUEST as $key => $valor) {
            if ($key == "Valor_$contParcelas") {
                $soma +=  str_replace(',', '.', str_replace ( '.', '', $valor) );
                $contParcelas++;
            }
        }
    }

    $soma = number_format ( $soma, 2 );
    $soma = str_replace('.', 'p', $soma);
    $soma = str_replace(',', '.', $soma);
    $soma = str_replace('p', ',', $soma);

    $js  = "d.getElementById('obValorTotal').innerHTML = '".$soma."';\n";
    $js .= 'f.obHdnValorTotal.value = "'.$soma.'";';
    sistemaLegado::executaFrameOculto($js);

}

/*
        FIM DAS FUNÇÕES
*/
switch ($_REQUEST ["stCtrl"]) {
    case "atualizarCalculo":
        $stJs = "f.submit();";
        break;

    case "BuscaCodCredito":
        $obRegra = new RARRGrupo;
        if ($_REQUEST["inCodGrupo"]) {
            $arDados = explode("/", $_REQUEST["inCodGrupo"]);
            $stMascara = "";
            $obRARRGrupo = new RARRGrupo;
            $obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascara );
            $stMascara .= "/9999";
            if ( strlen($_REQUEST["inCodGrupo"]) < strlen($stMascara) ) {
                $stJs = 'f.inCodGrupo.value= "";';
                $stJs .= 'f.inCodGrupo.focus();';
                $stJs .= 'd.getElementById("stGrupo").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Código Grupo/Ano exercício incompleto. (".$_REQUEST["inCodGrupo"].")', 'form','erro','".Sessao::getId()."');";
                if ($_REQUEST["FormLancamentoManual"] == "GrupoCrédito") {
                   $stJs .= "d.getElementById('spnCreditos').innerHTML = '&nbsp;';";
                }
            } else {
                $obRARRPermissao = new RARRPermissao;
                $obRARRPermissao->obRARRGrupo->setCodGrupo( $arDados[0] );
                $obRARRPermissao->obRARRGrupo->setExercicio( $arDados[1] );
                $obErro = $obRARRPermissao->obRARRGrupo->consultarGrupo();
                if ( !$obRARRPermissao->obRARRGrupo->getCodGrupo() ) {
                    $stErro = "@Grupo de Créditos informado não existe.(".$_REQUEST["inCodGrupo"].")";
                    $stJs .= "f.inCodGrupo.value ='';\n";
                    $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
                    $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
                    if ($_REQUEST["FormLancamentoManual"] == "GrupoCrédito") {
                        $stJs .= "d.getElementById('spnCreditos').innerHTML = '&nbsp;';\n ";
                    }
                    $stJs .= "f.inCodGrupo.focus();\n";

                } else {
                    $obRARRPermissao->obRARRGrupo->setCodGrupo( $arDados[0] );
                    $obRARRPermissao->obRARRGrupo->setExercicio( $arDados[1] );
                    $obRARRPermissao->obRCGM->setNumCGM( Sessao::read('numCgm'));
                    $obErro = $obRARRPermissao->consultarPermissao();
                    if ($obRARRPermissao->getPermitido == "false") {
                        $stErro = "Usuário não possui permissão para efetuar cálculos do grupo de crédito ".$obRARRPermissao->obRARRGrupo->getDescricao()."!";

                        $stJs .= "f.inCodGrupo.value ='';\n";
                        $stJs .= "f.inCodGrupo.focus();\n";
                        $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
                        $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";

                    } else {
                        $inCodGrupo     = $obRARRPermissao->obRARRGrupo->getCodGrupo () ;
                        $stDescricao    = $obRARRPermissao->obRARRGrupo->getDescricao() ;
                        $inCodModulo    = $obRARRPermissao->obRARRGrupo->getCodModulo() ;

                        $stJs = "d.getElementById('stGrupo').innerHTML = '".$stDescricao."';\n";
                        if ($_REQUEST["FormLancamentoManual"] == "GrupoCrédito") {
                            $stJs .= montaListaCreditos ( $arDados[0], $arDados[1] );
                        } else {
                            $stJs .= "d.getElementById('spnFiltros').innerHTML = '';\n";
                            $stJs .= "d.getElementById('stTipoCalculo').checked = false;\n";
                        }

                        $stJs .= "f.inCodModulo.value = '".$inCodModulo."';\n";
                        $stJs .= "f.inCodGrupo.focus();\n";
                    }
                }
            }
        } else {
            $stErro = "@Grupo de Créditos informado não existe.(".$_REQUEST["inCodGrupo"].")";
            $stJs .= "f.inCodGrupo.value ='';\n";
            $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
            if ($_REQUEST["FormLancamentoManual"] == "GrupoCrédito") {
                $stJs .= "d.getElementById('spnCreditos').innerHTML = '&nbsp;';\n ";
            }
            $stJs .= "f.inCodGrupo.focus();\n";
        }
        break;

    case "alteraData":
        $arParcelas = Sessao::read( 'parcelas' );
        $inTotalParcelas = count( $arParcelas );
        $js  = "";
        for ($inX=0; $inX<$inTotalParcelas; $inX++) {

            $stString = "Ven_".($inX + 1);
            $stData = $_REQUEST[$stString];

            if ($stData == '') {
                $stData = $arParcelas[$inX][data_vencimento];
            }

            $arParcelas[$inX]["data_vencimento"] = $stData;

            Sessao::write( 'parcelas', $arParcelas );
            $js .= 'f.'.$stString.'.value = "'.$stData.'";';
            sistemaLegado::executaFrameOculto($js);
        }
        break;

    case "buscaContribuinteIndividual":

    if ($_REQUEST["inCodContribuinteIndividual"] != "" ||  !empty($_REQUEST["inCodContribuinteIndividual"] )) {
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM ( $_REQUEST["inCodContribuinteIndividual"] );
        $stWhere = " numcgm = ".$obRCGM->getNumCGM();
        $null = "&nbsp;";
        $obRCGM->consultar($rsCgm, $stWhere);
        $inNumLinhas = $rsCgm->getNumLinhas();
        if ($inNumLinhas <= 0) {
            $stJs .= 'f.inCodContribuinteIndividual.value = "";';
            $stJs .= 'f.inCodContribuinteIndividual.focus();';
            $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$null.'";';
            $stJs .= "alertaAviso('@CGM não encontrado. (".$_REQUEST["inCodContribuinteIndividual"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stNomCgm = $rsCgm->getCampo("nom_cgm");
            $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNomCgm.'";';
        }
    }
    break;
    case "buscaCredito":
        $stAcao = $request->get('stAcao');
        $obRARRParametroCalculo = new RARRParametroCalculo;
        // pegar mascara de credito
        $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->consultarMascaraCredito();
        $stMascaraCredito = $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->getMascaraCredito();
        if (strlen($_REQUEST["inCodCredito"]) < strlen($stMascaraCredito) ) {
            if ($_REQUEST["inCodCredito"] == "") {
               $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
            } else {
               $stJs .= "f.inCodCredito.value ='';\n";
               $stJs .= "f.inCodCredito.focus();\n";
               $stJs .= "alertaAviso('@Crédito informado inválido. (".$_REQUEST["inCodCredito"].")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $arValores = explode('.',$_REQUEST["inCodCredito"]);
            // array [0]> cod_credito [1]> cod_especie [2]> cod_genero [3]> cod_natureza
            $obRARRGrupo = new RARRGrupo;
            $obRARRGrupo->addCredito();
            $obRARRGrupo->roUltimoCredito->setCodCredito ($inCodCreditoComposto[0]  );
            $obRARRGrupo->roUltimoCredito->setCodNatureza($inCodCreditoComposto[3]  );
            $obRARRGrupo->roUltimoCredito->setCodEspecie ($inCodCreditoComposto[1]  );
            $obRARRGrupo->roUltimoCredito->setCodGenero  ($inCodCreditoComposto[2]  );
            $obRARRGrupo->roUltimoCredito->listarCreditos($rsGrupos);

            $stErro = false;
            if ( !$rsGrupos->eof() ) {
                $obRARRGrupo->setCodGrupo($rsGrupos->getCampo("cod_grupo"));
                $obRARRGrupo->consultarGrupo();
                $stMsg = $rsGrupos->getCampo('cod_grupo')."-".$obRARRGrupo->getDescricao()."/".$obRARRGrupo->getExercicio();
                $stErro = "Crédito selecionado faz parte do Grupo de Crédito(".$stMsg."), não é possível executar calculo!";
            }
            if (!$stErro) {
                $obRARRGrupo->roUltimoCredito->consultarCredito();

                $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
                $stDescricao = $obRARRGrupo->obRMONCredito->getDescricao() ;

                if ( !empty($stDescricao) ) {
                    $stJs .= "d.getElementById('stCredito').innerHTML = '".$stDescricao."';\n";
                    if ( $stAcao == 'incluir')
                        $stJs .= "d.getElementById('stTipoCalculo').checked = true;\n";
                } else {
                    $stJs .= "f.inCodCredito.value ='';\n";
                    $stJs .= "f.inCodCredito.focus();\n";
                    $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
                    $stJs .= "alertaAviso('@Crédito informado nao existe. (".$_REQUEST["inCodCredito"].")','form','erro','".Sessao::getId()."');";
                }
            } else {
                $stJs .= "f.inCodCredito.value ='';\n";
                $stJs .= "f.inCodCredito.focus();\n";
                $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');";
            }
        }
    break;
    case "buscaFuncao":
        $arCodFuncao        = explode('.',$_REQUEST["inCodFuncao"]);
        $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
        $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
        $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
        $obRFuncao->consultar();

        $inCodFuncao = $obRFuncao->getCodFuncao () ;
        $stDescricao = "&nbsp;";
        $stDescricao = $obRFuncao->getComentario() ;
        if ( $obRFuncao->getNomeFuncao() ) {
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$obRFuncao->getNomeFuncao()."';\n";
        } else {
            $stJs .= "f.inCodFuncao.value ='';\n";
            $stJs .= "f.inCodFuncao.focus();\n";
            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Fórmula não cadastrada! (".$_REQUEST["inCodFuncao"].")','form','erro','".Sessao::getId()."');";
        }
        break;

    case "buscaProcesso":
        include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php" );
        $obRProcesso  = new RProcesso;
        if ($_POST['inProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();
            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inProcesso.value = "";';
                $stJs .= 'f.inProcesso.focus();';
                $stJs .= "alertaAviso('@Processo não encontrado. (".$_POST["inProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "buscaGrupo":
        $obRARRPermissao = new RARRPermissao;
        $obRARRPermissao->obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
        $obErro = $obRARRPermissao->obRARRGrupo->consultarGrupo();
        if ( !$obRARRPermissao->obRARRGrupo->getCodGrupo() ) {
            $stErro = "@Grupo de Créditos informado não existe.(".$_REQUEST["inCodGrupo"].")";
        } else {
            $obRARRPermissao->obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
            $obRARRPermissao->obRCGM->setNumCGM( Sessao::read('numCgm'));
            $obErro = $obRARRPermissao->consultarPermissao();
            if ($obRARRPermissao->getPermitido == "false") {
                $stErro = "Usuário não possui permissão para efetuar cálculos do grupo de crédito ".$obRARRPermissao->obRARRGrupo->getDescricao()."!";
            } else {
                $inCodGrupo     = $obRARRPermissao->obRARRGrupo->getCodGrupo () ;
                $stDescricao    = $obRARRPermissao->obRARRGrupo->getDescricao() ;
                $inCodModulo    = $obRARRPermissao->obRARRGrupo->getCodModulo() ;
            }
        }
        if (!$stErro) {
           $stJs .= "d.getElementById('stGrupo').innerHTML = '".$stDescricao."';\n";
           $stJs .= "d.getElementById('spnFiltros').innerHTML = '';\n";
           $stJs .= "f.inCodModulo.value = '".$inCodModulo."';\n";
           $stJs .= "d.getElementById('stTipoCalculo').checked = false;\n";
           $stJs .= "f.inCodGrupo.focus();\n";
        } else {
           $stJs .= "f.inCodGrupo.value ='';\n";
           $stJs .= "f.inCodGrupo.focus();\n";
           $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
           $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
        }
    break;
    case "mudaFiltraPor":
        $stTipoCalculo  = $_REQUEST["stTipoCalculo"];
        $stFiltraPor    = $_REQUEST["stFiltraPor"];

        $obFormulario = new Formulario;
        $obFormulario->addTitulo ("Filtros para Cálculo");

        switch ($stTipoCalculo) {
        case "parcial":
            $boMonta = false;
            if ($stFiltraPor == 'cgm') {
                montaBuscaContribuinte( $obFormulario );
                $boMonta = true;
            } elseif ($stFiltraPor == 'imobiliaria') {
                montaBuscaInscricaoImobiliaria( $obFormulario );
                $boMonta = true;
            } elseif ($stFiltraPor == 'economica') {
                montaBuscaInscricaoEconomica( $obFormulario );
                $boMonta = true;
            }
            if ($boMonta) {
                $obChcBoxUtilizaCalendario = new CheckBox;
                $obChcBoxUtilizaCalendario->setName ('chkUsaCalendario');
                $obChcBoxUtilizaCalendario->setRotulo ('Vencimento das Parcelas');
                $obChcBoxUtilizaCalendario->setLabel ( "Utilizar Calendário Fiscal");
                $obChcBoxUtilizaCalendario->obEvento->setOnChange("buscaValor(&#39;montaParcelamento&#39;);");
                $obChcBoxUtilizaCalendario->setChecked (true);
                $obFormulario->addComponente( $obChcBoxUtilizaCalendario );

                $obFormulario->montaInnerHtml();
            }

        break;
        case "individual":
            if ( $stFiltraPor == 'cgm' )
                $obFormulario->addComponente( montaBuscaContribuinteIndividual() );
            elseif ($stFiltraPor == 'imobiliaria') {
                $obFormulario->addComponente( montaBuscaInscricaoImobiliariaIndividual() );
            }elseif ( $stFiltraPor == 'economica')
                $obFormulario->addComponente( montaBuscaInscricaoEconomicaIndividual() );

            montaParcelamento();
            $obFormulario->montaInnerHtml();
        break;
        }
        if ($obFormulario) {
            $stHtml = $obFormulario->getHtml();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace('"',"'",$stHtml);
        } else {
            $stHtml = "";
            $stJs = 'd.getElementById("spnParcelas").innerHTML = "'.$stHtml.'"';
        }
        $stJs = 'd.getElementById("spnFiltros").innerHTML = "'.$stHtml.'"';
    break;
    case "mudaTipoCalculo":

        if (!$_REQUEST['inCodGrupo']) {
            $stJs .= "alertaAviso('Grupo de Crédito deve ser setado!','form','erro','".Sessao::getId()."');\n";
            break;
        }
    /*
        aqui é verificado quais componentes devem ser mostrada de acordo com
        o modulo do grupo/credito, e depois pelo tipo de calculo a ser efetuado
    */

        // buscar modulo do exercicio
        list( $inCodGrupo , $inExercicio ) = explode( '/' , $_REQUEST[ 'inCodGrupo' ] );
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->setCodGrupo ( $inCodGrupo );
        $obRARRGrupo->setExercicio ( $inExercicio );
        $obRARRGrupo->consultarGrupo();

        $inCodModulo = $obRARRGrupo->getCodModulo();

        $stTipoCalculo  = $_REQUEST[ "stTipoCalculo"    ];

        if ($stTipoCalculo != "geral") {
            $obFormulario = new Formulario;
            $obFormulario->addTitulo ("Dados para Filtro");
            if ($stTipoCalculo  == "parcial") {
                if ($inCodModulo == 12) { /* imobiliario */

                    montaBuscaContribuinte( $obFormulario );
                    montaBuscaInscricaoImobiliaria( $obFormulario );
                    $obFormulario->agrupaComponentes ( montaBuscaLocalizacao() );

                    $obChcBoxUtilizaCalendario = new CheckBox;
                    $obChcBoxUtilizaCalendario->setName ('chkUsaCalendario');
                    $obChcBoxUtilizaCalendario->setRotulo ('Vencimento das Parcelas');
                    $obChcBoxUtilizaCalendario->setLabel ( "Utilizar Calendário Fiscal");
                    $obChcBoxUtilizaCalendario->obEvento->setOnChange("buscaValor(&#39;montaParcelamento&#39;);");
                    $obChcBoxUtilizaCalendario->setChecked (true);
                    $obFormulario->addComponente( $obChcBoxUtilizaCalendario );

                    $obFormulario->montaInnerHtml();

                } elseif ($inCodModulo == 14) { /* economico */
                    montaBuscaContribuinte( $obFormulario );
                    $obFormulario->agrupaComponentes ( montaBuscaLocalizacao() );
                    montaBuscaInscricaoEconomica( $obFormulario );
                    $obFormulario->agrupaComponentes ( montaBuscaAtividade() );
                    $obFormulario->montaInnerHtml();
                }
            } elseif ($stTipoCalculo  == "individual") {
                if ($inCodModulo == 12) {
                    $obFormulario->addComponente( montaBuscaInscricaoImobiliariaIndividual() );
                    $obFormulario->montaInnerHtml();
                } elseif ($inCodModulo == 14) {
                    $obFormulario->addComponente ( montaBuscaInscricaoEconomicaIndividual() );
                    $obFormulario->montaInnerHtml();
                }
            }
        }

        if ($obFormulario) {
            $stHtml = $obFormulario->getHtml();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace('"',"'",$stHtml);
        } else {
            $stHtml = '';
        }
        $stJs .= 'd.getElementById("spnFiltros").innerHTML = "'.$stHtml.'";';

        if ($_REQUEST[ "stTipoCalculo" ] == 'individual') {
                montaParcelamento();

                $stJs .= "f.efetuar_lancamentos[0].disabled = false;\n";
                $stJs .= "f.efetuar_lancamentos[1].disabled = false;\n";
        } else {
            if ($_REQUEST[ "stTipoCalculo" ] == 'geral') {
                $stJs .= "d.getElementById('spnFiltros').innerHTML = null ;\n";
            }
            $stJs .= "d.getElementById('spnModoParcelamento').innerHTML = null ;\n";
            $stJs .= "d.getElementById('spnInfosAdicionais').innerHTML  = null ;\n";
            $stJs .= "d.getElementById('spnEmissao').innerHTML          = null ;\n";
            $stJs .= "d.getElementById('spnModelo').innerHTML           = null ;\n";
            $stJs .= "d.getElementById('spnParcelas').innerHTML         = null ;\n";
            $stJs .= "f.efetuar_lancamentos[0].click();\n";
            $stJs .= "f.efetuar_lancamentos[0].disabled = true ;\n";
            $stJs .= "f.efetuar_lancamentos[1].disabled = true ;\n";

            Sessao::remove('parcelas');
        }
    break;

    case "mudaTipoCalculoCredito":
        /*
            aqui é verificado quais componentes devem ser mostrada de acordo com
            o modulo do grupo/credito, e depois pelo tipo de calculo a ser efetuado
        */

        // buscar modulo do exercicio
        list( $inCodGrupo , $inExercicio ) = explode( '/' , $_REQUEST[ 'inCodGrupo' ] );
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->setCodGrupo ( $inCodGrupo );
        $obRARRGrupo->setExercicio ( $inExercicio );
        $obRARRGrupo->consultarGrupo();

        $inCodModulo = $obRARRGrupo->getCodModulo();

        $stTipoCalculo  = $_REQUEST[ "stTipoCalculo"    ];

        if ($stTipoCalculo != "geral") {
            $obFormulario = new Formulario;
            $obFormulario->addTitulo ("Dados para Filtro");
            if ($stTipoCalculo  == "parcial") {
                $boMonta = false;
                if ($_REQUEST['stFiltraPor'] == 'imobiliaria') { /* imobiliario */
                    $boMonta = true;
                    montaBuscaInscricaoImobiliaria( $obFormulario );
                } elseif ($_REQUEST['stFiltraPor'] == 'economica') { /* economico */
                    $boMonta = true;
                    montaBuscaInscricaoEconomica( $obFormulario );
                } elseif ($_REQUEST['stFiltraPor'] == 'cgm') {
                    $boMonta = true;
                    montaBuscaContribuinte( $obFormulario );
                }

                if ($boMonta) {
                    $obChcBoxUtilizaCalendario = new CheckBox;
                    $obChcBoxUtilizaCalendario->setName ('chkUsaCalendario');
                    $obChcBoxUtilizaCalendario->setRotulo ('Vencimento das Parcelas');
                    $obChcBoxUtilizaCalendario->setLabel ( "Utilizar Calendário Fiscal");
                    $obChcBoxUtilizaCalendario->obEvento->setOnChange("buscaValor(&#39;montaParcelamento&#39;);");
                    $obChcBoxUtilizaCalendario->setChecked (true);
                    $obFormulario->addComponente( $obChcBoxUtilizaCalendario );

                    $obFormulario->montaInnerHtml();
                }

            } elseif ($stTipoCalculo  == "individual") {
                $boMonta = false;
                if ($_REQUEST['stFiltraPor'] == 'imobiliaria') {
                    $boMonta = true;
                    $obFormulario->addComponente( montaBuscaInscricaoImobiliariaIndividual() );
                } elseif ($_REQUEST['stFiltraPor'] == 'economica') {
                    $boMonta = true;
                    $obFormulario->addComponente ( montaBuscaInscricaoEconomicaIndividual() );
                } elseif ($_REQUEST['stFiltraPor'] == 'cgm') {
                    $boMonta = true;
                    $obFormulario->addComponente ( montaBuscaContribuinteIndividual() );
                }
                if ( $boMonta ) $obFormulario->montaInnerHtml();
            }
        }

        if ($obFormulario) {
            $stHtml = $obFormulario->getHtml();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace('"',"'",$stHtml);
        } else {
            $stHtml = '';
        }
        $stJs .= 'd.getElementById("spnFiltros").innerHTML = "'.$stHtml.'";';

        if ($_REQUEST[ "stTipoCalculo" ] == 'individual') {

            montaParcelamento();

            $stJs .= "f.efetuar_lancamentos[0].disabled = false;\n";
            $stJs .= "f.efetuar_lancamentos[1].disabled = false;\n";

        } else {

            if ($_REQUEST[ "stTipoCalculo" ] == 'geral') {
                $stJs .= "d.getElementById('spnFiltros').innerHTML = null ;\n";
            }
            $stJs .= "d.getElementById('spnModoParcelamento').innerHTML = null ;\n";
            $stJs .= "d.getElementById('spnInfosAdicionais').innerHTML  = null ;\n";
            $stJs .= "d.getElementById('spnEmissao').innerHTML          = null ;\n";
            $stJs .= "d.getElementById('spnModelo').innerHTML           = null ;\n";
            $stJs .= "d.getElementById('spnParcelas').innerHTML         = null ;\n";
            $stJs .= "f.efetuar_lancamentos[0].click();\n";
            $stJs .= "f.efetuar_lancamentos[0].disabled = true ;\n";
            $stJs .= "f.efetuar_lancamentos[1].disabled = true ;\n";

            Sessao::remove('parcelas');
        }
    break;

    case "limpaSessao":
        Sessao::remove('parcelas');
        break;

    case "grupo":
        $arLink = explode('?',$_SERVER['REQUEST_URI']);
        $stAcao = $request->get('stAcao');
        print '<script type="text/javascript">
                    mudaTelaPrincipal( "'.$pgFormGrupo.'?'.$arLink[1].'&stAcao='.$stAcao.'" );
            </script>';
        break;

    case "credito":
        $arLink = explode('?',$_SERVER['REQUEST_URI']);
        $stAcao = $request->get('stAcao');
        print '<script type="text/javascript">
                    mudaTelaPrincipal( "'.$pgFormCredito.'?'.$arLink[1].'&stAcao='.$stAcao.'" );
            </script>';
        break;

    case "emissao":
    $stEmissao  = $_REQUEST["efetuar_lancamentos"];

    if ($stEmissao  == "sim") {
/**
* Foi retirado a parte que monta modelos de carnes pela falta de suporte do Agata Reports!
*/
        $obRdbEmissaoNaoEmitir = new Radio;
        $obRdbEmissaoNaoEmitir->setTitle ( "Informe se deverá ou não ser emitido carnê de cobrança." );
        $obRdbEmissaoNaoEmitir->setRotulo   ( "Emissão de Carnês"                            );
        $obRdbEmissaoNaoEmitir->setName     ( "emissao_carnes"                               );
        $obRdbEmissaoNaoEmitir->setId       ( "emissao_carnes"                               );
        $obRdbEmissaoNaoEmitir->setLabel    ( "Não Emitir"                                   );
        $obRdbEmissaoNaoEmitir->setValue    ( "nao_emitir"                                   );
        $obRdbEmissaoNaoEmitir->setNull     ( false                                          );
        $obRdbEmissaoNaoEmitir->setChecked  ( true                                           );
        $obRdbEmissaoNaoEmitir->obEvento->setOnChange( "montaModeloCarne();"  );

        $obRdbEmissaoLocal = new Radio;
        $obRdbEmissaoLocal->setRotulo   ( "Emissão de Carnês"                            );
        $obRdbEmissaoLocal->setName     ( "emissao_carnes"                               );
        $obRdbEmissaoLocal->setId       ( "emissao_carnes"                               );
        $obRdbEmissaoLocal->setLabel    ( "Impressão Local"                              );
        $obRdbEmissaoLocal->setValue    ( "local"                                         );
        $obRdbEmissaoLocal->setNull     ( false                                          );
        $obRdbEmissaoLocal->setChecked  ( false                                          );
        $obRdbEmissaoLocal->obEvento->setOnChange( "montaModeloCarne();"  );

        $obRdbEmissaoGrafica = new Radio;
        $obRdbEmissaoGrafica->setRotulo   ( "Emissão de Carnês"                            );
        $obRdbEmissaoGrafica->setName     ( "emissao_carnes"                               );
        $obRdbEmissaoGrafica->setId       ( "emissao_carnes"                               );
        $obRdbEmissaoGrafica->setLabel    ( "Gráfica"                                      );
        $obRdbEmissaoGrafica->setValue    ( "grafica"                                      );
        $obRdbEmissaoGrafica->setNull     ( false                                          );
        $obRdbEmissaoGrafica->setChecked  ( false                                          );
        $obRdbEmissaoGrafica->obEvento->setOnChange( "montaModeloCarne();"  );

        $obFormulario = new Formulario;
        $obFormulario->agrupaComponentes( array($obRdbEmissaoNaoEmitir,$obRdbEmissaoLocal,$obRdbEmissaoGrafica));
        $obFormulario->montaInnerHtml();

        if ($obFormulario) {
            $stHtml = $obFormulario->getHtml();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace('"',"'",$stHtml);
        } else {
            $stHtml = "";
        }

        $stJs .= "d.getElementById('spnEmissao').innerHTML = \"".$stHtml."\"; \n";

            include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
            $obRARRConfiguracao = new RARRConfiguracao;
            $obRARRConfiguracao->setAnoExercicio ( Sessao::getExercicio() );
            $obRARRConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

            $obFormulario = new Formulario;

            $obBscProcesso = new BuscaInner;
            $obBscProcesso->setRotulo ( "Processo" );
            $obBscProcesso->setTitle ( "Processo referente ao cálculo." );
            $obBscProcesso->setNull ( true );
            $obBscProcesso->obCampoCod->setName ("inProcesso");
            $obBscProcesso->obCampoCod->setId   ("inProcesso");
            $obBscProcesso->obCampoCod->setValue( $inProcesso );
            $obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor(&#39;buscaProcesso&#39;,&#39;&#39;);" );
            $obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
            $obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
            $obBscProcesso->obCampoCod->obEvento->setOnKeyUp ("mascaraDinamico(&#39;".$stMascaraProcesso."&#39;, this, event);");
            $obBscProcesso->setFuncaoBusca("abrePopUp(&#39;".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php&#39;,&#39;frm&#39;,&#39;inProcesso&#39;,&#39;campoInner2&#39;,&#39;&#39;,&#39;".Sessao::getId()."&#39;,&#39;800&#39;,&#39;550&#39;)" );

            $obTxtObservacao = new TextArea;
            $obTxtObservacao->setName ( "stObservacao" );
            $obTxtObservacao->setTitle ( "Observações referentes ao cálculo disponíveis para o contribuinte." );
            $obTxtObservacao->setRotulo ( "Observações p/ Boleto" );
            $obTxtObservacao->setValue ( "" );
            $obTxtObservacao->setNull    ( true );
            $obTxtObservacao->setCols   ( 30 );
            $obTxtObservacao->setRows  ( 5 );

            $obTxtObservacaoInterna = new TextArea;
            $obTxtObservacaoInterna->setName ( "stObservacaoInterna" );
            $obTxtObservacaoInterna->setTitle ( "Observações referentes ao cálculo disponíveis apenas no sistema." );
            $obTxtObservacaoInterna->setRotulo ( "Observações Internas" );
            $obTxtObservacaoInterna->setValue ( "" );
            $obTxtObservacaoInterna->setNull    ( true );
            $obTxtObservacaoInterna->setCols   ( 30 );
            $obTxtObservacaoInterna->setRows  ( 5 );

            $obFormulario->addComponente ( $obBscProcesso );
            $obFormulario->addComponente ( $obTxtObservacao );
            $obFormulario->addComponente ( $obTxtObservacaoInterna );
            $obFormulario->montaInnerHtml();

            $stHtml = $obFormulario->getHtml();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace('"',"'",$stHtml);
            $stJs .= 'd.getElementById("spnInfosAdicionais").innerHTML = "'.$stHtml.'"';

    } else {
        $stJs .= 'd.getElementById("spnEmissao").innerHTML = "'. null .'"; ';
        $stJs .= 'd.getElementById("spnInfosAdicionais").innerHTML = "'. null .'";';
    }

    break;
    case "montaModeloCarne":
    $stMontaModelo = $_REQUEST["emissao_carnes"];

    if ($stMontaModelo == "local") {
        include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );

        $obRARRCarne = new RARRCarne;
        $obRARRCarne->listarModeloDeCarne( $rsModelos, Sessao::read('acao') );

        $obCmbModelo =  new Select;
        $obCmbModelo->setRotulo        ( "Modelo" );
        $obCmbModelo->setName          ( "stArquivo" );
        $obCmbModelo->setStyle         ( "width: 200px");
        $obCmbModelo->setCampoID       ( "[nom_arquivo]§[cod_modelo]" );
        $obCmbModelo->setCampoDesc     ( "nom_modelo" );
        $obCmbModelo->addOption        ( "", "Selecione" );
        $obCmbModelo->setNull          ( false );
        $obCmbModelo->preencheCombo    ( $rsModelos );

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obCmbModelo );
        $obFormulario->montaInnerHtml();
    }

    if ($obFormulario) {
        $stHtml = $obFormulario->getHtml();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace('"',"'",$stHtml);
    } else {
        $stHtml = "";
    }

    $stJs .= "d.getElementById('spnModelo').innerHTML = \"".$stHtml."\"; \n";
    break;
    case "incluirParcela":
        if (!$_REQUEST['data_vencimento']) {
           $stErro = 'A Data de Vencimento deve ser informada!';
           $stJs .= "f.data_vencimento.focus();\n";
           $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
        } else {
            $inMaximoParcelas = 12;
            if ($_REQUEST['FormLancamentoManual'] == 'GrupoCrédito') {
                $arGrupo = explode( "/", $_REQUEST['inCodGrupo'] );
                $obRARRGrupo->setCodGrupo ( $arGrupo[0] );
                $obRARRGrupo->setExercicio ( $arGrupo[1] );
                $obRARRGrupo->listarCreditos($rsCreditos);
                $PodeDesconto = false;
                while ( !$rsCreditos->eof() ) {
                    if ( $rsCreditos->getCampo('desconto') =='t' ) {
                        $PodeDesconto = true;
                    }
                    $rsCreditos->proximo();
                }

                if (!$PodeDesconto && $_REQUEST['flDesconto'] != 0.00) {
                    $stErro = 'Nenhum valor de desconto pode ser lançado para este Grupo de Crédito!';
                    $stJs .= "f.flDesconto.focus();\n";
                    $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
                }

                include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
                $obRARRConfiguracao = new RARRConfiguracao;
                $obRARRConfiguracao->consultar();
                $rsListaGruposSelecionados = $obRARRConfiguracao->getRSSuperSimples();
                while ( !$rsListaGruposSelecionados->Eof() ) {
                    if ( ( $arGrupo[0] == $rsListaGruposSelecionados->getCampo("cod_grupo") ) && ( $arGrupo[1] == $rsListaGruposSelecionados->getCampo("ano_exercicio") ) ) {
                        $inMaximoParcelas = 120;
                        break;
                    }

                    $rsListaGruposSelecionados->proximo();
                }
            }

            $inTotalParcelas = $_REQUEST["cmbQtdParcelas"];
            if (!$inTotalParcelas) {
                $stErro = 'A quantidade de parcelas a ser incluida nao foi definida!';
                $stJs .= "f.cmbQtdParcelas.focus();\n";
                $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
            } else
                if ($inTotalParcelas > $inMaximoParcelas) {
                    $stErro = 'A quantidade de parcelas é maior que '.$inMaximoParcelas.'!';
                    $stJs .= "f.cmbQtdParcelas.value = 1;\n";
                    $stJs .= "f.cmbQtdParcelas.focus();\n";
                    $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
                } else
                    if ($inTotalParcelas <= 0) {
                        $stErro = 'A quantidade de parcelas é menor que 1!';
                        $stJs .= "f.cmbQtdParcelas.value = 1;\n";
                        $stJs .= "f.cmbQtdParcelas.focus();\n";
                        $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
                    }

            if (!$stErro) {
                $arrayParcelasTMP = Sessao::read('parcelas');
                $inContParcelas = count ( $arrayParcelasTMP );

                //insere na array os novos valores
                $arrayParcelasTMP[$inContParcelas]['inIndice'] = $inContParcelas;
                if (!$_REQUEST['flDesconto']) {
                    $flValorDesconto = 0.00;
                } else {
                    $flValorDesconto = $_REQUEST['flDesconto'];
                }

                $flValorTemp = str_replace( ",", ".", str_replace( ".", "", $_REQUEST["flDesconto"] ) );
                if ($_REQUEST["stTipoDesconto"] == "Percentual") {
                    if ($flValorTemp > 100.00) {
                        $js = "alertaAviso('@Valor percentual acima do limite de 100%.','form','erro','".Sessao::getId()."');";
                        sistemaLegado::executaFrameOculto($js);
                        exit;
                    }
                } else {
                    $flValorTotalTemp = str_replace( ",", ".", str_replace( ".", "", $_REQUEST["obHdnValorTotal"] ) );
                    if ($_REQUEST["stTipoParcela"] == "Normal") {
                        $inTempTotal = $_REQUEST["cmbQtdParcelas"];
                        $arParcelas = Sessao::read('parcelas');
                        for ( $inX=0; $inX<count( $arParcelas ); $inX++) {
                            if ( $arParcelas[$inX][stTipoParcela] != "Única" )
                                $inTempTotal++;
                        }
                    }
                }

                $newDtVencimento = $_REQUEST['data_vencimento'];
                for ($inQtdParcelas=0; $inQtdParcelas < $inTotalParcelas; $inQtdParcelas++) {

                    $dataOrdenacao = explode ('/', $newDtVencimento);

                    if ($dataOrdenacao[1] > 12) {
                        $dataOrdenacao[1] = 01;
                        $dataOrdenacao[2] = $dataOrdenacao[2] + 1;
                    }

                    $inDiaInicial = $dataOrdenacao[0];
                    $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $dataOrdenacao[0]), sprintf("%04d", $dataOrdenacao[2])));
                    $inNroDiasMes = date("t", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), 01, sprintf("%04d", $dataOrdenacao[2])));

                    if ($inNroDiasMes <= $inDiaInicial) {
                        $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $inNroDiasMes), sprintf("%04d", $dataOrdenacao[2])));
                        if ($inDiaSemana == 0) {
                            $dtDiaVencimento = $inNroDiasMes - 2;
                        } elseif ($inDiaSemana == 6) {
                            $dtDiaVencimento = $inNroDiasMes - 1;
                        } else {
                            $dtDiaVencimento = $inNroDiasMes;
                        }

                    } elseif ( ($inNroDiasMes - 1) == $inDiaInicial ) {
                        $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $inNroDiasMes), sprintf("%04d", $dataOrdenacao[2])));
                        if ($inDiaSemana == 0) {
                            $dtDiaVencimento = $inNroDiasMes + 1;
                        } elseif ($inDiaSemana == 6) {
                            $dtDiaVencimento = $inNroDiasMes - 1;
                        } else {
                            $dtDiaVencimento = $inNroDiasMes;
                        }

                    } else {
                        $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $inDiaInicial), sprintf("%04d", $dataOrdenacao[2])));
                        if ($inDiaSemana == 0) {
                            $dtDiaVencimento = $inDiaInicial + 1;
                        } elseif ($inDiaSemana == 6) {
                            $dtDiaVencimento = $inDiaInicial + 2;
                        } else {
                            $dtDiaVencimento = $inDiaInicial;
                        }

                    }

                    if ($_REQUEST['stTipoParcela'] == 'Única') {
                        $arrayParcelasTMP[$inContParcelas + $inQtdParcelas]['stTipoParcela']     = $_REQUEST['stTipoParcela'];
                    } else {
                        $arrayParcelasTMP[$inContParcelas + $inQtdParcelas]['stTipoParcela']     = 1;
                    }

                    $arrayParcelasTMP[$inContParcelas + $inQtdParcelas]['stTipoDesconto'] = $_REQUEST['stTipoDesconto'];
                    $arrayParcelasTMP[$inContParcelas + $inQtdParcelas]['data_vencimento'] = sprintf("%02d/%02d/%04d", $dtDiaVencimento, $dataOrdenacao[1], $dataOrdenacao[2]);
                    $arrayParcelasTMP[$inContParcelas + $inQtdParcelas]['flDesconto'] = $flValorDesconto;

                    $arrayParcelasTMP[$inContParcelas + $inQtdParcelas]['dtVencimentoOrdenacao'] = sprintf("%04d%02d%02d", $dataOrdenacao[2], $dataOrdenacao[1], $dtDiaVencimento);

                    $newDtVencimento = sprintf("%02d/%02d/%04d", $inDiaInicial, $dataOrdenacao[1]+1, $dataOrdenacao[2] );
                } // FINAL FOR

                $arrayParcelas = OrdenaParcelas ( $arrayParcelasTMP );
                Sessao::write ( "parcelas", $arrayParcelas );
                montaParcelas ( $arrayParcelas );
            }
        }
        break;

    case "excluirParcela":
        $arTmpParcelas = array ();
        $arParcelas = Sessao::read ( "parcelas" );
        $inCountSessao = count( $arParcelas );
        $inCountArray = 0;
        $inCountArrayNormais = 1;
        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
            if ($inCount != $_REQUEST[ "inLinha" ]) {
                $arTmpParcelas[$inCountArray]['inIndice'] = $inCountArray;
                if ($arParcelas[$inCount]["stTipoParcela"] != 'Única') {
                    $arTmpParcelas[$inCountArray]["stTipoParcela"]     = $inCountArrayNormais++;
                } else {
                    $arTmpParcelas[$inCountArray]['stTipoParcela']     = 'Única';
                }
                $arTmpParcelas[$inCountArray]["data_vencimento"] = $arParcelas[$inCount][ "data_vencimento"     ];
                $arTmpParcelas[$inCountArray]["flDesconto"] = $arParcelas[$inCount][ "flDesconto"  ];
                $arTmpParcelas[$inCountArray]["valor"] = $arParcelas[$inCount][ "flDesconto"  ];
                $arTmpParcelas[$inCountArray]["stTipoDesconto"] = $arParcelas[$inCount][ "stTipoDesconto" ];
                $arTmpParcelas[$inCountArray]["dtVencimentoOrdenacao"] = $arParcelas[$inCount][ "dtVencimentoOrdenacao" ];
                $inCountArray++;
            }
        }

        Sessao::write ( "parcelas", $arTmpParcelas );
        montaParcelas ( $arTmpParcelas );
        break;

    case "BuscaDoCredito":
        $inCodCreditoComposto  = explode('.',$_REQUEST["inCodCredito"]);

        $obRARRGrupo->obRMONCredito->setCodCredito  ($inCodCreditoComposto[0]);
        $obRARRGrupo->obRMONCredito->setCodEspecie  ($inCodCreditoComposto[1]);
        $obRARRGrupo->obRMONCredito->setCodGenero   ($inCodCreditoComposto[2]);
        $obRARRGrupo->obRMONCredito->setCodNatureza ($inCodCreditoComposto[3]);
        $obRARRGrupo->obRMONCredito->consultarCredito();

        $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
        $stDescricao = $obRARRGrupo->obRMONCredito->getDescricao() ;

        if ( !empty($stDescricao) ) {
            $stJs .= "d.getElementById('stCredito').innerHTML = '".$stDescricao."';\n";

        } else {
            $stJs .= "f.inCodCredito.value ='';\n";
            $stJs .= "f.inCodCredito.focus();\n";
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Crédito informado não existe. (".$_REQUEST["inCodCredito"].")','form','erro','".Sessao::getId()."');";
        }
    break;

    case "buscaGrupoLancamentoManual":
        ;
        $obRARRPermissao = new RARRPermissao;
        $obRARRPermissao->obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
        $obErro = $obRARRPermissao->obRARRGrupo->consultarGrupo();
        if ( !$obRARRPermissao->obRARRGrupo->getCodGrupo() ) {
            $stErro = "@Grupo de Créditos informado não existe.(".$_REQUEST["inCodGrupo"].")";
        } else {
            $obRARRPermissao->obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
            $obRARRPermissao->obRCGM->setNumCGM( Sessao::read('numCgm') );
            $obErro = $obRARRPermissao->consultarPermissao();
            if ($obRARRPermissao->getPermitido == "false") {
                $stErro = "Usuário não possui permissão para efetuar cálculos do grupo de crédito ".$obRARRPermissao->obRARRGrupo->getDescricao()."!";
            } else {
                $inCodGrupo     = $obRARRPermissao->obRARRGrupo->getCodGrupo () ;
                $stDescricao    = $obRARRPermissao->obRARRGrupo->getDescricao() ;
                $inCodModulo    = $obRARRPermissao->obRARRGrupo->getCodModulo() ;
            }
        }
        if (!$stErro) {
           $stJs .= "d.getElementById('stGrupo').innerHTML = '".$stDescricao."';\n";
           $stJs .= "f.inCodModulo.value = '".$inCodModulo."';\n";
           $stJs .= "f.inCodGrupo.focus();\n";
           $stJs .= montaListaCreditos ( $_REQUEST['inCodGrupo'] );
        } else {
           $stJs .= "f.inCodGrupo.value ='';\n";
           $stJs .= "f.inCodGrupo.focus();\n";
           $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
           $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
        }

    break;

    case "limpaLancamento":
        Sessao::remove('parcelas');
    case "montaReferenciaParcelamento":
        montaParcelamento();
    case "referencia":
        $obFormulario = new Formulario;

        switch ($_REQUEST["stReferencia"]) {
            case "cgm":
                $obBscContribuinte = new BuscaInner;
                $obBscContribuinte->setId               ( "stContribuinte"          );
                $obBscContribuinte->setRotulo           ( "Contribuinte"            );
                $obBscContribuinte->setTitle            ( "Codigo do contribuinte." );
                $obBscContribuinte->setNull             ( false                     );
                $obBscContribuinte->obCampoCod->setName ("inCodContribuinte"        );
                $obBscContribuinte->obCampoCod->setValue( $inCodContribuinte        );
                $obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor(&#39;buscaContribuinte&#39;);");
                $obBscContribuinte->setFuncaoBusca( "abrePopUp(&#39;".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php&#39;,&#39;frm&#39;,&#39;inCodContribuinte&#39;,&#39;stContribuinte&#39;,&#39;&#39;,&#39;".Sessao::getId()."&#39;,&#39;800&#39;,&#39;450&#39;);" );
                $obFormulario->addComponente ( $obBscContribuinte );
            break;
            case "ii":
                $obBscInscricaoMunicipal = new BuscaInner;
                $obBscInscricaoMunicipal->setId  ( "stInscricaoImobiliaria"          );
                $obBscInscricaoMunicipal->setRotulo                ( "Inscrição Imobiliária"      );
                $obBscInscricaoMunicipal->setNull                  ( false                        );
                $obBscInscricaoMunicipal->obCampoCod->obEvento->setOnChange("buscaValor(&#39;procuraImovel2&#39;);");
                $obBscInscricaoMunicipal->obCampoCod->setName      ( "inInscricaoImobiliaria"     );
                $obBscInscricaoMunicipal->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)  );
                $obBscInscricaoMunicipal->obCampoCod->setMascara   ( $stMascaraInscricao          );
                $obBscInscricaoMunicipal->obCampoCod->setInteiro   ( false                        );
                $obBscInscricaoMunicipal->setFuncaoBusca( "abrePopUp( &#39;".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php&#39;, &#39;frm&#39;, &#39;inInscricaoImobiliaria&#39;, &#39;stInscricaoImobiliaria&#39;, &#39;todos&#39;, &#39;".Sessao::getId()."&#39;, &#39;800&#39;, &#39;550&#39; );" );

                $obFormulario->addComponente ( $obBscInscricaoMunicipal );
            break;
            case "ie":

                $obBscInscricaoEconomica = new BuscaInner;
                $obBscInscricaoEconomica->setId                   ( "stInscricaoEconomica"  );
                $obBscInscricaoEconomica->setRotulo               ( "Inscrição Econômica"   );
                $obBscInscricaoEconomica->setTitle                ( "Pessoa física ou jurídica cadastrada como inscrição econômica.");
                $obBscInscricaoEconomica->obCampoCod->setName     ( "inInscricaoEconomica"  );
                $obBscInscricaoEconomica->setNull                 ( false                   );
                $obBscInscricaoEconomica->obCampoCod->setMaxLength( strlen($stMascaraInscricaoEconomico ));
                $obBscInscricaoEconomica->obCampoCod->setMascara  ( $stMascaraInscricao         );
                $obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange("buscaValor('buscaIE2');");
                $obBscInscricaoEconomica->setFuncaoBusca          ( "abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stInscricaoEconomica','todos','".Sessao::getId()."','800','550');" );
                $obFormulario->addComponente ( $obBscInscricaoEconomica );
            break;
        }

    $obFormulario->montaInnerHTML();
    $js .= "d.getElementById('spnReferencia').innerHTML = '". $obFormulario->getHTML(). "';\n";
    sistemaLegado::executaFrameOculto($js);
    break;

    case "buscaContribuinte3":
        if ($_REQUEST["inCodContribuinteIndividual"]) {
            $obRCGM = new RCGM;
            $inCodContribuinteAtual = $_REQUEST["inCodContribuinteIndividual"];
            $obRCGM->setNumCGM( $inCodContribuinteAtual );
            $obRCGM->consultar( $rsCGM );
            $stNull = "&nbsp";
            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.inCodContribuinteIndividual.value = "";';
                $stJs .= 'f.inCodContribuinteIndividual.focus();';
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNull.'";';
                $stJs .= "alertaAviso('@Valor inválido. (".$inCodContribuinteAtual.")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaContribuinte":
        if ($_REQUEST["inCodContribuinteInicial"] || $_REQUEST["inCodContribuinteFinal"] || $_REQUEST["inCodContribuinteIndividual"] || $_REQUEST["inCodContribuinte"]) {

            $obRCGM = new RCGM;
            if ($_REQUEST["inCodContribuinte"]) {
                $stNomCampo = 'stContribuinte';
                $inCodContribuinteAtual = $_REQUEST["inCodContribuinte"];
            }else
            if ($_REQUEST["inCodContribuinteIndividual"]) {
                $stNomCampo = 'inCodContribuinteIndividual';
                $inCodContribuinteAtual = $_REQUEST["inCodContribuinteIndividual"];
            } elseif ($_REQUEST["inCodContribuinteInicial"]) {
                $stNomCampo = 'inCodContribuinteInicial';
                $inCodContribuinteAtual = $_REQUEST["inCodContribuinteInicial"];
            } elseif ($_REQUEST["inCodContribuinteFinal"]) {
                $stNomCampo = 'inCodContribuinteFinal';
                $inCodContribuinteAtual = $_REQUEST["inCodContribuinteFinal"];
            }

            $obRCGM->setNumCGM( $inCodContribuinteAtual );
            $obRCGM->consultar( $rsCGM );
            $stNull = "";
            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.'.$stNomCampo.'.value = "";';
                $stJs .= 'f.'.$stNomCampo.'.focus();';
                $stJs .= 'd.getElementById("'.$stNomCampo.'").innerHTML = "'.$stNull.'";';
                $stJs .= "alertaAviso('@Valor inválido. (".$inCodContribuinteAtual.")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("'.$stNomCampo.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaIE3":
        if ($_REQUEST["inNumInscricaoEconomica"]) {
            $inInscricaoAtual = $_REQUEST["inNumInscricaoEconomica"];

            include_once(CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php");

            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica($inInscricaoAtual);
            $obRCEMInscricaoEconomica->consultarNomeInscricaoEconomica($rsInscricao);
            if ( $rsInscricao->getNumLinhas() < 1 ) {
                $stJs .= "f.inNumInscricaoEconomica.value = '';\n";
                $stJs .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;' ;\n";
                $stJs .= 'f.inNumInscricaoEconomica.focus();';
                $stJs .= "alertaAviso('@Código de inscrição econômica inválido. (".$inInscricaoAtual.")','form','erro','".Sessao::getId()."');";
            } else {
                $obRCEMInscricaoEconomica->consultarInscricaoEconomicaBaixa( $rsEmpresaBaixa );
                if ( $rsEmpresaBaixa->getNumLinhas() > 0 ) {
                    if ( !$rsEmpresaBaixa->getCampo("dt_termino") ) {
                        $stJs .= 'f.inNumInscricaoEconomica.value = "";';
                        $stJs .= 'f.inNumInscricaoEconomica.focus();';
                        $stJs .= 'd.getElementById("stInscricaoEconomica").innerHTML = "&nbsp;";';
                        $stJs .= "alertaAviso('@Código de inscrição econômica inválido. <b>Empresa Baixada</b>  (".$inInscricaoAtual.")','form','erro','".Sessao::getId()."');";
                    } else {
                        $stNomeEmpresa = $rsInscricao->getCampo('nom_cgm');
                        $stJs .= 'd.getElementById("stInscricaoEconomica").innerHTML = "'.$stNomeEmpresa.'";';
                    }
                } else {
                    $stNomeEmpresa = $rsInscricao->getCampo('nom_cgm');
                    $stJs .= 'd.getElementById("stInscricaoEconomica").innerHTML = "'.$stNomeEmpresa.'";';
                }
            }

        } else {
            $stJs .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;' ;\n";
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaIE2":
        if ($_REQUEST["inInscricaoEconomica"]) {
            $inInscricaoAtual = $_REQUEST["inInscricaoEconomica"];

            include_once(CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php");

            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica($inInscricaoAtual);
            $obRCEMInscricaoEconomica->consultarNomeInscricaoEconomica($rsInscricao);
            if ( $rsInscricao->getNumLinhas() < 1 ) {
                $stJs .= "f.inInscricaoEconomica.value = '';\n";
                $stJs .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;' ;\n";
                $stJs .= 'f.inInscricaoEconomica.focus();';
                $stJs .= "alertaAviso('@Código de inscrição econômica inválido. (".$inInscricaoAtual.")','form','erro','".Sessao::getId()."');";
            } else {
                $obRCEMInscricaoEconomica->consultarInscricaoEconomicaBaixa( $rsEmpresaBaixa );
                if ( $rsEmpresaBaixa->getNumLinhas() > 0 ) {
                    if ( !$rsEmpresaBaixa->getCampo("dt_termino") ) {
                        $stJs .= 'f.inInscricaoEconomica.value = "";';
                        $stJs .= 'f.inInscricaoEconomica.focus();';
                        $stJs .= 'd.getElementById("stInscricaoEconomica").innerHTML = "&nbsp;";';
                        $stJs .= "alertaAviso('@Código de inscrição econômica inválido. <b>Empresa Baixada</b>  (".$inInscricaoAtual.")','form','erro','".Sessao::getId()."');";
                    } else {
                        $stNomeEmpresa = $rsInscricao->getCampo('nom_cgm');
                        $stJs .= 'd.getElementById("stInscricaoEconomica").innerHTML = "'.$stNomeEmpresa.'";';
                    }
                } else {
                    $stNomeEmpresa = $rsInscricao->getCampo('nom_cgm');
                    $stJs .= 'd.getElementById("stInscricaoEconomica").innerHTML = "'.$stNomeEmpresa.'";';
                }
            }

        } else {
            $stJs .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;' ;\n";
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaIE":
        if ($_REQUEST["inNumInscricaoEconomica"] || $_REQUEST["inNumInscricaoEconomicaInicial"] || $_REQUEST["inNumInscricaoEconomicaFinal"] || $_REQUEST["inInscricaoEconomica"]) {
            if ($_REQUEST["inInscricaoEconomica"]) {
                $stNomCampo = 'inInscricaoEconomica';
                $inInscricaoAtual = $_REQUEST["inInscricaoEconomica"];
            }else
            if ($_REQUEST["inNumInscricaoEconomica"]) {
                $stNomCampo = 'inNumInscricaoEconomica';
                $inInscricaoAtual = $_REQUEST["inNumInscricaoEconomica"];
            } elseif ($_REQUEST["inNumInscricaoEconomicaInicial"]) {
                $stNomCampo = 'inNumInscricaoEconomicaInicial';
                $inInscricaoAtual = $_REQUEST["inNumInscricaoEconomicaInicial"];
            } elseif ($_REQUEST["inNumInscricaoEconomicaFinal"]) {
                $stNomCampo = 'inNumInscricaoEconomicaFinal';
                $inInscricaoAtual = $_REQUEST["inNumInscricaoEconomicaFinal"];
            }

            include_once(CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php");
            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica($inInscricaoAtual);
            $obRCEMInscricaoEconomica->consultarNomeInscricaoEconomica($rsInscricao);
            if ( $rsInscricao->getNumLinhas() < 1 ) {
                $stJs .= "f.".$stNomCampo.".value = '';\n";
                $stJs .= 'f.'.$stNomCampo.'.focus();';
                $stJs .= "alertaAviso('@Código de inscrição econômica inválido. (".$inInscricaoAtual.")','form','erro','".Sessao::getId()."');";

            } else {
                $obRCEMInscricaoEconomica->consultarInscricaoEconomicaBaixa( $rsEmpresaBaixa );
                if ( $rsEmpresaBaixa->getNumLinhas() > 0 ) {
                    if ( !$rsEmpresaBaixa->getCampo("dt_termino") ) {
                        $stJs .= 'f.'.$stNomCampo.'.value = "";';
                        $stJs .= 'f.'.$stNomCampo.'.focus();';
                           $stJs .= "alertaAviso('@Código de inscrição econômica inválido. <b>Empresa Baixada</b>  (".$inInscricaoAtual.")','form','erro','".Sessao::getId()."');";
                    } else {
                        $stNomeEmpresa = $rsInscricao->getCampo('nom_cgm');
                    }
                } else {
                    $stNomeEmpresa = $rsInscricao->getCampo('nom_cgm');
                }
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "procuraImovel2":
        $stJs = "";
        $stNull = "&nbsp;";
        if ($_REQUEST["inInscricaoImobiliaria"]) {
            $inInscricaoAtual = $_REQUEST["inInscricaoImobiliaria"];

            include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php"       );
            $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) );

            $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao ( $inInscricaoAtual );
            $obRCIMUnidadeAutonoma->roRCIMImovel->listarImoveisConsulta( $rsImoveis );

            if ( $rsImoveis->getNumLinhas() < 1  ) {
                //nao encontrada
                $stJs .= 'f.inInscricaoImobiliaria.value = "";';
                $stJs .= 'f.inInscricaoImobiliaria.focus();';
                $stJs .= 'd.getElementById("stInscricaoImobiliaria").innerHTML = "'.$stNull.'";';
                $stJs .= "alertaAviso('@Código de inscrição imobiliária inválido. (".$inInscricaoAtual.")','form','erro','".Sessao::getId()."');";
            } else {
                if ( $rsImoveis->getCampo('situacao') == 'Baixado' ) {
                    $stJs .= 'f.inInscricaoImobiliaria.value = "";';
                    $stJs .= 'f.inInscricaoImobiliaria.focus();';
                    $stJs .= "alertaAviso('@Código de inscrição imobiliária inválido. <b>Imóvel Baixado</b>  (".$inInscricaoAtual.")','form','erro','".Sessao::getId()."');";
                } else {
                    $stJs .= 'd.getElementById("stInscricaoImobiliaria").innerHTML = "'.$rsImoveis->getCampo("endereco").'";';
                }
            }
        } else {
            $stJs .= 'f.inInscricaoImobiliaria.value = "";';
            $stJs .= 'd.getElementById("stInscricaoInscricaoImobiliaria").innerHTML = "'.$stNull.'";';
        }

        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "procuraImovel":
        $stJs = "";
        $stNull = "&nbsp;";
        if ($_REQUEST["inInscricaoImobiliaria"] || $_REQUEST['inNumInscricaoImobiliariaInicial'] || $_REQUEST['inNumInscricaoImobiliariaFinal']) {

            if ($_REQUEST["inInscricaoImobiliaria"]) {
                $stNomCampo = 'stInscricaoImobiliaria';
                $inInscricaoAtual = $_REQUEST["inInscricaoImobiliaria"];
            } elseif ($_REQUEST["inNumInscricaoImobiliariaInicial"]) {
                $stNomCampo = 'inNumInscricaoImobiliariaInicial';
                $inInscricaoAtual = $_REQUEST["inNumInscricaoImobiliariaInicial"];
            } elseif ($_REQUEST["inNumInscricaoImobiliariaFinal"]) {
                $stNomCampo = 'inNumInscricaoImobiliariaFinal';
                $inInscricaoAtual = $_REQUEST["inNumInscricaoImobiliariaFinal"];
            }

            include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php"       );
            $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) );

            $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao ( $inInscricaoAtual );
            $obRCIMUnidadeAutonoma->roRCIMImovel->listarImoveisConsulta( $rsImoveis );

            if ( $rsImoveis->getNumLinhas() < 1  ) {
                //nao encontrada
                $stJs .= 'f.'.$stNomCampo.'.value = "";';
                $stJs .= 'f.'.$stNomCampo.'.focus();';
                $stJs .= "alertaAviso('@Código de inscrição imobiliária inválido. (".$inInscricaoAtual.")','form','erro','".Sessao::getId()."');";
            } else {
                if ( $rsImoveis->getCampo('situacao') == 'Baixado' ) {
                    $stJs .= 'f.'.$stNomCampo.'.value = "";';
                    $stJs .= 'f.'.$stNomCampo.'.focus();';
                       $stJs .= "alertaAviso('@Código de inscrição imobiliária inválido. <b>Imóvel Baixado</b>  (".$inInscricaoAtual.")','form','erro','".Sessao::getId()."');";
                }
            }
        } else {
            $stJs .= 'f.'.$stNomCampo.'.value = "";';
        }

        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "montaParcelamento":
        montaParcelamento();
    break;
    case "somaValoresCreditos":
        somaValoresCreditos(0);
    break;
    case 'validarGrupoCredito':
        include_once( CAM_GT_ARR_MAPEAMENTO."TARRGrupoCredito.class.php" );
        $obTARRGrupoCredito = new TARRGrupoCredito();
        $arGrupo = preg_split( "/[^0-9a-zA-Z]/", $_REQUEST[stChaveGrupo] );
        $stFiltro  = " WHERE \n";
        $stFiltro .= "     grupo_credito.cod_grupo = ".(int) $arGrupo[0]." AND \n";
        $stFiltro .= "     grupo_credito.ano_exercicio = '".$arGrupo[1]."' \n";
        $obErro = $obTARRGrupoCredito->recuperaParametroCalculo($rsGrupoCalculo, $stFiltro);
        if ( !$obErro->ocorreu() ) {
            while ( !$rsGrupoCalculo->eof() ) {
                if ($rsGrupoCalculo->getCampo('calculo') == 'f' ) {
                    include_once( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
                    $obRMONCredito   = new RMONCredito;
                    $obRMONCredito->consultarMascaraCredito();
                    $stMascaraCredito = $obRMONCredito->getMascaraCredito();
                    $arMascaraCredito = explode(".", $stMascaraCredito);
                    for ($inX=0; $inX<4; $inX++) {
                        $arMascaraCredito[$inX] = strlen($arMascaraCredito[$inX]);
                    }
                    $stCredito  = sprintf("%0".$arMascaraCredito[0]."d", $rsGrupoCalculo->getCampo('cod_credito')      );
                    $stCredito .= ".".sprintf("%0".$arMascaraCredito[1]."d", $rsGrupoCalculo->getCampo('cod_especie')  );
                    $stCredito .= ".".sprintf("%0".$arMascaraCredito[2]."d", $rsGrupoCalculo->getCampo('cod_genero')   );
                    $stCredito .= ".".sprintf("%0".$arMascaraCredito[3]."d", $rsGrupoCalculo->getCampo('cod_natureza') );

                    $stErro  = "Crédito ".$stCredito." do grupo de crédito ".$_REQUEST[stChaveGrupo]." não possui fórmula de cálculo cadastrada.";
                    $stErro .= " Cálculo não pode ser efetuado!";
                    $stJs  = "f.inCodGrupo.focus();\n";
                    $stJs .= "alertaAviso('".urlencode($stErro)."','n_erro','erro','".Sessao::getId()."');\n";
                    break;
                }
                $rsGrupoCalculo->proximo();
            }

        } else {
            $stJs .= "alertaAviso('".urlencode($obErro->getDescricao())."','n_erro','erro','".Sessao::getId()."');\n";
        }
    break;
    case 'validarCredito':
         include_once( CAM_GT_ARR_MAPEAMENTO."TARRParametroCalculo.class.php" );
        $obTARRParametroCalculo = new TARRParametroCalculo();
        $arGrupo = preg_split( '[^0-9a-zA-Z]', $_REQUEST[stChaveGrupo] );
        $stFiltro  = " WHERE \n";
        $stFiltro .= "     cod_credito  = ".(int) $arGrupo[0]." AND \n";
        $stFiltro .= "     cod_especie  = ".(int) $arGrupo[1]." AND \n";
        $stFiltro .= "     cod_genero   = ".(int) $arGrupo[2]." AND \n";
        $stFiltro .= "     cod_natureza = ".(int) $arGrupo[3]." \n";
        $obErro = $obTARRParametroCalculo->recuperaTodos($rsParametroCalculo, $stFiltro);
        if ( !$obErro->ocorreu() ) {
            if ( $rsParametroCalculo->eof() ) {
                $stErro  = "Crédito ".$_REQUEST[stChaveGrupo]." não possui fórmula de cálculo cadastrada.";
                $stErro .= " Cálculo não pode ser efetuado!";
                $stJs  = "f.inCodCredito.focus();\n";
                $stJs .= "alertaAviso('".urlencode($stErro)."','n_erro','erro','".Sessao::getId()."');\n";
            }
        } else {
            $stJs .= "alertaAviso('".urlencode($obErro->getDescricao())."','n_erro','erro','".Sessao::getId()."');\n";
        }
    break;
    case "montaReferenciaParcelamentoAjax":
        echo montaParcelamentoBase();
        $obFormulario = new Formulario;
        $obBscContribuinte = new BuscaInner;
        $obBscContribuinte->setId               ( "stContribuinte"          );
        $obBscContribuinte->setRotulo           ( "Contribuinte"            );
        $obBscContribuinte->setTitle            ( "Codigo do contribuinte." );
        $obBscContribuinte->setNull             ( false                     );
        $obBscContribuinte->obCampoCod->setName ("inCodContribuinte"        );
        $obBscContribuinte->obCampoCod->setValue( $inCodContribuinte        );
        $obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor(&#39;buscaContribuinte&#39;);");
        $obBscContribuinte->setFuncaoBusca( "abrePopUp(&#39;".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php&#39;,&#39;frm&#39;,&#39;inCodContribuinte&#39;,&#39;stContribuinte&#39;,&#39;&#39;,&#39;".Sessao::getId()."&#39;,&#39;800&#39;,&#39;450&#39;);" );
        $obFormulario->addComponente ( $obBscContribuinte );
        $obFormulario->montaInnerHTML();
        echo "d.getElementById('spnReferencia').innerHTML = '". $obFormulario->getHTML(). "';";
    break;

    case "validarCalculo":
        /*include_once( CAM_GT_ARR_MAPEAMENTO."TARRGrupoCredito.class.php" );
        $obTARRGrupoCredito = new TARRGrupoCredito();
        $arGrupo = preg_split( '/[^0-9a-zA-Z]/', $_REQUEST['stGrupo'] );
        $stFiltro  = " WHERE \n";
        $stFiltro .= "     grupo_credito.cod_grupo = ".(int) $arGrupo[0]." AND \n";
        $stFiltro .= "     grupo_credito.ano_exercicio = '".$arGrupo[1]."' \n";
        $obErro = $obTARRGrupoCredito->recuperaParametroCalculo($rsGrupoCalculo, $stFiltro);
        if ( !$obErro->ocorreu() ) {
            while ( !$rsGrupoCalculo->eof() ) {
                if ($rsGrupoCalculo->getCampo('calculo') == 'f' ) {
                    include_once( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
                    $obRMONCredito   = new RMONCredito;
                    $obRMONCredito->consultarMascaraCredito();
                    $stMascaraCredito = $obRMONCredito->getMascaraCredito();
                    $arMascaraCredito = explode(".", $stMascaraCredito);
                    for ($inX=0; $inX<4; $inX++) {
                        $arMascaraCredito[$inX] = strlen($arMascaraCredito[$inX]);
                    }
                    $stCredito  = sprintf("%0".$arMascaraCredito[0]."d", $rsGrupoCalculo->getCampo('cod_credito')      );
                    $stCredito .= ".".sprintf("%0".$arMascaraCredito[1]."d", $rsGrupoCalculo->getCampo('cod_especie')  );
                    $stCredito .= ".".sprintf("%0".$arMascaraCredito[2]."d", $rsGrupoCalculo->getCampo('cod_genero')   );
                    $stCredito .= ".".sprintf("%0".$arMascaraCredito[3]."d", $rsGrupoCalculo->getCampo('cod_natureza') );

                    $stErro  = "Crédito ".$stCredito." do grupo de crédito ".$_REQUEST['stGrupo']." não possui fórmula de cálculo cadastrada.";
                    $stErro .= " Cálculo não pode ser efetuado!";
                    $stJs  = "f.inCodGrupo.focus();\n";
                    $stJs .= "alertaAviso('".urlencode($stErro)."','n_erro','erro','".Sessao::getId()."');\n";
                    break;
                }
                $rsGrupoCalculo->proximo();
            }
        } else {
            $stJs .= "alertaAviso('".urlencode($obErro->getDescricao())."','n_erro','erro','".Sessao::getId()."');\n";
        }*/
        $obConexao = new Conexao;

        $arGrupo = explode('/',$_REQUEST['stGrupo']);
        $stGrupo = $arGrupo[0];
        $stExercicio = $arGrupo[1];

        $stSql = " SELECT fn_desativa_calculo('".$stGrupo."','".$stExercicio."');";
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        $stSql = " SELECT arrecadacao.validaCalculosSimulados('".$stGrupo."','".$stExercicio."');";
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        if (!$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso("FMRelatorioExecucao.php"."?stAcao=incluir","Cálculos validados com sucesso ! ","definir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");
        }
    break;
}

if ( $stJs )
    SistemaLegado::executaFrameOculto($stJs);
?>
