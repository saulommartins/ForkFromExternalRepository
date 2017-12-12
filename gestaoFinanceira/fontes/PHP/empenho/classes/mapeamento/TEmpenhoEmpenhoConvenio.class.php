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
    * Classe de mapeamento da tabela empenho.empenho_convenio
    * Data de Criação: 28/02/2008

    * @author Analista: Tonismar
    * @author Desenvolvedor: Alexandre Melo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.03.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoEmpenhoConvenio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoEmpenhoConvenio()
{
    parent::Persistente();
    $this->setTabela("empenho.empenho_convenio");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_empenho');

    $this->AddCampo('exercicio'   , 'char'   , true  , '04'  , true, 'TLicitacaoConvenio' );
    $this->AddCampo('cod_entidade', 'integer', true  , ''    , true, 'TEmpenhoEmpenho'    );
    $this->AddCampo('cod_empenho' , 'integer', true  , ''    , true, 'TEmpenhoEmpenho'    );
    $this->AddCampo('num_convenio', 'integer', true  , ''    , false ,'TLicitacaoConvenio');
//  $this->AddCampo('GRANT'       ,   'select, false , ''    , false 					  );

}

function recuperaConvenioEmpenhoParticipante(&$rsRecordSet, $stFiltro = "", $stOrder)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaConvenioEmpenhoParticipante().$stFiltro.$stOrder;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql);

    return $obErro;
}

function montaRecuperaConvenioEmpenhoParticipante()
{
    $stSql  = "SELECT 														\n";
    $stSql .= " 	  ec.num_convenio   			  					 	\n";
    $stSql .= "		, ec.exercicio											\n";
    $stSql .= "		, to_char(c.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura  \n";
    $stSql .= "		, to_char(c.dt_vigencia, 'dd/mm/yyyy') as dt_vigencia	   \n";
    $stSql .= "		, cgm.nom_cgm 		as nom_participante					\n";
    $stSql .= "		, cgm.numcgm											\n";
    $stSql .= "     , c.fundamentacao                                       \n";
    $stSql .= "  FROM 														\n";
    $stSql .= " 	  empenho.empenho_convenio    	  as ec   				\n";
    $stSql .= "     , licitacao.convenio			  as c					\n";
    $stSql .= "     , licitacao.participante_convenio as pc					\n";
    $stSql .= "       JOIN( SELECT numcgm									\n";
    $stSql .= "					 , nom_cgm									\n";
    $stSql .= "				  FROM sw_cgm ) as cgm							\n";
    $stSql .= "		    ON ( cgm.numcgm = pc.cgm_fornecedor ) 				\n";
    $stSql .= " WHERE 														\n";
    $stSql .= " 	  ec.exercicio 	  = c.exercicio							\n";
    $stSql .= "   AND ec.num_convenio = c.num_convenio						\n";
    $stSql .= "   AND c.num_convenio  = pc.num_convenio						\n";
    $stSql .= "   AND c.exercicio     = pc.exercicio						\n";
    $stSql .= "GROUP BY ec.num_convenio                                     \n";
    $stSql .= "                  , ec.exercicio                             \n";
    $stSql .= "                  , dt_assinatura                            \n";
    $stSql .= "                  , dt_vigencia                              \n";
    $stSql .= "                  , nom_participante                         \n";
    $stSql .= "                  , cgm.numcgm                               \n";
    $stSql .= "                  , c.fundamentacao                          \n";

    return $stSql;
}

function recuperaConvenioEmpenhoItem(&$rsRecordSet, $stFiltro = "", $stOrder)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stGroupBy = "\n GROUP BY ec.num_convenio
    , e.cod_empenho
    , e.exercicio
    , to_char( e.dt_empenho,   'dd/mm/yyyy')
    , to_char( e.dt_vencimento, 'dd/mm/yyyy')
    , e.vl_saldo_anterior
    , e.cod_entidade
    , cgm.nom_cgm \n";
    $stSql = $this->montaRecuperaConvenioEmpenhoItem().$stFiltro.$stGroupBy.$stOrder;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql);

    return $obErro;
}

function montaRecuperaConvenioEmpenhoItem()
{
    $stSql  = "	SELECT ec.num_convenio									\n";
    $stSql .= "		 , e.cod_empenho									\n";
    $stSql .= "		 , e.exercicio										\n";
    $stSql .= "		 , to_char( e.dt_empenho,   'dd/mm/yyyy') as dt_empenho  	\n";
    $stSql .= "		 , to_char( e.dt_vencimento, 'dd/mm/yyyy') as dt_vencimento	\n";
    $stSql .= "		 , e.vl_saldo_anterior 								\n";
    $stSql .= "		 , e.cod_entidade									\n";
    $stSql .= "		 , sum(ie.vl_total) AS vl_total 					\n";
    $stSql .= "		 , cgm.nom_cgm as nom_credor						\n";
    $stSql .= "	  FROM empenho.empenho_convenio 		as ec 			\n";
    $stSql .= "		 , empenho.empenho				    as e			\n";
    $stSql .= "		 , empenho.pre_empenho				as pe			\n";
    $stSql .= "		   JOIN( SELECT nom_cgm								\n";
    $stSql .= "					  , numcgm								\n";
    $stSql .= "				   FROM sw_cgm ) as cgm						\n";
    $stSql .= "			 ON( cgm.numcgm = pe.cgm_beneficiario )			\n";
    $stSql .= "		 , empenho.item_pre_empenho			as ie			\n";
    $stSql .= "	 WHERE ec.exercicio       = e.exercicio					\n";
    $stSql .= "	   AND ec.cod_empenho     = e.cod_empenho				\n";
    $stSql .= "	   AND ec.cod_entidade    = e.cod_entidade				\n";
    $stSql .= "	   AND e.exercicio		  = pe.exercicio   				\n";
    $stSql .= "	   AND e.cod_pre_empenho  = pe.cod_pre_empenho			\n";
    $stSql .= "    AND ie.cod_pre_empenho = pe.cod_pre_empenho 			\n";
    $stSql .= "    AND ie.exercicio       = pe.exercicio   				\n";

    return $stSql;
}

}
?>
