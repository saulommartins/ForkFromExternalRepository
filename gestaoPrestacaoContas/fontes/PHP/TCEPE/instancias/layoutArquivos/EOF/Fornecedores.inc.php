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
  * Layout exportação TCE-PE arquivo : Fornecedores
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: Michel Teixeira
  *
  * @ignore
  * $Id: Fornecedores.inc.php 60191 2014-10-06 16:47:58Z michel $
  * $Date: 2014-10-06 13:47:58 -0300 (Mon, 06 Oct 2014) $
  * $Author: michel $
  * $Rev: 60191 $
  *
*/

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once(CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEFornecedores.class.php');

$boTransacao = new Transacao();

$obTTCEPEFornecedores = new TTCEPEFornecedores();
$obTTCEPEFornecedores->setDado('stEntidades', $inCodEntidade          );
$obTTCEPEFornecedores->setDado('dtInicial', $stDataInicial            );
$obTTCEPEFornecedores->setDado('dtFinal', $stDataFinal                );
$obTTCEPEFornecedores->setDado('stExercicio', Sessao::getExercicio()  );
$obTTCEPEFornecedores->recuperaFornecedor($rsRecordSet);

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sigla_uf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_municipio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

?>