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
  * Página de Include Oculta - Exportação Arquivos TCEMG - JULGLIC.csv
  * Data de Criação: 02/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: JULGLIC.csv.inc.php 62269 2015-04-15 18:28:39Z franver $
  * $Date: 2015-04-15 15:28:39 -0300 (Wed, 15 Apr 2015) $
  * $Author: franver $
  * $Rev: 62269 $
  *
*/
/**
* JULGLIC.csv | Autor : Jean da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGJulgamentoLicitacao.class.php";

$rsRecordSetJULGLIC10 = new RecordSet();
$rsRecordSetJULGLIC20 = new RecordSet();
$rsRecordSetJULGLIC30 = new RecordSet();

$obTTCEMGJulgamentoLicitacao = new TTCEMGJulgamentoLicitacao();
$obTTCEMGJulgamentoLicitacao->setDado('exercicio',Sessao::getExercicio());
$obTTCEMGJulgamentoLicitacao->setDado('entidades',$stEntidades);
$obTTCEMGJulgamentoLicitacao->setDado('mes', $stMes);
$obTTCEMGJulgamentoLicitacao->setDado('dataInicial', $stDataInicial);
$obTTCEMGJulgamentoLicitacao->setDado('dataFinal',   $stDataFinal);

//Tipo Registro 10
$obTTCEMGJulgamentoLicitacao->recuperaExportacao10($rsRecordSetJULGLIC10);

////Tipo Registro 20
//$obTTCEMGJulgamentoLicitacao->recuperaExportacao20($rsRecordSetJULGLIC20);

////Tipo Registro 30
$obTTCEMGJulgamentoLicitacao->recuperaExportacao30($rsRecordSetJULGLIC30);

//Tipo Registro 99
$arRecordSetJULGLIC99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetJULGLIC99 = new RecordSet();
$rsRecordSetJULGLIC99->preenche($arRecordSetJULGLIC99);

$inContador =0;
$boChave = false;
//10
$arRecordSetJULGLIC10 = $rsRecordSetJULGLIC10->getElementos();
if (count($arRecordSetJULGLIC10) > 0) {
    $boChave = true;
    $stChave10 = '';
    foreach ($arRecordSetJULGLIC10 as $arJULGLIC10) {
        $inContador++;
        $inCount++;
        $stChave10Aux = $arJULGLIC10['num_processo_licitatorio'].$arJULGLIC10['cod_item'];
        
        if(!($stChave10===$stChave10Aux)){
            $stNumProcLic = $arJULGLIC10['num_processo_licitatorio'];
            $stChave10 = $arJULGLIC10['num_processo_licitatorio'].$arJULGLIC10['cod_item'];

            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arJULGLIC10));
            
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
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_unitario");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
 
 
            //20
            if (count($rsRecordSetJULGLIC20->getElementos()) > 0) {
                $boChave = true;
                foreach ($$rsRecordSetJULGLIC20->getElementos() as $arJULGLIC20) {
                    $inCount++;
                     
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arJULGLIC20));
                     
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
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("perc_desconto");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);
                }
            }
            
            //30
            //Verifica se  o proximo num_processo_licitatorio do array é diferente
            if($arRecordSetJULGLIC10[$inContador]['num_processo_licitatorio'] != $stNumProcLic){
                if (count($rsRecordSetJULGLIC30->getElementos()) > 0) {
                    $boChave = true;
                    foreach ($rsRecordSetJULGLIC30->getElementos() as $arJULGLIC30) {
                        $inCount++;
                        $stChave= $arJULGLIC30['num_processo_licitatorio'];
                        if( $stChave == $stNumProcLic ){
                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arJULGLIC30));
                            
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
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_julgamento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("presenca_licitantes");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("renuncia_recurso");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                        }
                    }
                }
            }
        }
    }
}else{
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetJULGLIC99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetJULGLIC10 = null;
$rsRecordSetJULGLIC20 = null;
$rsRecordSetJULGLIC30 = null;
$obTTCEMGJulgamentoLicitacao = null;
$rsRecordSetJULGLIC99 = null;
$arRecordSetJULGLIC10 = null;

?>