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
    * Classe de mapeamento da contabilidade.lancamento_reavaliacao
    * Data de Criação: 18/05/2016

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TContabilidadeLancamentoReavaliacao.class.php 66372 2016-08-19 19:06:35Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TContabilidadeLancamentoReavaliacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::__construct();
        $this->setTabela('contabilidade.lancamento_reavaliacao');

        $this->setCampoCod('');
        $this->setComplementoChave('id, exercicio, cod_entidade, tipo, cod_lote, sequencia, cod_reavaliacao, cod_bem');

        $this->AddCampo('id'              ,'integer'   ,true ,''   ,true  ,false);
        $this->AddCampo('competencia'     ,'char'      ,true ,'06' ,false ,false);
        $this->AddCampo('exercicio'       ,'char'      ,true ,'04' ,false ,true);
        $this->AddCampo('cod_entidade'    ,'integer'   ,true ,''   ,false ,true);
        $this->AddCampo('tipo'            ,'char'      ,true ,'01' ,false ,true);
        $this->AddCampo('cod_lote'        ,'integer'   ,true ,''   ,false ,true);
        $this->AddCampo('sequencia'       ,'integer'   ,true ,''   ,false ,true);
        $this->AddCampo('cod_reavaliacao' ,'integer'   ,true ,''   ,false ,true);
        $this->AddCampo('cod_bem'         ,'integer'   ,true ,''   ,false ,true);
        $this->AddCampo('estorno'         ,'boolean'   ,true ,''   ,false ,false);
        $this->AddCampo('timestamp'       ,'timestamp' ,true ,'04' ,false ,false);
    }

    public function insereLancamentoReavaliacao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaInsereLancamentoReavaliacao();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaInsereLancamentoReavaliacao()
    {

        $stSql  = " SELECT contabilidade.fn_insere_lancamentos_reavaliacao(  '".$this->getDado("exercicio")."'
                                                                           , '".$this->getDado("competencia")."'
                                                                           , '".$this->getDado("dt_inicial")."'
                                                                           , '".$this->getDado("dt_final")."'
                                                                           , '".$this->getDado("cod_entidade")."'
                                                                           , ".$this->getDado("cod_historico")."
                                                                           , '".$this->getDado("tipo")."'
                                                                           , '".$this->getDado("complemento")."'
                                                                           , ".$this->getDado("estorno")."
                                                                           , '".$this->getDado("cod_bem")."'
                                                                          ); ";
        return $stSql;
    }

    public function verificaLancamentosAnteriores(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaVerificaLancamentosAnteriores().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaVerificaLancamentosAnteriores()
    {
        $stSql  = "   SELECT *
                        FROM contabilidade.lancamento_reavaliacao

                  INNER JOIN patrimonio.reavaliacao
                          ON reavaliacao.cod_reavaliacao = lancamento_reavaliacao.cod_reavaliacao
                         AND reavaliacao.cod_bem         = lancamento_reavaliacao.cod_bem

                       WHERE lancamento_reavaliacao.exercicio = '".$this->getDado("exercicio")."'
                         AND lancamento_reavaliacao.timestamp = ( SELECT MAX(lancamento_interno.timestamp) AS timestamp
                                                                    FROM contabilidade.lancamento_reavaliacao AS lancamento_interno
                                                                   WHERE lancamento_interno.competencia  = '".$this->getDado("competencia")."'
                                                                     AND lancamento_interno.cod_entidade = ".$this->getDado("cod_entidade")."
                                                                     AND lancamento_interno.exercicio    = '".$this->getDado("exercicio")."'
                                                                 ) \n";
        return $stSql;
    }

    public function recuperaMinCompetenciaLancada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

        $stGroup = " \n GROUP BY lancamento_depreciacao.estorno ";

        $stSql = $this->montaRecuperaMinCompetenciaLancada().$stFiltro.$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMinCompetenciaLancada()
    {
        $stSql  = " SELECT MIN(depreciacao.competencia) AS min_competencia
                         , TO_CHAR(TO_DATE(MIN(depreciacao.competencia), 'YYYYMM'), 'MM/YYYY') AS min_competencia_formatada
                         , lancamento_depreciacao.estorno
                     FROM contabilidade.lancamento_depreciacao

               INNER JOIN patrimonio.depreciacao
                       ON lancamento_depreciacao.cod_depreciacao       = depreciacao.cod_depreciacao
                      AND lancamento_depreciacao.cod_bem               = depreciacao.cod_bem
                      AND lancamento_depreciacao.timestamp_depreciacao = depreciacao.timestamp
        ";

        return $stSql;
    }

    public function recuperaMaxCompetenciaLancada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

        $stGroup = " \n GROUP BY lancamento_reavaliacao.estorno
                               , lancamento_reavaliacao.exercicio  ";

        $stSql = $this->montaRecuperaMaxCompetenciaLancada().$stFiltro.$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMaxCompetenciaLancada()
    {
        $stSql  = "  SELECT MAX(lancamento_reavaliacao.competencia) AS max_competencia
                          , TO_CHAR(TO_DATE(lancamento_reavaliacao.exercicio || MAX(lancamento_reavaliacao.competencia), 'YYYYMM'), 'MM/YYYY') AS max_competencia_formatada
                          , lancamento_reavaliacao.estorno
                          , lancamento_reavaliacao.exercicio

                       FROM contabilidade.lancamento_reavaliacao

                 INNER JOIN patrimonio.reavaliacao
                         ON lancamento_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao
                        AND lancamento_reavaliacao.cod_bem         = reavaliacao.cod_bem
        ";

        return $stSql;
    }

}

?>