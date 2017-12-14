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
  * Página de Include Oculta - Exportação Arquivos TCEMG - REC.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: REC.csv.inc.php 62407 2015-05-05 14:45:37Z arthur $
  * $Date: 2015-05-05 11:45:37 -0300 (Tue, 05 May 2015) $
  * $Author: arthur $
  * $Rev: 62407 $
  *
*/
/**
* REC.csv | Autor : Jean da Silva
* Pode haver mudança da classe de Negócio RTCEMGExportacaoArquivosPlanejamento.class.php
* Assim o mapeamento TExportacaoTCEMGItem.class.php deve ir junto na nova classe de Negócio
*/

include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGExportacaoREC.class.php";

$rsRecordSetREC10 = new RecordSet();
$rsRecordSetREC11 = new RecordSet();

$obTTCEMGExportacaoREC = new TTCEMGExportacaoREC();
$obTTCEMGExportacaoREC->setDado('entidades', $stEntidades);
$obTTCEMGExportacaoREC->setDado('dt_inicial', $stDataInicial);
$obTTCEMGExportacaoREC->setDado('dt_final', $stDataFinal);

//Tipo Registro 10
$obTTCEMGExportacaoREC->recuperaReceitaExportacao10($rsRecordSetREC10);

//Tipo Registro 11
$obTTCEMGExportacaoREC->recuperaReceitaExportacao11($rsRecordSetREC11);

//Tipo Registro 99
$arRecordSetRECS99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetREC99 = new RecordSet();
$rsRecordSetREC99->preenche($arRecordSetRECS99);
   
$stChave10 = '';    
$inCount = 0;

if (count($rsRecordSetREC10->getElementos() ) > 0) {

    foreach ($rsRecordSetREC10->getElementos() as $arREC10){
        
        // retirado  a chave $arREC10['identificador_deducao'] pois nao haver dados
        if ($stChave10 != $arREC10['tipo_registro'].$arREC10['cod_orgao'].$arREC10['deducao_receita'].$arREC10['natureza_receita']){
            
            $inCount++;
            $stChave10 = $arREC10['tipo_registro'].$arREC10['cod_orgao'].$arREC10['deducao_receita'].$arREC10['natureza_receita'];
        
            $rsBloco10 = 'rsBloco10_'.$inCount;
            unset($$rsBloco10);
            $$rsBloco10 = new RecordSet();
            $$rsBloco10->preenche(array($arREC10));
            
            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco($$rsBloco10);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_receita");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("deducao_receita");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificador_deducao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_receita");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_previsto");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_NUMERICO_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
        
            if (count($rsRecordSetREC11->getElementos()) > 0) {
                $stChave11 = '';
                foreach ($rsRecordSetREC11->getElementos() as $arREC11){
                
                    if ("10".$arREC10['cod_receita'] == "10".$arREC11['cod_receita']){
                
                        if ($stChave11 != $arREC11['tipo_registro'].$arREC11['cod_receita'].$arREC11['cod_font_recursos']){
                            
                            $stChave11 = $arREC11['tipo_registro'].$arREC11['cod_receita'].$arREC11['cod_font_recursos'];
                    
                            $rsBloco11 = 'rsBloco11_'.$inCount;
                            unset($$rsBloco11);
                            $$rsBloco11 = new RecordSet();
                            $$rsBloco11->preenche(array($arREC11));
                    
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco11);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_receita");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_font_recursos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                    
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_arrecadado_fonte");
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
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetREC99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecordSetREC10    = null;
$rsRecordSetREC11    = null;
$obTTCEMGExportacaoREC = null;
$rsRecordSetREC99    = null;

?>