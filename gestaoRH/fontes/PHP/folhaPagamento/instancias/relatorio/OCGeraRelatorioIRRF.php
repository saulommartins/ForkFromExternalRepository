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
    * Página oculta para geração do Relatório de FGTS
    * Data de Criação: 11/05/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore
    * Casos de uso: uc-04.05.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RRelatorioIRRF.class.php"                                      );
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                   );

$obRelatorioIRRF = new RRelatorioIRRF;
$arFiltro = Sessao::read('filtroRelatorio');
// passando para o filtro os contratos escolhidos pelo usuário
$arContratos = Sessao::read("arContratos");
if ( count( $arContratos ) > 0 ) {
        foreach ($arContratos as $linha) {
       $obRelatorioIRRF->addContrato ( $linha['Contrato'] );
    }
}

// passando a competencia escolhida
if ( ( $arFiltro['inCodMes'] ) and ( $arFiltro['inAno'] ) ) {
    $stMes = $arFiltro['inCodMes'];
    if (strlen($stMes) == 1 ) {
        $stMes = '0'.$stMes;
    }
    $obRelatorioIRRF->setCompetencia ( $stMes. '/'. $arFiltro['inAno'] );
}

// passando as lotações
if ( count( $arFiltro['inCodLotacaoSelecionados'] ) > 0 ) {
  foreach ($arFiltro['inCodLotacaoSelecionados'] as $linha) {
        $obRelatorioIRRF->addLotacao ( $linha );
  }
}

/// passando os locais
if ( count( $arFiltro['inCodLocalSelecionados'] ) > 0 ) {
   foreach ($arFiltro['inCodLocalSelecionados'] as $linha) {
        $obRelatorioIRRF->addLocal ( $linha );
   }
}

// passando o tipo de calculo
$obRelatorioIRRF->setTipoCalculo ( $arFiltro['inCodConfiguracao'] );

$stOrdem = $arFiltro['stOrdenacao'];

// passando a ordenação
$obRelatorioIRRF->setOrdenacao ( $stOrdem );

if ($arFiltro['boAgrupar'] == true) {

    if ($arFiltro['boQuebrarPagina'] == true) {
        $obRelatorioIRRF->setQuebraPagina( true );
    }

    $obRelatorioIRRF->geraRecordSetAgrupado( $rsRecordSet );
} else {
    $stOrdem = $arFiltro['stOrdenacao'];
    $obRelatorioIRRF->geraRecordSet( $rsRecordSet );
}
Sessao::write("relatorioIRRF",$rsRecordSet);

$obRRelatorio = new RRelatorio;
$obRRelatorio->executaFrameOculto( "OCGeraPDFRelatorioIRRF.php" );

?>
