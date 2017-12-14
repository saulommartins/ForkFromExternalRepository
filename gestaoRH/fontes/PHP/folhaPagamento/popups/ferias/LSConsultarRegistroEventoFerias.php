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
* Página de Formulario de consulta dos registros dos eventos Ferias.
* Data de Criação: 26/07/2006

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

* Casos de uso: uc-04.05.41
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoFerias.class.php"          );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php" );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"     );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php" );

//Pega os parametros tanto do POST quanto do GET
$inRegistro    = $_POST["inContrato"]    ? $_POST["inContrato"]    : $_GET["inContrato"];
$inCodMes      = $_POST["inCodMes"]      ? $_POST["inCodMes"]      : $_GET["inCodMes"];
$inAno = $_POST["inAno"]         ? $_POST["inAno"]         : $_GET["inAno"];

if ( strlen($inCodMes) < 2 ) {
    $inCodMes = "0".$inCodMes;
}

//Consulta o CGM a partir do registro
$obRPessoalServidor = new RPessoalServidor;
$obRPessoalServidor->addContratoServidor();
$obRPessoalServidor->roUltimoContratoServidor->setRegistro( $inRegistro );
$obRPessoalServidor->roUltimoContratoServidor->listarContratosServidorResumido( $rsContratoServidor , $boTransacao );

//Define a string numcgm - nome_do_servidor
$stCGM = $rsContratoServidor->getCampo("numcgm") ." - ". $rsContratoServidor->getCampo("servidor");

//Define o OBJETO label para informar a competencia
$obLblCompetencia = new Label;
$obLblCompetencia->setRotulo ( 'Competência'        );
$obLblCompetencia->setName   ( 'stLblCompetencia'   );
$obLblCompetencia->setId     ( 'stLblCompetencia'   );
$obLblCompetencia->setValue  ( $inCodMes."/".$inAno );

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
$obPeriodoMovimentacao->listarPeriodoMovimentacao($rsPeriodoMovimentacao);

$obTFolhaPagamentoRegistroEventoFerias = new TFolhaPagamentoRegistroEventoFerias;
$stFiltro  = " AND cod_contrato = ".$rsContratoServidor->getCampo('cod_contrato');
$stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
$obTFolhaPagamentoRegistroEventoFerias->recuperaRelacionamento($rsRegistroEvento,$stFiltro);

//Variaveis utilizadas para montar os record set das listas
$arEventosProporcionais = array();
$arEventosFixos         = array();
$arEventosVariaveis     = array();
$arBaseCalculo          = array();
$inCountProporcionais   = 0;
$inCountFixos           = 0;
$inCountVariaveis       = 0;
$inCountBase            = 0;

//Algoritmo para separar o record set em 3 para as listas.
$rsRegistroEvento->setPrimeiroElemento();
while ( !$rsRegistroEvento->eof() ) {
    if ( $rsRegistroEvento->getCampo("proporcional") == "t" ) {
        $arEventosProporcionais[$inCountProporcionais]["codigo"] = $rsRegistroEvento->getCampo("codigo");
        $arEventosProporcionais[$inCountProporcionais]["descricao"]  = $rsRegistroEvento->getCampo("descricao");
        $arEventosProporcionais[$inCountProporcionais]["valor"]      = $rsRegistroEvento->getCampo("valor");
        $arEventosProporcionais[$inCountProporcionais]["quantidade"] = $rsRegistroEvento->getCampo("quantidade");
        if ( $rsRegistroEvento->getCampo("automatico") == "t" )
            $arEventosProporcionais[$inCountProporcionais]["automatico"] = "Sim";
        else
            $arEventosProporcionais[$inCountProporcionais]["automatico"] = "Não";
        $inCountProporcionais = $inCountProporcionais + 1;
    } elseif ( $rsRegistroEvento->getCampo("natureza") == "B" ) {
        $arBaseCalculo[$inCountBase]["codigo"] = $rsRegistroEvento->getCampo("codigo");
        $arBaseCalculo[$inCountBase]["descricao"]  = $rsRegistroEvento->getCampo("descricao");
        $arBaseCalculo[$inCountBase]["valor"]      = $rsRegistroEvento->getCampo("valor");
        $arBaseCalculo[$inCountBase]["quantidade"] = $rsRegistroEvento->getCampo("quantidade");
        if ( $rsRegistroEvento->getCampo("automatico") == "t" )
            $arBaseCalculo[$inCountBase]["automatico"] = "Sim";
        else
            $arBaseCalculo[$inCountBase]["automatico"] = "Não";

        $inCountBase = $inCountBase + 1;
    } else {
        switch ( $rsRegistroEvento->getCampo("tipo") ) {
            case "F":
                $arEventosFixos[$inCountFixos]["codigo"] = $rsRegistroEvento->getCampo("codigo");
                $arEventosFixos[$inCountFixos]["descricao"]  = $rsRegistroEvento->getCampo("descricao");
                $arEventosFixos[$inCountFixos]["valor"]      = $rsRegistroEvento->getCampo("valor");
                $arEventosFixos[$inCountFixos]["quantidade"] = $rsRegistroEvento->getCampo("quantidade");
                if ( $rsRegistroEvento->getCampo("automatico") == "t" )
                    $arEventosFixos[$inCountFixos]["automatico"] = "Sim";
                else
                    $arEventosFixos[$inCountFixos]["automatico"] = "Não";
                $inCountFixos = $inCountFixos + 1;
            break;
            case "V":
                $arEventosVariaveis[$inCountVariaveis]["codigo"] = $rsRegistroEvento->getCampo("codigo");
                $arEventosVariaveis[$inCountVariaveis]["descricao"]  = $rsRegistroEvento->getCampo("descricao");
                $arEventosVariaveis[$inCountVariaveis]["valor"]      = $rsRegistroEvento->getCampo("valor");
                $arEventosVariaveis[$inCountVariaveis]["quantidade"] = $rsRegistroEvento->getCampo("quantidade");
                if ( $rsRegistroEvento->getCampo("automatico") == "t" )
                    $arEventosVariaveis[$inCountVariaveis]["automatico"] = "Sim";
                else
                    $arEventosVariaveis[$inCountVariaveis]["automatico"] = "Não";

                $inCountVariaveis = $inCountVariaveis + 1;
            break;
        }
    }
    $rsRegistroEvento->proximo();
}

//Monta as 3 listas
for ($i=0 ; $i<=3 ; $i++) {
    $obLista = new Lista;
    $obLista->setMostraPaginacao    ( false );

    switch ($i) {
        case 0:
            $rsEventosFixos = new Recordset;
            $rsEventosFixos->preenche( $arEventosFixos );

            $obLista->setRecordSet( $rsEventosFixos );
            $obLista->setTitulo   ("Eventos Fixos");
        break;

        case 1:
            $rsEventosVariaveis = new Recordset;
            $rsEventosVariaveis->preenche( $arEventosVariaveis );

            $obLista->setRecordSet( $rsEventosVariaveis );
            $obLista->setTitulo   ("Eventos Variáveis");
        break;

        case 2:
            $rsEventosProporcionais = new Recordset;
            $rsEventosProporcionais->preenche( $arEventosProporcionais );

            $obLista->setRecordSet( $rsEventosProporcionais );
            $obLista->setTitulo   ("Eventos Proporcionais");
        break;

        case 3:
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

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Automático" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

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

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "automatico" );
    $obLista->commitDado();

    $obLista->show();
}

$obBtnFechar = new Button;
$obBtnFechar->setName                    ( "btnFechar" );
$obBtnFechar->setValue                   ( "Fechar"    );
$obBtnFechar->setTipo                    ( "button"    );
$obBtnFechar->obEvento->setOnClick       ( "window.parent.window.close();"  );

$stLink    = CAM_FW_POPUPS."relatorio/OCRelatorio.php?".Sessao::getId()."&stCaminho=".CAM_GRH_FOL_INSTANCIAS."relatorio/OCRelatorioEventoPorContrato.php";
$stFiltros = "&inContrato=".$inRegistro."&inCodMes=".$inCodMes."&inAno=".$inAno;

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
