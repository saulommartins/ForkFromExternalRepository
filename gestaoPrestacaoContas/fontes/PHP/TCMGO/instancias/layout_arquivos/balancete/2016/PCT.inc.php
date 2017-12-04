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
    * Página de Include Oculta - Exportação Arquivos GRH

    * Data de Criação   : 19/02/2013

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

    $Id: PCT.inc.php 65190 2016-04-29 19:36:51Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio().'/TTGOPCT.class.php';

$arFiltroRelatorio = Sessao::read('filtroRelatorio');
$boValidaGeracaoRegistro11 = true;
$inCount = 0;
$obTTGOPCT = new TTGOPCT;
$obTTGOPCT->recuperaRegistro10( $rsRegistro10 );

// tipo de registro 11
$stEntidades = implode(',', $arFiltroRelatorio['inCodEntidade']);
$arMes = explode('/', $arFiltroRelatorio['stDataFinal']);
$inMes = $arMes[1];

$obTTGOPCT->setDado('exercicio', Sessao::getExercicio());
$obTTGOPCT->setDado('dt_inicial', '01/'.$inMes.'/'.Sessao::getExercicio());
$obTTGOPCT->setDado('dt_final', $arFiltroRelatorio['stDataFinal']);
$obTTGOPCT->setDado('entidades', $stEntidades);
$obTTGOPCT->recuperaRegistros( $rsRegistro); 

// tipo de registro 10
foreach ($rsRegistro10->arElementos as $arRegistro10) {
    if ( $rsRegistro->getNumLinhas() > 0) {
        
        $arRegistro10['numero_sequencial'] = ++$inCount;
    
        if ($inMes == '01') {
            $arRegistro10['planocontas'] = 0;
        } else {
            $arRegistro10['planocontas'] = 1;
        }
    
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arRegistro10));
    
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("planocontas");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(174);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
    
        
        if(array_key_exists("planocontas", $arRegistro10)){
            if($arRegistro10["planocontas"] == 1){
                $boValidaGeracaoRegistro11 = false;
            } else {
                $boValidaGeracaoRegistro11 = true;
            }
        } else {
            $boValidaGeracaoRegistro11 = true;
        }
        $arCodNivel = array();
        if ($boValidaGeracaoRegistro11) {
            foreach ($rsRegistro->arElementos as $arRegistro) {
                if (!in_array($arRegistro['nivel'], $arCodNivel)) {
                    $arCodNivel[] = $arRegistro['nivel'];
                    //Registro Tipo 11
                    $arRegistro['numero_sequencial'] = ++$inCount;
                    $arRegistro11 = $arRegistro;
            
                    $arRegistro11['tipo_registro_11'] = 11;
                    if ($arRegistro11['nivel'] <= 5) {
                        $arRegistro11['quantidade_digitos'] = 1;
                    } else {
                        $arRegistro11['quantidade_digitos'] = 2;
                    }
            
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arRegistro11));
            
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro_11");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
            
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_unidade_orcamentaria");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
            
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nivel");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
            
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade_digitos");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
            
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(171);
            
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                }
            }
        }
        foreach ($rsRegistro->arElementos as $arRegistro) {
            //Registro Tipo 12
            $arRegistro['numero_sequencial'] = ++$inCount;
    
            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arRegistro));
    
            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_unidade_orcamentaria");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_conta");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_superavit");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_conta_pai");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nivel");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_conta");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_conta_pcasp");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_superavit_pcasp");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        }
    }
}

$arRegistro99 = array();
$arRegistro99[0] = array( 'tipo_registro'=> '99', 'brancos'=> '', 'numero_sequencial' => ++$inCount );

$rsBloco = 'rsBloco_'.$inCount;
unset($$rsBloco);
$$rsBloco = new RecordSet();
$$rsBloco->preenche($arRegistro99);

$obExportador->roUltimoArquivo->addBloco($$rsBloco);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(177);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
?>
