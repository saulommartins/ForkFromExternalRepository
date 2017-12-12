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
  * Página de Include Oculta - Exportação Arquivos TCEMG - ARC.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: ARC.csv.inc.php 63835 2015-10-22 13:53:31Z franver $
  * $Date: 2015-10-22 11:53:31 -0200 (Qui, 22 Out 2015) $
  * $Author: franver $
  * $Rev: 63835 $
  *
*/
/**
* ARC.csv | Autor : Jean da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGExportacaoARC.class.php";

$rsRecordSetARC10 = new RecordSet();
$rsRecordSetARC11 = new RecordSet();
$rsRecordSetARC12 = new RecordSet();
$rsRecordSetARC20 = new RecordSet();
$rsRecordSetARC21 = new RecordSet();

$obTTCEMGExportacaoARC = new TTCEMGExportacaoARC();
$obTTCEMGExportacaoARC->setDado('entidades' , $stEntidades);
$obTTCEMGExportacaoARC->setDado('exercicio' , Sessao::getExercicio());
$obTTCEMGExportacaoARC->setDado('mes'       , $stMes);

//Tipo Registro 10
//$obTTCEMGExportacaoARC->recuperaCorrecoesReceitas10($rsRecordSetARC10);

//Tipo Registro 11
//$obTTCEMGExportacaoARC->recuperaCorrecoesReceitas11($rsRecordSetARC11);

//Tipo Registro 12
/*
 *  Comentado o Registro 12, pois o sistema não possui Acrescida.
 *  Para ativa-lo é necessário ajustar a consulta sql do mesmo.
 *  
 *  $obTTCEMGExportacaoARC->recuperaCorrecoesReceitas12($rsRecordSetARC12);
*/ 

//Tipo Registro 20
$obTTCEMGExportacaoARC->recuperaCorrecoesReceitas20($rsRecordSetARC20);

//Tipo Registro 21
$obTTCEMGExportacaoARC->recuperaCorrecoesReceitas21($rsRecordSetARC21);

//Tipo Registro 99
$arRecordSetARCS99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetARC99 = new RecordSet();
$rsRecordSetARC99->preenche($arRecordSetARCS99);

$inCount = 0;
$boArc = false;

if (count($rsRecordSetARC20->getElementos()) > 0) {
    $boArc = true;
    $inCount=0;
  
    foreach ($rsRecordSetARC20->getElementos() as $arARC20) {
        $inCount++;
        
        /*
        $stChave = $arARC10['cod_correcao'];
        
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arARC10));
        
        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_correcao");
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
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indentificador_deducao_reduzida");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_receita_reduzida");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacao_reduzida");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificador_acrescida");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_receita_acrescida");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacao_acrescida");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_reduzido_acrescido");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

        if (count($rsRecordSetARC11->getElementos()) > 0) {
            foreach ($rsRecordSetARC11->getElementos() as $arARC11) {
                $stChave1 = $arARC11['cod_correcao'];
                if ($stChave === $stChave1) {
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arARC11));
                    
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                  
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_correcao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_reduzida");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_reduzido_fonte");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                }
            }
        }
     
        if (count($rsRecordSetARC12->getElementos()) > 0) {
            foreach ($rsRecordSetARC12->getElementos() as $arARC12) {
                $stChave1 = $arARC12['cod_correcao'];
           
                if ($stChave === $stChave1) {
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arARC12));
                    
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_correcao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_acrescida");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_acrescido_fonte");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                }
            }
        }
        */

        //if (count($rsRecordSetARC20->getElementos()) > 0) {
            //foreach ($rsRecordSetARC20->getElementos() as $arARC20) {
                $stChave1 = $arARC20['cod_correcao'];
                
                //if ($stChave === $stChave1) {
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arARC20));
                    
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
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estorno");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("deducao_receita");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificador_deducao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_receita_estornada");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacao_estornada");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_estornado");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                //}
           // }
       // }
     
        if (count($rsRecordSetARC21->getElementos()) > 0) {
            foreach ($rsRecordSetARC21->getElementos() as $arARC21) {
                $stChave2 = $arARC21['cod_correcao'];
           
                if ($stChave1 === $stChave2) {
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arARC21));
                    
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estorno");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_estornada");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_estornado_fonte");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                }
            }
        }
    }
}

if ($boArc = false){
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetARC99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecordSetARC10 = null;
$rsRecordSetARC11 = null;
$rsRecordSetARC12 = null;
$rsRecordSetARC20 = null;
$rsRecordSetARC21 = null;
$obTTCEMGExportacaoARC = null;
$rsRecordSetARC99 = null;

?>