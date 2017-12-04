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
* Página de Formulario de consulta dos registros dos eventos da complementar.
* Data de Criação: 15/02/2006

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

* Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoRegistroEvento.class.php"          );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php" );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"     );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php" );

//Pega os parametros pelo POST ou pelo GET
$inRegistro        = $_POST["inContrato"]        ? $_POST["inContrato"]        : $_GET["inContrato"];
$inCodMes          = $_POST["inCodMes"]          ? $_POST["inCodMes"]          : $_GET["inCodMes"];
$inAno             = $_POST["inAno"]             ? $_POST["inAno"]             : $_GET["inAno"];
$inCodComplementar = $_POST["inCodComplementar"] ? $_POST["inCodComplementar"] : $_GET["inCodComplementar"];

$stMeses = array(1 =>"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
$stMes = $stMeses[$inCodMes];

if ( strlen($inCodMes) < 2 ) {
    $inCodMes = "0".$inCodMes;
}
if($inRegistro != ''){
    $stFiltro = ' AND registro = '.$inRegistro;
} else {
     $stFiltro ='';
}

//Consulta o CGM a partir do registro
$obTPessoalContrato = new TPessoalContrato();
$obTPessoalContrato->recuperaCgmDoRegistro($rsContratoServidor, $stFiltro);

//Define a string numcgm - nome_do_servidor
$stCGM = $rsContratoServidor->getCampo("numcgm") ." - ". $rsContratoServidor->getCampo("nom_cgm");

//Define o código do contrato do servidor
$inContrato = $rsContratoServidor->getCampo("cod_contrato");

//Define o OBJETO label para informar a competencia
$obLblCompetencia = new Label;
$obLblCompetencia->setRotulo ( 'Complementar - Competência'        );
$obLblCompetencia->setName   ( 'stLblCompetencia'   );
$obLblCompetencia->setId     ( 'stLblCompetencia'   );
$obLblCompetencia->setValue  ( $inCodComplementar." - ".$stMes."/".$inAno );

//Define o OBJETO label para informar o contrato
$obLblContrato = new Label;
$obLblContrato->setRotulo ( 'Matrícula'      );
$obLblContrato->setName   ( 'inLblContrato' );
$obLblContrato->setId     ( 'inLblContrato' );
$obLblContrato->setValue  ( $inRegistro     );

//Define o OBJETO label para informar o CGM
$obLblCGM = new Label;
$obLblCGM->setRotulo ( 'CGM'      );
$obLblCGM->setName   ( 'inLblCGM' );
$obLblCGM->setId     ( 'inLblCGM' );
$obLblCGM->setValue  ( $stCGM     );

$obFormulario = new Formulario;
$obFormulario->addTitulo    ( "Dados da Matrícula Servidor" );
$obFormulario->addComponente( $obLblCompetencia );
$obFormulario->addComponente( $obLblContrato    );
$obFormulario->addComponente( $obLblCGM         );
$obFormulario->addTitulo    ( "Dados do Evento" );
$obFormulario->show();

//Define os objetos e faz a consulta
$obPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obPeriodoMovimentacao->setDtFinal( $inAno."-".$inCodMes );
$obPeriodoMovimentacao->listarPeriodoMovimentacao( $rsPeriodoMovimentacao );
$obPeriodoMovimentacao->setCodPeriodoMovimentacao( $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao") );

$obPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
$obPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->setCodContrato( $inContrato );

$obFolhaComplementar = new RFolhaPagamentoFolhaComplementar( $obPeriodoMovimentacao );
$obFolhaComplementar->setCodComplementar($inCodComplementar);

$obRegistroEventoComplementar = new RFolhaPagamentoRegistroEventoComplementar( $obFolhaComplementar );
$obRegistroEventoComplementar->listarRegistroEventoComplementar( $rsRegistroEventoComplementar );

//Variaveis utilizadas para montar os record set das listas
$arEventos      = array();
$arBaseCalculo  = array();
$inCountEventos = 0;
$inCountBase    = 0;

//Algoritmo para separar o record set em 3 para as listas.
$rsRegistroEventoComplementar->setPrimeiroElemento();
while ( !$rsRegistroEventoComplementar->eof() ) {
    if ( $rsRegistroEventoComplementar->getCampo("natureza") == "B" ) {
        $arBaseCalculo[$inCountBase]["codigo"]     = $rsRegistroEventoComplementar->getCampo("codigo");
        $arBaseCalculo[$inCountBase]["descricao"]  = $rsRegistroEventoComplementar->getCampo("descricao");
        $arBaseCalculo[$inCountBase]["valor"]      = $rsRegistroEventoComplementar->getCampo("valor");
        $arBaseCalculo[$inCountBase]["quantidade"] = $rsRegistroEventoComplementar->getCampo("quantidade");
//         if ( $rsRegistroEvento->getCampo("automatico") == "t" )
//             $arBaseCalculo[$inCountBase]["automatico"] = "Sim";
//         else
//             $arBaseCalculo[$inCountBase]["automatico"] = "Não";

        $inCountBase = $inCountBase + 1;
    } else {
        $arEventos[$inCountEventos]["codigo"]     = $rsRegistroEventoComplementar->getCampo("codigo");
        $arEventos[$inCountEventos]["descricao"]  = $rsRegistroEventoComplementar->getCampo("descricao");
        $arEventos[$inCountEventos]["valor"]      = $rsRegistroEventoComplementar->getCampo("valor");
        $arEventos[$inCountEventos]["quantidade"] = $rsRegistroEventoComplementar->getCampo("quantidade");
        $arEventos[$inCountEventos]["parcela"] = $rsRegistroEventoComplementar->getCampo("parcela");
        if (array_key_exists('parcela', $arEventos[$inCountEventos]) and $arEventos[$inCountEventos]['parcela'] != '') {
            $arEventos[$inCountEventos]['quantidade'] = number_format($rsRegistroEventoComplementar->getCampo('quantidade')).'/'.$rsRegistroEventoComplementar->getCampo('parcela');
        }
        if ( $rsRegistroEventoComplementar->getCampo("automatico") == "t" ) {
            $arEventos[$inCountEventos]["automatico"] = "Sim";
        } else {
            $arEventos[$inCountEventos]["automatico"] = "Não";
        }
        $inCountEventos = $inCountEventos + 1;
    }
    $rsRegistroEventoComplementar->proximo();
}

//Monta as 3 listas
for ($i=0 ; $i<=1 ; $i++) {
    $obLista = new Lista;
    $obLista->setMostraPaginacao    ( false );

    switch ($i) {
        case 0:
            $rsEventos = new Recordset;
            $rsEventos->preenche( $arEventos );

            $obLista->setRecordSet( $rsEventos );
            $obLista->setTitulo   ("Eventos Cadastrados");
        break;
        case 1:
            $rsBaseCalculo = new Recordset;
            $rsBaseCalculo->preenche( $arBaseCalculo );

            $obLista->setRecordSet( $rsBaseCalculo );
            $obLista->setTitulo   ("Base de Cálculo");
        break;
    }

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Evento" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Quantidade" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

//     $obLista->addCabecalho();
//     $obLista->ultimoCabecalho->addConteudo( "Automático" );
//     $obLista->ultimoCabecalho->setWidth( 20 );
//     $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "codigo" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->commitDado();

//     $obLista->addDado();
//     $obLista->ultimoDado->setAlinhamento("CENTRO");
//     $obLista->ultimoDado->setCampo( "automatico" );
//     $obLista->commitDado();

    $obLista->show();
}

$obBtnFechar = new Button;
$obBtnFechar->setName                    ( "btnFechar" );
$obBtnFechar->setValue                   ( "Fechar"    );
$obBtnFechar->setTipo                    ( "button"    );
$obBtnFechar->obEvento->setOnClick       ( "window.parent.window.close();"  );

$stLink    = CAM_FW_POPUPS."relatorio/OCRelatorio.php?".Sessao::getId()."&stCaminho=".CAM_GRH_FOL_INSTANCIAS."relatorio/OCRelatorioEventoComplementarPorContrato.php";
$stFiltros = "&inContrato=".$inRegistro."&inCodMes=".$inCodMes."&inAno=".$inAno."&inCodComplementar=".$inCodComplementar;

$obBtnImprimir = new Button;
$obBtnImprimir->setName                    ( "btnImprimir"      );
$obBtnImprimir->setValue                   ( "Imprimir"         );
$obBtnImprimir->setTipo                    ( "button"           );
$obBtnImprimir->obEvento->setOnClick       ( "window.parent.window.frames['oculto'].location='".$stLink.$stFiltros."';" );

$botoesForm = array ( $obBtnFechar , $obBtnImprimir );

$obFormulario = new Formulario;
$obFormulario->defineBarra ( $botoesForm );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
