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
    * Página de Include Oculta - Exportação Arquivos GPC

    * Data de Criação   : 21/05/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * Casos de uso: uc-06.04.00
*/
/*
$Log$
Revision 1.2  2007/06/14 21:29:11  hboaventura
Correção de bugs

Revision 1.1  2007/05/21 21:31:35  hboaventura
Arquivos para geração do TCMGO

*/

include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContrato.class.php" );

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$stFiltro = " WHERE dt_empenho between TO_DATE('".$arFiltroRelatorio[stDataInicial]."','dd/mm/yyyy')
                AND TO_DATE('".$arFiltroRelatorio[stDataFinal]."','dd/mm/yyyy')
                AND ped.exercicio = '".Sessao::read('exercicio')."' \n";

$obTTCMGOContrato        = new TTCMGOContrato();
$obTTCMGOContrato->recuperaTodosDespesa($arRecordSet[$stArquivo], $stFiltro);
//$obTTCMGOContrato->debug();

if (Sessao::getExercicio() <= '2008') { //deixar == geracao do arquivo exercicio menor ou igual a 2008
    for ($i=1; $i<=count($rsTTCMGOContrato->arElementos); $i++) {
       $arRecordSet[$stArquivo]->arElementos[$i-1]['nro_sequencial'] = str_pad($i,6,'0', STR_PAD_LEFT);
    }
    $rsBlocoCON = new RecordSet();
    $rsBlocoCON->preenche( $arRecordSet[$stArquivo]->arElementos);

    $obExportador->roUltimoArquivo->addBloco($rsBlocoCON);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_acao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_proj_ativ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_modalidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_assunto");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    if (Sessao::getExercicio() > 2012) {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(255);
    } else {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(200);
    }

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_publicacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_inicio");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_final");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_contrato");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_credor");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pessoa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_cnpj");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

    // rodape
    $arRegistro = array();
    $arRegistro[0][ 'tipo_registro'  ] = 99 ;
    $arRegistro[0][ 'brancos'        ] = ' ';
    $arRegistro[0][ 'numero_registro'] = $i ;

    $rsRecordSetRodapeCON = new RecordSet();
    $rsRecordSetRodapeCON->preenche ($arRegistro);

    $obExportador->roUltimoArquivo->addBloco( $rsRecordSetRodapeCON );
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 346 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
} elseif ((Sessao::getExercicio() > '2008') and (Sessao::getExercicio() < '2011' )) {//geracao arquivo exercicios 2009 e 2010

    $obTTCMGOContrato->recuperaDetalhamentoContrato( $rsTTCMGODetalhamento  , $stFiltro);
    $obTTCMGOContrato->recuperaProrrogacaoPrazo    ( $rsProrrogacaoPrazo    , $stFiltro);
    $obTTCMGOContrato->recuperaAcrescimoDecrescimo ( $rsAcrescimoDecrescimo , $stFiltro);
    $obTTCMGOContrato->recuperaRescisaoContratual  ( $rsRescisaoContratual  , $stFiltro);

    $inCount = 0;
    foreach ($arRecordSet[$stArquivo]->arElementos as $arContrato) {
        $arContrato['nro_sequencial'] = ++$inCount;
        $stChave = $arContrato['cod_programa'].$arContrato['num_orgao'].$arContrato['num_unidade'].
                   $arContrato['cod_funcao'].$arContrato['cod_subfuncao'].$arContrato['natureza_acao'].$arContrato['nro_proj_ativ'].
                   $arContrato['elemento'].$arContrato['subelemento'].$arContrato['cod_empenho'];
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new Recordset();
        $$rsBloco->preenche(array($arContrato));

        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_acao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_proj_ativ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_modalidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_assunto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(200);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_publicacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_inicio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_final");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_credor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pessoa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_cnpj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_processo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_processo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(26);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);}
} else { //geracao arquivo apartir do exercicio de 2011

    $obTTCMGOContrato->recuperaDetalhamentoContrato( $rsTTCMGODetalhamento  , $stFiltro);
    $obTTCMGOContrato->recuperaProrrogacaoPrazo    ( $rsProrrogacaoPrazo    , $stFiltro);
    $obTTCMGOContrato->recuperaAcrescimoDecrescimo ( $rsAcrescimoDecrescimo , $stFiltro);
    $obTTCMGOContrato->recuperaRescisaoContratual  ( $rsRescisaoContratual  , $stFiltro);

    $inCount = 0;

    foreach ($arRecordSet[$stArquivo]->arElementos as $arContrato) {
        $arContrato['nro_sequencial'] = ++$inCount;
        $stChave = $arContrato['cod_programa'].$arContrato['num_orgao'].$arContrato['num_unidade'].
                   $arContrato['cod_funcao'].$arContrato['cod_subfuncao'].$arContrato['natureza_acao'].$arContrato['nro_proj_ativ'].
                   $arContrato['elemento'].$arContrato['subelemento'].$arContrato['cod_empenho'];
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new Recordset();
        $$rsBloco->preenche(array($arContrato));

        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        if (Sessao::getExercicio() > 2011) {
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_ajuste");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_cnpj");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        }

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_firmatura");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_publicacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_inicio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_final");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        if (Sessao::getExercicio() > 2012) {
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto_contrato");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(255);
        } else {
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto_contrato");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(200);
        }

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_credor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pessoa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        if (Sessao::getExercicio() > 2012) {
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_modalidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fundamentacao_legal");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("justificativa_dispensa");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("razao_escolha");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(245);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_processo");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_processo");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("processo_administrativo");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("instrumento_contrato");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_assunto");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        }

        if (Sessao::getExercicio() < 2011) {
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_cnpj");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        }

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

    }

        // tipo registro 11 -- Detalhamento de Obras e Servicos de Engenharia
        foreach ($rsTTCMGODetalhamento->arElementos as $arDetalhamento) {
                 $stChaveDetalhamento = $arDetalhamento['cod_programa'].$arDetalhamento['num_orgao'].
                            $arDetalhamento['num_unidade'].$arDetalhamento['cod_funcao'].$arDetalhamento['cod_subfuncao'].
                            $arDetalhamento['natureza_acao'].$arDetalhamento['nro_proj_ativ'].$arDetalhamento['elemento'].
                            $arDetalhamento['subelemento'].$arDetalhamento['cod_empenho'];
                 if ($stChave == $stChaveDetalhamento AND $arDetalhamento['cod_assunto'] == 1) {
                     $arDetalhamento['nro_sequencial'] = ++$inCount;
                     $rsBloco = 'rsBloco_'.$inCount;
                     unset($$rsBloco);
                     $$rsBloco = new Recordset();
                     $$rsBloco->preenche(array($arDetalhamento));

                     $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                     $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                    if (Sessao::getExercicio() < '2011') {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                    }

                     $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                     $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                    if (Sessao::getExercicio() < '2011') {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_acao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_proj_ativ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                    }

                    if (Sessao::getExercicio() > '2010') {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_contrato");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                    }

                    if (Sessao::getExercicio() > '2011') {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_ajuste");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                    }

                     $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_sub_assunto");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                     if (Sessao::getExercicio() > 2012) {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_obra");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_obra");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                    }

                     $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("detalhamentosubassunto");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(200);

                     if (Sessao::getExercicio() < '2011') {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("endereco");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("latitude");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("longitude");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bairro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);
                     }

                    if (Sessao::getExercicio() == 2011) {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_obra");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_obra");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                    }

                    if (Sessao::getExercicio() > 2012) {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 692 );
                    } elseif (Sessao::getExercicio() > 2012) {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 101 );
                    }

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        // tipo registro 21 --Termos Aditivos de Prorrogacao de Prazos
        foreach ($rsProrrogacaoPrazo->arElementos as $arTempProrrogacao) {
                $stChaveProrrogacao = $arTempProrrogacao['cod_programa'].$arTempProrrogacao['num_orgao'].
                           $arTempProrrogacao['num_unidade'].$arTempProrrogacao['cod_funcao'].$arTempProrrogacao['cod_subfuncao'].
                           $arTempProrrogacao['natureza_acao'].$arTempProrrogacao['nro_proj_ativ'].$arTempProrrogacao['elemento'].
                           $arTempProrrogacao['subelemento'].$arTempProrrogacao['cod_empenho'];

                 if ($stChaveDetalhamento == $stChaveProrrogacao) {
                     $arTempProrrogacao['nro_sequencial'] = ++$inCount;

                     $rsBloco = 'rsBloco_'.$inCount;
                     unset($$rsBloco);
                     $$rsBloco = new Recordset();
                     $$rsBloco->preenche(array($arTempProrrogacao));
                     $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                     $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                     if (Sessao::getExercicio() < '2011') {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                     }

                     $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                     $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                     $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                    if (Sessao::getExercicio() > '2010') {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_contrato");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                        if (Sessao::getExercicio() > '2011') {
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_ajuste");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                        }

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_termo");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                    }

                    if (Sessao::getExercicio() < '2011') {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_acao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_proj_ativ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                    }

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_firmatura");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("prazo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                    if (Sessao::getExercicio() < '2011') {
                       $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                       $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                       $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(346);
                    } elseif (Sessao::getExercicio() > 2012) {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(886);
                    } else {
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(295);
                    }

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                     // tipo registro 22 -- Acrescimo/Decrescimo
                     foreach ($rsAcrescimoDecrescimo->arElementos as $arAcDc) {
                        $stChaveAcDc = $arAcDc['cod_programa'].$arAcDc['num_orgao'].$arAcDc['num_unidade'].
                                $arAcDc['cod_funcao'].$arAcDc['cod_subfuncao'].$arAcDc['natureza_acao'].$arAcDc['nro_proj_ativ'].
                                $arAcDc['elemento'].$arAcDc['subelemento'].$arAcDc['cod_empenho'];
                        if ($stChaveAcDc == $stChaveProrrogacao) {
                            $arAcDc['nro_sequencial'] = ++$inCount;

                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arAcDc));

                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                            if (Sessao::getExercicio() < '2011') {
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                            }

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                            if (Sessao::getExercicio() > '2010') {
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_contrato");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                                if (Sessao::getExercicio() > '2011') {
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_ajuste");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                                }

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_termo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                            }

                            if (Sessao::getExercicio() < '2011') {
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_acao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_proj_ativ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                            }

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lancamento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_acrescimo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_decrescimo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_contratual");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                            if (Sessao::getExercicio() < '2011') {
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(311);
                            } elseif (Sessao::getExercicio() > 2012) {
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(851);
                            } else {
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(260);
                            }

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                        }
                     }

                    // tipo registro 23 --Rescisao contratual
                     foreach ($rsRescisaoContratual->arElementos  as $arRescisao) {
                        $stChaveRescisao = $arRescisao['cod_programa'].$arRescisao['num_orgao'].
                                $arRescisao['num_unidade'].$arRescisao['cod_funcao'].$arRescisao['cod_subfuncao'].
                                $arRescisao['natureza_acao'].$arRescisao['nro_proj_ativ'].$arRescisao['elemento'].
                                $arRescisao['subelemento'].$arRescisao['cod_empenho'];

                     if ($stChaveRescisao == $stChaveProrrogacao) {
                         $arRescisao['nro_sequencial'] = ++$inCount;

                         $rsBloco = 'rsBloco_'.$inCount;
                         unset($$rsBloco);
                         $$rsBloco = new Recordset();
                         $$rsBloco->preenche(array($arRescisao));

                         $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                         if (Sessao::getExercicio() < '2011') {
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                         }

                         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                         if (Sessao::getExercicio() > '2010') {
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_contrato");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                                if (Sessao::getExercicio() > '2011') {
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_ajuste");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                                }

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_termo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                            }

                         if (Sessao::getExercicio() < '2011') {
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_acao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_proj_ativ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                         }

                         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_rescisao");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_cancelamento");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cancelamento");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_final_contrato");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                         if (Sessao::getExercicio() < '2011') {
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(316);
                         } elseif (Sessao::getExercicio() > 2012) {
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(856);
                         } else {
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(265);
                         }

                         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                     }
            }
        }//fim foreach prorrogacao prazo
}
       }
        }//fim foreach detalhamento

    }//fim else
    // rodape
    $arRegistro = array();
    $arRegistro[0][ 'tipo_registro'  ] = 99 ;
    $arRegistro[0][ 'brancos'        ] = ' ';
    $arRegistro[0][ 'numero_registro'] = $inCount+1 ;

    $rsRecordSetRodapeCON = new RecordSet();
    $rsRecordSetRodapeCON->preenche ($arRegistro);

    if (Sessao::getExercicio() < '2011') {
        $obExportador->roUltimoArquivo->addBloco( $rsRecordSetRodapeCON );
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 389 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    } else {
        $obExportador->roUltimoArquivo->addBloco( $rsRecordSetRodapeCON );
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        if (Sessao::getExercicio() > 2012) {
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 931 );
        } else {
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 340 );
        }

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    }
