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
 * Mapeamento da tabela stn.despesa_pessoal
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TSTNDespesaPessoal extends Persistente
{
    /**
     * Método Construtor da classe TSTNDespesaPessoal
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('stn.despesa_pessoal');
        $this->setCampoCod        ('');
        $this->setComplementoChave('mes, ano, exercicio, cod_entidade');

        $this->AddCampo('mes'                         , 'integer', true, ''    , true , false);
        $this->AddCampo('ano'                         , 'varchar', true, '4'   , true , false);
        $this->AddCampo('exercicio'                   , 'varchar', true, '4'   , true , false);
        $this->AddCampo('cod_entidade'                , 'integer', true, ''    , true , true );
        $this->AddCampo('valor_pessoal_ativo'         , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_pessoal_inativo'       , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_terceirizacao'         , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_indenizacoes'          , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_decisao_judicial'      , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_exercicios_anteriores' , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_inativos_pensionistas' , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor'                       , 'numeric', true, '14,2', false, false);
    }

    /**
     * Método que retorna os valores dos periodos
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
    public function listValorPeriodo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT despesa_pessoal.exercicio
                 , despesa_pessoal.cod_entidade
                 , despesa_pessoal.mes
                 , despesa_pessoal.ano
                 , despesa_pessoal.valor
              FROM stn.despesa_pessoal
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    /**
     * Método que retorna os valores dos periodos
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
    public function listValorEntidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT despesa_pessoal.exercicio
                 , despesa_pessoal.cod_entidade
                 , despesa_pessoal.mes
                 , despesa_pessoal.ano
                 , despesa_pessoal.valor
                 , despesa_total.total as total
              FROM stn.despesa_pessoal
      INNER JOIN ( SELECT SUM(valor) AS total
                                   , exercicio
                                   , cod_entidade                 
                            FROM stn.despesa_pessoal
                          WHERE despesa_pessoal.mes >   ".$this->getDado('mes')."                    
                         GROUP BY exercicio
                                       , cod_entidade    
                        ) AS despesa_total
                 ON despesa_total.exercicio = despesa_pessoal.exercicio
               AND despesa_total.cod_entidade = despesa_pessoal.cod_entidade
          WHERE despesa_pessoal.cod_entidade IN (".$this->getDado('cod_entidades').")
                 AND  despesa_pessoal.exercicio = '".$this->getDado('exercicio')."'
                 AND  despesa_pessoal.mes >   '".$this->getDado('mes')."'
             ORDER BY despesa_pessoal.cod_entidade,  despesa_pessoal.mes
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}
