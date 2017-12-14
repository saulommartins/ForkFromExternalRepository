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
  * Página de Include Oculta - Exportação Arquivos TCEMG - LQD.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: LQD.csv.inc.php 64534 2016-03-10 16:35:02Z franver $
  * $Date: 2016-03-10 13:35:02 -0300 (Thu, 10 Mar 2016) $
  * $Author: franver $
  * $Rev: 64534 $
  *
*/
/**
* LQD.csv | Autor : Jean da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGLQD.class.php";

$rsRecordSetLQD10 = new RecordSet();
$rsRecordSetLQD11 = new RecordSet();
$rsRecordSetLQD12 = new RecordSet();

$obTTCEMGLQD = new TTCEMGLQD();
$obTTCEMGLQD->setDado('exercicio' , Sessao::getExercicio());
$obTTCEMGLQD->setDado('entidades' , $stEntidades);
$obTTCEMGLQD->setDado('mes'       , $stMes);
$obTTCEMGLQD->setDado('dt_inicial', $stDataInicial);
$obTTCEMGLQD->setDado('dt_final'  , $stDataFinal);

//Tipo Registro 10
$obTTCEMGLQD->recuperaExportacao10($rsRecordSetLQD10);

//Tipo Registro 11
$obTTCEMGLQD->recuperaExportacao11($rsRecordSetLQD11);

//Tipo Registro 12
$obTTCEMGLQD->recuperaExportacao12($rsRecordSetLQD12);

//Tipo Registro 99
$arRecordSetLQD99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecuperaLQD99 = new RecordSet();
$rsRecuperaLQD99->preenche($arRecordSetLQD99);


$inCount=0;    
if (count($rsRecordSetLQD10->getElementos()) > 0) {
    $stChave10 = '';
    foreach ($rsRecordSetLQD10->getElementos() as $arLQD10) {
        if ($stChave10 != $arLQD10['tipo_registro'].$arLQD10['cod_reduzido'].$arLQD10['cod_unidade'].$arLQD10['num_empenho'].$arLQD10['dt_empenho'].$arLQD10['num_liquidacao'] ) {

            $inCount++;
            $stChave10 = $arLQD10['tipo_registro'].$arLQD10['cod_reduzido'].$arLQD10['cod_unidade'].$arLQD10['num_empenho'].$arLQD10['dt_empenho'].$arLQD10['num_liquidacao'];

            $inCodReduzido10 = $arLQD10['cod_reduzido'];

            $rsBloco10 = 'rsBloco10_'.$inCount;
            unset($$rsBloco10);
            $$rsBloco10 = new RecordSet();
            $$rsBloco10->preenche(array($arLQD10));

            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco($$rsBloco10);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_liquidacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

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

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_liquidado");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_liquidante");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

            if (count($rsRecordSetLQD11->getElementos()) > 0) {
                $stChave11 = '';
                foreach ($rsRecordSetLQD11->getElementos() as $arLQD11) {
                    //Verifica se registro 11 bate com chave do registro 10
                    //if ($stChave10 == '10'.$arLQD11['cod_reduzido'].$arLQD11['cod_unidade'].$arLQD11['num_empenho'].$arLQD11['dt_empenho'].$arLQD11['num_liquidacao']) {
                        //Chave única do registro 11
                        if ( $inCodReduzido10 == $arLQD11['cod_reduzido'] ) {

                            $stChave11 = $arLQD11['tipo_registro'].$arLQD11['cod_reduzido'].$arLQD11['num_liquidacao'].$arLQD11['cod_font_recursos'];
                            $inCodReduzido11 = $arLQD11['cod_reduzido'];
                            $rsBloco11 = 'rsBloco11_'.$inCount;
                            unset($$rsBloco11);
                            $$rsBloco11 = new RecordSet();
                            $$rsBloco11->preenche(array($arLQD11));

                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco( $$rsBloco11 );

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_font_recursos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_fonte");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    
                            if (count($rsRecordSetLQD12->getElementos()) > 0) {
                                $stChave12 = '';
                                foreach ($rsRecordSetLQD12->getElementos() as $arLQD12) {
                                    //Chave única do registro 12
                                    if ($inCodReduzido11 ===  $arLQD12['cod_reduzido'] ) {
                                        $stChave12 = $arLQD12['tipo_registro']
                                                       .$arLQD12['cod_reduzido']
                                                       .$arLQD12['num_empenho']
                                                       .$arLQD12['dt_empenho']
                                                       .$arLQD12['num_liquidacao']
                                                       .$arLQD12['mes_competencia']
                                                       .$arLQD12['exercicio_comptencia'];
                                 
                                        $rsBloco12 = 'rsBloco12_'.$inCount;
                                        unset($$rsBloco12);
                                        $$rsBloco12 = new RecordSet();
                                        $$rsBloco12->preenche(array($arLQD12));
                                 
                                        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                        $obExportador->roUltimoArquivo->addBloco( $$rsBloco12 );
                                 
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
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                                 
                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_despesa_anterior");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                    }
                                }//FOREACH registro 12
                            }//IF Registro 12
                        }
                    //}
                }//FOREACH registro 11
            }//IF registro 11
        }
    }//FOREACH registro 10
} else {//IF registro 10
    $obExportador->roUltimoArquivo->addBloco($rsRecuperaLQD99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetLQD10 = null;
$rsRecordSetLQD11 = null;
$rsRecordSetLQD12 = null;
$rsRecuperaLQD99  = null;
$obTTCEMGLQD      = null;
?>