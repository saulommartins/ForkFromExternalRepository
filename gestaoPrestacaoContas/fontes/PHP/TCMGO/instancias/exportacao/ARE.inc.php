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

    * @author Analista: Tonismar Régis Bernardo
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore
*/

$inCount = 0;

if (Sessao::getExercicio() > 2011) {
    include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOAnulacaoRecita.class.php" );

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');
    $obTTCMGOAnulacaoReceita = new TTCMGOAnulacaoReceita;

    $rsDetalhamento = new RecordSet();
    $rsDetalhamentoConta = new RecordSet();
    $rsDetalhamentoFonteRecurso = new RecordSet();
    
    $stDtInicial = $arFiltroRelatorio['stDataInicial'];
    $stDtFinal   = $arFiltroRelatorio['stDataFinal'];

    $obTTCMGOAnulacaoReceita->setDado('exercicio'  , Sessao::getExercicio() );
    $obTTCMGOAnulacaoReceita->setDado('stEntidades', $stEntidades );
    $obTTCMGOAnulacaoReceita->setDado('dtInicio'   , $stDtInicial );
    $obTTCMGOAnulacaoReceita->setDado('dtFim'      , $stDtFinal   );
    $obTTCMGOAnulacaoReceita->recuperaRelacionamento( $rsDetalhamento );
    $obTTCMGOAnulacaoReceita->recuperaDetalhamentoConta( $rsDetalhamentoConta );
    $obTTCMGOAnulacaoReceita->recuperaDetalhamentoFonteRecurso( $rsDetalhamentoFonteRecurso );

    //tipo10
    foreach ($rsDetalhamento->getElementos() as $arDetalhamento) {

        $arDetalhamento['numero_registro'] = ++$inCount;
        $stChave = $arDetalhamento['cod_orgao'].$arDetalhamento['rubrica'];

        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arDetalhamento));

        $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        if (Sessao::getExercicio() > '2011') {
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
        }

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rubrica");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_estornado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("justificativa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(255);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        /* TIPO REGISTRO 11 -- MOVIMENTAÇÃO FINANCEIRA*/
        foreach ($rsDetalhamentoConta->getElementos() as $arDetalhamentoConta) {
            $stChaveElemento = $arDetalhamentoConta['cod_orgao'].$arDetalhamentoConta['rubrica'];

            $stChaveElemento2 = $arDetalhamentoConta['cod_orgao'].$arDetalhamentoConta['rubrica'].$arDetalhamentoConta['banco'].$arDetalhamentoConta['agencia'].$arDetalhamentoConta['conta_corrente'].$arDetalhamentoConta['digito'].$arDetalhamentoConta['tipo_conta'];
            $boChave = false;

            if ($stChave == $stChaveElemento) {
                $arDetalhamentoConta['numero_registro'] = ++$inCount;
                $boChave = true;

                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arDetalhamentoConta));

                $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                if (Sessao::getExercicio() > '2011') {
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                }

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rubrica");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 3 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 4 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 12 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_estornado");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(233);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                /* TIPO REGISTRO 12 -- DETALHAMENTO FONTE RECURSO*/
                foreach ($rsDetalhamentoFonteRecurso->getElementos() as $arDetalhamentoFonteRecurso) {
                    $stChaveElemento3 = $arDetalhamentoFonteRecurso['cod_orgao'].$arDetalhamentoFonteRecurso['rubrica'].$arDetalhamentoFonteRecurso['banco'].$arDetalhamentoFonteRecurso['agencia'].$arDetalhamentoFonteRecurso['conta_corrente'].$arDetalhamentoFonteRecurso['digito'].$arDetalhamentoFonteRecurso['tipo_conta'];

                    if ($boChave == true AND $stChaveElemento2 === $stChaveElemento3) {
                        $arDetalhamentoFonteRecurso['numero_registro'] = ++$inCount;

                        $rsBloco = 'rsBloco_'.$inCount;
                        unset($$rsBloco);
                        $$rsBloco = new RecordSet();
                        $$rsBloco->preenche(array($arDetalhamentoFonteRecurso));

                        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rubrica");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 3 );

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 4 );

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 12 );

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fonte");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_estornado");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(227);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                    }
                }
            }
        }
    }
}

$arrayDado = array (
        'tipo_registro'  => '99',
        'brancos'        => '',
        'nro_sequencial' => ++$inCount
);

$recordSet[$stArquivo] = new RecordSet();
$recordSet[$stArquivo]->preenche( array($arrayDado) );

$obExportador->roUltimoArquivo->addBloco($recordSet[$stArquivo]);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(281);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

?>
