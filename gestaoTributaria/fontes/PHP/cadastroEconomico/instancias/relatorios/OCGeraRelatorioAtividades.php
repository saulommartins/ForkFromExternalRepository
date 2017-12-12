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
    * Data de Criação   : 27/04/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: OCGeraRelatorioAtividades.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.15

*/

/*
$Log$
Revision 1.7  2006/12/12 18:20:30  dibueno
Alterações para o relatório de atividades

Revision 1.6  2006/09/15 14:33:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );

$rsListaAtividades = Sessao::read( "sessao_transf5" );
$arDados = $rsListaAtividades->getElementos();

$arDadosTMP = array();
$inTamanhoMaximo = 90;
for ( $inX=0; $inX<count($arDados); $inX++ ) {
    $arDadosTMP[] = $arDados[$inX];
    $stTMP = $arDados[$inX]["servico_completo"];
    $boPrimeiro = true;
    while ( strlen( $stTMP ) > $inTamanhoMaximo ) {
        if ($boPrimeiro) {
            $inY = count( $arDadosTMP )-1;
            $inPosicao = $inTamanhoMaximo-1;
            while ( ( $stTMP[$inPosicao] != " " ) && $inPosicao ) {
                $inPosicao--;
            }

            if (!$inPosicao) { //eh uma palavra so de 80 caracteres nao ha divisao!
                $arDadosTMP[$inY]["servico_completo"] = substr( $stTMP, 0, $inTamanhoMaximo );
                $stTMP = substr( $stTMP, $inTamanhoMaximo );
            } else { //copiando palavra ate o espaço em branco
                $arDadosTMP[$inY]["servico_completo"] = substr( $stTMP, 0, $inPosicao+1 );
                $stTMP = substr( $stTMP, $inPosicao+1 );
            }

            $boPrimeiro = false;
        } else {
            $inPosicao = $inTamanhoMaximo-1;
            while ( ( $stTMP[$inPosicao] != " " ) && $inPosicao ) {
                $inPosicao--;
            }

            if (!$inPosicao) { //eh uma palavra so de 80 caracteres nao ha divisao!
                $arDadosTMP[]["servico_completo"] = substr( $stTMP, 0, $inTamanhoMaximo );
                $stTMP = substr( $stTMP, $inTamanhoMaximo );
            } else { //copiando palavra ate o espaço em branco
                $arDadosTMP[]["servico_completo"] = substr( $stTMP, 0, $inPosicao+1 );
                $stTMP = substr( $stTMP, $inPosicao+1 );
            }
        }

        if ( strlen( $stTMP ) <= $inTamanhoMaximo ) {
            $arDadosTMP[]["servico_completo"] = $stTMP;
        }
    }
}

$rsListaAtividades->preenche( $arDadosTMP );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Cadastro Econômico - Relatórios"   );
$obPDF->setTitulo            ( "Atividades" );
$obPDF->setSubTitulo         ( "Exercicio - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( $rsListaAtividades );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CÓDIGO"     ,8, 10  );
$obPDF->addCabecalho   ( "ATIVIDADES" ,38, 10 );
$obPDF->addCabecalho   ( "DESCRIÇÃO"  ,38, 10 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ( "VIGÊNCIA"   ,8, 10  );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "ALÍQUOTA"   ,9, 10  );
$obPDF->addIndentacao  ( "cod_nivel" , "masc_servico", "   " );
$obPDF->addQuebraPagina( "pagina" , 1 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "cod_estrutural"  , 7 );
$obPDF->addCampo       ( "nom_atividade"     , 7 );
$obPDF->addCampo       ( "servico_completo"   , 7 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ( "dt_inicio"      , 7 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "aliquota"      , 7 );

$obPDF->show();
?>
