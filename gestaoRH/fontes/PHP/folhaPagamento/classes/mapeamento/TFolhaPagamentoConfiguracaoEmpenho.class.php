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
    * Classe de mapeamento da tabela folhapagamento.configuracao_empenho_evento
    * Data de Criação: 10/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 31015 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 10:02:38 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-04.05.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  folhapagamento.configuracao_empenho_evento
  * Data de Criação: 10/07/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConfiguracaoEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela("folhapagamento.configuracao_empenho");
    
        $this->setCampoCod('sequencia');
        $this->setComplementoChave('exercicio,cod_configuracao,sequencia,vigencia,timestamp');
    
        $this->AddCampo('exercicio'        ,'char'          ,true  ,'4'  ,true ,false);
        $this->AddCampo('cod_configuracao' ,'integer'       ,true  ,''   ,true ,'TFolhaPagamentoConfiguracaoEvento');
        $this->AddCampo('exercicio_despesa','char'          ,true  ,'4'  ,true ,'TOrcamentoDespesa');
        $this->AddCampo('cod_despesa'      ,'integer'       ,true  ,''   ,true ,'TOrcamentoDespesa');
        $this->AddCampo('sequencia'        ,'sequence'      ,true  ,''   ,true ,false);
        $this->AddCampo('num_pao'          ,'integer'       ,true  ,''   ,false,'TOrcamentoProjetoAtividade');
        $this->AddCampo('exercicio_pao'    ,'char'          ,true  ,'4'  ,false,'TOrcamentoProjetoAtividade','exercicio');
        $this->AddCampo('vigencia'         ,'date'          ,true  ,''   ,true ,false);
        $this->AddCampo('timestamp'        ,'timestamp_now' ,true  ,''   ,true ,false);
    }
    
    public function montaRecuperaRelacionamento()
    {
        $stSql  = "    SELECT configuracao_empenho.*
                            , pao.nom_pao
                            , acao.num_acao
                         FROM folhapagamento.configuracao_empenho
                   INNER JOIN (   SELECT cod_configuracao
                                       , exercicio
                                       , vigencia
                                       , max(timestamp) as timestamp
                                    FROM folhapagamento.configuracao_empenho
                                GROUP BY cod_configuracao
                                       , exercicio
                                       , vigencia
                            ) as max_configuracao_empenho
                           ON configuracao_empenho.cod_configuracao = max_configuracao_empenho.cod_configuracao
                          AND configuracao_empenho.exercicio        = max_configuracao_empenho.exercicio
                          AND configuracao_empenho.timestamp        = max_configuracao_empenho.timestamp
                   INNER JOIN orcamento.pao
                           ON configuracao_empenho.num_pao   = pao.num_pao
                          AND configuracao_empenho.exercicio = pao.exercicio
                   INNER JOIN orcamento.pao_ppa_acao
                           ON pao_ppa_acao.exercicio = pao.exercicio
                          AND pao_ppa_acao.num_pao = pao.num_pao
                   INNER JOIN ppa.acao
                           ON acao.cod_acao = pao_ppa_acao.cod_acao
                          ";
    
        return $stSql;
    }
    
    public function recuperaVigencias(&$rsRecordSet, $stFiltro="", $stOrdem="")
    {
        $obErro = $this->executaRecupera("montaRecuperaVigencias",$rsRecordSet,$stFiltro,$stOrdem);
    
        return $obErro;
    }
    
    public function montaRecuperaVigencias()
    {
        $stSql  = "  SELECT ultima_vigencia_competencia.vigencia as dt_vigencia						\n";
        $stSql .= "       , to_char(ultima_vigencia_competencia.vigencia,'dd/mm/yyyy') as vigencia	\n";
        $stSql .= "       , to_char(ultima_vigencia_competencia.vigencia,'yyyy') as exercicio	    \n";
        $stSql .= "       , ultima_vigencia_competencia.cod_periodo_movimentacao 					\n";
        $stSql .= "       , (   SELECT max(timestamp)                                               \n";
        $stSql .= "               FROM (                                                            \n";
        $stSql .= "                        SELECT max(timestamp) as timestamp                       \n";
        $stSql .= "                          FROM folhapagamento.configuracao_empenho               \n";
        $stSql .= "                         WHERE vigencia = ultima_vigencia_competencia.vigencia   \n";
        $stSql .= "                         UNION                                                   \n";
        $stSql .= "                        SELECT max(timestamp) as timestamp                       \n";
        $stSql .= "                          FROM folhapagamento.configuracao_autorizacao_empenho   \n";
        $stSql .= "                         WHERE vigencia = ultima_vigencia_competencia.vigencia   \n";
        $stSql .= "                         UNION                                                   \n";
        $stSql .= "                        SELECT max(timestamp) as timestamp                       \n";
        $stSql .= "                          FROM folhapagamento.configuracao_empenho_lla           \n";
        $stSql .= "                         WHERE vigencia = ultima_vigencia_competencia.vigencia   \n";
        $stSql .= "                    ) as max_timestamp_vigencia                                  \n";
        $stSql .= "         ) as timestamp                                                          \n";
        $stSql .= "    FROM (   SELECT DISTINCT max(vigencia) as vigencia							\n";
        $stSql .= "                  , ( SELECT cod_periodo_movimentacao 							\n";
        $stSql .= "                        FROM folhapagamento.periodo_movimentacao					\n";
        $stSql .= "                       WHERE vigencia BETWEEN dt_inicial AND dt_final			\n";
        $stSql .= "                    ) as cod_periodo_movimentacao 								\n";
        $stSql .= "               FROM ( SELECT vigencia                                            \n";
        $stSql .= "                        FROM folhapagamento.configuracao_empenho                 \n";
        $stSql .= "                       UNION                                                     \n";
        $stSql .= "                      SELECT vigencia                                            \n";
        $stSql .= "                        FROM folhapagamento.configuracao_autorizacao_empenho     \n";
        $stSql .= "                       UNION                                                     \n";
        $stSql .= "                      SELECT vigencia                                            \n";
        $stSql .= "                        FROM folhapagamento.configuracao_empenho_lla             \n";
        $stSql .= "                    ) as configuracoes_empenho  						            \n";
        $stSql .= "           GROUP BY cod_periodo_movimentacao 									\n";
        $stSql .= "         ) as ultima_vigencia_competencia										\n";
        return $stSql;
    }

    /**
        * Método Destruct
        * @access Private
    */
    public function __destruct() {}    
}
?>
