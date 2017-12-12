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
    * Extensão da Classe de mapeamento TOrcamentoProjetoAtividade
    * Data de Criação: 13/03/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 13/03/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTPBLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBLicitacao()
{
    parent::Persistente();
    $this->setDado('exercicio',Sessao::getExercicio());
}

function montaRecuperaTodos()
{
    $stSql  = " select                                                                                     \n";
    $stSql .= "      licitacao.cod_licitacao || licitacao.cod_entidade as num_licitacao                    \n";
    $stSql .= "     ,tcepb.fn_depara_modalidade_licitacao(licitacao.cod_modalidade) as cod_modalidade      \n";
    $stSql .= "     ,to_char(edital.dt_aprovacao_juridico, 'dd/mm/yyyy' ) as data_homologacao              \n";
    $stSql .= "     ,tot_cotacao.num_prop_participantes                                                    \n";
    $stSql .= "     ,tcepb.fn_depara_objeto_licitacao(licitacao.cod_tipo_objeto) as id_objeto              \n";
    $stSql .= "     ,licitacao.vl_cotado as valor_estimado                                                 \n";
    $stSql .= "     ,objeto.descricao as desc_objeto                                                       \n";
    $stSql .= " from                                                                                       \n";
    $stSql .= "     licitacao.licitacao                                                                    \n";
    $stSql .= "     ,licitacao.edital                                                                      \n";
    $stSql .= "     ,compras.objeto                                                                        \n";
    $stSql .= "     ,(                                                                                     \n";
    $stSql .= "         select                                                                             \n";
    $stSql .= "              mapa.cod_mapa                                                                 \n";
    $stSql .= "             ,mapa.exercicio                                                                \n";
    $stSql .= "             ,count(1) as num_prop_participantes                                            \n";
    $stSql .= "         from                                                                               \n";
    $stSql .= "              compras.mapa                                                                  \n";
    $stSql .= "             ,compras.mapa_cotacao                                                          \n";
    $stSql .= "             ,compras.cotacao                                                               \n";
    $stSql .= "             ,compras.cotacao_item                                                          \n";
    $stSql .= "             ,compras.cotacao_fornecedor_item                                               \n";
    $stSql .= "         where                                                                              \n";
    $stSql .= "                 mapa.exercicio = mapa_cotacao.exercicio_mapa                               \n";
    $stSql .= "             and mapa.cod_mapa  = mapa_cotacao.cod_mapa                                     \n";
    $stSql .= "                                                                                            \n";
    $stSql .= "             and mapa_cotacao.cod_cotacao = cotacao.cod_cotacao                             \n";
    $stSql .= "             and mapa_cotacao.exercicio_cotacao   = cotacao.exercicio                       \n";
    $stSql .= "                                                                                            \n";
    $stSql .= "             and cotacao.exercicio = cotacao_item.exercicio                                 \n";
    $stSql .= "             and cotacao.cod_cotacao = cotacao_item.cod_cotacao                             \n";
    $stSql .= "                                                                                            \n";
    $stSql .= "             and cotacao_item.exercicio = cotacao_fornecedor_item.exercicio                 \n";
    $stSql .= "             and cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao             \n";
    $stSql .= "             and cotacao_item.cod_item = cotacao_fornecedor_item.cod_item                   \n";
    $stSql .= "             and cotacao_item.lote = cotacao_fornecedor_item.lote                           \n";
    $stSql .= "                                                                                            \n";
    $stSql .= "             and mapa.exercicio = '".$this->getDado('exercicio')."'                         \n";
    $stSql .= "                                                                                            \n";
    $stSql .= "             and not exists ( select                                                        \n";
    $stSql .= "                                 1                                                          \n";
    $stSql .= "                              from                                                          \n";
    $stSql .= "                                 compras.cotacao_anulada                                    \n";
    $stSql .= "                              where                                                         \n";
    $stSql .= "                                     cotacao.cod_cotacao = cotacao_anulada.cod_cotacao      \n";
    $stSql .= "                                 and cotacao.exercicio   = cotacao_anulada.exercicio        \n";
    $stSql .= "                                 and exercicio = '".$this->getDado('exercicio')."'          \n";
    $stSql .= "                            )                                                               \n";
    $stSql .= "                       group by                                                             \n";
    $stSql .= "                            mapa.cod_mapa                                                   \n";
    $stSql .= "                           ,mapa.exercicio                                                  \n";
    $stSql .= "      ) as tot_cotacao                                                                      \n";
    $stSql .= " where                                                                                      \n";
    $stSql .= "         edital.exercicio_licitacao = licitacao.exercicio                                   \n";
    $stSql .= "     and edital.cod_entidade        = licitacao.cod_entidade                                \n";
    $stSql .= "     and edital.cod_modalidade      = licitacao.cod_modalidade                              \n";
    $stSql .= "     and edital.cod_licitacao       = licitacao.cod_licitacao                               \n";
    $stSql .= "                                                                                            \n";
    $stSql .= "     and licitacao.cod_objeto = objeto.cod_objeto                                           \n";
    $stSql .= "     and licitacao.exercicio = '".$this->getDado('exercicio')."'                            \n";
    $stSql .= "                                                                                            \n";
    $stSql .= "     and tot_cotacao.cod_mapa  = licitacao.cod_mapa                                         \n";
    $stSql .= "     and tot_cotacao.exercicio = licitacao.exercicio                                        \n";

    if ( $this->getDado('stEntidades') ) {
        $stSql .= " and licitacao.cod_entidade  in  ( ".$this->getDado('stEntidades')." )  \n";
    }

    if ( $this->getDado('inMes') ) {
        $stSql .= " and to_char(licitacao.timestamp,'mm') = '".$this->getDado('inMes')."'  \n";
    }

    return $stSql;
}

}
