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
    * Data de Criação   : 02/03/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: ARC.inc.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

    include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOAnulacaoRecita.class.php" );

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');

    $obTMapeamento = new TTCMGOAnulacaoReceita;
    $obTMapeamento->setDado('exercicio'  , Sessao::getExercicio() );
    $obTMapeamento->setDado('dtInicio'   , $arFiltroRelatorio['stDataInicial'] );
    $obTMapeamento->setDado('dtFim'   	 , $arFiltroRelatorio['stDataFinal']   );
    $obTMapeamento->setDado('stEntidades', $stEntidades       );
    $obTMapeamento->recuperaTodos($arRecordSet[$stArquivo]    );

    $i = 0;
    foreach ($arRecordSet[$stArquivo]->arElementos as $stChave) {
        $arRecordSet[$stArquivo]->arElementos[$i]['numero_registro'] = $i+1;
        $i++;
    }

    $obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rubrica_deduzida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(09);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rubrica_acrescida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(09);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacao_deduzida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("TEXTO");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacao_acrescida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("TEXTO");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_deduzido");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_acrescido");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    $arTemp[0] = array( 'tipo_registro'=> '99', 'espacador'=> '', 'numero_sequencial' => $i+1 );

    $arRecordSet[$stArquivo] = new RecordSet();
    $arRecordSet[$stArquivo]->preenche( $arTemp );

    $obExportador->roUltimoArquivo->addBloco( $arRecordSet[$stArquivo] );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("TEXTO");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 246 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
