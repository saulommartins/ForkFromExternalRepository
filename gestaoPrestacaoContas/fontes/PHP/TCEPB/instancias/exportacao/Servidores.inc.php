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
    * Página de geração do arquivo Servidores.txt
    * Data de Criação   : 13/07/2009

    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package URBEM
    * @subpackage TCEPB
*/

include_once CAM_GPC_TPB_MAPEAMENTO.'TTPBServidores.class.php';

$obTTPBServidores = new TTPBServidores;
$obTTPBServidores->setDado('stExercicio'     , Sessao::getExercicio() );
$obTTPBServidores->setDado('inMes'           , $inMes);
$obTTPBServidores->setDado('stSchemaEntidade', $stSchemaEntidade);

// Verifica se deve ser feita a consulta ou apenas retornar o recordset vazio
// Foi feito isto para que se a entidade possuir unidade gestora, mas não tiver schema no RH, deve gerar o arquivo em branco
if ($boExecutaConsulta) {
    $obTTPBServidores->recuperaRelacionamento($arRecordSet[$stArquivo]);
} else {
    $arRecordSet[$stArquivo] = new RecordSet;
}

$obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('reservado_tse');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cpf');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('rg');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('orgao_emissor');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nom_cgm');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('dt_nascimento');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('sexo');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
if (Sessao::getExercicio() > 2010) {
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('deficiente');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
}
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('reservado_tse');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
