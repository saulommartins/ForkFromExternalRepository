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

  * @author Analista:      Ane Caroline
  * @author Desenvolvedor: Lisane Morais
  *
  * @ignore
  * $Id: LDO.inc.php 61674 2015-02-24 16:08:16Z michel $
  * $Date:$
  * $Author:$
  * $Rev:$
  *
*/
include_once CAM_GPC_TGO_MAPEAMENTO."TTCMGOLDO.class.php";

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$obTTCMGOLDO = new TTCMGOLDO();
$obTTCMGOLDO->setDado('exercicio', Sessao::getExercicio());

//Tipo Registro 10
$obTTCMGOLDO->recuperaArquivoExportacao10($rsRegistro10, $boTransacao);
$obTTCMGOLDO->recuperaArquivoExportacao11($rsRegistro11, $boTransacao);
$obTTCMGOLDO->recuperaArquivoExportacao20($rsRegistro20, $boTransacao);
$obTTCMGOLDO->recuperaArquivoExportacao21($rsRegistro21, $boTransacao);
//$obTTCMGOLDO->debug();die();

$inCount = 0;
if ($rsRegistro10->getNumLinhas() > 0) {
    foreach ($rsRegistro10->arElementos as $stChave) {
        $stChave['numero_sequencial'] = ++$inCount;
        $stKey = $stChave['nro_ldo'] . $stChave['data_ldo'];
    
        $rsBloco = 'rsBloco_' . $inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($stChave));
            
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_ldo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_ldo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(296);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        
        //Registro 11    
        foreach ($rsRegistro11->arElementos as $stChave11) {        
            $stKey11 = $stChave11['nro_ldo'] . $stChave11['data_ldo'];
    
            if ($stKey11 === $stKey) {
                $stChave11['numero_sequencial'] = ++$inCount;
    
                $rsBloco = 'rsBloco_' . $inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($stChave11));
            
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meio_pub_ldo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_meio_ldo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(300);
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_pub_lei_ldo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
            }//if registro 11
        }//foreach registro 11
    }//foreach registro 10
    
    //Registro 20
    foreach ($rsRegistro20->arElementos as $stChave20) {
        $stChave20['numero_sequencial'] = ++$inCount;
        $stKey20 = $stChave20['exercicio'];
    
        $rsBloco = 'rsBloco_' . $inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($stChave20));
            
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_rec");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_desp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_rp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_rn");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_dcl");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(240);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    
        foreach ($rsRegistro21->arElementos as $stChave21) {        
            $stKey21 = $stChave21['exercicio'];
    
            if ($stKey21 === $stKey20) {
                $stChave21['numero_sequencial'] = ++$inCount;
    
                $rsBloco = 'rsBloco_' . $inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($stChave21));
            
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_arrec_1_bim");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);                
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_arrec_2_bim");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_arrec_3_bim");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_arrec_4_bim");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_arrec_5_bim");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_arrec_6_bim");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(226);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
            }//if registro 21
        }//foreach registro 21
    }//foreach registro 20
}

//Tipo Registro 99
$arRecordSet99[0] = array( 'tipo_registro'=> '99', 'brancos'=> '', 'numero_sequencial' => ++$inCount );

$rsRecordSet99 = new RecordSet();
$rsRecordSet99->preenche($arRecordSet99);

$obExportador->roUltimoArquivo->addBloco($rsRecordSet99);
    
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");    
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");    
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(310);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

unset($obTTCMGOLDO);
unset($rsRegistro10);
unset($rsRegistro11);
unset($rsRegistro20);
unset($rsRegistro21);
unset($arRecordSet99);
unset($rsRecordSet99);

?>


 
