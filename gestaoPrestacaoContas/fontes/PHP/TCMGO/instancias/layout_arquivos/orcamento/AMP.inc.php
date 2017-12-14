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

include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOAMP.class.php" );

$obTMapeamento = new TTCMGOAMP();
$obTMapeamento->setDado('exercicio', Sessao::getExercicio());
$obTMapeamento->recuperaDadosTipo10($rsRegistro10);

$i = 0;

if (!$rsRegistro10->eof()) {
  foreach ($rsRegistro10->arElementos as $stChave){
    $stChave['sequencial'] = ++$i;

    $rsBloco10 = 'rsBloco10_'.$inCount;
    unset($$rsBloco10);
    $$rsBloco10 = new RecordSet();
    $$rsBloco10->preenche(array($stChave));
    
    $obExportador->roUltimoArquivo->addBloco($$rsBloco10);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 4 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 3 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_natureza");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_proj_ativ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 3 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("esfera_orcamentaria");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("acao_detalhada");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 200 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("publico_alvo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 100 );
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("produto");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 100 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("acao_reduzida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 60 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_medida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_fisica_1ano");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 15 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_fisica_2ano");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 15 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_fisica_3ano");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 15 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_fisica_4ano");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 15 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_financeira_1ano");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 15 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_financeira_2ano");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 15 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_financeira_3ano");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 15 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_financeira_4ano");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 15 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );

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
  $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 600 );

  $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
  $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
  $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
  
?>


 
