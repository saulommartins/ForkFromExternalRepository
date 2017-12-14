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
  * $Id: LPP.inc.php 61672 2015-02-24 14:21:04Z michel $
  * $Date:$
  * $Author:$
  * $Rev:$
  *
*/
include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOConfiguracaoLeisPPA.class.php" );

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$obTTCMGOConfiguracaoLeisPPA = new TTCMGOConfiguracaoLeisPPA();
$obTTCMGOConfiguracaoLeisPPA->recuperaExportacao10( $rsRegistro10, "", "", $boTransacao );
$obTTCMGOConfiguracaoLeisPPA->recuperaExportacao11( $rsRegistro11, "", "", $boTransacao );
$obTTCMGOConfiguracaoLeisPPA->recuperaExportacao20( $rsRegistro20, "", "", $boTransacao );
$obTTCMGOConfiguracaoLeisPPA->recuperaExportacao21( $rsRegistro21, "", "", $boTransacao );

//Tipo Registro 99
$arRecordSet99 = array(    
    'tipo_registro' => '99',
    'brancos' => '',
    'numero_sequencial' => 1
);

$inCount = 0;
$arDescPubLei = array();
$arDescPubLei[1] = 'Diário Oficial do Estado';
$arDescPubLei[2] = 'Diário Oficial do Município';
$arDescPubLei[3] = 'Placar da Prefeitura ou da Câmara Municipal';
$arDescPubLei[4] = 'Jornal de grande circulação';
$arDescPubLei[5] = 'Diário Oficial da União';
$arDescPubLei[9] = 'Endereço eletrônico completo (Internet)';
        
if ($rsRegistro10->getNumLinhas() > 0) {    
    foreach ($rsRegistro10->arElementos as $stChave) {
        $stChave['numero_sequencial'] = ++$inCount;
        $stKey = $stChave['nro_lei_ppa'] . $stChave['data_pub_lei_ppa'];
    
        $rsBloco = 'rsBloco_' . $inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($stChave));
            
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_lei_ppa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_lei_ppa");
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
            $stKey11 = $stChave11['nro_lei_ppa'] . $stChave11['data_pub_lei_ppa'];
    
            if ($stKey11 === $stKey) {
                if($stChave11['meio_pub_ppa']!=9)
                    $stChave11['desc_meio_ppa'] = $arDescPubLei[$stChave11['meio_pub_ppa']];
                
                $stChave11['numero_sequencial'] = ++$inCount;
    
                $rsBloco = 'rsBloco_' . $inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($stChave11));
            
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meio_pub_ppa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_meio_ppa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(300);
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_pub_lei_ppa");
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
        $stKey20 = $stChave20['nro_lei_alt_ppa'] . $stChave20['data_lei_alt_ppa'];
    
        $rsBloco = 'rsBloco_' . $inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($stChave20));
            
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_lei_alt_ppa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_lei_alt_ppa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(296);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    
        foreach ($rsRegistro21->arElementos as $stChave21) {        
            $stKey21 = $stChave21['nro_lei_alt_ppa'] . $stChave21['data_lei_alt_ppa'];
    
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
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meio_pub_alt_ppa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_meio_alt_ppa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(300);
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_pub_lei_alt_ppa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
            }//if registro 21
        }//foreach registro 21
    }//foreach registro 20
}else{
    //Tipo Registro 99
    $rsRegistro99 = new RecordSet();
    $rsRegistro99->preenche($arRecordSet99);

    $obExportador->roUltimoArquivo->addBloco($rsRegistro99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(310);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
}

unset($rsRegistro10);
unset($rsRegistro11);
unset($rsRegistro20);
unset($rsRegistro21);
unset($arRecordSet99);

?>


 
