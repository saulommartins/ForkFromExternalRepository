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
    * Classe de mapeamento da tabela folhapagamento.tabela_irrf_evento
    * Data de Criação: 05/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: alex $
    $Date: 2008-03-12 16:23:42 -0300 (Qua, 12 Mar 2008) $

    * Casos de uso: uc-04.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.tabela_irrf_evento
  * Data de Criação: 05/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoTabelaIrrfEvento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoTabelaIrrfEvento()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.tabela_irrf_evento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_tabela,timestamp,cod_tipo');

    $this->AddCampo('cod_tabela','integer',true,'',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('cod_evento','integer',true,'',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',true,true);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT tabela_irrf_evento.*                                  \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                      \n";
    $stSql .= "     , evento.codigo                                         \n";
    $stSql .= "  FROM folhapagamento.tabela_irrf_evento                     \n";
    $stSql .= "     , (SELECT cod_tabela                                    \n";
    $stSql .= "             , max(timestamp) as timestamp                   \n";
    $stSql .= "          FROM folhapagamento.tabela_irrf_evento             \n";
    $stSql .= "        GROUP BY cod_tabela) as max_tabela_irrf_evento       \n";
    $stSql .= "     , folhapagamento.evento                                 \n";
    $stSql .= " WHERE tabela_irrf_evento.cod_evento = evento.cod_evento     \n";
    $stSql .= "   AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela \n";
    $stSql .= "   AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp  \n";

    return $stSql;
}

function recuperaEventosDeIrrfPorContrato(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaEventosDeIrrfPorContrato().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosDeIrrfPorContrato()
{
    $stSql .= "SELECT evento.cod_evento                                     \n";
    $stSql .= "  FROM folhapagamento.tabela_irrf_evento                     \n";
    $stSql .= "     , folhapagamento.tabela_irrf                            \n";
    $stSql .= "     , (SELECT cod_tabela                                    \n";
    $stSql .= "             , max(timestamp) as timestamp                   \n";
    $stSql .= "          FROM folhapagamento.tabela_irrf                    \n";
    $stSql .= "        GROUP BY cod_tabela) as max_tabela_irrf              \n";
    $stSql .= "     , folhapagamento.evento                                 \n";
    $stSql .= "     , folhapagamento.registro_evento                        \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento                 \n";
    $stSql .= "     , folhapagamento.registro_evento_periodo                \n";
    $stSql .= " WHERE tabela_irrf_evento.cod_evento = evento.cod_evento     \n";
    $stSql .= "   AND tabela_irrf_evento.cod_tabela = tabela_irrf.cod_tabela\n";
    $stSql .= "   AND tabela_irrf_evento.timestamp  = tabela_irrf.timestamp \n";
    $stSql .= "   AND tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela   \n";
    $stSql .= "   AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp    \n";
    $stSql .= "   AND evento.cod_evento = registro_evento.cod_evento        \n";
    $stSql .= "   AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento \n";
    $stSql .= "   AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro \n";
    $stSql .= "   AND registro_evento.timestamp  = ultimo_registro_evento.timestamp  \n";
    $stSql .= "   AND registro_evento.cod_registro = registro_evento_periodo.cod_registro \n";

    return $stSql;
}

function recuperaRelatorioIRRF(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelatorioIRRF().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioIRRF()
{
    $stSql .= "    SELECT contrato.registro  as campo1                                                                                                 \n";
    $stSql .= "         , sw_cgm.numcgm || ' - ' ||  sw_cgm.nom_cgm as campo2                                                                          \n";
    $stSql .= "         , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao) || ' - ' || recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as campo3  \n";
    $stSql .= "         , evento_base.valor as campo4                                                                                                  \n";
    $stSql .= "         , evento_desconto.valor as campo5                                                                                              \n";
    $stSql .= "         , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as lotacao									   \n";
    $stSql .= "      FROM pessoal.contrato                                                                                    \n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor                                                                           \n";
    $stSql .= "        ON contrato.cod_contrato = contrato_servidor.cod_contrato                                                                       \n";
    $stSql .= "INNER JOIN pessoal.servidor_contrato_servidor                                                                  \n";
    $stSql .= "        ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato                                                     \n";
    $stSql .= "INNER JOIN pessoal.servidor                                                                                    \n";
    $stSql .= "        ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor                                                              \n";
    $stSql .= "INNER JOIN sw_cgm_pessoa_fisica                                                                                                         \n";
    $stSql .= "        ON servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                \n";
    $stSql .= "INNER JOIN sw_cgm                                                                                                                       \n";
    $stSql .= "        ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                                                                  \n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor_orgao                                                                     \n";
    $stSql .= "        ON contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                                        \n";
    $stSql .= "INNER JOIN ( SELECT cod_contrato                                                                                                        \n";
    $stSql .= "                  , max(timestamp) as timestamp                                                                                         \n";
    $stSql .= "               FROM pessoal.contrato_servidor_orgao                                                            \n";
    $stSql .= "           GROUP BY cod_contrato ) as max_contrato_servidor_orgao                                                                       \n";
    $stSql .= "        ON contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                              \n";
    $stSql .= "       AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                                    \n";
    $stSql .= "INNER JOIN organograma.orgao                                                                                                            \n";
    $stSql .= "        ON orgao.cod_orgao       = contrato_servidor_orgao.cod_orgao                                                                    \n";
    $stSql .= "INNER JOIN organograma.orgao_nivel                                                                                                      \n";
    $stSql .= "        ON orgao.cod_orgao        = orgao_nivel.cod_orgao                                                                               \n";
    $stSql .= "       AND orgao_nivel.cod_nivel =  publico.fn_nivel(organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao))       \n";
    $stSql .= "INNER JOIN folhapagamento.contrato_servidor_periodo                                                            \n";
    $stSql .= "        ON contrato_servidor_periodo.cod_contrato = contrato_servidor.cod_contrato                                                      \n";
    $stSql .= "INNER JOIN folhapagamento.periodo_movimentacao                                                                 \n";
    $stSql .= "        ON periodo_movimentacao.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                           \n";
    switch ($this->getDado("cod_configuracao")) {
        case 0:
            //Complementar
            $stSql .= " LEFT JOIN (SELECT registro_evento_complementar.cod_contrato                                                                            \n";
            $stSql .= "                 , registro_evento_complementar.cod_periodo_movimentacao                                                                \n";
            $stSql .= "                 , evento_complementar_calculado.valor                                                                                  \n";
            $stSql .= "              FROM folhapagamento.registro_evento_complementar                                                                          \n";
            $stSql .= "                 , folhapagamento.evento_complementar_calculado                                                                         \n";
            $stSql .= "                 , folhapagamento.tabela_irrf_evento                                                                                    \n";
            $stSql .= "                 , (SELECT cod_tabela                                                                                                   \n";
            $stSql .= "                         , max(timestamp) as timestamp                                                                                  \n";
            $stSql .= "                      FROM folhapagamento.tabela_irrf_evento                                                                            \n";
            $stSql .= "                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
            $stSql .= "             WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                               \n";
            $stSql .= "               AND evento_complementar_calculado.cod_evento = tabela_irrf_evento.cod_evento                                             \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
            $stSql .= "               AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                      \n";
            $stSql .= "               AND registro_evento_complementar.cod_complementar = ".$this->getDado("cod_complementar")."                               \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tipo = 7) as evento_base                                                                      \n";
            $stSql .= "        ON evento_base.cod_contrato             = contrato_servidor_periodo.cod_contrato                                                \n";
            $stSql .= "       AND evento_base.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                    \n";
            $stSql .= " LEFT JOIN (SELECT registro_evento_complementar.cod_contrato                                                                            \n";
            $stSql .= "                 , registro_evento_complementar.cod_periodo_movimentacao                                                                \n";
            $stSql .= "                 , evento_complementar_calculado.valor as valor                                                                         \n";
            $stSql .= "              FROM folhapagamento.registro_evento_complementar                                                                          \n";
            $stSql .= "                 , folhapagamento.evento_complementar_calculado                                                                         \n";
            $stSql .= "                 , folhapagamento.tabela_irrf_evento                                                                                    \n";
            $stSql .= "                 , (SELECT cod_tabela                                                                                                   \n";
            $stSql .= "                         , max(timestamp) as timestamp                                                                                  \n";
            $stSql .= "                      FROM folhapagamento.tabela_irrf_evento                                                                            \n";
            $stSql .= "                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
            $stSql .= "             WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                               \n";
            $stSql .= "               AND evento_complementar_calculado.cod_evento = tabela_irrf_evento.cod_evento                                             \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
            $stSql .= "               AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                      \n";
            $stSql .= "               AND registro_evento_complementar.cod_complementar = ".$this->getDado("cod_complementar")."                               \n";
            $stSql .= "               AND (tabela_irrf_evento.cod_tipo = 6 OR                                                                                  \n";
            $stSql .= "                    tabela_irrf_evento.cod_tipo = 3)) as evento_desconto                                                                \n";
            $stSql .= "        ON evento_desconto.cod_contrato             = contrato_servidor_periodo.cod_contrato                                            \n";
            $stSql .= "       AND evento_desconto.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                \n";
            break;
        case 1:
            //Salário
            $stSql .= " LEFT JOIN (SELECT registro_evento_periodo.cod_contrato                                                                                 \n";
            $stSql .= "                 , registro_evento_periodo.cod_periodo_movimentacao                                                                     \n";
            $stSql .= "                 , evento_calculado.valor                                                                                               \n";
            $stSql .= "              FROM folhapagamento.registro_evento_periodo                                                                               \n";
            $stSql .= "                 , folhapagamento.evento_calculado                                                                                      \n";
            $stSql .= "                 , folhapagamento.tabela_irrf_evento                                                                                    \n";
            $stSql .= "                 , (SELECT cod_tabela                                                                                                   \n";
            $stSql .= "                         , max(timestamp) as timestamp                                                                                  \n";
            $stSql .= "                      FROM folhapagamento.tabela_irrf_evento                                                                            \n";
            $stSql .= "                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
            $stSql .= "             WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro                                                 \n";
            $stSql .= "               AND evento_calculado.cod_evento = tabela_irrf_evento.cod_evento                                                          \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
            $stSql .= "               AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                      \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tipo = 7) as evento_base                                                                      \n";
            $stSql .= "        ON evento_base.cod_contrato             = contrato_servidor_periodo.cod_contrato                                                \n";
            $stSql .= "       AND evento_base.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                    \n";
            $stSql .= " LEFT JOIN (SELECT registro_evento_periodo.cod_contrato                                                                                 \n";
            $stSql .= "                 , registro_evento_periodo.cod_periodo_movimentacao                                                                     \n";
            $stSql .= "                 , evento_calculado.valor                                                                                               \n";
            $stSql .= "              FROM folhapagamento.registro_evento_periodo                                                                               \n";
            $stSql .= "                 , folhapagamento.evento_calculado                                                                                      \n";
            $stSql .= "                 , folhapagamento.tabela_irrf_evento                                                                                    \n";
            $stSql .= "                 , (SELECT cod_tabela                                                                                                   \n";
            $stSql .= "                         , max(timestamp) as timestamp                                                                                  \n";
            $stSql .= "                      FROM folhapagamento.tabela_irrf_evento                                                                            \n";
            $stSql .= "                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
            $stSql .= "             WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro                                                 \n";
            $stSql .= "               AND evento_calculado.cod_evento = tabela_irrf_evento.cod_evento                                                          \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
            $stSql .= "               AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                      \n";
            $stSql .= "               AND (tabela_irrf_evento.cod_tipo = 6 OR                                                                                  \n";
            $stSql .= "                    tabela_irrf_evento.cod_tipo = 3)) as evento_desconto                                                                \n";
            $stSql .= "        ON evento_desconto.cod_contrato             = contrato_servidor_periodo.cod_contrato                                            \n";
            $stSql .= "       AND evento_desconto.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                \n";
            break;
        case 2:
            //Férias
            $stSql .= " LEFT JOIN (SELECT registro_evento_ferias.cod_contrato                                                                                  \n";
            $stSql .= "                 , registro_evento_ferias.cod_periodo_movimentacao                                                                      \n";
            $stSql .= "                 , evento_ferias_calculado.valor                                                                                        \n";
            $stSql .= "              FROM folhapagamento.registro_evento_ferias                                                                                \n";
            $stSql .= "                 , folhapagamento.evento_ferias_calculado                                                                               \n";
            $stSql .= "                 , folhapagamento.tabela_irrf_evento                                                                                    \n";
            $stSql .= "                 , (SELECT cod_tabela                                                                                                   \n";
            $stSql .= "                         , max(timestamp) as timestamp                                                                                  \n";
            $stSql .= "                      FROM folhapagamento.tabela_irrf_evento                                                                            \n";
            $stSql .= "                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
            $stSql .= "             WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro                                           \n";
            $stSql .= "               AND evento_ferias_calculado.cod_evento = tabela_irrf_evento.cod_evento                                                   \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
            $stSql .= "               AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                      \n";
            $stSql .= "               AND evento_ferias_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                        \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tipo = 7) as evento_base                                                                      \n";
            $stSql .= "        ON evento_base.cod_contrato             = contrato_servidor_periodo.cod_contrato                                                \n";
            $stSql .= "       AND evento_base.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                    \n";
            $stSql .= " LEFT JOIN (SELECT registro_evento_ferias.cod_contrato                                                                                  \n";
            $stSql .= "                 , registro_evento_ferias.cod_periodo_movimentacao                                                                      \n";
            $stSql .= "                 , evento_ferias_calculado.valor                                                                                        \n";
            $stSql .= "              FROM folhapagamento.registro_evento_ferias                                                                                \n";
            $stSql .= "                 , folhapagamento.evento_ferias_calculado                                                                               \n";
            $stSql .= "                 , folhapagamento.tabela_irrf_evento                                                                                    \n";
            $stSql .= "                 , (SELECT cod_tabela                                                                                                   \n";
            $stSql .= "                         , max(timestamp) as timestamp                                                                                  \n";
            $stSql .= "                      FROM folhapagamento.tabela_irrf_evento                                                                            \n";
            $stSql .= "                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
            $stSql .= "             WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro                                           \n";
            $stSql .= "               AND evento_ferias_calculado.cod_evento = tabela_irrf_evento.cod_evento                                                   \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
            $stSql .= "               AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                      \n";
            $stSql .= "               AND evento_ferias_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                        \n";
            $stSql .= "               AND (tabela_irrf_evento.cod_tipo = 6 OR                                                                                  \n";
            $stSql .= "                    tabela_irrf_evento.cod_tipo = 3)) as evento_desconto                                                                \n";
            $stSql .= "        ON evento_desconto.cod_contrato             = contrato_servidor_periodo.cod_contrato                                            \n";
            $stSql .= "       AND evento_desconto.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                \n";
            break;
        case 3:
            //Décimo
            $stSql .= " LEFT JOIN (SELECT registro_evento_decimo.cod_contrato                                                                                  \n";
            $stSql .= "                 , registro_evento_decimo.cod_periodo_movimentacao                                                                      \n";
            $stSql .= "                 , evento_decimo_calculado.valor                                                                                        \n";
            $stSql .= "              FROM folhapagamento.registro_evento_decimo                                                                                \n";
            $stSql .= "                 , folhapagamento.evento_decimo_calculado                                                                               \n";
            $stSql .= "                 , folhapagamento.tabela_irrf_evento                                                                                    \n";
            $stSql .= "                 , (SELECT cod_tabela                                                                                                   \n";
            $stSql .= "                         , max(timestamp) as timestamp                                                                                  \n";
            $stSql .= "                      FROM folhapagamento.tabela_irrf_evento                                                                            \n";
            $stSql .= "                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
            $stSql .= "             WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro                                           \n";
            $stSql .= "               AND evento_decimo_calculado.cod_evento = tabela_irrf_evento.cod_evento                                                   \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
            $stSql .= "               AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                      \n";
            $stSql .= "               AND evento_decimo_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                        \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tipo = 7) as evento_base                                                                      \n";
            $stSql .= "        ON evento_base.cod_contrato             = contrato_servidor_periodo.cod_contrato                                                \n";
            $stSql .= "       AND evento_base.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                    \n";
            $stSql .= " LEFT JOIN (SELECT registro_evento_decimo.cod_contrato                                                                                  \n";
            $stSql .= "                 , registro_evento_decimo.cod_periodo_movimentacao                                                                      \n";
            $stSql .= "                 , evento_decimo_calculado.valor                                                                                        \n";
            $stSql .= "              FROM folhapagamento.registro_evento_decimo                                                                                \n";
            $stSql .= "                 , folhapagamento.evento_decimo_calculado                                                                               \n";
            $stSql .= "                 , folhapagamento.tabela_irrf_evento                                                                                    \n";
            $stSql .= "                 , (SELECT cod_tabela                                                                                                   \n";
            $stSql .= "                         , max(timestamp) as timestamp                                                                                  \n";
            $stSql .= "                      FROM folhapagamento.tabela_irrf_evento                                                                            \n";
            $stSql .= "                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
            $stSql .= "             WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro                                           \n";
            $stSql .= "               AND evento_decimo_calculado.cod_evento = tabela_irrf_evento.cod_evento                                                   \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
            $stSql .= "               AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                      \n";
            $stSql .= "               AND evento_decimo_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                        \n";
            $stSql .= "               AND (tabela_irrf_evento.cod_tipo = 6 OR                                                                                  \n";
            $stSql .= "                    tabela_irrf_evento.cod_tipo = 3)) as evento_desconto                                                                \n";
            $stSql .= "        ON evento_desconto.cod_contrato             = contrato_servidor_periodo.cod_contrato                                            \n";
            $stSql .= "       AND evento_desconto.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                \n";
            break;
        case 4:
            //Rescisão
            $stSql .= " LEFT JOIN (SELECT registro_evento_rescisao.cod_contrato                                                                                \n";
            $stSql .= "                 , registro_evento_rescisao.cod_periodo_movimentacao                                                                    \n";
            $stSql .= "                 , evento_rescisao_calculado.valor                                                                                      \n";
            $stSql .= "              FROM folhapagamento.registro_evento_rescisao                                                                              \n";
            $stSql .= "                 , folhapagamento.evento_rescisao_calculado                                                                             \n";
            $stSql .= "                 , folhapagamento.tabela_irrf_evento                                                                                    \n";
            $stSql .= "                 , (SELECT cod_tabela                                                                                                   \n";
            $stSql .= "                         , max(timestamp) as timestamp                                                                                  \n";
            $stSql .= "                      FROM folhapagamento.tabela_irrf_evento                                                                            \n";
            $stSql .= "                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
            $stSql .= "             WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro                                       \n";
            $stSql .= "               AND evento_rescisao_calculado.cod_evento = tabela_irrf_evento.cod_evento                                                 \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
            $stSql .= "               AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                      \n";
            $stSql .= "               AND evento_rescisao_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                      \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tipo = 7) as evento_base                                                                      \n";
            $stSql .= "        ON evento_base.cod_contrato             = contrato_servidor_periodo.cod_contrato                                                \n";
            $stSql .= "       AND evento_base.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                    \n";
            $stSql .= " LEFT JOIN (SELECT registro_evento_rescisao.cod_contrato                                                                                \n";
            $stSql .= "                 , registro_evento_rescisao.cod_periodo_movimentacao                                                                    \n";
            $stSql .= "                 , evento_rescisao_calculado.valor                                                                                      \n";
            $stSql .= "              FROM folhapagamento.registro_evento_rescisao                                                                              \n";
            $stSql .= "                 , folhapagamento.evento_rescisao_calculado                                                                             \n";
            $stSql .= "                 , folhapagamento.tabela_irrf_evento                                                                                    \n";
            $stSql .= "                 , (SELECT cod_tabela                                                                                                   \n";
            $stSql .= "                         , max(timestamp) as timestamp                                                                                  \n";
            $stSql .= "                      FROM folhapagamento.tabela_irrf_evento                                                                            \n";
            $stSql .= "                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
            $stSql .= "             WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro                                       \n";
            $stSql .= "               AND evento_rescisao_calculado.cod_evento = tabela_irrf_evento.cod_evento                                                 \n";
            $stSql .= "               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
            $stSql .= "               AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                      \n";
            $stSql .= "               AND evento_rescisao_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                      \n";
            $stSql .= "               AND (tabela_irrf_evento.cod_tipo = 6 OR                                                                                  \n";
            $stSql .= "                    tabela_irrf_evento.cod_tipo = 3)) as evento_desconto                                                                \n";
            $stSql .= "        ON evento_desconto.cod_contrato             = contrato_servidor_periodo.cod_contrato                                            \n";
            $stSql .= "       AND evento_desconto.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                \n";
            break;
    }
    $stSql .= "     WHERE (evento_base.valor IS NOT NULL AND evento_desconto.valor IS NOT NULL)                                                         	   \n";

    return $stSql;
}

function recuperaEventoCalculadoIrrf(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaEventoCalculadoIrrf().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventoCalculadoIrrf()
{
    $stSql .= "SELECT *                                                                                                                \n";
    $stSql .= "  FROM (SELECT SUM(evento_calculado.valor) as valor                                                            \n";
    $stSql .= "             , contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_periodo.cod_periodo_movimentacao                                                         \n";
    $stSql .= "             , 1 AS inFolha                                                                                             \n";
    $stSql .= "          FROM folhapagamento.evento_calculado                                                \n";
    $stSql .= "             , folhapagamento.registro_evento                                                 \n";
    $stSql .= "             , folhapagamento.registro_evento_periodo                                         \n";
    $stSql .= "             , pessoal.contrato                                                               \n";
    $stSql .= "             , folhapagamento.tabela_irrf_evento                                              \n";
    $stSql .= "             , (SELECT cod_tabela                                                                                       \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                      \n";
    $stSql .= "                  FROM folhapagamento.tabela_irrf_evento                                      \n";
    $stSql .= "                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                          \n";
    $stSql .= "         WHERE evento_calculado.cod_evento = registro_evento.cod_evento                                                 \n";
    $stSql .= "           AND evento_calculado.cod_registro = registro_evento.cod_registro                                             \n";
    $stSql .= "           AND evento_calculado.timestamp_registro = registro_evento.timestamp                                          \n";
    $stSql .= "           AND registro_evento.cod_registro = registro_evento_periodo.cod_registro                                      \n";
    $stSql .= "           AND registro_evento_periodo.cod_contrato = contrato.cod_contrato                                             \n";
    $stSql .= "           AND registro_evento.cod_evento = tabela_irrf_evento.cod_evento                                               \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                        \n";
    $stSql .= "           AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                          \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                             \n";
    $stSql .= "      GROUP BY contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_periodo.cod_periodo_movimentacao                                                         \n";
    $stSql .= "         UNION                                                                                                          \n";
    $stSql .= "        SELECT sum(evento_ferias_calculado.valor) as valor                                                     \n";
    $stSql .= "             , contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_ferias.cod_periodo_movimentacao                                                          \n";
    $stSql .= "             , 2 AS inFolha                                                                                             \n";
    $stSql .= "          FROM folhapagamento.evento_ferias_calculado                                         \n";
    $stSql .= "             , folhapagamento.registro_evento_ferias                                          \n";
    $stSql .= "             , pessoal.contrato                                                               \n";
    $stSql .= "             , folhapagamento.tabela_irrf_evento                                              \n";
    $stSql .= "             , (SELECT cod_tabela                                                                                       \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                      \n";
    $stSql .= "                  FROM folhapagamento.tabela_irrf_evento                                      \n";
    $stSql .= "                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                          \n";
    $stSql .= "         WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento                           \n";
    $stSql .= "           AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro                         \n";
    $stSql .= "           AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento                        \n";
    $stSql .= "           AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp                            \n";
    $stSql .= "           AND registro_evento_ferias.cod_contrato = contrato.cod_contrato                                              \n";
    $stSql .= "           AND registro_evento_ferias.cod_evento = tabela_irrf_evento.cod_evento                                        \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                        \n";
    $stSql .= "           AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                          \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                             \n";

    if ($this->getDado("desdobramento") != "") {
        $stSql .= "           AND evento_ferias_calculado.desdobramento = '".$this->getDado("desdobramento")."'                            \n";
    }

    $stSql .= "      GROUP BY contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_ferias.cod_periodo_movimentacao                                                          \n";
    $stSql .= "         UNION                                                                                                          \n";
    $stSql .= "        SELECT sum(evento_decimo_calculado.valor) as valor                                                     \n";
    $stSql .= "             , contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_decimo.cod_periodo_movimentacao                                                          \n";
    $stSql .= "             , 3 AS inFolha                                                                                             \n";
    $stSql .= "          FROM folhapagamento.evento_decimo_calculado                                         \n";
    $stSql .= "             , folhapagamento.registro_evento_decimo                                          \n";
    $stSql .= "             , pessoal.contrato                                                               \n";
    $stSql .= "             , folhapagamento.tabela_irrf_evento                                              \n";
    $stSql .= "             , (SELECT cod_tabela                                                                                       \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                      \n";
    $stSql .= "                  FROM folhapagamento.tabela_irrf_evento                                      \n";
    $stSql .= "                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                          \n";
    $stSql .= "         WHERE evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento                           \n";
    $stSql .= "           AND evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro                         \n";
    $stSql .= "           AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento                        \n";
    $stSql .= "           AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp                            \n";
    $stSql .= "           AND registro_evento_decimo.cod_contrato = contrato.cod_contrato                                              \n";
    $stSql .= "           AND registro_evento_decimo.cod_evento = tabela_irrf_evento.cod_evento                                        \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                        \n";
    $stSql .= "           AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                          \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                             \n";

    if ($this->getDado("desdobramento") != "") {
        $stSql .= "           AND evento_decimo_calculado.desdobramento = '".$this->getDado("desdobramento")."'                            \n";
    }

    $stSql .= "      GROUP BY contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_decimo.cod_periodo_movimentacao                                                          \n";
    $stSql .= "        UNION                                                                                                           \n";
    $stSql .= "        SELECT sum(evento_rescisao_calculado.valor) as valor                                                   \n";
    $stSql .= "             , contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_rescisao.cod_periodo_movimentacao                                                        \n";
    $stSql .= "             , 4 AS inFolha                                                                                             \n";
    $stSql .= "          FROM folhapagamento.evento_rescisao_calculado                                       \n";
    $stSql .= "             , folhapagamento.registro_evento_rescisao                                        \n";
    $stSql .= "             , pessoal.contrato                                                               \n";
    $stSql .= "             , folhapagamento.tabela_irrf_evento                                              \n";
    $stSql .= "             , (SELECT cod_tabela                                                                                       \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                      \n";
    $stSql .= "                  FROM folhapagamento.tabela_irrf_evento                                      \n";
    $stSql .= "                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                          \n";
    $stSql .= "         WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento                       \n";
    $stSql .= "           AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro                     \n";
    $stSql .= "           AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento                    \n";
    $stSql .= "           AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp                        \n";
    $stSql .= "           AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                            \n";
    $stSql .= "           AND registro_evento_rescisao.cod_evento = tabela_irrf_evento.cod_evento                                      \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                        \n";
    $stSql .= "           AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                          \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                             \n";

    if ($this->getDado("desdobramento") != "") {
        $stSql .= "           AND evento_rescisao_calculado.desdobramento = '".$this->getDado("desdobramento")."'                          \n";
    }

    $stSql .= "      GROUP BY contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_rescisao.cod_periodo_movimentacao                                                        \n";
    $stSql .= "        UNION                                                                                                           \n";
    $stSql .= "        SELECT sum(evento_complementar_calculado.valor) as valor                                               \n";
    $stSql .= "             , contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_complementar.cod_periodo_movimentacao                                                    \n";
    $stSql .= "             , 0 AS inFolha                                                                                             \n";
    $stSql .= "          FROM folhapagamento.evento_complementar_calculado                                   \n";
    $stSql .= "             , folhapagamento.registro_evento_complementar                                    \n";
    $stSql .= "             , pessoal.contrato                                                               \n";
    $stSql .= "             , folhapagamento.tabela_irrf_evento                                              \n";
    $stSql .= "             , (SELECT cod_tabela                                                                                       \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                      \n";
    $stSql .= "                  FROM folhapagamento.tabela_irrf_evento                                      \n";
    $stSql .= "                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                          \n";
    $stSql .= "         WHERE evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento               \n";
    $stSql .= "           AND evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro             \n";
    $stSql .= "           AND evento_complementar_calculado.cod_configuracao      = registro_evento_complementar.cod_configuracao      \n";
    $stSql .= "           AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp                \n";
    $stSql .= "           AND registro_evento_complementar.cod_contrato = contrato.cod_contrato                                        \n";
    $stSql .= "           AND registro_evento_complementar.cod_evento = tabela_irrf_evento.cod_evento                                  \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                        \n";
    $stSql .= "           AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                          \n";
    $stSql .= "           AND registro_evento_complementar.cod_complementar = ".$this->getDado("cod_complementar")."                   \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                             \n";
    $stSql .= "      GROUP BY contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_complementar.cod_periodo_movimentacao) as irrf                                           \n";

    return $stSql;
}

}
