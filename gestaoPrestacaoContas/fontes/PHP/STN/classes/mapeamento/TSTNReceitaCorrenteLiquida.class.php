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
 * Mapeamento da tabela stn.receita_corrente_liquida
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TSTNReceitaCorrenteLiquida extends Persistente
{
    /**
     * Método Construtor da classe TSTNReceitaCorrenteLiquida
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('stn.receita_corrente_liquida');
        $this->setCampoCod        ('');
        $this->setComplementoChave('mes, ano, exercicio, cod_entidade');

        $this->AddCampo('mes'                                  , 'integer', true, ''    , true , false);
        $this->AddCampo('ano'                                  , 'varchar', true, '4'   , true , false);
        $this->AddCampo('exercicio'                            , 'varchar', true, '4'   , true , false);
        $this->AddCampo('cod_entidade'                         , 'integer', true, ''    , true , true );
        $this->AddCampo('valor_receita_tributaria'             , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_iptu'                           , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_iss'                            , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_itbi'                           , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_irrf'                           , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_outras_receitas_tributarias'    , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_receita_contribuicoes'          , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_receita_patrimonial'            , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_receita_agropecuaria'           , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_receita_industrial'             , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_receita_servicos'               , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_transferencias_correntes'       , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_cota_parte_fpm'                 , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_cota_parte_icms'                , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_cota_parte_ipva'                , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_cota_parte_itr'                 , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_transferencias_lc_871996'       , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_transferencias_lc_611989'       , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_transferencias_fundeb'          , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_outras_transferencias_correntes', 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_outras_receitas'                , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_contrib_plano_sss'              , 'numeric', true, '14,2', false, false);   
        $this->AddCampo('valor_compensacao_financeira'         , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor_deducao_fundeb'                 , 'numeric', true, '14,2', false, false);
        $this->AddCampo('valor'                                , 'numeric', true, '14,2', false, false);
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
            SELECT receita_corrente_liquida.exercicio
                 , receita_corrente_liquida.cod_entidade
                 , receita_corrente_liquida.mes
                 , receita_corrente_liquida.ano
                 , receita_corrente_liquida.valor
              FROM stn.receita_corrente_liquida
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}
