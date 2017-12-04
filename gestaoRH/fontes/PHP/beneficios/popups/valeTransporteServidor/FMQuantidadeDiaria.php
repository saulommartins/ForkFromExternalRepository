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
    * Lista do popup de Vale-Tranporte Servidor Detalhar Quantidade Diária
    * Data de Criação: 18/10/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30880 $
    $Name$
    $Author: tiago $
    $Date: 2007-06-28 15:07:38 -0300 (Qui, 28 Jun 2007) $

    * Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"         );

//Define o nome dos arquivos PHP
$stPrograma = "QuantidadeDiaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$obCalendario = new Calendario;
//$inMes     = ( strlen(Sessao::read('inMes')) == 1 ) ? '0'.Sessao::read('inMes') : Sessao::read('inMes');
$inMes     = (strlen($_GET['inCodMes']) == 1) ? '0'.$_GET['inCodMes'] : $_GET['inCodMes'];
//$inAno     = Sessao::read('inAno');
$inAno     = $_GET['inAno'];
$inDiasMes = $obCalendario->retornaUltimoDiaMes($inMes,$inAno);
$dtInicial = "01/".$inMes."/".$inAno;
$dtFinal   = $inDiasMes."/".$inMes."/".$inAno;
$obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
$obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->setCodCalendar( Sessao::read('inCodCalendario') );
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->addFeriadoVariavel();
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->ultimoFeriadoVariavel->setDtInicial($dtInicial);
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->ultimoFeriadoVariavel->setDtFinal($dtFinal);
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->listarFeriados( $rsFeriados );

$arQuantidadeDias = array();
$stJs = "";
for ($inDia=1;$inDia<=$inDiasMes;$inDia++) {
    $inIndex          = $inDia;
    $inDia            = ( strlen($inDia) == 1 ) ? '0'.$inDia : $inDia;
    $stData           = $inDia."/".$inMes."/".$inAno;
    $inDiaSemana      = $obCalendario->retornaDiaSemana($inDia,$inMes,$inAno);
    $boDadosAlterados = false;

    if ( count(Sessao::read('valeDias')) > 0 ) {
        foreach (Sessao::read('valeDias') as $arDadosDia) {
            if ($arDadosDia['stData'] == $stData) {
                $inQuantidade     = $arDadosDia['inQuantidade'];
                $boObrigatorio    = $arDadosDia['boObrigatorio'];

                if ( !empty($inQuantidade) ) {
                    $boDadosAlterados = true;
                }

            }
        }
    }

    switch ($inDiaSemana) {
        case 0:
            $stDiaSemana = "Domingo";
            if ($boDadosAlterados) {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".$inQuantidade."';   \n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".$boObrigatorio."';\n";
            } else {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".Sessao::read('inDomingo')."';   \n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".Sessao::read('boDomingo')."';\n";
            }
        break;
        case 1:
            $stDiaSemana = "Segunda";
            if ($boDadosAlterados) {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".$inQuantidade."';   \n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".$boObrigatorio."';\n";
            } else {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".Sessao::read('inSegunda')."';\n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".Sessao::read('boSegunda')."';\n";
            }
        break;
        case 2:
            $stDiaSemana = "Terça";
            if ($boDadosAlterados) {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".$inQuantidade."';   \n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".$boObrigatorio."';\n";
            } else {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".Sessao::read('inTerca')."';\n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".Sessao::read('boTerca')."';\n";
            }
        break;
        case 3:
            $stDiaSemana = "Quarta";
            if ($boDadosAlterados) {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".$inQuantidade."';   \n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".$boObrigatorio."';\n";
            } else {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".Sessao::read('inQuarta')."';\n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".Sessao::read('boQuarta')."';\n";
            }
        break;
        case 4:
            $stDiaSemana = "Quinta";
            if ($boDadosAlterados) {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".$inQuantidade."';   \n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".$boObrigatorio."';\n";
            } else {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".Sessao::read('inQuinta')."';\n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".Sessao::read('boQuinta')."';\n";
            }
        break;
        case 5:
            $stDiaSemana = "Sexta";
            if ($boDadosAlterados) {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".$inQuantidade."';   \n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".$boObrigatorio."';\n";
            } else {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".Sessao::read('inSexta')."';\n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".Sessao::read('boSexta')."';\n";
            }
        break;
        case 6:
            $stDiaSemana = "Sábado";
            if ($boDadosAlterados) {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".$inQuantidade."';   \n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".$boObrigatorio."';\n";
            } else {
                $stJs .= "f.inQuantidade_".$inIndex.".value = '".Sessao::read('inSabado')."';\n";
                $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".Sessao::read('boSabado')."';\n";
            }
        break;
    }
    $stCaracteristica = "";
    while ( !$rsFeriados->eof() ) {
        if ( $rsFeriados->getCampo('dt_feriado') == $stData ) {
            $stCaracteristica = $rsFeriados->getCampo('tipoferiado');
        }
        $rsFeriados->proximo();
    }
    $rsFeriados->setPrimeiroElemento();
    $arTemp['data']             = $stData;
    $arTemp['dia_semana']       = $stDiaSemana;
    $arTemp['caracteristica']   = $stCaracteristica;
    $arQuantidadeDias[]         = $arTemp;
}

$rsRecordSet = new recordset;
$rsRecordSet->preenche( $arQuantidadeDias );
$obLista = new Lista;
$obLista->setTitulo("Quantidade Diária");
$obLista->setRecordSet( $rsRecordSet );
$obLista->setMostraPaginacao( false );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Dia da Semana" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Característica" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Obrigatório" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Quantidade" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "data" );
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dia_semana" );
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "caracteristica" );
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->commitDado();

$obChkObrigatorio = new CheckBox;
$obChkObrigatorio->setName           ( "boObrigatorio"  );
$obChkObrigatorio->setValue          ( "true"           );
if ($_GET['tipoBusca'] == 'consultarConcessao') {
    $obChkObrigatorio->setDisabled   ( true             );
}
$obLista->addDadoComponente( $obChkObrigatorio );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('[boObrigatorio]');
$obLista->commitDadoComponente();

$obTxtQuantidade = new TextBox;
$obTxtQuantidade->setName                 ( "inQuantidade"                      );
$obTxtQuantidade->setValue                ( $inQuantidade                       );
$obTxtQuantidade->setMaxlength            ( 2                                   );
$obTxtQuantidade->setInteiro			  ( true 								);
if ($_GET['tipoBusca'] == 'consultarConcessao') {
    $obTxtQuantidade->setDisabled         ( true                                );
}
$obLista->addDadoComponente( $obTxtQuantidade );
$obLista->ultimoDado->setCampo('[inQuantidade]');
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDadoComponente();

$obLista->montaHTML();
$stHtml = $obLista->getHTML();
$stHtml = str_replace("\n","",$stHtml);
$stHtml = str_replace("  ","",$stHtml);
$stHtml = str_replace("'","\\'",$stHtml);

$obSpanPopup = new Span;
$obSpanPopup->setId       ( "spnPopup"      );
$obSpanPopup->setValue    ( $stHtml         );

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm           );
$obFormulario->addIFrameOculto      ( "oculto"          );
$obFormulario->obIFrame->setWidth   ( "100%"            );
$obFormulario->obIFrame->setHeight  ( "0"               );
$obFormulario->addSpan              ( $obSpanPopup      );
if ($_GET['tipoBusca'] == 'consultarConcessao') {
    $obBtnFechar = new Button;
    $obBtnFechar->setName               ( 'fechar'                      );
    $obBtnFechar->setValue              ( 'Fechar'                      );
    $obBtnFechar->obEvento->setOnClick  ( "window.close();"             );
    $obFormulario->defineBarra          ( array( $obBtnFechar ) , '', '');
} else {
    $obBtnOk = new Ok;

    $obBtnFechar = new Button;
    $obBtnFechar->setName               ( 'cancelar'                    );
    $obBtnFechar->setValue              ( 'Cancelar'                    );
    $obBtnFechar->obEvento->setOnClick  ( "window.close();"             );
    $obFormulario->defineBarra          ( array( $obBtnOk,$obBtnFechar ) , '', '');
}
$obFormulario->show();

sistemaLegado::executaIFrameOculto($stJs);
?>
