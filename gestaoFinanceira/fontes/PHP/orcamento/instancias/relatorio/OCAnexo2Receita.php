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
    * Data de Criação   : 23/09/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 32085 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['stTipoRelatorio']=="orcamento") {
    include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo2Receita.class.php" );

    $obRRelatorio             = new RRelatorio;
    $obROrcamentoAnexo2Receita = new ROrcamentoRelatorioAnexo2Receita;

    //seta elementos do filtro
    $stFiltro = "";

    if ($arFiltro['inCodEntidade'] != "") {
        $stFiltro = " AND cod_entidade IN  (";
        foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
            $stFiltro .= $valor.",";
        }
        if ($stFiltro != "") {

            $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 1 ) . ")";
        }
        $obROrcamentoAnexo2Receita->setFiltro( $stFiltro );
    }

    $obROrcamentoAnexo2Receita->setExercicio (Sessao::getExercicio());
    $obROrcamentoAnexo2Receita->geraRecordSet( $rsAnexo2Receita, $rsAnexo2Resumo );

    Sessao::write('rsAnexo2Receita',$rsAnexo2Receita);
    Sessao::write('rsAnexo2Resumo',$rsAnexo2Resumo);

    $obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo2Receita.php" );

} elseif ($arFiltro['stTipoRelatorio']=="balanco") {

    include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo2ReceitaBalanco.class.php" );

    $obRRelatorio             = new RRelatorio;
    $obROrcamentoAnexo2ReceitaBalanco = new ROrcamentoRelatorioAnexo2ReceitaBalanco;

    //seta elementos do filtro
    $stFiltro = "";

    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidades .= $valor.",";
    }
    $stEntidades = trim(substr( $stEntidades, 0, strlen($stEntidades) - 1 ));
    $obROrcamentoAnexo2ReceitaBalanco->setEntidades    ($stEntidades);
    $obROrcamentoAnexo2ReceitaBalanco->setDataInicial  ($arFiltro['stDataInicial']);
    $obROrcamentoAnexo2ReceitaBalanco->setDataFinal    ($arFiltro['stDataFinal']);
    $obROrcamentoAnexo2ReceitaBalanco->setExercicio (Sessao::getExercicio());
    $obROrcamentoAnexo2ReceitaBalanco->geraRecordSet( $rsAnexo2Receita, $rsAnexo2Resumo );

    Sessao::write('rsAnexo2Receita',$rsAnexo2Receita);
    Sessao::write('rsAnexo2Resumo',$rsAnexo2Resumo);

    $obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo2ReceitaBalanco.php" );
}

?>
