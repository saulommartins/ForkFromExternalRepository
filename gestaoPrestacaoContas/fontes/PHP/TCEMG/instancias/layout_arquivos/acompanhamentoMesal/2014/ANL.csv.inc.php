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
  * $Id: ANL.csv.inc.php 62269 2015-04-15 18:28:39Z franver $
  * $Date: 2015-04-15 15:28:39 -0300 (Wed, 15 Apr 2015) $
  * $Author: franver $
  * $Rev: 62269 $
  *
*/
/**
* ANL.csv | Autor : Eduardo Schitz EM ANDAMENTO TEM QUE SER CRIADO O DOCUMENTO DE ANÁLISE AINDA
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGAnulacaoEmpenho.class.php";

$rsRecordSetANL10 = new RecordSet();
$rsRecordSetANL11 = new RecordSet();

$obTTCEMGAnulacaoEmpenho = new TTCEMGAnulacaoEmpenho();
$obTTCEMGAnulacaoEmpenho->setDado('entidades', $stEntidades);
$obTTCEMGAnulacaoEmpenho->setDado('mes', $stMes);

//Tipo Registro 10
$obTTCEMGAnulacaoEmpenho->recuperaExportacao10($rsRecordSetANL10);

//Tipo Registro 11
$obTTCEMGAnulacaoEmpenho->recuperaExportacao11($rsRecordSetANL11);

//Tipo Registro 99
$arRecordSetANL99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetANL99 = new RecordSet();
$rsRecordSetANL99->preenche($arRecordSetANL99);

$inCount = 0;

if (count($rsRecordSetANL10->getElementos()) > 0) {
    $stChave10 = '';

    foreach ($rsRecordSetANL10->getElementos() as $arANL10) {
        if ($stChave10 <> $arANL10['tipo_registro'].$arANL10['cod_orgao'].$arANL10['cod_unidade'].$arANL10['num_empenho'].$arANL10['num_anulacao']) {

            $inCount++;
            $stChave = $arANL10['num_empenho'].$arANL10['num_anulacao'];
            $stChave10 = $arANL10['tipo_registro'].$arANL10['cod_orgao'].$arANL10['cod_unidade'].$arANL10['num_empenho'].$arANL10['num_anulacao'];

            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arANL10));
            
            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_empenho");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_empenho");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_anulacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_anulacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_anulacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espc_anl_emp");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(200);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulado");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
            
            if (count($rsRecordSetANL11->getElementos()) > 0) {
                $stChave11 = '';
                foreach ($rsRecordSetANL11->getElementos() as $arANL11) {

                    if ($stChave11 <> $arANL11['tipo_registro'].$arANL11['cod_unidade'].$arANL11['num_empenho'].$arANL11['num_anulacao'].$arANL11['cod_fonte_recurso']) {
                        $stChaveElemento = $arANL11['num_empenho'].$arANL11['num_anulacao'];
                        $stChave11 = $arANL11['tipo_registro'].$arANL11['cod_unidade'].$arANL11['num_empenho'].$arANL11['num_anulacao'].$arANL11['cod_fonte_recurso'];

                        if ($stChave === $stChaveElemento) {
                            $inCount++;
                            $arANL11['tipo_registro'] = 11;
                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arANL11));

                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_empenho");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_anulacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_recurso");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulacao_fonte");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                        }
                    }
                }
            }
        }
    }
} else {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetANL99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetANL10 = null;
$rsRecordSetANL11 = null;
$obTTCEMGAnulacaoEmpenho = null;
$rsRecordSetANL99 = null;
?>