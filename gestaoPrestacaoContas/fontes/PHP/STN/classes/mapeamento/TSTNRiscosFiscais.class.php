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
    * Arquivo de mapeamento da tabela stn.riscos_fiscais
    * Data de Criação   : 01/06/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
 */

include_once CLA_PERSISTENTE;

class TSTNRiscosFiscais extends Persistente
{
    /**
     * Método Construtor da classe TSTNRiscosFiscais
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('stn.riscos_fiscais');
        $this->setCampoCod        ('cod_risco');
        $this->setComplementoChave('cod_entidade, exercicio');

        $this->AddCampo('cod_risco'        , 'integer', true , ''    , true , false);
        $this->AddCampo('cod_entidade'     , 'integer', true , ''    , true , true);
        $this->AddCampo('exercicio'        , 'char'   , true , '4'   , true , true);
        $this->AddCampo('descricao'        , 'varchar', true , '100' , false, false);
        $this->AddCampo('valor'            , 'numeric', true , '14,2', false, false);
        $this->AddCampo('cod_identificador', 'integer', false, ''    , false, true);
    }

    /**
     * Método que retorna os riscos fiscais
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listRiscosFiscais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT riscos_fiscais.cod_risco
                 , riscos_fiscais.cod_entidade
                 , riscos_fiscais.exercicio
                 , riscos_fiscais.descricao
                 , riscos_fiscais.valor
                 , riscos_fiscais.cod_identificador
                 , sw_cgm.nom_cgm
              FROM stn.riscos_fiscais
              JOIN orcamento.entidade
                ON entidade.cod_entidade = riscos_fiscais.cod_entidade
               AND entidade.exercicio    = riscos_fiscais.exercicio
              JOIN sw_cgm
                ON sw_cgm.numcgm = entidade.numcgm
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    /**
     * Método que retorna risco fiscal - Returna um único resultado
     *
     * @author      Analista        Ane Caroline   
     * @author      Desenvolvedor   Carlos Adriano 
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function buscaRiscoFiscal(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT riscos_fiscais.cod_risco
                 , riscos_fiscais.cod_entidade
                 , riscos_fiscais.exercicio
                 , riscos_fiscais.descricao
                 , riscos_fiscais.valor
                 , riscos_fiscais.cod_identificador
                 , sw_cgm.nom_cgm
              FROM stn.riscos_fiscais
              JOIN orcamento.entidade
                ON entidade.cod_entidade = riscos_fiscais.cod_entidade
               AND entidade.exercicio    = riscos_fiscais.exercicio
              JOIN sw_cgm
                ON sw_cgm.numcgm = entidade.numcgm
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
     * Método que retorna os riscos fiscais para exportação do TCEMG
     *
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listRiscosFiscaisExportacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
    SELECT 10 AS tipo_registro
         , riscos_fiscais.cod_risco
         , codigo_unidade_gestora.cod_orgao
         , riscos_fiscais.exercicio
         , 'config' AS cod_risco_fiscal
         , riscos_fiscais.descricao
         , riscos_fiscais.valor
      FROM stn.riscos_fiscais
 LEFT JOIN ( SELECT valor AS cod_orgao
                  , cod_entidade
               FROM administracao.configuracao_entidade
              WHERE parametro = 'tcemg_codigo_unidade_gestora'
                AND exercicio = '".Sessao::getExercicio()."'
         ) AS codigo_unidade_gestora
        ON codigo_unidade_gestora.cod_entidade = riscos_fiscais.cod_entidade
       AND riscos_fiscais.cod_entidade IN (".$this->getDado('entidades').")
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function listaRiscosFiscaisExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
                   SELECT 10 AS tipo_registro
                        , riscos_fiscais.cod_risco
                        , codigo_unidade_gestora.cod_orgao
                        , riscos_fiscais.exercicio
                        , riscos_fiscais.cod_identificador AS cod_risco_fiscal
                        , CASE WHEN riscos_fiscais.cod_identificador = 10
                               THEN riscos_fiscais.descricao
                               ELSE ' '
                           END AS descricao
                        , riscos_fiscais.valor
                     FROM stn.riscos_fiscais
                LEFT JOIN ( SELECT valor AS cod_orgao
                                 , cod_entidade
                              FROM administracao.configuracao_entidade
                             WHERE parametro = 'tcemg_codigo_orgao_entidade_sicom'
                               AND exercicio = '".Sessao::getExercicio()."'
                        ) AS codigo_unidade_gestora
                       ON codigo_unidade_gestora.cod_entidade = riscos_fiscais.cod_entidade
                      AND riscos_fiscais.cod_entidade IN (".$this->getDado('entidades').")
                WHERE stn.riscos_fiscais.exercicio = '".Sessao::getExercicio()."'
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}
