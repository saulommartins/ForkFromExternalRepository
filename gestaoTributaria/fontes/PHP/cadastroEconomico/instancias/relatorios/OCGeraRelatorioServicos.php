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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 16/06/2009

    * @author Fernando Cercato

    * @ignore

    * $Id: $

    *Casos de uso: uc-05.02.15

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Cadastro Econômico - Relatórios"   );
$obPDF->setTitulo            ( "Serviços" );
$obPDF->setSubTitulo         ( "Exercicio - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsListaServicos = Sessao::read( "lista_servicos" );
$arDados = $rsListaServicos->getElementos();
$arDadosTMP = array();
$inTamanhoMaximo = 180;
for ( $inX=0; $inX<count($arDados); $inX++ ) {
    $arDadosTMP[] = $arDados[$inX];
    $stTMP = $arDados[$inX]["nom_servico"];
    $boPrimeiro = true;
    while ( strlen( $stTMP ) > $inTamanhoMaximo ) {
        if ($boPrimeiro) {
            $inY = count( $arDadosTMP )-1;
            $inPosicao = $inTamanhoMaximo-1;
            while ( ( $stTMP[$inPosicao] != " " ) && $inPosicao ) {
                $inPosicao--;
            }

            if (!$inPosicao) { //eh uma palavra so de 80 caracteres nao ha divisao!
                $arDadosTMP[$inY]["nom_servico"] = substr( $stTMP, 0, $inTamanhoMaximo );
                $stTMP = substr( $stTMP, $inTamanhoMaximo );
            } else { //copiando palavra ate o espaço em branco
                $arDadosTMP[$inY]["nom_servico"] = substr( $stTMP, 0, $inPosicao+1 );
                $stTMP = substr( $stTMP, $inPosicao+1 );
            }

            $boPrimeiro = false;
        } else {
            $inPosicao = $inTamanhoMaximo-1;
            while ( ( $stTMP[$inPosicao] != " " ) && $inPosicao ) {
                $inPosicao--;
            }

            if (!$inPosicao) { //eh uma palavra so de 80 caracteres nao ha divisao!
                $arDadosTMP[]["nom_servico"] = substr( $stTMP, 0, $inTamanhoMaximo );
                $stTMP = substr( $stTMP, $inTamanhoMaximo );
            } else { //copiando palavra ate o espaço em branco
                $arDadosTMP[]["nom_servico"] = substr( $stTMP, 0, $inPosicao+1 );
                $stTMP = substr( $stTMP, $inPosicao+1 );
            }
        }

        if ( strlen( $stTMP ) <= $inTamanhoMaximo ) {
            $arDadosTMP[]["nom_servico"] = $stTMP;
        }
    }
}

$rsListaServicos->preenche( $arDadosTMP );

$obPDF->addRecordSet( $rsListaServicos );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CÓDIGO", 8, 10  );
$obPDF->addCabecalho   ( "DESCRIÇÃO", 75, 10 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ( "VIGÊNCIA", 8, 10 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "ALÍQUOTA", 9, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "cod_estrutural", 7 );
$obPDF->addCampo       ( "nom_servico", 7 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ( "vigencia", 7 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "aliquota", 7 );

$obPDF->show();
?>
