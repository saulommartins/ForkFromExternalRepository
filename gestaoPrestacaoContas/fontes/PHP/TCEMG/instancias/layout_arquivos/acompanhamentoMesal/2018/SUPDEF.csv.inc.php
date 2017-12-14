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
  * Página de Include Oculta - Exportação Arquivos TCEMG - SUPDEF.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: SUPDEF.csv.inc.php 64277 2016-01-05 13:48:01Z lisiane $
  * $Date: 2016-01-05 11:48:01 -0200 (Ter, 05 Jan 2016) $
  * $Author: lisiane $
  * $Rev: 64277 $
  *
*/

include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMSUPDEF.class.php";

$rsRecordSetSUPDEF10 = new RecordSet();
$rsRecordSetSUPDEF11 = new RecordSet();
if ( $stMes == 2 ) {
    $obTTCEMSUPDEF = new TTCEMSUPDEF();
    $obTTCEMSUPDEF->setDado('exercicio' , Sessao::getExercicio());
    $obTTCEMSUPDEF->setDado('entidade'  , $stEntidades);

    //Tipo Registro 10
    $obTTCEMSUPDEF->recuperaDadosSUPDEF10($rsRecordSetSUPDEF10);
    $obTTCEMSUPDEF->recuperaDadosSUPDEF11($rsRecordSetSUPDEF11);
}
//Tipo Registro 99
$arRecordSetSUPDEF99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$boGera99 = true;

if ($rsRecordSetSUPDEF10->getNumLinhas() > 0 && $stMes == 2) {
    $boGera99 = false;

    foreach ($rsRecordSetSUPDEF10->getElementos() as $arSUPDEF10) {
        $stChave10 = $arSUPDEF10['superavit_deficit'];

        $rsBloco10 = new RecordSet();
        $rsBloco10->preenche(array($arSUPDEF10));

        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco( $rsBloco10 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("superavit_deficit");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_apurado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

        if ($rsRecordSetSUPDEF10->getNumLinhas() > 0) {
            foreach ($rsRecordSetSUPDEF11->getElementos() as $arSUPDEF11) {
                $rsBloco11 = new RecordSet();
                $rsBloco11->preenche(array($arSUPDEF11));

                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                $obExportador->roUltimoArquivo->addBloco( $rsBloco11 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_recurso");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("superavit_deficit");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_apurado");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
            }
        }
    }
}

$rsRecordSetSUPDEF99 = new RecordSet();
$rsRecordSetSUPDEF99->preenche($arRecordSetSUPDEF99);

if ($boGera99) {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetSUPDEF99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

?>