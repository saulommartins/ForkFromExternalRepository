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
    * Data de Criação   : 16/08/2005

    * @author Analista: Muriel Preuss
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso : uc-02.03.26
*/

/*
$Log$
Revision 1.4  2006/07/05 20:49:08  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioProgramacaoPagamentos.class.php"  );

$obRegra            = new REmpenhoRelatorioProgramacaoPagamentos;

$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );

$arFiltro = Sessao::read('filtroRelatorio');

//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    $inCount = 0;
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidade .= $valor.",";
        $inCount++;
    }
    $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
} else {
    $stEntidade .= $arFiltro['stTodasEntidades'];
}

$obRegra->setCodEntidade( $stEntidade   );
$obRegra->obREmpenhoEmpenho->setExercicio               ( $arFiltro['stExercicio']        );
$obRegra->setDtVencimentoInicial                        ( $arFiltro['stDataInicial']      );
$obRegra->setDtVencimentoFinal                          ( $arFiltro['stDataFinal']        );
$obRegra->obREmpenhoEmpenho->obRCGM->setNumCGM          ( $arFiltro['inCGM']              );
$obRegra->obROrcamentoDespesa->setCodDespesa                     ( $arFiltro['inCodDespesa']       );
$obRegra->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso         ( $arFiltro['inCodRecurso']       );
if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
    $obRegra->obROrcamentoDespesa->obROrcamentoRecurso->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );
$obRegra->obROrcamentoDespesa->obROrcamentoRecurso->setCodDetalhamento ( $arFiltro['inCodDetalhamento'] );

$obRegra->geraRecordSet( $arRecordSet,$arRecordSet1,$arRecordSet2,$arRecordSet3,$arRecordSet4,$arRecordSet5,$arRecordSet6,$arRecordSet7,$arRecordSet8,$arRecordSet9);
Sessao::write('rsRecordSet0', $arRecordSet);
Sessao::write('rsRecordSet1', $arRecordSet1);
Sessao::write('rsRecordSet2', $arRecordSet2);
Sessao::write('rsRecordSet3', $arRecordSet3);
Sessao::write('rsRecordSet4', $arRecordSet4);
Sessao::write('rsRecordSet5', $arRecordSet5);
Sessao::write('rsRecordSet6', $arRecordSet6);
Sessao::write('rsRecordSet7', $arRecordSet7);
Sessao::write('rsRecordSet8', $arRecordSet8);
Sessao::write('rsRecordSet9', $arRecordSet9);

$obRegra->obRRelatorio->executaFrameOculto( "OCGeraRelatorioProgramacaoPagamentos.php" );

?>
