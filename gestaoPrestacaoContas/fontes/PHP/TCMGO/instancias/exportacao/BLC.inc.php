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

    * Casos de uso: uc-06.04.00
*/

include_once CAM_GPC_TGO_MAPEAMENTO."TTGOOrgao.class.php";
include_once CAM_GPC_TGO_MAPEAMENTO."TTGOBLC.class.php";
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeBalanceteVerificacao.class.php"             );

$arFiltroRelatorio = Sessao::read('filtroRelatorio');
$inCount = 0;
/*
$obTTGOOrgao = new TTGOOrgao;
$obTTGOOrgao->setDado( 'exercicio', Sessao::getExercicio() );
$obTTGOOrgao->recuperaRegistro10BLC( $rsRegistro10 );

$inCount = 0;
foreach ($rsRegistro10->arElementos as $arRegistro10) {
    $arRegistro10['numero_sequencial'] = ++$inCount;

    $rsBloco = 'rsBloco_'.$inCount;
    unset($$rsBloco);
    $$rsBloco = new RecordSet();
    $$rsBloco->preenche(array($arRegistro10));

    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_unidade_orcamentaria");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTERES_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(85);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

    //tipo de registro 11
    // $obTTGOBLC = new TTGOBLC;
    // $stFiltro = " WHERE plano_banco.exercicio = '".Sessao::getExercicio()."'
    //                 AND plano_banco.cod_entidade IN (".implode(',', $arFiltroRelatorio['inCodEntidade']).") ";
    // $obTTGOBLC->setDado('data_inicial'    , $arFiltroRelatorio['stDataInicial'] );
    // $obTTGOBLC->setDado('data_final'      , $arFiltroRelatorio['stDataFinal']   );
    // $obTTGOBLC->recuperaRegistro11( $rsRegistro11, $stFiltro );
    // $obTTGOBLC->debug();
    $mesReferencia = explode('/', $arFiltroRelatorio['stDataFinal']);
    $mesReferencia = $mesReferencia[1];
    $obFContabilidadeBalanceteVerificacao = new FContabilidadeBalanceteVerificacao;
    $obFContabilidadeBalanceteVerificacao->setDado('exercicio'    , Sessao::getExercicio());
    $obFContabilidadeBalanceteVerificacao->setDado('stFiltro'     , " cod_entidade IN (".implode(',', $arFiltroRelatorio['inCodEntidade']).") ");
    $obFContabilidadeBalanceteVerificacao->setDado('stDtInicial'  , $arFiltroRelatorio['stDataInicial']);
    $obFContabilidadeBalanceteVerificacao->setDado('stDtFinal'    , $arFiltroRelatorio['stDataFinal']);
    $obFContabilidadeBalanceteVerificacao->setDado('chEstilo'     , 'A');//seta fixo 'A' pois a consulta utilizada é a do relatório de balancete
    $obFContabilidadeBalanceteVerificacao->setDado('exportacao_blc', true);

    $obErro = $obFContabilidadeBalanceteVerificacao->recuperaTodos($rsRegistro11, '', '');

    foreach ($rsRegistro11->arElementos as $arRegistro11) {
        $arRegistro11['numero_sequencial'] = ++$inCount;
        $arRegistro11['tipo_registro'] = 11;
        $arRegistro11['cod_orgao'] = $arRegistro10['cod_orgao'];
        $arRegistro11['tipo_unidade_orcamentaria'] = $arRegistro10['tipo_unidade_orcamentaria'];
        $arRegistro11['mes_referencia'] = $mesReferencia;
        $arRegistro11['cod_estrutural'] = str_replace('.', '', $arRegistro11['cod_estrutural']);
        switch (trim($arRegistro11['indicador_superavit'])) {
            case 'financeiro':
                $arRegistro11['indicador_superavit'] = '1';
                break;
            case 'permanente':
                $arRegistro11['indicador_superavit'] = '2';
                break;
            default:
                $arRegistro11['indicador_superavit'] = '0';
                break;
        }

        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arRegistro11));

        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_unidade_orcamentaria");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estrutural");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_superavit");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes_referencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_anterior");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_debitos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_creditos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_atual");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
    }
}
*/
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
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(91);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

?>
