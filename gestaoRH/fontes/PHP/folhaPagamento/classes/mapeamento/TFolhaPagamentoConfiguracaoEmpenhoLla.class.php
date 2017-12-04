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
    * Classe de mapeamento da tabela folhapagamento.configuracao_empenho_lla
    * Data de Criação: 10/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-24 16:43:58 -0300 (Ter, 24 Jul 2007) $

    * Casos de uso: uc-04.05.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.configuracao_empenho_lla
  * Data de Criação: 10/07/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConfiguracaoEmpenhoLla extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoConfiguracaoEmpenhoLla()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.configuracao_empenho_lla");

    $this->setCampoCod('cod_configuracao_lla');
    $this->setComplementoChave('exercicio,cod_configuracao_lla,timestamp');

    $this->AddCampo('vigencia'                      ,'date'                     ,true  ,''   ,false,'TFolhaPagamentoConfiguracaoEmpenho');
    $this->AddCampo('timestamp'                  ,'timestamp_now'    ,true  ,''   ,true,'TFolhaPagamentoConfiguracaoEmpenho');
    $this->AddCampo('cod_configuracao_lla'  ,'sequence'            ,true  ,''   ,true,false);
    $this->AddCampo('exercicio'                     ,'char'                    ,true  ,'4'  ,true,false);

}

function recuperaEmissaoAutorizacoesEmpenho(&$rsRecordSet, $stFiltro="" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = " ORDER BY $stOrdem";
    }
    $stSql = $this->montaEmissaoAutorizacoesEmpenho().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaEmissaoAutorizacoesEmpenho()
{
    $stSql .= "SELECT despesa.num_orgao                                                                                                                                            \n";
    $stSql .= "     , adm_orgao.nom_orgao                                                                                                                                          \n";
    $stSql .= "     , despesa.num_unidade                                                                                                                                          \n";
    $stSql .= "     , adm_unidade.nom_unidade                                                                                                                                      \n";
    $stSql .= "     , despesa.cod_despesa as red_dotacao                                                                                                                           \n";
    $stSql .= "     , (SELECT cod_estrutural FROM orcamento.conta_despesa WHERE conta_despesa.cod_conta = configuracao_evento_despesa.cod_conta and exercicio = 2007) as rubrica_despesa       \n";
    $stSql .= "     , to_real(sum(evento_calculado.valor)) as valor                                                                                                                \n";
    $stSql .= "     , configuracao_empenho_lla_lotacao.num_pao                                                                                                                     \n";
    $stSql .= "     , (SELECT orgao||'-'||recuperaDescricaoOrgao(cod_orgao, '".Sessao::getExercicio()."-01-01') FROM organograma.vw_orgao_nivel WHERE cod_orgao = configuracao_empenho_lla_lotacao.cod_orgao) as lla     \n";
    $stSql .= "  FROM folhapagamento.configuracao_empenho_lla_lotacao                                                                                    \n";
    $stSql .= "     , pessoal.contrato_servidor_orgao                                                                                                    \n";
    $stSql .= "     , (SELECT cod_contrato                                                                                                                                         \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                                                          \n";
    $stSql .= "          FROM pessoal.contrato_servidor_orgao                                                                                            \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                                       \n";
    $stSql .= "     , folhapagamento.registro_evento_periodo                                                                                             \n";
    $stSql .= "     , folhapagamento.evento_calculado                                                                                                    \n";
    $stSql .= "     , folhapagamento.configuracao_evento_despesa                                                                                         \n";
    $stSql .= "     , (SELECT cod_evento                                                                                                                                           \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                                                          \n";
    $stSql .= "          FROM folhapagamento.configuracao_evento_despesa                                                                                 \n";
    $stSql .= "        GROUP BY cod_evento) as max_configuracao_evento_despesa                                                                                                     \n";
    $stSql .= "     , orcamento.despesa                                                                                                                                            \n";
    $stSql .= "     , orcamento.unidade                                                                                                                                            \n";
    $stSql .= "     , administracao.unidade as adm_unidade                                                                                                                         \n";
    $stSql .= "     , orcamento.orgao                                                                                                                                              \n";
    $stSql .= "     , administracao.orgao as adm_orgao                                                                                                                             \n";
    $stSql .= " WHERE configuracao_empenho_lla_lotacao.cod_orgao = contrato_servidor_orgao.cod_orgao                                                                               \n";
    $stSql .= "   AND contrato_servidor_orgao.cod_contrato       = max_contrato_servidor_orgao.cod_contrato                                                                              \n";
    $stSql .= "   AND contrato_servidor_orgao.timestamp          = max_contrato_servidor_orgao.timestamp                                                                                    \n";
    $stSql .= "   AND contrato_servidor_orgao.cod_contrato       = registro_evento_periodo.cod_contrato                                                                                  \n";
    $stSql .= "   AND registro_evento_periodo.cod_registro       = evento_calculado.cod_registro                                                                                         \n";
    $stSql .= "   AND evento_calculado.cod_evento                = configuracao_evento_despesa.cod_evento                                                                                         \n";
    $stSql .= "   AND configuracao_evento_despesa.cod_evento     = max_configuracao_evento_despesa.cod_evento                                                                          \n";
    $stSql .= "   AND configuracao_evento_despesa.timestamp      = max_configuracao_evento_despesa.timestamp                                                                            \n";
    $stSql .= "   AND despesa.cod_conta                          = configuracao_evento_despesa.cod_conta                                                                                                          \n";
    $stSql .= "   AND despesa.num_pao                            = configuracao_empenho_lla_lotacao.num_pao                                                                                                   \n";
    $stSql .= "   AND despesa.exercicio                          = unidade.exercicio                                                                                                                        \n";
    $stSql .= "   AND despesa.num_orgao                          = unidade.num_orgao                                                                                                                        \n";
    $stSql .= "   AND despesa.num_unidade                        = unidade.num_unidade                                                                                                                    \n";
    $stSql .= "   AND adm_unidade.ano_exercicio                  = unidade.ano_exercicio                                                                                                            \n";
    $stSql .= "   AND adm_unidade.cod_orgao                      = unidade.cod_orgao                                                                                                                    \n";
    $stSql .= "   AND adm_unidade.cod_unidade                    = unidade.cod_unidade                                                                                                                \n";
    $stSql .= "   AND unidade.exercicio                          = orgao.exercicio                                                                                                                          \n";
    $stSql .= "   AND unidade.num_orgao                          = orgao.num_orgao                                                                                                                          \n";
    $stSql .= "   AND orgao.ano_exercicio                        = adm_orgao.ano_exercicio                                                                                                                \n";
    $stSql .= "   AND orgao.cod_orgao                            = adm_orgao.cod_orgao                                                                                                                        \n";
    $stSql .= "   AND despesa.exercicio                          = ".Sessao::getExercicio()."                                                                                                                   \n";
    $stSql .= "   AND registro_evento_periodo.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."                                                            \n";
    $stSql .= "   AND configuracao_evento_despesa.cod_configuracao = ".$this->getDado("cod_configuracao")."                                                                        \n";
    $stSql .= "GROUP BY configuracao_empenho_lla_lotacao.cod_orgao                                                                                                                 \n";
    $stSql .= "       , evento_calculado.cod_evento                                                                                                                                \n";
    $stSql .= "       , configuracao_empenho_lla_lotacao.num_pao                                                                                                                   \n";
    $stSql .= "       , configuracao_evento_despesa.cod_conta                                                                                                                    \n";
    $stSql .= "       , despesa.cod_despesa                                                                                                                                        \n";
    $stSql .= "       , despesa.num_orgao                                                                                                                                          \n";
    $stSql .= "       , despesa.num_unidade                                                                                                                                        \n";
    $stSql .= "       , adm_orgao.nom_orgao                                                                                                                                        \n";
    $stSql .= "       , adm_unidade.nom_unidade                                                                                                                                    \n";

    return $stSql;
}

function resumoEmissaoAutorizacaoEmpenho(&$rsRecordSet, $stFiltro="" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = " ORDER BY $stOrdem";
    }
    $stSql = $this->montaResumoEmissaoAutorizacaoEmpenho().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaResumoEmissaoAutorizacaoEmpenho()
{
    $stSql = "SELECT * FROM resumoEmissaoAutorizacaoEmpenho(".$this->getDado("cod_periodo_movimentacao").",
                                                            ".$this->getDado("cod_configuracao").", ";

    if ($this->getDado('exercicio')) {
        $stSql .= " '".$this->getDado('exercicio')."', ";
    } else {
        $stSql .= " '".Sessao::getExercicio()."', ";
    }

    $stSql .= "
                                                           '".Sessao::getEntidade()."',
                                                            ".$this->getDado("cod_configuracao_autorizacao").",
                                                           '".$this->getDado("cadastro")."',
                                                           '".$this->getDado("origem")."',
                                                            ".$this->getDado("cod_previdencia").",
                                                           '".$this->getDado("filtro")."',
                                                           '".$this->getDado("join")."')";

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSql  = "    SELECT configuracao_empenho_lla.*
                     FROM folhapagamento.configuracao_empenho_lla
               INNER JOIN (   SELECT exercicio
                                   , vigencia
                                   , max(timestamp) as timestamp
                                FROM folhapagamento.configuracao_empenho_lla
                            GROUP BY exercicio
                                   , vigencia
                        ) as max_configuracao_empenho_lla
                       ON configuracao_empenho_lla.exercicio            = max_configuracao_empenho_lla.exercicio
                      AND configuracao_empenho_lla.timestamp            = max_configuracao_empenho_lla.timestamp
                      AND configuracao_empenho_lla.vigencia             = max_configuracao_empenho_lla.vigencia";

    return $stSql;
}

}
?>
