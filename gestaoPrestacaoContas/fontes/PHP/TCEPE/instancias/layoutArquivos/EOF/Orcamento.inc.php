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
  * Layout exportação TCE-PE arquivo : Orcamento
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor:
  *
  * @ignore
  * $Id: Orcamento.inc.php 60282 2014-10-10 12:44:25Z lisiane $
  * $Date: 2014-10-10 09:44:25 -0300 (Fri, 10 Oct 2014) $
  * $Author: lisiane $
  * $Rev: 60282 $
  *
*/

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEOrcamento.class.php";

$boTransacao = new Transacao();
$obTTCEPEOrcamento = new TTCEPEOrcamento();

$obTTCEPEOrcamento->setDado('exercicio', Sessao::getExercicio());
$obTTCEPEOrcamento->recuperaArquivoTCEPEOrcamento( $rsRecordSet, "" , "" , $boTransacao );

foreach ($rsRecordSet->getElementos() as $configuracao) {
    switch ($configuracao['parametro']) {
        //Ano de vigência da Lei orçamentária
        case 'tcepe_ano_vigencia':
            $arDados['tcepe_ano_vigencia'] = $configuracao['valor'];
        break;
        
        //Data de aprovação da LOA DDMMAAAA 
        case 'tcepe_data_aprovacao_LOA':
            $stData =  str_replace("/", "", $configuracao['valor']);
            $arDados['tcepe_data_aprovacao_LOA'] = $stData;
        break;
        
        //Número da Lei orçamentária LOA NNNNNAAAA 
        case 'tcepe_lei_orcamentaria_LOA':
            $pegaNumNorma = SistemaLegado::pegaDado("num_norma","normas.norma","WHERE cod_norma = ".$configuracao['valor']."");
            $pegaExercicioNorma = SistemaLegado::pegaDado("exercicio","normas.norma","WHERE cod_norma = ".$configuracao['valor']."");            
            $inLei = $pegaNumNorma.$pegaExercicioNorma;
            $arDados['tcepe_lei_orcamentaria_LOA'] = $inLei;
        break;
        
        //Data de aprovação da LDO DDMMAAAA 
        case 'tcepe_data_aprovacao_LDO':
            $stData =  str_replace("/", "", $configuracao['valor']);
            $arDados['tcepe_data_aprovacao_LDO'] = $stData;
        break;
        
        //Número da Lei orçamentária LDO NNNNNAAAA 
        case 'tcepe_lei_orcamentaria_LDO':
            $pegaNumNorma = SistemaLegado::pegaDado("num_norma","normas.norma","WHERE cod_norma = ".$configuracao['valor']."");
            $pegaExercicioNorma = SistemaLegado::pegaDado("exercicio","normas.norma","WHERE cod_norma = ".$configuracao['valor']."");            
            $inLei =$pegaNumNorma.$pegaExercicioNorma;
            $arDados['tcepe_lei_orcamentaria_LDO'] = $inLei;
        break;
        
        //Data de aprovação da PPA DDMMAAAA 
        case 'tcepe_data_aprovacao_PPA':
            $stData =  str_replace("/", "", $configuracao['valor']);
            $arDados['tcepe_data_aprovacao_PPA'] = $stData;
        break;
        
        //Número da Lei orçamentária PPA NNNNNAAAA
        case 'tcepe_lei_orcamentaria_PPA':
            $pegaNumNorma = SistemaLegado::pegaDado("num_norma","normas.norma","WHERE cod_norma = ".$configuracao['valor']."");            
            $pegaExercicioNorma = SistemaLegado::pegaDado("exercicio","normas.norma","WHERE cod_norma = ".$configuracao['valor']."");            
            $inLei = $pegaNumNorma.$pegaExercicioNorma;
            $arDados['tcepe_lei_orcamentaria_PPA'] = $inLei;
        break;
    }
}

$rsDados = new RecordSet();
$rsDados->preenche( array($arDados) );

$obExportador->roUltimoArquivo->addBloco($rsDados);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tcepe_ano_vigencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tcepe_data_aprovacao_LOA");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tcepe_lei_orcamentaria_LOA");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tcepe_data_aprovacao_LDO");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tcepe_lei_orcamentaria_LDO");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tcepe_data_aprovacao_PPA");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tcepe_lei_orcamentaria_PPA");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

?>