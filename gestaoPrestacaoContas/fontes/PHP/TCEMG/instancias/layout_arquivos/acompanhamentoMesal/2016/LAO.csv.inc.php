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
  * Página de Include Oculta - Exportação Arquivos TCEMG - LAO.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: LAO.csv.inc.php 62439 2015-05-11 13:14:01Z michel $
  * $Date: 2015-05-11 10:14:01 -0300 (Seg, 11 Mai 2015) $
  * $Author: michel $
  * $Rev: 62439 $
  *
*/
/**
* LAO.csv | Autor : Jean da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGLeiAlteracaoOrcamentaria.class.php";

$rsRecordSetLAO10 = new RecordSet();
$rsRecordSetLAO11 = new RecordSet();
$rsRecordSetLAO20 = new RecordSet();
$rsRecordSetLAO21 = new RecordSet();

$obTTCEMGLeiAlteracaoOrcamentaria = new TTCEMGLeiAlteracaoOrcamentaria();

$obTTCEMGLeiAlteracaoOrcamentaria->setDado('exercicio', Sessao::getExercicio());

$entidades = preg_split( '/,/', $stEntidades);

foreach ($entidades as $i) {
    if ($i == '2') {

        $obTTCEMGLeiAlteracaoOrcamentaria->setDado('entidades', 2);
        $obTTCEMGLeiAlteracaoOrcamentaria->setDado('mes', $stMes);

        //Tipo Registro 10
        $obTTCEMGLeiAlteracaoOrcamentaria->recuperaExportacao10($rsRecordSetLAO10);

        //Tipo Registro 11
        $obTTCEMGLeiAlteracaoOrcamentaria->recuperaExportacao11($rsRecordSetLAO11);

        //Tipo Registro 20
        $obTTCEMGLeiAlteracaoOrcamentaria->recuperaExportacao20($rsRecordSetLAO20);

        //Tipo Registro 21
        $obTTCEMGLeiAlteracaoOrcamentaria->recuperaExportacao21($rsRecordSetLAO21);
    }
}

//Tipo Registro 99
$arRecordSetLAO99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetLAO99 = new RecordSet();
$rsRecordSetLAO99->preenche($arRecordSetLAO99);
$boRegistro99 = true;

$inCount = 0;
if (count($rsRecordSetLAO10->getElementos()) > 0) {

    foreach ($rsRecordSetLAO10->getElementos() as $arLAO10) {
        $inCount++;
        $boRegistro99 = false;
        $stChave = $arLAO10['tipo_registro'].$arLAO10['cod_orgao'].$arLAO10['nro_lei_alteracao'];
        $inNumLei = $arLAO10['nro_lei_alteracao'];

        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arLAO10));

        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_lei_alteracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lei_alteracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        //11
        if (count($rsRecordSetLAO11->getElementos()) > 0) {

            foreach ($rsRecordSetLAO11->getElementos() as $arLAO11) {
                $inCount++;
                $stChave2 = $arLAO11['tipo_registro'].$arLAO11['nro_lei_alteracao'].$arLAO11['tipo_lei_alteracao'];

                if ($inNumLei == $arLAO11['nro_lei_alteracao']) {

                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arLAO11));
                    
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_lei_alteracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lei_alteracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERIC_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("artigo_lei_alteracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_artigo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(512);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_autorizacao_alteracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                }
            }
        }
    }
}
unset($rsRecordSetLAO10, $rsRecordSetLAO11);
 //20
if (count($rsRecordSetLAO20->getElementos()) > 0) {

    foreach ($rsRecordSetLAO20->getElementos() as $arLAO20) {
        $inCount++;
        $boRegistro99 = false;
        $stChave = $arLAO20['tipo_registro'].$arLAO20['cod_orgao'].$arLAO20['nro_lei_alter_orcam'];
        $inNumLei2 = $arLAO20['nro_lei_alter_orcam'];

        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arLAO20));

        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_lei_alter_orcam");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lei_alter_orcam");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        if (count($rsRecordSetLAO21->getElementos()) > 0) {

            foreach ($rsRecordSetLAO21->getElementos() as $arLAO21) {
                $inCount++;

                $stChave2 = $arLAO21['tipo_registro'].$arLAO21['nro_lei_alter_orcam'].$arLAO21['tipo_autorizacao'];

                if ($inNumLei2 == $arLAO21['nro_lei_alter_orcam']) {

                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arLAO21));

                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_lei_alter_orcam");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_autorizacao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("artigo_lei_alter_orcamento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_artigo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(512);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("novo_percentual");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);
                }
            }
        }
    }
}

if ($boRegistro99) {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetLAO99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetLAO20 = null;
$rsRecordSetLAO21 = null;
$obTTCEMGLeiAlteracaoOrcamentaria = null;
$rsRecordSetLAO99 = null;
?>