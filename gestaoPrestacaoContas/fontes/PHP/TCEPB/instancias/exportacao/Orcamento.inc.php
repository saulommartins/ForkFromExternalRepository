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
    *
    * Data de Criação: 11/04/2008
    *
    *
    * @author Desenvolvedor: Diogo Zarpelon
    * @ignore
    *
    */

    include_once( CAM_GPC_TPB_MAPEAMENTO."TTPBOrcamentos.class.php" );

    $obTTPBOrcamentos = new TTPBOrcamentos();
    $rsRecordSet   = new RecordSet;
    $arTemp        = array();

    $obTTPBOrcamentos->setDado( "cod_modulo"   , Sessao::read('modulo') );
    $obTTPBOrcamentos->setDado( "cod_entidade" , $stEntidades    );
    $obTTPBOrcamentos->setDado( "parametro"    , "data_aprovacao_loa" );
    
    $obTTPBOrcamentos->recuperaParametro( $rsRecordSet );
      
    while ( !$rsRecordSet->eof() ) {
        // Retira o caracter separador (-) do resultado.
        list($ano, $mes, $dia) = explode("-", $rsRecordSet->getCampo ( "data_aprovacao_loa" ));        
        $rsRecordSet->setCampo ( "data_aprovacao_loa", $dia.$mes.$ano );

        $arTemp[$rsRecordSet->getCorrente()-1]["data_aprovacao_loa"] = $rsRecordSet->getCampo("data_aprovacao_loa");
        $rsRecordSet->proximo();
    }

    $obTTPBOrcamentos->setDado( "parametro" , "numero_loa" );
    $obTTPBOrcamentos->recuperaParametro( $rsRecordSet );
    
    while ( !$rsRecordSet->eof() ) {
        $arTemp[$rsRecordSet->getCorrente()-1]["numero_loa"] = $rsRecordSet->getCampo("numero_loa");
        //Seta o ano de vigência da lei orçamentária
        $arTemp[$rsRecordSet->getCorrente()-1]["exercicio"] = Sessao::getExercicio();
        $rsRecordSet->proximo();
    }

    $obTTPBOrcamentos->setDado( "parametro" , "data_aprovacao_ldo" );
    $obTTPBOrcamentos->recuperaParametro( $rsRecordSet );
    
    while ( !$rsRecordSet->eof() ) {
        // Retira o caracter separador (-) do resultado.
        list($ano, $mes, $dia) = explode("-", $rsRecordSet->getCampo ( "data_aprovacao_ldo" ));
        $rsRecordSet->setCampo ( "data_aprovacao_ldo", $dia.$mes.$ano );

        $arTemp[$rsRecordSet->getCorrente()-1]["data_aprovacao_ldo"] = $rsRecordSet->getCampo("data_aprovacao_ldo");
        $rsRecordSet->proximo();
    }

    $obTTPBOrcamentos->setDado( "parametro" , "numero_ldo" );
    $obTTPBOrcamentos->recuperaParametro( $rsRecordSet );
    
    while ( !$rsRecordSet->eof() ) {
        $arTemp[$rsRecordSet->getCorrente()-1]["numero_ldo"] = $rsRecordSet->getCampo("numero_ldo");
        $rsRecordSet->proximo();
    }

    $rsRecordSet->preenche($arTemp);

    $obExportador->roUltimoArquivo->addBloco($rsRecordSet);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_aprovacao_loa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[numero_loa][exercicio]");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_aprovacao_ldo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[numero_ldo][exercicio]");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    
?>
