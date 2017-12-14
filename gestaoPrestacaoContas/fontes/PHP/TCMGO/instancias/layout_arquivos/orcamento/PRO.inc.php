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
  * Página de Include Oculta - Exportação Arquivos TCMGO - 
  * Data de Criação: 23/01/2015

  * @author Analista:      Ane Pereira
  * @author Desenvolvedor: Arthur Cruz
  *
  * @ignore
  * $Id:$
  * $Date:$
  * $Author:$
  * $Rev:$
  *
*/

include_once CAM_GPC_TGO_MAPEAMENTO."TCMGOArquivoProgramasPPA.class.php";

$rsRecordSetPRO10 = new RecordSet();

$obTCMGOArquivoProgramasPPA = new TCMGOArquivoProgramasPPA();
$obTCMGOArquivoProgramasPPA->setDado('exercicio'  , Sessao::getExercicio());

//Tipo Registro 10
$obTCMGOArquivoProgramasPPA->recuperaTotalRecursos($rsRecordSetPRO10);

$i = 0;
foreach ($rsRecordSetPRO10->arElementos as $stChave) {
    $rsRecordSetPRO10->arElementos[$i]['nro_sequencial'] = $i+1;
    $i++;
}

//Tipo Registro 99
$arRecordSetPRO99 = array(
    '0' => array(
        'tipo_registro' => '99',
        'brancos' => '',
        'nro_sequencial' => $i+1,
    )
);

$rsRecordSetPRO99 = new RecordSet();
$rsRecordSetPRO99->preenche($arRecordSetPRO99);

if (count($rsRecordSetPRO10->getElementos()) > 0) {
    
    $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');    
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetPRO10);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_programa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_programa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_programa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objetivo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(300);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_recursos_ano_1");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_recursos_ano_2");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_recursos_ano_3");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_recursos_ano_4");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    
} 
    
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetPRO99);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");    
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");    
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    if ( Sessao::getExercicio() >= '2015' ) {
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(415);
    } else {
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(215);
    }

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$rsRecordSetRESPLIC10         = null;
$rsRecordSetRESPLIC20         = null;
$obTTCMGOResponsavelLicitacao = null;
$rsRecordSetRESPLIC99         = null;

?>