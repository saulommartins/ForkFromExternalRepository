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
  * Página de Include Oculta - Exportação Arquivos TCEMG - ANL.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: ITEM.csv.inc.php 62269 2015-04-15 18:28:39Z franver $
  * $Date: 2015-04-15 15:28:39 -0300 (Wed, 15 Apr 2015) $
  * $Author: franver $
  * $Rev: 62269 $
  *
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TExportacaoTCEMGItem.class.php";

$rsRecordSetITEM10 = new RecordSet();

$obTExportacaoTCEMGItem = new TExportacaoTCEMGItem();
$obTExportacaoTCEMGItem->setDado('exercicio', Sessao::getExercicio());
$obTExportacaoTCEMGItem->setDado('mes'      , $stMes );
$obTExportacaoTCEMGItem->criaTabelaItem($rsTabelaItem,"","",$boTransacao);
$obTExportacaoTCEMGItem->recuperaTodos($rsRecordSetITEM10);

//Tipo Registro 99
$arRecordSetITEM99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetITEM99 = new RecordSet();
$rsRecordSetITEM99->preenche($arRecordSetITEM99);

$arRecordSetITEM10 = $rsRecordSetITEM10->getElementos();
if (count($arRecordSetITEM10) > 0) {
    for($i=0; $i<count($arRecordSetITEM10);$i++){   
            $map = array(
                'á' => 'a',
                'à' => 'a',
                'ã' => 'a',
                'â' => 'a',
                'é' => 'e',
                'ê' => 'e',
                'í' => 'i',
                'ó' => 'o',
                'ô' => 'o',
                'õ' => 'o',
                'ú' => 'u',
                'ü' => 'u',
                'ç' => 'c',
                'Á' => 'A',
                'À' => 'A',
                'Ã' => 'A',
                'Â' => 'A',
                'É' => 'E',
                'Ê' => 'E',
                'Í' => 'I',
                'Ó' => 'O',
                'Ô' => 'O',
                'Õ' => 'O',
                'Ú' => 'U',
                'Ü' => 'U',
                'Ç' => 'C'
            );
        $arRecordSetITEM10[$i]['dscitem']=  strtr($arRecordSetITEM10[$i]['dscitem'], $map);
        $arRecordSetITEM10[$i]['unidademedida']=utf8_encode($arRecordSetITEM10[$i]['unidademedida']);
    }
    
    $rsRecordSetITEM10->preenche($arRecordSetITEM10);
    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetITEM10);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("coditem");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dscitem");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidademedida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipocadastro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("justificativaalteracao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);

}else {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetITEM99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetITEM10 = null;
$obTExportacaoTCEMGItem = null;
$rsRecordSetITEM99 = null;
$arRecordSetITEM10 = null;
?>