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
    * Pagina de Lista de Contribuinte/Parcelas em Aberto para Parcelamento de Créditos
    * Data de Criação   : 20/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FMParcelarCreditosParcelamento.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.03.20
*/

/*
$Log$
Revision 1.2  2006/09/15 11:16:00  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRParcelamento.class.php"                                                     );

$obRARRParcelamento = new RARRParcelamento ( new RARRCalculo );

//Define o nome dos arquivos PHP
$stPrograma = "ParcelarCreditos";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgForm2   = "FMParcelarCreditosParcelamento.php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$stAcao = 'incluir';

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"] );

$obLblContribuinte = new Label;
$obLblContribuinte->setRotulo ('Contribuinte');
$obLblContribuinte->setTitle ('Contribuinte');
$obLblContribuinte->setValue ( $_REQUEST["stContribuinte"] );

$arContribuinte = explode ( ' - ', $_REQUEST["stContribuinte"] );
$obHdnContribuinte = new Hidden;
$obHdnContribuinte->setName ('inNumCGM');
$obHdnContribuinte->setValue ($arContribuinte[0]);

if ($_REQUEST["inCodInscricaoEconomica"]) {
    $obRARRParcelamento->obRCEMInscricaoEconomica->setInscricaoEconomica ($_REQUEST["inCodInscricaoEconomica"]);

    $obLblInscricaoEconomica = new Label;
    $obLblInscricaoEconomica->setRotulo ('Inscrição Econômica');
    $obLblInscricaoEconomica->setValue ( $_REQUEST['inCodInscricaoEconomica']);

    $obHdnInscricaoEconomica = new Hidden;
    $obHdnInscricaoEconomica->setName ('inCodInscricaoEconomica');
    $obHdnInscricaoEconomica->setValue ( $_REQUEST['inCodInscricaoEconomica'] );

    $filtros++;
}
if ($_REQUEST["inCodInscricaoImobiliaria"]) {
    $obRARRParcelamento->obRCIMImovel->setNumeroInscricao($_REQUEST["inCodInscricaoImobiliaria"]);

    $obLblInscricaoImobiliaria = new Label;
    $obLblInscricaoImobiliaria->setRotulo ('Inscrição Imobiliária');
    $obLblInscricaoImobiliaria->setValue ( $_REQUEST['inCodInscricaoImobiliaria']);

    $obHdnInscricaoImobiliaria = new Hidden;
    $obHdnInscricaoImobiliaria->setName ('inCodInscricaoImobiliaria');
    $obHdnInscricaoImobiliaria->setValue ( $_REQUEST['inCodInscricaoImobiliaria'] );

    $filtros++;
}

if ($_REQUEST['inCodGrupo']) {

    $obLblCodGrupo = new Label;
    $obLblCodGrupo->setRotulo ('Código do Grupo');
    $obLblCodGrupo->setValue ( $_REQUEST['inCodGrupo']);

    $obHdnCodGrupo = new Hidden;
    $obHdnCodGrupo->setName ('inCodGrupo');
    $obHdnCodGrupo->setValue ( $_REQUEST['inCodGrupo'] );

    $filtros++;
}
if ($_REQUEST['inCodCredito']) {

    $obLblCodCredito = new Label;
    $obLblCodCredito->setRotulo ('Código do Crédito');
    $obLblCodCredito->setValue ( $_REQUEST['inCodCredito']);

    $obHdnCodCredito = new Hidden;
    $obHdnCodCredito->setName ('inCodCredito');
    $obHdnCodCredito->setValue ( $_REQUEST['inCodCredito'] );

    $filtros++;
}

$obTxtNumeroParcelas = new TextBox;
$obTxtNumeroParcelas->setRotulo        ('Número de Parcelas');
$obTxtNumeroParcelas->setTitle           ('Número de Parcelas');
$obTxtNumeroParcelas->setName         ('inNumParcelas');
$obTxtNumeroParcelas->setValue         (1);
//$obTxtNumeroParcelas->setMinValue    (1);
//$obTxtNumeroParcelas->setMaxValue   (12);
$obTxtNumeroParcelas->setMaxLength (2);
$obTxtNumeroParcelas->setSize            (4);
$obTxtNumeroParcelas->setNull            ( false );
$obTxtNumeroParcelas->setInteiro        ( true );
$obTxtNumeroParcelas->obEvento->setOnChange("buscaValor('CalculaValorParcelas');");

     $nextmonth = mktime (0, 0, 0, date("m")+1, date("d"),  date("Y"));
     $vencimento =strftime("%d/%m/%Y", $nextmonth);

$obTxtVencimentoParcela = new Data;
$obTxtVencimentoParcela->setRotulo     ('Vencimento da Primeira Parcela');
$obTxtVencimentoParcela->setTitle         ('Data de Vencimento da Primeira Parcela');
$obTxtVencimentoParcela->setName      ('dtVencimento');
$obTxtVencimentoParcela->setNull         ( false );
$obTxtVencimentoParcela->setValue      ( $vencimento );
$obTxtVencimentoParcela->obEvento->setOnChange("buscaValor('CalculaValorParcelas');");

    $cont = 0;
    $contParcelas = 0;
    foreach ($_REQUEST as $key => $valor) {
        $cont++;
        if ($cont > 5 + $_REQUEST['inNumFiltros']) {
            $arrayParcelas[$contParcelas] = $valor;
            $contParcelas++;
        }
    }

// Monta o ARray e soma valores de juros, multa e total apurado
$somaJuros = 0.00;
$somaMulta = 0.00;
$totalApurado = 0.00;
$arrayParcelamento = Array();
$num_parcelas = count ( $arrayParcelas );
$cont = 0;
while ($cont < $num_parcelas) {
    $arrayP = explode ('-', $arrayParcelas[$cont] );

    if ($arrayP[0] != 'on') {

        $arrayParcelamento[$cont]['numeracao'] = $arrayP[0];
        $arrayParcelamento[$cont]['exercicio'] = $arrayP[1];
        $arrayParcelamento[$cont]['numeracao_migracao'] = $arrayP[2];
        $arrayParcelamento[$cont]['prefixo'] = $arrayP[3];
        $arrayParcelamento[$cont]['info_parcela'] = $arrayP[4].'/'.$arrayP[5];
        $arrayParcelamento[$cont]['vencimento'] = $arrayP[6];
        $arrayParcelamento[$cont]['valortotal'] = $arrayP[7];
        $arrayParcelamento[$cont]['cod_parcela'] = $arrayP[8];
        $arrayParcelamento[$cont]['cod_lancamento'] = $arrayP[9];

        $somaJuros  += str_replace(",",".",$arrayP[10]);
        $somaMulta += str_replace(",",".",$arrayP[11]);
        $totalApurado += str_replace(",",".",$arrayP[10]) + str_replace(",",".",$arrayP[11]) + str_replace(",",".",$arrayP[12]);

    }

    $cont++;
}

Sessao::write( 'arParcelamento', $arrayParcelamento );

$obLblJuros = new Label;
$obLblJuros->setRotulo ('Juros sobre as parcelas vencidas');
$obLblJuros->setValue ( "R$ ".str_replace(".",",",$somaJuros )) ;

$obLblMulta = new Label;
$obLblMulta->setRotulo ('Multa sobre as parcelas vencidas');
$obLblMulta->setValue ( "R$ ".str_replace(".",",",$somaMulta )) ;

$obLblTotalApurado = new Label;
$obLblTotalApurado->setRotulo ('TotalApurado');
$obLblTotalApurado->setValue (  "R$ ".str_replace(".",",",$totalApurado) );

$obHdnTotalApurado = new Hidden;
$obHdnTotalApurado->setName ('flTotalApurado');
$obHdnTotalApurado->setValue ($totalApurado);

$obLblValorPorParcela = new Label;
$obLblValorPorParcela->setRotulo ('Valor Por Parcela');
$obLblValorPorParcela->setId ('stValorPorParcela');
$obLblValorPorParcela->setValue (  "R$ ".str_replace(".",",",$totalApurado) );

$obHdnValorPorParcela = new Hidden;
$obHdnValorPorParcela->setName ('flValorPorParcela');
$obHdnValorPorParcela->setValue ( $totalApurado );

Sessao::write( 'filtro', "&inInscricaoImobiliaria=".$_REQUEST["inInscricaoImobiliaria"]."&inInscricaoEconomica=".$_REQUEST["inInscricaoEconomica"]."&inCodContribuinte=".$_REQUEST["inCodContribuinte"]."&stExercicio=".$_REQUEST["stExercicio"]."" );
//passa filtro pra sessao

//$rsLista->addFormatacao( "valortotal"   , "NUMERIC_BR" );
$x = new Recordset;
$x->preenche ( $arrayParcelamento );
//MONTA LISTA DE IMOVEIS
$obLista = new Lista;
$obLista->setMostraPaginacao    ( false                      );
$obLista->setTotaliza       ( "valortotal, Valor Total (R$),center,6"  );
$obLista->setTitulo             ( "Lista de Parcelas Originais"  );
$obLista->setRecordSet( $x );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Numeração");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Numeração Migrada");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Parcela");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Vencimento Original");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor (R$)");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numeracao]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numeracao_migracao]/[prefixo]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[info_parcela]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[vencimento]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[valortotal]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

    $obLista->montaHTML                    (                        );
    $stHTML =  $obLista->getHtml       (                        );
    $stHTML = str_replace                  ( "\n","",$stHTML        );
    $stHTML = str_replace                  ( "  ","",$stHTML        );
    $stHTML = str_replace                  ( "'","\\'",$stHTML      );

$obSpanLIsta = new Span;
$obSpanLIsta->setiD ('obSpanLIsta');
$obSpanLIsta->setValue ( $stHTML );

$obSpanNovasParcelas = new Span;
$obSpanNovasParcelas->setiD ('obSpanNovasParcelas');
//$obSpanNovasParcelas->setValue ( $stHTML );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao            );
$obFormulario->addHidden( $obHdnCtrl            );
$obFormulario->addHidden ( $obHdnTotalApurado );
$obFormulario->addHidden ( $obHdnValorPorParcela );
$obFormulario->addHidden ( $obHdnContribuinte );

$obFormulario->addComponente ( $obLblContribuinte );

if ($_REQUEST["inCodInscricaoEconomica"]) {
    $obFormulario->addComponente ($obLblInscricaoEconomica );
    $obFormulario->addHidden (  $obHdnInscricaoEconomica );
}
if ($_REQUEST["inCodInscricaoImobiliaria"]) {
    $obFormulario->addComponente ($obLblInscricaoImobiliaria );
    $obFormulario->addHidden (  $obHdnInscricaoImobiliaria );
}
if ($_REQUEST['inCodGrupo']) {
    $obFormulario->addComponente ($obLblCodGrupo );
    $obFormulario->addHidden (  $obHdnCodGrupo );
}
if ($_REQUEST['inCodCredito']) {
    $obFormulario->addComponente ($obLblCodCredito );
    $obFormulario->addHidden (  $obHdnCodCredito );
}

$obFormulario->addSpan ( $obSpanLIsta );

//$obFormulario->addComponente ( $obTxtDescontoParcela );

//$obFormulario->agrupaComponentes (  array ($obChckFormaDescontoP, $obChckFormaDescontoV) );

$obFormulario->addTitulo             ( "Valores"                 );
$obFormulario->addComponente ( $obLblJuros );
$obFormulario->addComponente ( $obLblMulta );
$obFormulario->addComponente ( $obLblTotalApurado );

$obFormulario->addTitulo             ( "Parcelamento"               );
$obFormulario->addComponente ( $obTxtNumeroParcelas    );
$obFormulario->addComponente ( $obLblValorPorParcela     );
$obFormulario->addComponente ( $obTxtVencimentoParcela);

$obFormulario->addSpan ( $obSpanNovasParcelas );

$obFormulario->ok();
$obFormulario->show();

$stJs .= 'f.inNumParcelas.focus();';
$stJs .= "buscaValor('CalculaValorParcelas');";
sistemaLegado::executaFrameOculto ( $stJs );

//sistemaLegado::executaFrameOculto("buscaValor('CalculaValorParcelas');");
?>
