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
    * Pagina de Lista de Parcelas em Aberto para Parcelamento de Créditos
    * Data de Criação   : 20/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FMParcelarCreditos.php 63839 2015-10-22 18:08:07Z franver $

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
include_once( CAM_GT_ARR_NEGOCIO."RARRParcelamento.class.php"                                                   );

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

// instancia regra de lancamento

$obRARRParcelamento->obRCgm->setNumCgm( $_REQUEST["inCodContribuinte"] );
$obRARRParcelamento->obRCgm->consultar( $rsCGM );
$num_cgm = $obRARRParcelamento->obRCgm->getNumCGM();
$nom_cgm = $obRARRParcelamento->obRCgm->getNomCGM();

$filtros = 1;
if ($_REQUEST["inInscricaoEconomica"]) {
    $obRARRParcelamento->obRCEMInscricaoEconomica->setInscricaoEconomica ($_REQUEST["inInscricaoEconomica"]);
    $obRARRParcelamento->obRCEMInscricaoEconomica->consultarNomeInscricaoEconomica($rsInscricao);

    $obLblInscricaoEconomica = new Label;
    $obLblInscricaoEconomica->setRotulo ('Inscrição Econômica');
    $obLblInscricaoEconomica->setValue ( $_REQUEST['inInscricaoEconomica']. ' - '. $rsInscricao->getCampo("nom_cgm") );

    $obHdnInscricaoEconomica = new Hidden;
    $obHdnInscricaoEconomica->setName ('inCodInscricaoEconomica');
    $obHdnInscricaoEconomica->setValue ( $_REQUEST['inInscricaoEconomica']. ' - '. $rsInscricao->getCampo("nom_cgm") );

    $filtros++;
}
if ($_REQUEST["inInscricaoImobiliaria"]) {
    $obRARRParcelamento->obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);
    $obRARRParcelamento->obRCIMImovel->listarImoveisConsulta( $rsImoveis );

    $obLblInscricaoImobiliaria = new Label;
    $obLblInscricaoImobiliaria->setRotulo ('Inscrição Imobiliária');
    $obLblInscricaoImobiliaria->setValue ( $_REQUEST['inInscricaoImobiliaria'].' - '.$rsImoveis->getCampo("endereco") );

    $obHdnInscricaoImobiliaria = new Hidden;
    $obHdnInscricaoImobiliaria->setName ('inCodInscricaoImobiliaria');
    $obHdnInscricaoImobiliaria->setValue ( $_REQUEST['inInscricaoImobiliaria'].' - '.$rsImoveis->getCampo("endereco") );

    $filtros++;
}

if ($_REQUEST['inCodGrupo']) {

    include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"  );

    $obRARRGrupo = new RARRGrupo;
    $obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
    $obRARRGrupo->consultarGrupo();

    $inCodGrupo     = $obRARRGrupo->getCodGrupo () ;
    $stDescricao    = $obRARRGrupo->getDescricao() ;
    $inCodModulo    = $obRARRGrupo->getCodModulo() ;
    $stExercicio    = $obRARRGrupo->getExercicio() ;

    $obLblCodGrupo = new Label;
    $obLblCodGrupo->setRotulo ('Código do Grupo');
    $obLblCodGrupo->setValue ( $_REQUEST['inCodGrupo'] . ' - '. $stDescricao);

    $obHdnCodGrupo = new Hidden;
    $obHdnCodGrupo->setName ('inCodGrupo');
    $obHdnCodGrupo->setValue ( $_REQUEST['inCodGrupo'].' - '. $stDescricao );

    $filtros++;
}
if ($_REQUEST['inCodCredito']) {

    include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
    $obRMONCredito = new RMONCredito;

    $arValores = explode('.',$_REQUEST["inCodCredito"]);
    // array [0]> cod_credito [1]> cod_especie [2]> cod_genero [3]> cod_natureza
    $obRMONCredito->setCodCredito  ($arValores[0]);
    $obRMONCredito->setCodEspecie  ($arValores[1]);
    $obRMONCredito->setCodGenero   ($arValores[2]);
    $obRMONCredito->setCodNatureza ($arValores[3]);

    $obRMONCredito->consultarCredito();

    $inCodCredito = $obRMONCredito->getCodCredito();
    $stDescricao = $obRMONCredito->getDescricao() ;

    $obLblCodCredito = new Label;
    $obLblCodCredito->setRotulo ('Código do Crédito');
    $obLblCodCredito->setValue ( $inCodCredito .' - '. $stDescricao );

    $obHdnCodCredito = new Hidden;
    $obHdnCodCredito->setName ('inCodCredito');
    $obHdnCodCredito->setValue ( $inCodCredito .' - '. $stDescricao );

    $filtros++;
}

$obRARRParcelamento->listarParcelamentoConsulta($rsLista);

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"] );

$obHdnFiltros = new Hidden;
$obHdnFiltros->setName ('inNumFiltros');
$obHdnFiltros->setValue ( $filtros );

$obLblContribuinte = new Label;
$obLblContribuinte->setName ('obLblContribuinte');
$obLblContribuinte->setRotulo ('Contribuinte');
$obLblContribuinte->setValue ( $num_cgm. ' - ' .$nom_cgm );

$obHdnContribuinte = new Hidden;
$obHdnContribuinte->setName ('stContribuinte');
$obHdnContribuinte->setValue ($num_cgm. ' - ' .$nom_cgm);

//passa filtro pra sessao
Sessao::write( 'filtro', "&inInscricaoImobiliaria=".$_REQUEST["inInscricaoImobiliaria"]."&inInscricaoEconomica=".$_REQUEST["inInscricaoEconomica"]."&inCodContribuinte=".$_REQUEST["inCodContribuinte"]."&stExercicio=".$_REQUEST["stExercicio"]."&stNomeContribuinte=".$_REQUEST['stNomeContribuinte'] );

$rsLista->addFormatacao( "valor"   , "NUMERIC_BR" );
$rsLista->addFormatacao( "juros"   , "NUMERIC_BR" );
$rsLista->addFormatacao( "multa"   , "NUMERIC_BR" );
$rsLista->addFormatacao( "valortotal"   , "NUMERIC_BR" );

//MONTA LISTA DE IMOVEIS
$obLista = new Lista;
$obLista->setMostraPaginacao    ( false                      );
$obLista->setTitulo             ( "Registros de Parcelas para Parcelamento"  );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Numeração");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Parcela");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Contribuinte");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Origem da Cobrança");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor (R$)");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numeracao]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[info_parcela]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->ultimoDado->setAlinhamento( 'ESQ' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[inscricao]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[origem]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[valortotal]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

    $obChkReemitir = new Checkbox;
    $obChkReemitir->setName                          ( "boReemitir"                                );
    $obChkReemitir->setValue                          ( "[numeracao]-[exercicio]-[numeracao_migracao]-[prefixo]-[nr_parcela]-[total_parcelas]-[vencimento]-[valortotal]-[cod_parcela]-[cod_lancamento]-[juros]-[multa]-[valor]" );

    $obLista->addDadoComponente                 ( $obChkReemitir         );
    $obLista->ultimoDado->setAlinhamento     ( 'CENTRO'                    );
    $obLista->ultimoDado->setCampo              ( "reemitir"                   );
    $obLista->commitDadoComponente           (                                   );

    $obLista->setMostraSelecionaTodos          ( true                           );

    $obLista->montaHTML                  ();
    $stHTML =  $obLista->getHtml     ();
    $stHTML = str_replace                  ( "\n","",$stHTML        );
    $stHTML = str_replace                  ( "  ","",$stHTML        );
    $stHTML = str_replace                  ( "'","\\'",$stHTML      );

$obSpanLIsta = new Span;
$obSpanLIsta->setiD ('obSpanLIsta');
$obSpanLIsta->setValue ( $stHTML );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgForm2 );
$obForm->setTarget( "telaPrincipal" );
//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden ( $obHdnAcao            );
$obFormulario->addHidden ( $obHdnCtrl               );
$obFormulario->addHidden ( $obHdnFiltros           );
$obFormulario->addHidden ( $obHdnContribuinte );
$obFormulario->addComponente ( $obLblContribuinte );
if ($_REQUEST["inInscricaoEconomica"]) {
    $obFormulario->addComponente ($obLblInscricaoEconomica );
    $obFormulario->addHidden (  $obHdnInscricaoEconomica );
}
if ($_REQUEST["inInscricaoImobiliaria"]) {
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

$obFormulario->Cancelar();
$obFormulario->show();

?>
