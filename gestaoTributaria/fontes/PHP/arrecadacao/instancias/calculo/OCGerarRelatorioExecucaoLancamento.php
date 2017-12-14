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
    * Arquivo paga geração de relatorio de Lancamento de Calculo
    * Data de Criação: 04/01/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCGerarRelatorioExecucaoLancamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.7  2006/11/24 12:06:35  dibueno
Bug #7590#

Revision 1.6  2006/11/22 18:46:14  dibueno
Alterações no tamanho das colunas

Revision 1.5  2006/11/01 13:19:29  dibueno
Tratamento da variavel de sessao, com os codigos dos lançamentos

Revision 1.4  2006/09/15 10:57:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php" );
include_once ( CAM_FW_PDF."RRelatorio.class.php" );

set_time_limit(0);

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Relatorio:"   );
$obPDF->setTitulo            ( "Lançamento:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$inCountCabecalho = count( $arCabecalho );

$obRARRLancamento = new RARRLancamento( new RARRCalculo );

$LancamentoCods = Sessao::read( 'lancamentos_cods' );
$LancamentoCods = substr ( $LancamentoCods, 0, strlen ( $LancamentoCods ) - 1 );

$arLancs = explode( ",", $LancamentoCods );

$inX=0;
while ( $inX< count( $arLancs ) ) {
    $stLancs = $arLancs[$inX];
    for ($inY=0; $inY<2000; $inY++) {
        $stLancs .= ",".$arLancs[$inX];
        $inX++;
        if ( $inX >= count( $arLancs ) )
            break;
    }

    $obRARRLancamento->setCodLancamento( $stLancs );
    unset( $rsLancamentos );
    $obRARRLancamento->listarRelatorioLancamento( $rsLancamentos );

    $inCount = 1;
    unset( $arNovo );
    $arNovo = array();
    foreach ($rsLancamentos->arElementos as $valor) {
        if ($inCount == 1) {
            $inInscricaoAtual = $valor["cod_lancamento"];
            $inCount = 2;
            $valor["cgm"] = $valor["numcgm"]." - ".$valor["nom_cgm"];
        } else {
            if ($inInscricaoAtual == $valor["cod_lancamento"]) {
                $valor["inscricao"] = "";
                $valor["nom_cgm"] = "";
                $valor["numcgm"] = "";
                $valor["cod_lancamento"] = "";
            } else {
                $inInscricaoAtual = $valor["cod_lancamento"];
                $valor["cgm"] = $valor["numcgm"]." - ".$valor["nom_cgm"];
            }
        }

        $arNovo[] = $valor;
    }

    unset( $rsLancamentos );
    $rsLancamentos = new RecordSet;
    $rsLancamentos->preenche( $arNovo );

    $obPDF->addRecordSet( $rsLancamentos );
    $rsLancamentos->addFormatacao('valor_parcela' ,'NUMERIC_BR');

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "Código", 10, 12 );
    $obPDF->addCabecalho    ( "Usuário", 34, 12 );

    $obPDF->addCabecalho    ( "Inscrição", 10, 12 );
    $obPDF->addCabecalho    ( "Numeração", 16, 12 );

    $obPDF->addCabecalho    ( "Parcela", 8 , 12 );
    $obPDF->addCabecalho    ( "Vencimento", 10, 12 );
    $obPDF->addCabecalho    ( "Valor", 12, 11 );

    $obPDF->addCampo        ( "cod_lancamento", 10 );
    $obPDF->addCampo        ( "cgm", 10 );

    $obPDF->addCampo        ( "inscricao", 10 );
    $obPDF->addCampo        ( "numeracao", 10 );

    $obPDF->addCampo        ( "nr_parcela", 10 );
    $obPDF->addCampo        ( "data_vencimento", 10 );
    $obPDF->addCampo        ( "R$ [valor_parcela]", 10 );
}

$obPDF->show();
?>
