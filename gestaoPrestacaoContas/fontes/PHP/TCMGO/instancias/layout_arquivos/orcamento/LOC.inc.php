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
  * $Id:$
  * $Date:$
  * $Author:$
  * $Rev:$
  *
*/

include_once( CAM_GPC_TGO_MAPEAMENTO."TCMGOConfiguracaoLOA.class.php" );

$obTMapeamento = new TCMGOConfiguracaoLOA();
$obTMapeamento->setDado('exercicio', Sessao::getExercicio());
$obTMapeamento->recuperaRegistro10($rsRegistro10);
$obTMapeamento->recuperaRegistro11($rsRegistro11);

$i = 0;

if ($rsRegistro10->getNumLinhas() > 0) {
    foreach ($rsRegistro10->arElementos as $stChave){

        $stChaveRegistro10 = $stChave['num_loa'];
    
        $stChave['sequencial'] = ++$i;

        $rsBloco10 = 'rsBloco10_'.$inCount;
        unset($$rsBloco10);
        $$rsBloco10 = new RecordSet();
        $$rsBloco10->preenche(array($stChave));
    
        $obExportador->roUltimoArquivo->addBloco($$rsBloco10);
        $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_loa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_loa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 8 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("perc_suplementacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("perc_op_cred_int");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("perc_op_cred_aro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 278 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
    
        if ($rsRegistro11->getNumLinhas() > 0) {
            foreach ($rsRegistro11->arElementos as $stChave){

                $stChaveRegistro11 = $stChave['num_loa'];

                if ($stChaveRegistro11 === $stChaveRegistro10) {          
                    $stChave['sequencial'] = ++$i;

                    $rsBloco11 = 'rsBloco11_'.$inCount;
                    unset($$rsBloco11);
                    $$rsBloco11 = new RecordSet();
                    $$rsBloco11->preenche(array($stChave));
          
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco11);
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
          
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );
          
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meio_pub_loa");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );
          
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_meio_loa");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 300 );
          
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lei_loa");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 8 );
          
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
                }
            }
        }
    }
}

    $arRegistro99 = array();
    $arRegistro99['tipo_registro'] = '99';
    $arRegistro99['sequencial'] = ++$i;
  
    $rsBloco99 = 'rsBloco99_'.$inCount;
    unset($$rsBloco99);
    $$rsBloco99 = new RecordSet();
    $$rsBloco99->preenche(array($arRegistro99));
  
    $obExportador->roUltimoArquivo->addBloco($$rsBloco99);
  
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );
  
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 310 );
  
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
  



?>