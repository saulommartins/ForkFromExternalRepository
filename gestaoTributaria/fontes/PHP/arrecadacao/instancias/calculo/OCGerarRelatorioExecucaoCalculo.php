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
    * Arquivo paga geração de relatorio de Execução de Calculo
    * Data de Criação: 04/01/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: OCGerarRelatorioExecucaoCalculo.php 62975 2015-07-14 14:03:02Z gelson $

    * Casos de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.4  2006/11/22 16:46:32  dibueno
Bug #7590#

Revision 1.3  2006/09/15 11:50:26  fabio
corrigidas tags de caso de uso

Revision 1.2  2006/09/15 10:57:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );

set_time_limit(300000);

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio:"   );
$obPDF->setTitulo            ( "Calculo:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$inCountCabecalho = count( $arCabecalho );

if ( Sessao::read( "grupo_automatico" ) ) {
    $obRARRGrupo = new RARRGrupo;
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    list( $inCodGrupo , $inExercicio ) = explode( '/' , Sessao::read( "grupo_automatico" ) );
    $obRARRGrupo->setCodGrupo   ( $inCodGrupo);
    $obRARRGrupo->setExercicio  ( $inExercicioGrupo);
    $obRARRGrupo->listarCreditos( $rsCreditosGrupo );
    $numeroCreditosGrupo = $rsCreditosGrupo->getNumLinhas();

    if ( Sessao::read( "simular_relatorio" ) ) {
        $stSql = "   SELECT acgr.cod_calculo
                        FROM arrecadacao.calculo_grupo_credito    AS acgr

                        JOIN arrecadacao.calculo                  AS ac
                            ON ac.cod_calculo     = acgr.cod_calculo
                        AND ac.ativo           = FALSE
                        AND ac.simulado        = TRUE

                    LEFT JOIN arrecadacao.lancamento_calculo       AS alc
                            ON alc.cod_calculo    = acgr.cod_calculo

                        WHERE cod_grupo          = ".$inCodGrupo."
                        AND acgr.ano_exercicio = '".$inExercicio."'
                        AND alc.cod_calculo IS NULL ";
    } else {
        $stSql = "   SELECT acgr.cod_calculo
                        FROM arrecadacao.calculo_grupo_credito    AS acgr

                        JOIN arrecadacao.calculo                  AS ac
                            ON ac.cod_calculo     = acgr.cod_calculo

                    LEFT JOIN arrecadacao.lancamento_calculo       AS alc
                            ON alc.cod_calculo    = acgr.cod_calculo

                        WHERE cod_grupo          = ".$inCodGrupo."
                        AND acgr.ano_exercicio = '".$inExercicio."'
                        AND alc.cod_calculo IS NULL
                        AND ac.calculado = TRUE";
    }

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $inX = 0;
    while ( !$rsRecordSet->Eof() ) {
        if ($inX) {
            $stCalculos .= ",";
        }else
            $inX = 1;

        $stCalculos .= $rsRecordSet->getCampo("cod_calculo");
        $rsRecordSet->proximo();
    }

    $arCalculos = explode ( ',', $stCalculos );
    $contTodosCalculos = count( $arCalculos );
    #echo 'Calculos: '.$contTodosCalculos; exit;
    $contArrayCalculosParaLancamento = 0;
    $cont = 0;
    $arCalculosParaLancamento = array();
    $limitador = 1000;
    $stLinha = null;
    while ($cont < $contTodosCalculos) {

        $boMudouLinha = false;
        $stLinha .= $arCalculos[$cont].", ";

        #$arCalculosParaLancamento
        if ( (($cont+1) % $numeroCreditosGrupo == 0) && $cont > $limitador ) {
            $limitador += 1000;
            $boMudouLinha = true;
        }

        if ( $boMudouLinha || ( ($cont+1) == $contTodosCalculos) ) {
            $stLinha = substr ( $stLinha, 0, (strlen( $stLinha ) -2) );
            $arCalculosParaLancamento[$contArrayCalculosParaLancamento] = $stLinha;
            $stLinha = null;
            $contArrayCalculosParaLancamento++;
        }

        $cont++;
    }

    $contCalculos = count ($arCalculosParaLancamento);
    $cont = 0;
    $obRARRCalculo = new RARRCalculo;
    $arNovoTudo = array();
    while ($cont < $contCalculos) {
        $obRARRCalculo->setCodCalculo( $arCalculosParaLancamento[$cont] );
        unset( $rsCalculos );
        $obRARRCalculo->listarRelatorioExecucao( $rsCalculos );
        if ( $rsCalculos->getNumLinhas() > 0 ) {
            $inCount = 1;
            unset( $arNovo );
            $arNovo = array();
            $rsCalculos->ordena( "cod_calculo" );
            foreach ($rsCalculos->arElementos as $valor) {
                if ($inCount == 1) {
                    $inInscricaoAtual = $valor["inscricao"];
                    $stCGM = $valor["numcgm"]." - ".$valor["nom_cgm"];
                    $inCount = 2;
                } else {
                    if ($inInscricaoAtual == $valor["inscricao"]) {
                        $valor["inscricao"] = "";
                        $valor["nom_cgm"] = "";
                        $valor["numcgm"] = "";
                        $stCGM = "";
                    } else {
                        $inInscricaoAtual = $valor["inscricao"];
                        $stCGM = $valor["numcgm"]." - ".$valor["nom_cgm"];
                    }
                }

                $arNovo[] = $valor;
                $arNovo[count($arNovo)-1]["nomnum_cgm"] = $stCGM;
            }

            $rsCalculos->preenche( $arNovo );
            $rsCalculos->addFormatacao( "valor", "NUMERIC_BR" );

            $obPDF->addRecordSet( $rsCalculos );

            $obPDF->setBordas       ( true);
            $obPDF->setAlinhamento  ( "C" );
            $obPDF->addCabecalho    ( "Contribuinte"    ,30 , 8 );
            $obPDF->addCabecalho    ( "Inscrição"       ,8 , 8 );
            $obPDF->addCabecalho    ( "Cálculo"         , 7 , 8 );
            $obPDF->addCabecalho    ( "Crédito"         ,40 , 8 );
            $obPDF->addCabecalho    ( "Estado"          ,6 , 8 );
            $obPDF->setAlinhamento  ( "R" );
            $obPDF->addCabecalho    ( "Valor Calculado" ,9 , 8 );

            $obPDF->setAlinhamento  ( "L" );
            $obPDF->addCampo        ( "nomnum_cgm" , 5 );
            $obPDF->setAlinhamento  ( "C" );
            $obPDF->addCampo        ( "inscricao"           , 5 );
            $obPDF->addCampo        ( "cod_calculo"         , 5 );
            $obPDF->setAlinhamento  ( "L" );
            $obPDF->addCampo        ( "[cod_credito].[cod_especie].[cod_genero].[cod_natureza] - [descricao_credito]", 5 );
            $obPDF->setAlinhamento  ( "C" );
            $obPDF->addCampo        ( "status"              , 5 );
            $obPDF->setAlinhamento  ( "R" );
            $obPDF->addCampo        ( "R$ [valor]"          , 5 );
        }

        $cont++;
    }

    $rsCalculos = new RecordSet;
    $rsCalculos->preenche( $arNovoTudo );
    unset( $arNovoTudo );
    unset( $stCalculos );
    unset( $arCalculos );
} else {
    $rsCalculos = Sessao::read( "rsCalculos" );
    $inCount = 1;
    $arNovo = array();
    $rsCalculos->ordena( "cod_calculo" );
    foreach ($rsCalculos->arElementos as $valor) {
        if ($inCount == 1) {
            $inInscricaoAtual = $valor["inscricao"];
            $stCGM = $valor["numcgm"]." - ".$valor["nom_cgm"];
            $inCount = 2;
        } else {
            if ($inInscricaoAtual == $valor["inscricao"]) {
                $valor["inscricao"] = "";
                $valor["nom_cgm"] = "";
                $valor["numcgm"] = "";
                $stCGM = "";
            } else {
                $inInscricaoAtual = $valor["inscricao"];
                $stCGM = $valor["numcgm"]." - ".$valor["nom_cgm"];
            }
        }

        $arNovo[] = $valor;
        $arNovo[count($arNovo)-1]["nomnum_cgm"] = $stCGM;
    }

    $rsCalculos->preenche( $arNovo );
    $rsCalculos->addFormatacao( "valor", "NUMERIC_BR" );
    $obPDF->addRecordSet( $rsCalculos );

    $obPDF->setBordas       ( true);
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCabecalho    ( "Contribuinte", 30, 8 );
    $obPDF->addCabecalho    ( "Inscrição", 8, 8 );
    $obPDF->addCabecalho    ( "Cálculo", 7, 8 );
    $obPDF->addCabecalho    ( "Crédito", 40, 8 );
    $obPDF->addCabecalho    ( "Estado", 7, 8 );
    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCabecalho    ( "Valor Calculado", 9, 8 );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "nomnum_cgm", 5 );
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCampo        ( "inscricao", 5 );
    $obPDF->addCampo        ( "cod_calculo", 5 );
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "[cod_credito].[cod_especie].[cod_genero].[cod_natureza] - [descricao_credito]", 5 );
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCampo        ( "status", 5 );
    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCampo        ( "R$ [valor]", 5 );

}

// tratar array de elementos

$obPDF->show();
?>
