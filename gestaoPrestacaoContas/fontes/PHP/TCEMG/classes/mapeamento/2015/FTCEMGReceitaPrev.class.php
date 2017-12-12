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
    * Arquivo de mapeamento para a função que busca os dados do arquivo receitaPrev
    * Data de Criação   : 22/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGReceitaPrev extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function __construct()
    {
        parent::Persistente();
    
        $this->setTabela('tcemg.fn_receita_prev');
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
              SELECT EXTRACT( month FROM TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') ) AS periodo
                   , cod_tipo
                   , contrib_pat
                   , contrib_serv_ativo
                   , contrib_serv_inat_pens
                   , rec_patrimoniais
                   , alienacao_bens
                   , outras_rec_cap
                   , comp_prev
                   , outras_rec
                   , deducoes_receita
                   , 0.00 as contrib_pat_anterior
                   , 0.00 as repasses_prev
                   , receitas_prev_intra
                   , 0.00 as plano_fin_recursos_cobertura
                   , 0.00 as plano_fin_repasse_pag_resp
                   , 0.00 as plano_fin_rec_formacao_reserva
                   , 0.00 as plano_fin_outros_aportes_rpps
                   , 0.00 as plano_prev_rec_cob_def_fin
                   , 0.00 as plano_prev_rec_cob_def_atuarial
                   , 0.00 as plano_prev_outros_aportes_rpps
                FROM tcemg.fn_receita_prev( '".$this->getDado("exercicio")."'
                                          , '".$this->getDado("cod_entidade")."'
                                          , '".$this->getDado("dt_inicial")."'
                                          , '".$this->getDado("dt_final")."')
                  AS retorno( cod_tipo               VARCHAR
                            , contrib_pat            VARCHAR
                            , contrib_serv_ativo     VARCHAR
                            , contrib_serv_inat_pens VARCHAR
                            , rec_patrimoniais       VARCHAR
                            , alienacao_bens         VARCHAR
                            , outras_rec_cap         VARCHAR
                            , comp_prev              VARCHAR
                            , outras_rec             VARCHAR
                            , deducoes_receita       VARCHAR
                            , receitas_prev_intra    VARCHAR
                            )
        ";
        return $stSql;
    }
    
    public function __destruct(){}
}
