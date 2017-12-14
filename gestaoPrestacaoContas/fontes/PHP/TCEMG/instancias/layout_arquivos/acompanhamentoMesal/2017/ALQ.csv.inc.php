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
  * Página de Include Oculta - Exportação Arquivos TCEMG - ALQ.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: ALQ.csv.inc.php 62269 2015-04-15 18:28:39Z franver $
  * $Date: 2015-04-15 15:28:39 -0300 (Qua, 15 Abr 2015) $
  * $Author: franver $
  * $Rev: 62269 $
  *
*/
/**
* ALQ.csv | Autor : Jean da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGALQ.class.php";

$rsRecordSetALQ10 = new RecordSet();
$rsRecordSetALQ11 = new RecordSet();
$rsRecordSetALQ12 = new RecordSet();

$obTTCEMGALQ = new TTCEMGALQ();
$obTTCEMGALQ->setDado('exercicio', Sessao::getExercicio());
$obTTCEMGALQ->setDado('entidades', $stEntidades);
$obTTCEMGALQ->setDado('dt_inicial', $stDataInicial);
$obTTCEMGALQ->setDado('dt_final', $stDataFinal);

$stOrdem = "ORDER BY num_empenho";

//Tipo Registro 10
$obTTCEMGALQ->recuperaExportacaoALQ10($rsRecordSetALQ10,$stOrdem);

//Tipo Registro 11
$obTTCEMGALQ->recuperaExportacaoALQ11($rsRecordSetALQ11);

//Tipo Registro 12
$obTTCEMGALQ->recuperaExportacaoALQ12($rsRecordSetALQ12);

//Tipo Registro 99
$arRecordSetALQ99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecuperaALQ99 = new RecordSet();
$rsRecuperaALQ99->preenche($arRecordSetALQS99);

$inCount=0;
if (count($rsRecordSetALQ10->getElementos()) > 0) {
    $stChave10 = '';
    foreach ($rsRecordSetALQ10->getElementos() as $arALQ10) {
        $stChaveAux = $arALQ10['tipo_registro'].$arALQ10['cod_reduzido'].$arALQ10['codunidadesub'].$arALQ10['num_empenho'].$arALQ10['dt_empenho'].$arALQ10['num_liquidacao'].$arALQ10['num_anulacao'];
        $arALQ10['cod_reduzido']=$arALQ10['cod_reduzido'].$arALQ10['num_liquidacao'].$arALQ10['num_anulacao'];
        if($stChave10 === $stChaveAux){
            $boDiferente = false;
        } else {
            $boDiferente = true;
        }

        if ($boDiferente) {

            $inCount++;
            $stChave = $arALQ10['num_empenho'].$arALQ10['num_liquidacao'].$arALQ10['num_anulacao'];
            $stChave10 = $stChaveAux;

            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arALQ10));

            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco );    

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesub");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_empenho");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_empenho");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_liquidacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_liquidacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_anulacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_anulacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_liquidacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("justificativa_anulacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(500);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulado");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

            if (count($rsRecordSetALQ11->getElementos()) > 0) {
                $stChave11 = '';
                foreach ($rsRecordSetALQ11->getElementos() as $arALQ11) {
                    if ($stChave11 <> $arALQ11['tipo_registro'].$arALQ11['cod_reduzido'].$arALQ11['num_liquidacao'].$arALQ11['num_anulacao'].$arALQ11['cod_fonte_recurso'].$arALQ11['num_empenho']) {
                        $stChaveElemento = $arALQ11['num_empenho'].$arALQ11['num_liquidacao'].$arALQ11['num_anulacao'];
                        $stChave11 = $arALQ11['tipo_registro'].$arALQ11['cod_reduzido'].$arALQ11['num_liquidacao'].$arALQ11['num_anulacao'].$arALQ11['cod_fonte_recurso'].$arALQ11['num_empenho'];
                        $arALQ11['cod_reduzido']=$arALQ11['cod_reduzido'].$arALQ11['num_liquidacao'].$arALQ11['num_anulacao'];
                        if($stChaveElemento===$stChave){

                            $inCount++;
                            $arALQ11['tipo_registro'] = 11;
                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arALQ11));

                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_recurso");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);        

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulado_fonte");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            
                            if (count($rsRecordSetALQ12->getElementos()) > 0) {
                                $stChave12 = '';
                                foreach ($rsRecordSetALQ12->getElementos() AS $arALQ12) {
                                    if ($stChave12 <> $arALQ12['tipo_registro'].$arALQ12['cod_reduzido'].$arALQ12['num_liquidacao'].$arALQ12['num_anulacao'].$arALQ12['mes_competencia'].$arALQ12['exercicio_competencia']) {
                                        $stChaveElemento = $arALQ12['num_empenho'].$arALQ12['num_liquidacao'].$arALQ12['num_anulacao'];
                                        $stChave12 = $arALQ12['tipo_registro'].$arALQ12['cod_reduzido'].$arALQ12['num_liquidacao'].$arALQ12['num_anulacao'].$arALQ12['mes_competencia'].$arALQ12['exercicio_competencia'];
                                        $arALQ12['cod_reduzido']=$arALQ12['cod_reduzido'].$arALQ12['num_liquidacao'].$arALQ12['num_anulacao'];
                                        if($stChaveElemento===$stChave){
                                            $inCount++;
                                            $rsBloco = 'rsBloco_'.$inCount;
                                            unset($$rsBloco);
                                            $$rsBloco = new RecordSet();
                                            $$rsBloco->preenche(array($arALQ12));

                                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                                            
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                                            
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                                            
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes_competencia");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
                                            
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_competencia");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                                            
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulado_dsp_exercicio_ant");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
} else {
   $obExportador->roUltimoArquivo->addBloco($rsRecuperaALQ99);
   $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
   $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
   $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
   $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecordSetALQ10 = null;
$rsRecordSetALQ11 = null;
$rsRecordSetALQ12 = null;
$obTTCEMGALQ = null;
$rsRecuperaALQ99 = null;

?>