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
    * Pagina de formulário para Incluir Edital
    * Data de Criação   : 20/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id: PRManterEdital.php 64262 2015-12-23 12:48:09Z jean $

    * Casos de uso: uc-03.05.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( TLIC."TLicitacaoEdital.class.php" );
include_once ( TLIC."TLicitacaoComissao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEdital";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js" ;

// QUANDO TIVER O COMPONENTE QUE SELECIONA DOCUMENTOS E TEMPLATES
// ESSA VARIAVEL RECEBERA O VALOR DO DOCUMENTO SELECIONADO
$pgGera       = "OCGeraDocumentoEdital.php";

include_once( $pgJS );

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

$obTLicitacaoEdital = new TLicitacaoEdital();
Sessao::getTransacao()->setMapeamento( $obTLicitacaoEdital );

function buscaDataTerminoVigenciaComissao(Request $request)
{
    $rsDataTermino = new RecordSet;
    $obTLicitacaoComissao = new TLicitacaoComissao;

    $obTLicitacaoComissao->setDado('cod_licitacao', $request->get('inCodLicitacao') );
    $obTLicitacaoComissao->setDado('cod_modalidade', $request->get('inCodModalidade') );
    $obTLicitacaoComissao->setDado('cod_entidade', $request->get('inCodEntidade') );
    $obTLicitacaoComissao->setDado('exercicio', $request->get('stExercicioLicitacao') );

    $obTLicitacaoComissao->recuperaDataTerminoComissao($rsDataTermino);

    return $rsDataTermino->getCampo('dt_termino');
}

// FUNÇÃO PARA COMPARAR DATAS RETORNANDO TRUE E A PRIMEIRA DATA FOR MAIOR OU IGUAL A SEGUNDA* a que tem no sistema está meio esquisita *
function cmpDt($dt1,$dt2)
{
    $arDt = explode('/',$dt1);
    $dt1  = $arDt[2].$arDt[1].$arDt[0];
    $arDt = explode('/',$dt2);
    $dt2  = $arDt[2].$arDt[1].$arDt[0];
    if ($dt1 >= $dt2) {
            return true;
    } else {
            return false;
    }
}

/**
 * Recebe uma data no formato DD/MM/YYYY e retorna a data no formato YYYY-MM-DD
 * @param string $data
 * @return string
 */
function dataYMD($data)
{
    $tmp = explode('/',$data);
    if (count($tmp) == 3) {
        return $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
    } else {
        return $data;
    }
}

switch ($stAcao) {
    case 'incluir':
            $stMensagem = '';

            //verifica se a data de abertura é superior a data de entrega
            if ( implode('',array_reverse(explode('/',$request->get('dtEntrega')))) > implode('',array_reverse(explode('/',$request->get('dtAbertura')))) && $stMensagem == '' ) {
                $stMensagem = 'Data e hora da abertura (<b><i>'.$request->get('dtAbertura').' '.$request->get('stHoraAbertura').'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$request->get('dtEntrega').' '.$request->get('stHoraEntrega').'</i></b>).';
            } elseif ( ($request->get('dtEntrega') == $request->get('dtAbertura')) && ( str_replace(':','',$request->get('stHoraEntrega')) > str_replace(':','',$request->get('stHoraAbertura')) ) && $stMensagem == '' ) {
                $stMensagem = 'Data e hora da abertura (<b><i>'.$request->get('dtAbertura').' '.$request->get('stHoraAbertura').'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$request->get('dtEntrega').' '.$request->get('stHoraEntrega').'</i></b>).';
            }

            if ($request->get('dtEntregaFinal') != '') {
                //verifica se a data de entrega final é superior a data de entrega
                if ( implode('',array_reverse(explode('/',$request->get('dtEntrega')))) > implode('',array_reverse(explode('/',$request->get('dtEntregaFinal')))) && $stMensagem == '' ) {
                    $stMensagem = 'Data final de Entrega (<b><i>'.$request->get('dtEntregaFinal').' '.$request->get('stHoraEntregaFinal').'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$request->get('dtEntrega').' '.$request->get('stHoraEntrega').'</i></b>).';
                } elseif ( ($request->get('dtEntrega') == $request->get('dtEntregaFinal')) && ( str_replace(':','',$request->get('stHoraEntrega')) > str_replace(':','',$request->get('stHoraEntregaFinal')) ) && $stMensagem == '' ) {
                    $stMensagem = 'Data e hora finais da Entrega (<b><i>'.$request->get('dtEntregaFinal').' '.$request->get('stHoraEntregaFinal').'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$request->get('dtEntrega').' '.$request->get('stHoraEntrega').'</i></b>).';
                }
            }

            if ($request->get('stHoraEntregaFinal') != '') {
                $horaFinal = explode(':',$request->get('stHoraEntregaFinal'));
                $hora = explode(':',$request->get('stHoraEntrega'));
                //verifica se o horário de entrega final é superior ao horário de entrega
                if ( (($hora[0] > $horaFinal[0]) || ($hora[0] == $horaFinal[0] && $hora[1] > $horaFinal[1])) && $stMensagem == '' ) {
                    $stMensagem = 'Hora final de Entrega (<b><i>'.$request->get('stHoraEntregaFinal').'</i></b>) deve ser igual ou maior que a hora de entrega (<b><i>'.$request->get('stHoraEntrega').'</i></b>).';
                }
            }

            // VERIFICA SE A DATA DE APROVAÇÃO É SUPERIOR A DATA DE ENTREGA
            if ( ( SistemaLegado::comparaDatas($request->get('dtAprovacao'), $request->get('dtEntrega'),false) ) && $stMensagem == '' ) {
                $stMensagem = 'Data de aprovação do jurídico (<b><i>'.$request->get('dtAprovacao').'</i></b>) deve ser menor que a data de entrega (<b><i>'.$request->get('dtEntrega').'</i></b>).';
            }

            // VERIFICA SE A DATA DE VALIDADE É SUPERIOR A DATA DE ABERTURA
            if ( ( SistemaLegado::comparaDatas($request->get('dtAbertura'), $request->get('dtValidade'),true) ) && $stMensagem == '' ) {
                $stMensagem = 'Data de validade das propostas (<b><i>'.$request->get('dtValidade').'</i></b>) deve ser maior que a data de abertura das propostas(<b><i>'.$request->get('dtAbertura').'</i></b>).';
            }

            $dtInicio = dataYMD($request->get('dtEntrega')).' 00:00:00';
            $dtFim = dataYMD($request->get('dtValidade')).' 23:59:59';
            $qtd_dias_validade = SistemaLegado::datediff('d', $dtInicio, $dtFim);

            if ( ($request->get('inCodEdital') == '0') && $stMensagem =='' ) {
                $stMensagem = 'O número do edital inválido.';
            }
            //verifica se não existe um edital com o mesmo número no banco
            if ( ($request->get('inCodEdital') > 0 ) && $stMensagem == '' ) {
                $obTLicitacaoEdital->setDado( 'num_edital', $request->get('inCodEdital') );
                $obTLicitacaoEdital->setDado( 'exercicio', Sessao::getExercicio());
                $obTLicitacaoEdital->recuperaPorChave( $rsEdital );
                if ( $rsEdital->getNumLinhas() > 0 ) {
                    $stMensagem = 'Já existe um edital com este número.';
                }
            }

            $dtTerminoVigencia = buscaDataTerminoVigenciaComissao( $request );
            // VERIFICA SE A DATA DE ABERTURA É SUPERIOR A DATA DE TERMINO DA VIGÊNCIA(COMISSÂO)
            if ( ( SistemaLegado::comparaDatas($request->get('dtAbertura'), $dtTerminoVigencia,true) ) && $stMensagem == '' ) {
                $stMensagem = 'Data de abertura das propostas ( <b><i>'.$request->get('dtAbertura').'</i></b> ) deve ser menor ou igual a data de vigência da comissão de licitação( <b><i>'.$dtTerminoVigencia.'</i></b> )!';
            }

            if ($stMensagem == '') {
                if ($request->get('inCodEdital') != '') {
                    $obTLicitacaoEdital->setDado( 'num_edital' , $request->get('inCodEdital') );
                }
                $obTLicitacaoEdital->setDado( 'exercicio' , Sessao::getExercicio()                 );

                //*  POR QUE AINDA NÃO TEM O COMPONENTE QUE SELECIONA O DOCUMENTO O CODIGO TIPO E O CODIGO DO
                // DOCUMENTO ESTÃO FIXADOS COMO 0 (NÃO INFORMADO)

                $exercicioLicitacao = $request->get('stExercicioLicitacao');

                $obTLicitacaoEdital->setDado( 'cod_tipo_documento'            , 0  );
                $obTLicitacaoEdital->setDado( 'cod_documento'                 , 0  );

                $obTLicitacaoEdital->setDado( 'responsavel_juridico'          , $request->get('inResponsavelJuridico') );
                $obTLicitacaoEdital->setDado( 'exercicio_licitacao'           , $exercicioLicitacao                    );
                $obTLicitacaoEdital->setDado( 'cod_entidade'                  , $request->get('inCodEntidade')         );
                $obTLicitacaoEdital->setDado( 'cod_modalidade'                , $request->get('inCodModalidade')       );
                $obTLicitacaoEdital->setDado( 'cod_licitacao'                 , $request->get('inCodLicitacao')        );
                $obTLicitacaoEdital->setDado( 'local_entrega_propostas'       , $request->get('stLocalEntrega')        );
                $obTLicitacaoEdital->setDado( 'dt_entrega_propostas'          , $request->get('dtEntrega')             );
                $obTLicitacaoEdital->setDado( 'hora_entrega_propostas'        , $request->get('stHoraEntrega')         );
                $obTLicitacaoEdital->setDado( 'dt_final_entrega_propostas'    , $request->get('dtEntregaFinal')        );
                $obTLicitacaoEdital->setDado( 'hora_final_entrega_propostas'  , $request->get('stHoraEntregaFinal')    );
                $obTLicitacaoEdital->setDado( 'local_abertura_propostas'      , $request->get('stLocalAbertura')       );
                $obTLicitacaoEdital->setDado( 'dt_abertura_propostas'         , $request->get('dtAbertura')            );
                $obTLicitacaoEdital->setDado( 'hora_abertura_propostas'       , $request->get('stHoraAbertura')        );
                $obTLicitacaoEdital->setDado( 'dt_validade_proposta'          , $request->get('dtValidade')            );
                $obTLicitacaoEdital->setDado( 'observacao_validade_proposta'  , $request->get('txtValidade')       );
                $obTLicitacaoEdital->setDado( 'condicoes_pagamento'           , stripslashes(stripslashes($request->get('txtCodPagamento')))     );
                $obTLicitacaoEdital->setDado( 'local_entrega_material'        , $request->get('stLocalMaterial')       );
                $obTLicitacaoEdital->setDado( 'dt_aprovacao_juridico'         , $request->get('dtAprovacao')           );

                $obTLicitacaoEdital->inclusao();
            }

            if ($stMensagem == '') {                
                $request->set('qtdDiasValidade',$qtd_dias_validade);
                sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Edital: ".$obTLicitacaoEdital->getDado('num_edital')."/".Sessao::getExercicio(),"incluir","aviso", Sessao::getId(), "../");
                Sessao::write('request', $request->getAll());
                if ($request->get('boGerarDocumento') == 'S') {
                    SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
                }
            } else {
                sistemaLegado::exibeAviso(urlencode($stMensagem),'n_incluir','erro');
            }
    break;

    case 'alterar':

        //verifica se a data de abertura é superior a data de entrega
            if ( implode('',array_reverse(explode('/',$request->get('dtEntrega')))) > implode('',array_reverse(explode('/',$request->get('dtAbertura')))) && $stMensagem == '' ) {
                $stMensagem = 'Data e hora da abertura (<b><i>'.$request->get('dtAbertura').' '.$request->get('stHoraAbertura').'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$request->get('dtEntrega').' '.$request->get('stHoraEntrega').'</i></b>).';
            } elseif ( ($request->get('dtEntrega') == $request->get('dtAbertura')) && ( str_replace(':','',$request->get('stHoraEntrega')) > str_replace(':','',$request->get('stHoraAbertura')) ) && $stMensagem == '' ) {
            $stMensagem = 'Data e hora da abertura (<b><i>'.$request->get('dtAbertura').' '.$request->get('stHoraAbertura').'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$request->get('dtEntrega').' '.$request->get('stHoraEntrega').'</i></b>).';
            }

            if ($request->get('dtEntregaFinal') != '') {
                //verifica se a data de entrega final é superior a data de entrega
                if ( implode('',array_reverse(explode('/',$request->get('dtEntrega')))) > implode('',array_reverse(explode('/',$request->get('dtEntregaFinal')))) && $stMensagem == '' ) {
                    $stMensagem = 'Data final de Entrega (<b><i>'.$request->get('dtEntregaFinal').' '.$request->get('stHoraEntregaFinal').'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$request->get('dtEntrega').' '.$request->get('stHoraEntrega').'</i></b>).';
                } elseif ( ($request->get('dtEntrega') == $request->get('dtEntregaFinal')) && ( str_replace(':','',$request->get('stHoraEntrega')) > str_replace(':','',$request->get('stHoraEntregaFinal')) ) && $stMensagem == '' ) {
                    $stMensagem = 'Data e hora finais da Entrega (<b><i>'.$request->get('dtEntregaFinal').' '.$request->get('stHoraEntregaFinal').'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$request->get('dtEntrega').' '.$request->get('stHoraEntrega').'</i></b>).';
                }
            }

            if ($request->get('stHoraEntregaFinal') != '') {
                $horaFinal = explode(':',$request->get('stHoraEntregaFinal'));
                $hora = explode(':',$request->get('stHoraEntrega'));
                //verifica se o horário de entrega final é superior ao horário de entrega
                if ( (($hora[0] > $horaFinal[0]) || ($hora[0] == $horaFinal[0] && $hora[1] > $horaFinal[1])) && $stMensagem == '' ) {
                    $stMensagem = 'Hora final de Entrega (<b><i>'.$request->get('stHoraEntregaFinal').'</i></b>) deve ser igual ou maior que a hora de entrega (<b><i>'.$request->get('stHoraEntrega').'</i></b>).';
                }
            }

            // VERIFICA SE A DATA DE APROVAÇÃO É SUPERIOR A DATA DE ENTREGA
            if ( ( SistemaLegado::comparaDatas($request->get('dtAprovacao'), $request->get('dtEntrega'),false) ) && $stMensagem == '' ) {
                $stMensagem = 'Data de aprovação do jurídico (<b><i>'.$request->get('dtAprovacao').'</i></b>) deve ser menor que a data de entrega (<b><i>'.$request->get('dtEntrega').'</i></b>).';
            }

            // VERIFICA SE A DATA DE VALIDADE É SUPERIOR A DATA DE ABERTURA
            if ( ( SistemaLegado::comparaDatas($request->get('dtAbertura'), $request->get('dtValidade'),true) ) && $stMensagem == '' ) {
                $stMensagem = 'Data de validade das propostas (<b><i>'.$request->get('dtValidade').'</i></b>) deve ser maior que a data de abertura das propostas(<b><i>'.$request->get('dtAbertura').'</i></b>).';
            }

            $dtInicio = dataYMD($request->get('dtEntrega')).' 00:00:00';
            $dtFim = dataYMD($request->get('dtValidade')).' 23:59:59';
            $qtd_dias_validade = SistemaLegado::datediff('d', $dtInicio, $dtFim);

            if ($stMensagem == '') {
                $obTLicitacaoEdital->setDado( 'num_edital'              , $request->get('inNumEdital')           );
                $obTLicitacaoEdital->setDado( 'exercicio'               , Sessao::getExercicio()                 );

                // POR QUE AINDA NÃO TEM O COMPONENTE QUE SELECIONA O DOCUMENTO O CODIGO TIPO E O CODIGO DO
                // DOCUMENTO ESTÃO FIXADOS COMO 0 (NÃO INFORMADO)

                $obTLicitacaoEdital->setDado( 'cod_tipo_documento'            , 0    								   );
                $obTLicitacaoEdital->setDado( 'cod_documento'                 , 0    							       );

                $obTLicitacaoEdital->setDado( 'responsavel_juridico'          , $request->get('inResponsavelJuridico') );
                $obTLicitacaoEdital->setDado( 'exercicio_licitacao'           , $request->get('stExercicioLicitacao')  );
                $obTLicitacaoEdital->setDado( 'cod_entidade'                  , $request->get('inCodEntidade')         );
                $obTLicitacaoEdital->setDado( 'cod_modalidade'                , $request->get('inCodModalidade')       );
                $obTLicitacaoEdital->setDado( 'cod_licitacao'                 , $request->get('inCodLicitacao')        );
                $obTLicitacaoEdital->setDado( 'local_entrega_propostas'       , $request->get('stLocalEntrega')        );
                $obTLicitacaoEdital->setDado( 'dt_entrega_propostas'          , $request->get('dtEntrega')             );
                $obTLicitacaoEdital->setDado( 'hora_entrega_propostas'        , $request->get('stHoraEntrega')         );
                $obTLicitacaoEdital->setDado( 'dt_final_entrega_propostas'    , $request->get('dtEntregaFinal')        );
                $obTLicitacaoEdital->setDado( 'hora_final_entrega_propostas'  , $request->get('stHoraEntregaFinal')    );
                $obTLicitacaoEdital->setDado( 'local_abertura_propostas'      , $request->get('stLocalAbertura')       );
                $obTLicitacaoEdital->setDado( 'dt_abertura_propostas'         , $request->get('dtAbertura')            );
                $obTLicitacaoEdital->setDado( 'hora_abertura_propostas'       , $request->get('stHoraAbertura')        );
                $obTLicitacaoEdital->setDado( 'dt_validade_proposta'          , $request->get('dtValidade')            );
                $obTLicitacaoEdital->setDado( 'observacao_validade_proposta'  , $request->get('txtValidade')           );
                $obTLicitacaoEdital->setDado( 'condicoes_pagamento'           , $request->get('txtCodPagamento')       );
                $obTLicitacaoEdital->setDado( 'local_entrega_material'        , $request->get('stLocalMaterial')       );
                $obTLicitacaoEdital->setDado( 'dt_aprovacao_juridico'         , $request->get('dtAprovacao')           );

                $obTLicitacaoEdital->alteracao();

                sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Edital: ".$obTLicitacaoEdital->getDado('num_edital')."/".Sessao::getExercicio(),"alterar","aviso", Sessao::getId(), "../");
                if ($request->get('boGerarDocumento') == 'S') {
                    $request->set('qtdDiasValidade',$qtd_dias_validade);
                    Sessao::write('request', $request->getAll());
                    SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
                }
            } else {
            sistemaLegado::exibeAviso(urlencode($stMensagem),'n_alterar','erro');
            }
    break;

    case 'anular':
        $arEdital = explode('/',$request->get('stNumEdital'));
        include_once ( TLIC. "TLicitacaoEditalAnulado.class.php" );
        $obTLicitacaoEditalAnulado = new TLicitacaoEditalAnulado();
        $obTLicitacaoEditalAnulado->setDado( 'num_edital', $arEdital[0] );
        $obTLicitacaoEditalAnulado->setDado( 'exercicio' , $arEdital[1] );
        $obTLicitacaoEditalAnulado->setDado( 'justificativa', $request->get('stJustificativa') );

        $obTLicitacaoEditalAnulado->inclusao();

        sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=anular","Edital: ".$arEdital[0]."/".$arEdital[1], "anular","aviso", Sessao::getId(), "../");
    break;

    case 'imprimir':

        Sessao::write('request', $request->getAll());

        if ($request->get('boGerarDocumento') == 'S') {
            SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
        }
    break;

}

Sessao::encerraExcecao();
