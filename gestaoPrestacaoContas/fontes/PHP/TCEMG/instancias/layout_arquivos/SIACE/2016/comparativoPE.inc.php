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
/*
    * Arquivo de geracao do arquivo sertTerceiros TCM/MG
    * Data de Criação   : 19/01/2009
    * 
    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    * 
    * @package URBEM
    * @subpackage
    * 
    * @ignore
    * 
    * $Id: comparativoPE.inc.php 63324 2015-08-18 16:57:27Z franver $
    * $Rev: 63324 $
    * $Author: franver $
    * $Date: 2015-08-18 13:57:27 -0300 (Ter, 18 Ago 2015) $
    * 
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGComparativoPe.class.php';
    
$arFiltros = Sessao::read('filtroRelatorio');

$rsComparativoPe = new RecordSet();
$obFTCEMGComparativoPe = new FTCEMGComparativoPe();
foreach ($arDatasInicialFinal as $stDatas) {
    list($inDia,$inMes,$inAno) = explode("/", $stDatas['stDtInicial']);

    $obFTCEMGComparativoPe->setDado('exercicio'   , Sessao::getExercicio() );
    $obFTCEMGComparativoPe->setDado('dtInicial'   , $stDatas['stDtInicial'] );
    $obFTCEMGComparativoPe->setDado('dtFinal'     , $stDatas['stDtFinal'] );
    $obFTCEMGComparativoPe->setDado('cod_entidade', implode(',',$arFiltros['inCodEntidadeSelecionado']));
    $obFTCEMGComparativoPe->recuperaTodos( $rsComparativoPe );

    $rsDados = new RecordSet();
    $arDados = array();
    $arDados['mes'] = $inMes;

    foreach ( $rsComparativoPe->getElementos() as $value ) {
        
        //Valor da despesa líquida inativos e pensionistas
        if ( preg_match("/Inativo e Pensio/i", $value['descricao']) )
            $arDados['desp_liq_inat_pens'] = $value['valor'];
        
        //Valor da antecipação da receita orçamentária
        if ( preg_match("/Receita extra orcamentaria/i", $value['descricao']) )
            $arDados['ant_rec_orc'] = $value['valor'];
                
        //Valor da divida consolidada
        if ( preg_match("/VIDA CONSOLIDADA -/", $value['descricao']) )
            $arDados['div_cons'] = $value['valor'];
        
        //Valor da divida consolidada líquida
        if ( preg_match("/VIDA CONSOLIDADA L.*/", $value['descricao']) )
            $arDados['div_cons_liq'] = $value['valor'];
                
        //Valor da dívida mobiliária
        if ( preg_match("/Mobili/i", $value['descricao']) )
            $arDados['div_mobiliaria'] = $value['valor'];
                
        //Valor das concessões de garantia
        if ( preg_match("/concessoes de garantia/i", $value['descricao']) )
            $arDados['conc_garantia'] = $value['valor'];
        
        //Valor das operações de crédito
        if ( preg_match("/credito/i", $value['descricao']) )
            $arDados['op_credito'] = $value['valor'];
            
    }

    $rsDados->add($arDados);
    $obExportador->roUltimoArquivo->addBloco($rsDados);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('desp_liq_inat_pens');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('div_cons');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('div_cons_liq');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('div_mobiliaria');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('conc_garantia');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('op_credito');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('ant_rec_orc');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    unset($rsDados);
    unset($arDados);
}

unset($arFiltros);
unset($rsComparativoPe);
unset($obFTCEMGComparativoPe);

?>