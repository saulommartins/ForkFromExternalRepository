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
* Classe de mapeamento da tabela ima.consignacao_emprestimo_banrisul
* Data de Criação   : 10/10/2009
*
* @author Analista      Dagine Rodrigues Vieira
* @author Desenvolvedor Cassiano de Vasconcellos Ferreira
*
* @package URBEM
* @subpackage
*
* @ignore
* $Id:$
 *
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TIMAConsignacaoEmprestimoBanrisul extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAConsignacaoEmprestimoBanrisul()
{
    parent::Persistente();
    $this->setTabela('ima.consignacao_emprestimo_banrisul');

    $this->setCampoCod('num_linha');
    $this->setComplementoChave('cod_periodo_movimentacao');
    //$this->AddCampo($stNome, $stTipo, $boRequerido, $nrTamanho, $boPrimaryKey, $stForeignKey);
    $this->AddCampo( 'num_linha', 'integer', false, '', true, '' );
    $this->AddCampo( 'cod_periodo_movimentacao', 'integer', false, '', true, '' );
    $this->AddCampo( 'oa', 'integer', false, '', false, '' );
    $this->AddCampo( 'matricula', 'integer', false, '', false, '' );
    $this->AddCampo( 'cpf', 'varchar', false, 15, false, '' );
    $this->AddCampo( 'nom_funcionario', 'varchar', false, 35, false, '' );
    $this->AddCampo( 'cod_canal', 'integer', false, '', false, '' );
    $this->AddCampo( 'nro_contrato', 'varchar', false, 20, false, '' );
    $this->AddCampo( 'prestacao', 'varchar', false,  7, false, '' );
    $this->AddCampo( 'val_consignar', 'integer', false, '', false, '' );
    $this->AddCampo( 'val_consignado', 'integer', false, '', false, '' );
    $this->AddCampo( 'filler', 'varchar', false, 200, false, '' );
    $this->AddCampo( 'cod_contrato', 'integer', false, '', false, '' );
    $this->AddCampo( 'origem_pagamento', 'char', false, 1, false, '' );
}

function exclusaoPorMovimentacao($boTransacao = "")
{
    $obErro     = new Erro;
    $obConexao  = new Conexao;
    $this->setDebug( 'exclusao' );
    if ( $this->getDado('cod_periodo_movimentacao') != '' ) {
        $stSql = $this->montaExclusaoPorMovimentacao();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaDML( $stSql, $boTransacao );
    } else {
        $obErro->setDescricao('Erro executando exclusaoPorMovimentacao. O campo cod_periodo_movimentacao deve estar setado!');
    }

    return $obErro;
}

function montaExclusaoPorMovimentacao()
{
    $stSql = ' DELETE FROM '.$this->getTabela().' WHERE cod_periodo_movimentacao = '.$this->getDado('cod_periodo_movimentacao');

    return $stSql;
}

function recuperaQuantidadePorMovimentacao(&$rsRecordSet, $stFiltro='',$stOrdem='',$boTransacao='')
{
    $obErro     = new Erro;
    $obConexao  = new Conexao;
    $stSql = $this->montaRecuperaQuantidadePorMovimentacao().$stFiltro;

    $this->setDebug( $stSql );
    if ( $this->getDado('cod_periodo_movimentacao') != '' ) {
            $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);
    } else {
        $obErro->setDescricao('Erro executando exclusaoPorMovimentacao. O campo cod_periodo_movimentacao deve estar setado!');
    }

    return $obErro;
}

function montaRecuperaQuantidadePorMovimentacao()
{
    return 'SELECT count(*) as quantidade FROM '.$this->getTabela().' WHERE cod_periodo_movimentacao = '.$this->getDado('cod_periodo_movimentacao');
}

function recuperaTodosPorMovimentacao(&$rsRecordSet , $boTransacao = '')
{
    $obErro = new Erro;
    if ( $this->getDado('cod_periodo_movimentacao') != '' ) {
        $stCondicao = " WHERE cod_periodo_movimentacao=".$this->getDado('cod_periodo_movimentacao');
        $stOrdem = 'num_linha';
        $obErro = $this->recuperaTodos($rsRecordSet, $stCondicao, $stOrdem, $boTransacao);
    } else {
        $obErro->setDescricao('Erro executando recuperaTodosPorMovimentacao. O campo cod_periodo_movimentacao deve estar setado!');
    }

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSQL  = "    SELECT                                                                                            \n";
    $stSQL .= "           consignacao_emprestimo_banrisul.*                                                          \n";
    $stSQL .= "         , consignacao_emprestimo_banrisul_erro.cod_motivo_rejeicao                                   \n";
    $stSQL .= "         , consignacao_emprestimo_banrisul_erro.descricao_motivo                                      \n";
    $stSQL .= "         , contrato.*                                                                                 \n";
    $stSQL .= "         , contrato_servidor_orgao.*                                                                  \n";
    $stSQL .= "      FROM                                                                                            \n";
    $stSQL .= "           ima.consignacao_emprestimo_banrisul                                                        \n";
    $stSQL .= " LEFT JOIN                                                                                            \n";
    $stSQL .= "           pessoal.contrato                                                                           \n";
    $stSQL .= "        ON                                                                                            \n";
    $stSQL .= "           contrato.cod_contrato = consignacao_emprestimo_banrisul.cod_contrato                       \n";
    $stSQL .= " LEFT JOIN                                                                                            \n";
    $stSQL .= "           ultimo_contrato_servidor_orgao('".Sessao::getEntidade()."',".$this->getDado('cod_periodo_movimentacao').") AS contrato_servidor_orgao \n";
    $stSQL .= "        ON                                                                                            \n";
    $stSQL .= "           contrato_servidor_orgao.cod_contrato = consignacao_emprestimo_banrisul.cod_contrato        \n";
    $stSQL .= " LEFT JOIN                                                                                            \n";
    $stSQL .= "           ima.consignacao_emprestimo_banrisul_erro                                                   \n";
    $stSQL .= "        ON                                                                                            \n";
    $stSQL .= "           consignacao_emprestimo_banrisul_erro.num_linha = consignacao_emprestimo_banrisul.num_linha \n";
    $stSQL .= "       AND consignacao_emprestimo_banrisul_erro.cod_periodo_movimentacao = consignacao_emprestimo_banrisul.cod_periodo_movimentacao \n";

    return $stSQL;
}

function recuperaSituacaoContrato(&$rsRecordSet, $stFiltro='',$stOrdem='',$boTransacao='')
{
    $obErro     = new Erro;
    $obConexao  = new Conexao;
    $stSql = $this->montaRecuperaSituacaoContrato().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    if ( $this->getDado('cod_periodo_movimentacao') != '' ) {
        if ($this->getDado('cod_contrato') != '' ) {
            $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);
        } else {
            $obErro->setDescricao('Erro executando exclusaoPorMovimentacao. O campo cod_contrato deve estar setado!');
        }
    } else {
        $obErro->setDescricao('Erro executando exclusaoPorMovimentacao. O campo cod_periodo_movimentacao deve estar setado!');
    }

    return $obErro;
}

function montaRecuperaSituacaoContrato()
{
    $stSQL  = " SELECT                                      \n";
    $stSQL .= "     recuperarsituacaodocontrato as situacao \n";
    $stSQL .= " FROM                                        \n";
    $stSQL .= "     recuperarSituacaoDoContrato(".$this->getDado('cod_contrato').",".$this->getDado('cod_periodo_movimentacao').",'".Sessao::getEntidade()."') \n";

    return $stSQL;
}

function recuperaSomatorio(&$rsRecordSet, $stFiltro='',$stOrdem='',$boTransacao='')
{
    $obErro     = new Erro;
    $obConexao  = new Conexao;
    $stSql = $this->montaRecuperaSomatorio($stFiltro).$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
}

function montaRecuperaSomatorio($stFiltro)
{
    $stSQL  = "   SELECT                                                          \n";
    $stSQL .= "          COALESCE(SUM(val_consignar),0)  AS sum_consignar         \n";
    $stSQL .= "        , COALESCE(SUM(val_consignado),0) AS sum_consignado        \n";
    $stSQL .= "        , count(cod_periodo_movimentacao) AS num_registros         \n";
    $stSQL .= "     FROM ima.consignacao_emprestimo_banrisul                      \n";
    $stSQL .= $stFiltro;
    $stSQL .= " GROUP BY consignacao_emprestimo_banrisul.cod_periodo_movimentacao \n";

    return $stSQL;
}

function montaConsultaRelatorio($stFiltro)
{
    $stSQL .= "     SELECT consignacao_emprestimo_banrisul.num_linha                                                                                              \n";
    $stSQL .= "          , consignacao_emprestimo_banrisul.matricula                                                                                              \n";
    $stSQL .= "          , consignacao_emprestimo_banrisul.nom_funcionario                                                                                        \n";
    $stSQL .= "          , consignacao_emprestimo_banrisul.cod_canal                                                                                              \n";
    $stSQL .= "          , consignacao_emprestimo_banrisul.prestacao                                                                                              \n";
    $stSQL .= "          ,to_number(                                                                                                                              \n";
    $stSQL .= "                    SUBSTR(                                                                                                                        \n";
    $stSQL .= "                            to_char(consignacao_emprestimo_banrisul.val_consignar,'999999999999999'),                                              \n";
    $stSQL .= "                            1,                                                                                                                     \n";
    $stSQL .= "                            length(to_char(consignacao_emprestimo_banrisul.val_consignar,'999999999999999')) - 2)                                  \n";
    $stSQL .= "                    ||'.'||                                                                                                                        \n";
    $stSQL .= "                    SUBSTR(                                                                                                                        \n";
    $stSQL .= "                            to_char(consignacao_emprestimo_banrisul.val_consignar,'999999999999999'),                                              \n";
    $stSQL .= "                            length(to_char(consignacao_emprestimo_banrisul.val_consignar,'999999999999999'))-1,2),                                 \n";
    $stSQL .= "                            '9999999999999.99'                                                                                                     \n";
    $stSQL .= "                          )                                                                                                                        \n";
    $stSQL .= "            AS val_consignar                                                                                                                       \n";
    $stSQL .= "          ,to_number(                                                                                                                              \n";
    $stSQL .= "                    SUBSTR(                                                                                                                        \n";
    $stSQL .= "                            to_char(consignacao_emprestimo_banrisul.val_consignado,'999999999999999'),                                             \n";
    $stSQL .= "                            1,                                                                                                                     \n";
    $stSQL .= "                            length(to_char(consignacao_emprestimo_banrisul.val_consignado,'999999999999999')) - 2)                                 \n";
    $stSQL .= "                    ||'.'||                                                                                                                        \n";
    $stSQL .= "                    SUBSTR(                                                                                                                        \n";
    $stSQL .= "                            to_char(consignacao_emprestimo_banrisul.val_consignado,'999999999999999'),                                             \n";
    $stSQL .= "                            length(to_char(consignacao_emprestimo_banrisul.val_consignado,'999999999999999'))-1,2),                                \n";
    $stSQL .= "                            '9999999999999.99'                                                                                                     \n";
    $stSQL .= "                          )                                                                                                                        \n";
    $stSQL .= "            AS val_consignado                                                                                                                      \n";
    $stSQL .= "          , consignacao_emprestimo_banrisul.cod_contrato                                                                                           \n";
    $stSQL .= "          , consignacao_emprestimo_banrisul.cod_periodo_movimentacao                                                                               \n";
    $stSQL .= "          , consignacao_emprestimo_banrisul.origem_pagamento                                                                                       \n";
    $stSQL .= "          , evento.cod_evento                                                                                                                      \n";
    $stSQL .= "          , evento.descricao                                                                                                                       \n";
    $stSQL .= "          , motivos_rejeicao_consignacao_emprestimo_banrisul.cod_motivo_rejeicao                                                                   \n";
    $stSQL .= "          , motivos_rejeicao_consignacao_emprestimo_banrisul.descricao as descricao_rejeicao                                                       \n";
    $stSQL .= "       FROM ima.consignacao_emprestimo_banrisul                                                                                                    \n";
    $stSQL .= " INNER JOIN folhapagamento".Sessao::getEntidade().".evento                                                                                         \n";
    $stSQL .= "         ON consignacao_emprestimo_banrisul.cod_canal = evento.cod_evento                                                                          \n";
    $stSQL .= "  LEFT JOIN ima".Sessao::getEntidade().".consignacao_emprestimo_banrisul_erro                                                                      \n";
    $stSQL .= "         ON consignacao_emprestimo_banrisul.num_linha = consignacao_emprestimo_banrisul_erro.num_linha                                             \n";
    $stSQL .= "        AND consignacao_emprestimo_banrisul.cod_periodo_movimentacao = consignacao_emprestimo_banrisul_erro.cod_periodo_movimentacao               \n";
    $stSQL .= "  LEFT JOIN ima".Sessao::getEntidade().".motivos_rejeicao_consignacao_emprestimo_banrisul                                                          \n";
    $stSQL .= "         ON motivos_rejeicao_consignacao_emprestimo_banrisul.cod_motivo_rejeicao = consignacao_emprestimo_banrisul_erro.cod_motivo_rejeicao        \n";
    $stSQL .= " LEFT JOIN pessoal".Sessao::getEntidade().".contrato                                                                                               \n";
    $stSQL .= "        ON contrato.cod_contrato = consignacao_emprestimo_banrisul.cod_contrato                                                                    \n";
    $stSQL .= " LEFT JOIN ultimo_contrato_servidor_orgao('".Sessao::getEntidade()."',".$this->getDado('cod_periodo_movimentacao').") AS contrato_servidor_orgao \n";
    $stSQL .= "        ON contrato_servidor_orgao.cod_contrato = consignacao_emprestimo_banrisul.cod_contrato                                                     \n";
    $stSQL .= $stFiltro;

    return $stSQL;
}
}
?>
