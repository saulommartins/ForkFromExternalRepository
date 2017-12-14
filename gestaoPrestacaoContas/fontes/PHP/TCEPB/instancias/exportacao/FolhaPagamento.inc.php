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
    * Página de geração do arquivo FolhaPagamento
    * Data de Criação   : 15/07/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once CAM_GPC_TPB_MAPEAMENTO.'TTPBFolhaPagamento.class.php';

$obTTPBFolhaPagamento = new TTPBFolhaPagamento();
$obTTPBFolhaPagamento->setDado('exercicio'       , Sessao::getExercicio() );
$obTTPBFolhaPagamento->setDado('stMes'           , $inMes.Sessao::getExercicio());
$obTTPBFolhaPagamento->setDado('stSchemaEntidade', $stSchemaEntidade);

// Verifica se deve ser feita a consulta ou apenas retornar o recordset vazio
// Foi feito isto para que se a entidade possuir unidade gestora, mas não tiver schema no RH, deve gerar o arquivo em branco
if ($boExecutaConsulta) {
    if($obTTPBFolhaPagamento->getDado('stSchemaEntidade') == ""){
        $obTTPBFolhaPagamento->recuperaTodos($arRecordSet[$stArquivo]);   
    }else{
        $arRecordSet[$stArquivo] = new RecordSet;
    }
} else {
    $arRecordSet[$stArquivo] = new RecordSet;
}

$obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('reservado_tce');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cpf');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_cargo');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
if (Sessao::getExercicio() < 2011) {
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('acumulacao');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
} else {
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('matricula');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
}
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes_ano_referencia');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_operacao');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_vantagem_desconto');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tipo_folha');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valor');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('reservado_tce');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
