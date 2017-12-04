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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 06/03/2012

    * @author Analista: Tonismar Régis Bernardo
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore
    
    $Id: AEX.inc.php 66055 2016-07-13 13:04:46Z lisiane $

*/
include_once CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio().'/TTCMGOAnulacaoExtraOrcamentarias.class.php';

$arFiltroRelatorio = Sessao::read('filtroRelatorio');
$arData = explode('/', $arFiltroRelatorio['stDataFinal']);

$obTMapeamento = new TTCMGOAnulacaoExtraOrcamentarias;
$obTMapeamento->setDado('exercicio'  , Sessao::getExercicio() );
$obTMapeamento->setDado('dtInicio'   , $arFiltroRelatorio['stDataInicial'] );
$obTMapeamento->setDado('dtFim'   	 , $arFiltroRelatorio['stDataFinal']   );
$obTMapeamento->setDado('mes'        , $arData[1]);
$obTMapeamento->setDado('stEntidades', $stEntidades );


$obTMapeamento->recuperaReg10($rsRecordSetAEX10);
$obTMapeamento->recuperaReg11($rsRecordSetAEX11);
$obTMapeamento->recuperaReg12($rsRecordSetAEX12);
//Tipo Registro 99
$arRecordSetAEX99 = array(
    '0' => array(
        'tipo_registro'  => '99',
        'brancos'        => '',
        'nro_sequencial' => '1'
    )
);
$rsRecordSetAEX99 = new RecordSet();
$rsRecordSetAEX99->preenche($arRecordSetAEX99);

$inCount = 0;
if (count($rsRecordSetAEX10->getElementos()) > 0) {
    foreach ($rsRecordSetAEX10->getElementos() as $arAEX10) {
        $stChave10 = $arAEX10['orgao'].$arAEX10['categoria'].$arAEX10['tipo_lancamento'].$arAEX10['sub_tipo_lancamento'].$arAEX10['desdobra_subtipo'].$arAEX10['nro_extra_orcamentaria'].$arAEX10['dt_estorno'];
        $arAEX10['nro_sequencial'] = $inCount++;
        
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arAEX10));
        
        $obExportador->roUltimoArquivo->addBloco( $$rsBloco );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("categoria");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lancamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sub_tipo_lancamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desdobra_subtipo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_estorno");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 8 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
        
         /* REGISTRO 11 MOVIMENTAÇÃO FINANCEIRA */
        foreach ($rsRecordSetAEX11->arElementos as $arAEX11) {
           
            $stChave11 = $arAEX11['orgao'].$arAEX11['categoria'].$arAEX11['tipo_lancamento'].$arAEX11['sub_tipo_lancamento'].$arAEX11['desdobra_subtipo'].$arAEX11['nro_extra_orcamentaria'].$arAEX11['dt_estorno'];
            
            if ($stChave11 == $stChave10) {
                $arAEX11['sequencial'] =  $arAEX10['sequencial'];
                $stChave11 .= $arAEX11['banco'].$arAEX11['agencia'].$arAEX11['conta_corrente'].$arAEX11['digito'];
                
                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arAEX11));
                
                $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("categoria");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lancamento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sub_tipo_lancamento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desdobra_subtipo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_estorno");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 8 );
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                
                /* REGISTRO 12 MOVIMENTAÇÃO FINANCEIRA */
                foreach ($rsRecordSetAEX12->arElementos as $arAEX12) {
           
                    $stChave12 = $arAEX12['orgao'].$arAEX12['categoria'].$arAEX12['tipo_lancamento'].$arAEX12['sub_tipo_lancamento'].$arAEX12['desdobra_subtipo'].$arAEX12['nro_extra_orcamentaria'].$arAEX12['dt_estorno'];
                    $stChave12 .= $arAEX12['banco'].$arAEX12['agencia'].$arAEX12['conta_corrente'].$arAEX12['digito'];
                    
                    if ($stChave12 == $stChave11) {
                        $arAEX12['sequencial'] =  $arAEX10['sequencial'];
                        
                        $rsBloco = 'rsBloco_'.$inCount;
                        unset($$rsBloco);
                        $$rsBloco = new RecordSet();
                        $$rsBloco->preenche(array($arAEX12));
                        
                        $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("categoria");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lancamento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sub_tipo_lancamento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desdobra_subtipo");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_estorno");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DDMMYYYY");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 8 );
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_recurso");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                    }
                }
            }
        }
    }
}

$obExportador->roUltimoArquivo->addBloco($rsRecordSetAEX99);	
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");	
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");	
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(70);
  

