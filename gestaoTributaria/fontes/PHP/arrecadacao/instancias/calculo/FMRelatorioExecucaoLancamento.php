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
  * Página de Formulario para Relatorio de dados do lançamento realizado
  * Data de criação : 06/07/2006

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Fernando Piccini Cercato

    * $Id: FMRelatorioExecucaoLancamento.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.05
**/

/*
$Log$
Revision 1.12  2006/12/08 10:08:07  dibueno
Bug #7588#

Revision 1.11  2006/12/06 19:03:34  dibueno
Bug #7590#

Revision 1.10  2006/11/27 15:38:22  dibueno
Bug #7588#

Revision 1.9  2006/11/23 17:10:11  dibueno
Bug #7590#

Revision 1.8  2006/11/02 12:18:28  dibueno
Bug #7289#
Bug #7290#

Revision 1.7  2006/09/21 17:32:26  domluc
Lancamento Geral e Correções na Exibição dos Resumos pós calculo

Revision 1.6  2006/09/15 10:57:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterCalculos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PRManterCalculo.php";
$pgOcul          = "OCManterCalculo.php";
$pgJs            = "JSManterCalculo.js";

include_once( $pgJs );
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$stDescCredito = "";
if ($_REQUEST["inCodCredito"]) {
    $obRMONCredito = new RMONCredito;

    $arCodCredito = explode(".", $_REQUEST["inCodCredito"]);
    $obRMONCredito->setCodCredito  ( $arCodCredito[0] );
    $obRMONCredito->setCodEspecie  ( $arCodCredito[1] );
    $obRMONCredito->setCodGenero   ( $arCodCredito[2] );
    $obRMONCredito->setCodNatureza ( $arCodCredito[3] );
    $obRMONCredito->consultarCredito();
    $stDescCredito = $_REQUEST["inCodCredito"]." - ".$obRMONCredito->getDescricao();

    $numeroCreditos = 1;
}

$stDescGrupo = "";
if ($_REQUEST["inCodGrupo"]) {
    list($inCodGrupo,$inExercicioGrupo) = explode ( '/' , $_REQUEST["inCodGrupo"] );
    $obRARRGrupo = new RARRGrupo;
    $obRARRGrupo->setCodGrupo   ( $inCodGrupo);
    $obRARRGrupo->setExercicio  ( $inExercicioGrupo);
    $obRARRGrupo->consultarGrupo();
    $stDescGrupo = $obRARRGrupo->getCodGrupo()."/".$obRARRGrupo->getExercicio()." -  ".$obRARRGrupo->getDescricao();

    $obRARRGrupo->listarCreditos( $rsCreditosGrupo );
    $numeroCreditosGrupo = $rsCreditosGrupo->getNumLinhas();
} else { // se for calculo de credito
    $numeroCreditosGrupo =1;
}

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obLblGrupoCredito = new Label;
$obLblGrupoCredito->setRotulo   ( "Grupo de Créditos"   );
$obLblGrupoCredito->setValue    ( $stDescGrupo          );
$obLblGrupoCredito->setName     ( "lblGrupo"            );

$obLblCredito = new Label;
$obLblCredito->setRotulo   ( "Crédito"              );
$obLblCredito->setValue    ( $stDescCredito         );
$obLblCredito->setName     ( "lblCredito"           );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget ( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_ARR_INSTANCIAS."calculo/OCRelatorioLancamento.php" );

//se for calculo de credito mostrar credito
//se for calculo de grupo de credito mostrar grupo de credito

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo ( "Dados do Lançamento" );

if ($stDescGrupo)
    $obFormulario->addComponente ( $obLblGrupoCredito );

if ($stDescCredito)
    $obFormulario->addComponente ( $obLblCredito );

#echo '<h2>Lancamentos Cods</h2>';
//exit;

set_time_limit(0);
$obRARRLancamento = new RARRLancamento( new RARRCalculo );

if ( Sessao::read( 'lancamentos_cods' ) )
    $arCodLancamentos = explode ( ',', substr(Sessao::read( 'lancamentos_cods' ), 0, -1) );

    $contTodosLancamentos = count( $arCodLancamentos );

if ($contTodosLancamentos > 0 && $contTodosLancamentos < 1000) {
    #echo 'Calculos: '.$contTodosLancamentos; exit;
    $contArrayLancamento = 0;
    $cont = 0;
    $arLancamento = array();
    $limitador = 1000;
    $stLinha = null;
    while ($cont < $contTodosLancamentos) {

        $boMudouLinha = false;
        $stLinha .= $arCodLancamentos[$cont].", ";

        #$arLancamento
        if ( (($cont+1) % $numeroCreditosGrupo == 0) && $cont > $limitador ) {
            $limitador += 1000;
            $boMudouLinha = true;
        }
        if ( $boMudouLinha || ( ($cont+1) == $contTodosLancamentos) ) {
            $stLinha = substr ( $stLinha, 0, (strlen( $stLinha ) -2) );
            $arLancamento[$contArrayLancamento] = $stLinha;
            $stLinha = null;
            $contArrayLancamento++;
        }

        $cont++;
    }

    $cont = 0;
    $arTempLancamentos = array();
    while ( $cont < count ( $arLancamento ) ) {

        $stCodLancamentos = $arLancamento[$cont];

        $stCodLancamentos = Sessao::read( 'lancamentos_cods' );
        $stCodLancamentos = substr ( $stCodLancamentos, 0, strlen ($stCodLancamentos) -1);
        $obRARRLancamento->setCodLancamento( $stCodLancamentos );
        #echo 'Lancamento_cods: '.$stCodLancamentos;
        //$obRARRLancamento->listarRelatorioLancamento( $rsLancamentos );
        if ( strlen( $stCodLancamentos ) > 0 )
            $obRARRLancamento->listarRelatorioLancamentoGeral( $rsLancamentos );

        foreach ($rsLancamentos->arElementos as $valor) {
            $arTempLancamentos[] = $valor;
        }

        $cont++;
    }

    $rsLancamentos->preenche ( $arTempLancamentos );

    while ( !$rsLancamentos->eof() ) {
        $rsLancamentos->setCampo ('valor', number_format ($rsLancamentos->getCampo('valor'), 2, ',', '.' ) );
        $rsLancamentos->proximo();
    }
} else {
    $rsLancamentos = new RecordSet;
}

    if ( Sessao::read( 'lancados' ) > 0 ) {
        $stFrase = $contTodosLancamentos." lançamentos realizados com sucesso!";
    }else
    if ( $rsLancamentos->getNumLinhas() > 0 ) {
        $numLancamentos = $rsLancamentos->getNumLinhas();
        $stFrase = $numLancamentos. " lançamentos realizados com sucesso!";
    } else {
        $numLancamentos = 0;
        $stFrase = "Nenhum lançamento realizado";
    }

    $obLblTerminoLancamento = new Label;
    $obLblTerminoLancamento->setValue  ( $stFrase );
    $obLblTerminoLancamento->setRotulo ( "Situação:" );
    $obLblTerminoLancamento->setTitle  ( "Situação do Lançamento" );

    $obFormulario->addComponente ( $obLblTerminoLancamento );

    $obFormulario->show();

    $rsLancamentos->setPrimeiroElemento();

if ( !Sessao::read( 'lancados' ) && ($rsLancamentos->getNumLinhas() < 1000 )) {

    $obListaLancamentos = new Lista;
    $obListaLancamentos->setRecordSet          ( $rsLancamentos        );
    $obListaLancamentos->setTitulo             ( "Registros de Lançamento"  );
    $obListaLancamentos->setMostraPaginacao    ( false                      );

    $obListaLancamentos->addCabecalho();
    $obListaLancamentos->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaLancamentos->ultimoCabecalho->setWidth( 5 );
    $obListaLancamentos->commitCabecalho();

    $obListaLancamentos->addCabecalho();
    $obListaLancamentos->ultimoCabecalho->addConteudo("Código");
    $obListaLancamentos->ultimoCabecalho->setWidth( 3 );
    $obListaLancamentos->commitCabecalho();

    $obListaLancamentos->addCabecalho();
    $obListaLancamentos->ultimoCabecalho->addConteudo("Usuário");
    $obListaLancamentos->commitCabecalho();

    $obListaLancamentos->addCabecalho();
    $obListaLancamentos->ultimoCabecalho->addConteudo("Vencimento 1ª Parcela Única");
    $obListaLancamentos->ultimoCabecalho->setWidth( 14 );
    $obListaLancamentos->commitCabecalho();

    $obListaLancamentos->addCabecalho();
    $obListaLancamentos->ultimoCabecalho->addConteudo("Total de Parcelas");
    $obListaLancamentos->ultimoCabecalho->setWidth( 3 );
    $obListaLancamentos->commitCabecalho();

    $obListaLancamentos->addCabecalho();
    $obListaLancamentos->ultimoCabecalho->addConteudo("Valor Lançado");
    $obListaLancamentos->ultimoCabecalho->setWidth( 10 );
    $obListaLancamentos->commitCabecalho();

    $obListaLancamentos->addDado();
    $obListaLancamentos->ultimoDado->setCampo       ( "cod_lancamento" );
    $obListaLancamentos->ultimoDado->setAlinhamento ( "ESQUERDA" );
    $obListaLancamentos->commitDado();

    $obListaLancamentos->addDado();
    $obListaLancamentos->ultimoDado->setCampo       ( "[numcgm] - [nom_cgm]" );
    $obListaLancamentos->ultimoDado->setAlinhamento ( "ESQUERDA" );
    $obListaLancamentos->commitDado();

    $obListaLancamentos->addDado();
    $obListaLancamentos->ultimoDado->setCampo       ( "vencimento" );
    $obListaLancamentos->ultimoDado->setAlinhamento ( "CENTRO" );
    $obListaLancamentos->commitDado();

    $obListaLancamentos->addDado();
    $obListaLancamentos->ultimoDado->setCampo       ( "total_parcelas" );
    $obListaLancamentos->ultimoDado->setAlinhamento ( "CENTRO" );
    $obListaLancamentos->commitDado();

    $obListaLancamentos->addDado();
    $obListaLancamentos->ultimoDado->setCampo       ( "valor" );
    $obListaLancamentos->ultimoDado->setAlinhamento ( "DIREITA" );
    $obListaLancamentos->commitDado();

    $obListaLancamentos->show();
}

$obButtonRelatorio = new Button;
$obButtonRelatorio->setName  ( "Relatorio" );
$obButtonRelatorio->setValue ( "Relatório" );
//$obButtonRelatorio->obEvento->setOnClick( "Salvar();");
$obButtonRelatorio->obEvento->setOnClick( "document.frm2.submit();");

if ( ( Sessao::read( 'lancados' ) > 0 ) || ( $rsLancamentos->getNumLinhas() > 0 ) ) {
    $obForm->setName    ("frm2");
    $obFormulario = new Formulario;
    $obFormulario->addForm ($obForm);
    $obFormulario->addHidden     ( $obHdnCaminho            );
    $obFormulario->defineBarra( array( $obButtonRelatorio), "left", "" );
    $obFormulario->show();
}

?>
