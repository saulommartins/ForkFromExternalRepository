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
    * Classe de mapeamento da tabela TESOURARIA.CONCILIACAO_LANCAMENTO_MANUAL
    * Data de Criação: 07/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32224 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.19
*/

/*
$Log$
Revision 1.3  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTesourariaConciliacaoLancamentoManual extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaConciliacaoLancamentoManual()
{
    parent::Persistente();
    $this->setTabela("tesouraria.conciliacao_lancamento_manual");

    $this->setComplementoChave('exercicio,mes,cod_plano,sequencia');

    $this->AddCampo('cod_plano'            , 'integer'   , true, ''     , true  , true  );
    $this->AddCampo('exercicio'            , 'varchar'   , true, '04'   , true  , true  );
    $this->AddCampo('mes'                  , 'integer'   , true, ''     , true  , true  );
    $this->AddCampo('sequencia'            , 'integer'   , true, ''     , true  , false );
    $this->AddCampo('dt_lancamento'        , 'date'      , true, ''     , false , false );
    $this->AddCampo('vl_lancamento'        , 'numeric'   , true,'14.2' , false , false );
    $this->AddCampo('tipo_valor'           , 'char'      , true, '01'   , false , false );
    $this->AddCampo('descricao'            , 'text'      , true, ''     , false , false );
    $this->AddCampo('conciliado'           , 'boolean'   , true, ''     , false , false );
    $this->AddCampo('dt_conciliacao'       , 'date'      , false, ''    , false , false );

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function buscaProximaSequencia(&$rsRecordSet, $stCondicao = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaSequencia().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSequencia()
{
    $stSql  = " SELECT                                                                      \n";
    $stSql .= "     coalesce(max(sequencia),0)+1 as sequencia                                           \n";
    $stSql .= " FROM                                                                        \n";
    $stSql .= "     tesouraria.conciliacao_lancamento_manual                                \n";

    return $stSql;
}

    /**
     * Método que retorna os lancamentos manuais
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listLancamentosManuais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT cod_plano
                 , exercicio
                 , mes
                 , sequencia
                 , TO_CHAR(dt_lancamento,'dd/mm/yyyy') AS dt_lancamento
                 , vl_lancamento
                 , tipo_valor
                 , descricao
                 , conciliado
                 , CAST(CASE WHEN conciliado IS TRUE
                             THEN TO_CHAR(dt_conciliacao,'dd/mm/yyyy')
                             ELSE ''
                        END AS VARCHAR) AS dt_conciliacao ";
                        
        if (SistemaLegado::isTCMBA($boTransacao)) {
            $stSql.= " , ( SELECT cod_tipo_conciliacao    
                             FROM tcmba.conciliacao_lancamento_manual AS tcmba
                            WHERE tcmba.cod_plano     = conciliacao_lancamento_manual.cod_plano
                              AND tcmba.exercicio     = conciliacao_lancamento_manual.exercicio
                              AND tcmba.mes           = conciliacao_lancamento_manual.mes
                              AND tcmba.sequencia     = conciliacao_lancamento_manual.sequencia
                              AND tcmba.dt_lancamento = conciliacao_lancamento_manual.dt_lancamento
                              AND tcmba.vl_lancamento = conciliacao_lancamento_manual.vl_lancamento
                              AND tcmba.tipo_valor    = conciliacao_lancamento_manual.tipo_valor
                              AND tcmba.descricao     = conciliacao_lancamento_manual.descricao ) AS cod_tipo_conciliacao ";
        }
              
        $stSql.= " FROM tesouraria.conciliacao_lancamento_manual ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}
