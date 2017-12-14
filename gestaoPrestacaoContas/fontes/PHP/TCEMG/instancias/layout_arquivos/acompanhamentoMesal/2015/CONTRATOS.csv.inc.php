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
  * Página de Include Oculta - Exportação Arquivos TCEMG - CONTRATOS.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: CONTRATOS.csv.inc.php 62302 2015-04-20 17:54:18Z franver $
  * $Date: 2015-04-20 14:54:18 -0300 (Mon, 20 Apr 2015) $
  * $Author: franver $
  * $Rev: 62302 $
  *
*/
/**
* CONTRATOS.csv | Autor : Michel Teixeira
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGCONTRATOS.class.php";

$rsRecordSetCONTRATOS10 = new RecordSet();
$rsRecordSetCONTRATOS11 = new RecordSet();
$rsRecordSetCONTRATOS12 = new RecordSet();
$rsRecordSetCONTRATOS13 = new RecordSet();
$rsRecordSetCONTRATOS20 = new RecordSet();
$rsRecordSetCONTRATOS21 = new RecordSet();
$rsRecordSetCONTRATOS30 = new RecordSet();
$rsRecordSetCONTRATOS40 = new RecordSet();


$obTTCEMGCONTRATOS = new TTCEMGCONTRATOS();
$obTTCEMGCONTRATOS->setDado('exercicio' , Sessao::getExercicio());
$obTTCEMGCONTRATOS->setDado('entidade'  , $stEntidades);
$obTTCEMGCONTRATOS->setDado('dt_inicial', $stDataInicial);
$obTTCEMGCONTRATOS->setDado('dt_final'  , $stDataFinal);

//Tipo Registro 10
$obTTCEMGCONTRATOS->recuperaContrato10($rsRecordSetCONTRATOS10);

//Tipo Registro 11
$obTTCEMGCONTRATOS->recuperaContrato11($rsRecordSetCONTRATOS11);

//Tipo Registro 12
$obTTCEMGCONTRATOS->recuperaContrato12($rsRecordSetCONTRATOS12);

//Tipo Registro 13
$obTTCEMGCONTRATOS->recuperaContrato13($rsRecordSetCONTRATOS13);

//Tipo Registro 20
$obTTCEMGCONTRATOS->recuperaContrato20($rsRecordSetCONTRATOS20);

//Tipo Registro 21
$obTTCEMGCONTRATOS->recuperaContrato21($rsRecordSetCONTRATOS21);

//Tipo Registro 30
$obTTCEMGCONTRATOS->recuperaContrato30($rsRecordSetCONTRATOS30);

//Tipo Registro 40
$obTTCEMGCONTRATOS->recuperaContrato40($rsRecordSetCONTRATOS40);

//Tipo Registro 99
$arRecordSetCONTRATOS99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetCONTRATOS99 = new RecordSet();
$rsRecordSetCONTRATOS99->preenche($arRecordSetCONTRATOS99);

if (count($rsRecordSetCONTRATOS10->getElementos()) > 0) {
    $inCount = 0;
    foreach ($rsRecordSetCONTRATOS10->getElementos() as $arContrato10) {
        $inCount++;
        $stChave10 = $arContrato10['codcontrato'];
        
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arContrato10));
        
        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codcontrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesub");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrocontrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerciciocontrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dataassinatura");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contdeclicitacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgaoresp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesubresp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroprocesso");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicioprocesso");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoprocesso");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("naturezaobjeto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objetocontrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(500);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoinstrumento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("datainiciovigencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("datafinalvigencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlcontrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("formafornecimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("formapagamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("prazoexecucao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("multarescisoria");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("multa_inadimplemento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("garantia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpfsignatariocontratante");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("datapublicacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("veiculodivulgacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
        
        if (count($rsRecordSetCONTRATOS11->getElementos()) > 0) {
            
            foreach ($rsRecordSetCONTRATOS11->getElementos() as $arContrato11) {
                $stChave11 = $arContrato11['codcontrato'];
                
                if ($stChave10 === $stChave11) {
                    $inCount++;
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arContrato11));
                    
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codcontrato");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
            
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("coditem");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidadeitem");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valorunitarioitem");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                    
                    if (count($rsRecordSetCONTRATOS12->getElementos()) > 0) {
                        
                        foreach ($rsRecordSetCONTRATOS12->getElementos() as $arContrato12) {
                            $stChave12 = $arContrato12['codcontrato'];
                            
                            if ($stChave11 === $stChave12) {
                                $inCount++;
                                $rsBloco = 'rsBloco_'.$inCount;
                                unset($$rsBloco);
                                $$rsBloco = new RecordSet();
                                $$rsBloco->preenche(array($arContrato12));
                                
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codcontrato");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesub");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfuncao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codsubfuncao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codprograma");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("idacao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("idsubacao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("naturezadespesa");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfontrecursos");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlrecurso");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            }
                        }
                    }
                }
            }
        }
        
        if (count($rsRecordSetCONTRATOS13->getElementos()) > 0) {
                                    
           foreach ($rsRecordSetCONTRATOS13->getElementos() as $arContrato13) {
              $stChave13 = $arContrato13['codcontrato'];
              
              if ($stChave10 === $stChave13) {
                 $inCount++;
                 $rsBloco = 'rsBloco_'.$inCount;
                 unset($$rsBloco);
                 $$rsBloco = new RecordSet();
                 $$rsBloco->preenche(array($arContrato13));
                 
                 $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                 $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                 
                 $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                 $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                 
                 $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codcontrato");
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                 $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                 
                 $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipodocumento");
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                 $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                 
                 $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrodocumento");
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                 $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                 
                 $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpfrepresentantelegal");
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                 $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
              }
           }
        }
        
        if (count($rsRecordSetCONTRATOS20->getElementos()) > 0) {
            
            foreach ($rsRecordSetCONTRATOS20->getElementos() as $arContrato20) {
                $stChave20 = $arContrato20['codcontrato'];
                
                if ($stChave10 === $stChave20) {
                    $inCount++;
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arContrato20));
                    
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codaditivo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesub");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrocontrato");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dataassinaturacontrato");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroaditivo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dataassinaturaaditivo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("alteracaovalor");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipotermoaditivo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dscalteracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("novadatatermino");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valoraditivo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("datapublicacao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("veiculodivulgacao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
                    
                    if (count($rsRecordSetCONTRATOS21->getElementos()) > 0) {
                        
                        foreach ($rsRecordSetCONTRATOS21->getElementos() as $arContrato21) {
                            $stChave21 = $arContrato21['codcontrato'];
                            
                            if ($stChave20 === $stChave21) {
                                $inCount++;
                                $rsBloco = 'rsBloco_'.$inCount;
                                unset($$rsBloco);
                                $$rsBloco = new RecordSet();
                                $$rsBloco->preenche(array($arContrato21));
                                
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codaditivo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("coditem");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoalteracaoitem");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valorunitario");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);            
                            }
                        }
                    }
                }
            }
        }
                
        if (count($rsRecordSetCONTRATOS30->getElementos()) > 0) {
            foreach ($rsRecordSetCONTRATOS30->getElementos() as $arContrato30) {
                $stChave30 = $arContrato30['codcontrato'];
                
                if ($stChave10 === $stChave30) {
                    $inCount++;
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arContrato30));
                    
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesub");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrocontrato");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dataassinaturacontrato");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoapostila");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroapostila");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(3);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dataapostila");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoalteracaoapostila");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dscalteracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valorapostila");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                }
            }
        }
        
        if (count($rsRecordSetCONTRATOS40->getElementos()) > 0) {
            foreach ($rsRecordSetCONTRATOS40->getElementos() as $arContrato40) {
                $stChave40 = $arContrato40['codcontrato'];
                
                if ($stChave10 === $stChave40) {
                    $inCount++;
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arContrato40));
                    
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesub");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrocontrato");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dataassinaturacontrato");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("datarescisao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valorrescisao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                }
            }
        }
    }
} else {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetCONTRATOS99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecordSetCONTRATOS10 = null;
$rsRecordSetCONTRATOS11 = null;
$rsRecordSetCONTRATOS12 = null;
$rsRecordSetCONTRATOS13 = null;
$rsRecordSetCONTRATOS20 = null;
$rsRecordSetCONTRATOS21 = null;
$rsRecordSetCONTRATOS30 = null;
$rsRecordSetCONTRATOS40 = null;
$obTTCEMGCONTRATOS       = null;
$rsRecordSetCONTRATOS99 = null;

?>