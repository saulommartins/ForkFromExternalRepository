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
    * Data de Criação   : 02/03/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: REC.inc.php 62759 2015-06-16 18:00:15Z jean $

    * Casos de uso: uc-06.04.00
*/

include_once( CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio()."/TTCMGOReceita.class.php" );

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$obTTCMGOReceita = new TTCMGOReceita;
$obTTCMGOReceita->setDado('exercicio'  , Sessao::getExercicio() );
$obTTCMGOReceita->setDado('dtInicio'   , $arFiltroRelatorio['stDataInicial'] );
$obTTCMGOReceita->setDado('dtFim'      , $arFiltroRelatorio['stDataFinal']   );
$obTTCMGOReceita->setDado('stEntidades', $stEntidades );

$obTTCMGOReceita->recuperaRegistro10($rsConsulta10);
$obTTCMGOReceita->recuperaRegistro11($rsConsulta11);
$rsConsulta12 = $rsConsulta11;


$i = 0;
$inCount = 0;

// Registro 10 Detalhamento das Receitas do Ano
if ($rsConsulta10->getNumLinhas() > 0) {
    foreach ($rsConsulta10->getElementos() as $arLista10) {
        $arLista10['sequencial'] = ++$inCount;
        $stChave10 = $arLista10['cod_orgao'].$arLista10['rubrica'];

        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arLista10));

        $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
        $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rubrica");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_previsto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_atualizado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_arrecadado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        // Registro 11 Detalhamento das Receitas do Ano por Fonte de Recurso - Grupo/Especificação
        if ($rsConsulta11->getNumLinhas() > 0) {
            foreach ($rsConsulta11->getElementos() as $arLista11) {
                $stChave11 = $arLista11['cod_orgao'].$arLista11['rubrica'];

                if ($stChave10 == $stChave11) {
                    $arLista11['sequencial'] = ++$inCount;

                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arLista11));

                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rubrica");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_recurso");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 3 );
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_previsto");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_atualizado");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_arrecadado");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(97);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                    // Registro 12 Detalhamento das Receitas do Ano por Fonte de Recurso - Detalhamento
                    if ($rsConsulta12->getNumLinhas() > 0) {
                        foreach ($rsConsulta12->getElementos() as $arLista12) {
                            $stChave12 = $arLista12['cod_orgao'].$arLista12['rubrica'];
                            $arLista12['tipo_registro'] = 12;
                                    
                            if ($stChave11 == $stChave12) {
                                
                                $arLista12['sequencial'] = ++$inCount;
                                $rsBloco = 'rsBloco_'.$inCount;
                                unset($$rsBloco);
                                $$rsBloco = new RecordSet();
                                $$rsBloco->preenche(array($arLista12));
            
                                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rubrica");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_recurso");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("det_fonte_recurso");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_arrecadado");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(120);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                            }
                        }
                    }
                }
            }
        }
    }
}

//Registro 99 Final de Arquivo das Receitas do Ano
$arTemp[0] = array( 'tipo_registro'=> '99', 'brancos'=> '', 'sequencial' => $inCount+1 );

$arRecordSet[$stArquivo] = new RecordSet();
$arRecordSet[$stArquivo]->preenche( $arTemp );

$obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(150);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

?>